<?php
class base_rpc_service{

    private $start_time;

    public function process($path){

        if(!kernel::is_online()){
            die('error');
        }else{
            require(ROOT_DIR.'/config/config.php');
            @include(APP_DIR.'/base/defined.php');
        }

        if($path=='/api'){
            $this->process_rpc();
        }else{
            $args = explode('/',substr($path,5));
            $service_name = 'api.'.array_shift($args);
            $method = array_shift($args);
            foreach($args as $i=>$v){
                if($i%2){
                    $params[$k] = str_replace('%2F','/',$v);
                }else{
                    $k = $v;
                }
            }
            kernel::service($service_name)->$method($params);
        }
    }


    //app_id	 String	 Y	 分配的APP_KEY
    //method	 String	 Y	 api接口名称
    //date	 string	 Y	 时间戳，为datetime格式
    //format	 string	 Y	 响应格式，xml[暂无],json
    //certi_id	 int	 Y	 分配证书ID
    //v	 string	 Y	 API接口版本号
    //sign	 string	 Y	 签名，见生成sign
    private function parse_rpc_request($request){

        $sign = $request['sign'];
        unset($request['sign']);
        $sign_check = base_certificate::gen_sign($request);
        if($sign != $sign_check){
            trigger_error('sign error',E_USER_ERROR);
            return false;
        }

        $system_params = array('app_id','method','date','format','certi_id','v','sign');
        foreach($system_params as $name){
            $call[$name] = $request[$name];
            unset($request[$name]);
        }

        //if method request = 'aaa.bbb.ccc.ddd'
        //then: object_service = api.aaa.bbb.ccc, method=ddd
        if(isset($call['method']{2})){
            if($p = strrpos($call['method'],'.')){
                $service = 'api.'.substr($call['method'],0,$p);
                $method = substr($call['method'],$p+1);
            }
        }else{
            trigger_error('error method',E_ERROR);
            return false;
        }
        return array($service,$method,$request);
    }

    private function gen_uniq_process_id(){
        return uniqid();
    }

    private function process_rpc(){

        ignore_user_abort();
        set_time_limit(0);
        $process_id = $this->gen_uniq_process_id();
        header('Process-id: '.$process_id);
        header('Connection: close');
        flush();
        
        if(get_magic_quotes_gpc()){
            kernel::strip_magic_quotes($_REQUEST);
        }
        
        base_logger::begin(__FUNCTION__);
        set_error_handler(array(&$this,'user_error_handle'),E_USER_ERROR);

        $this->start_time = $_SERVER['REQUEST_TIME']?$_SERVER['REQUEST_TIME']:time();
        list($service,$method,$params) = $this->parse_rpc_request($_REQUEST);
        $data = array(
            'network'=>$this->network, //要读到来源，要加密
            'method'=>$service,
            'calltime'=>$this->start_time,
            'params'=>$request->params,
            'type'=>'response',
            'process_id'=>$process_id,
            'callback'=>$_SERVER['HTTP_CALLBACK'],
        );
        app::get('base')->model('rpcpoll')->insert($data);
        $object = kernel::service($service);
        $result = $object->$method($params,$this);
        $output = base_logger::end();

        $result_json = array(
            'rsp'=>'succ',
            'data'=>$result,
            'res'=>strip_tags($output)
        );

        $connection_aborted = $this->connection_aborted();

        if($connection_aborted){
            app::get('base')->model('rpcpoll')->update(array('result'=>$result),array('process_id'=>$process_id,'type'=>'response'));
            if($_SERVER['HTTP_CALLBACK']){
                $return = kernel::single('base_httpclient')->get($_SERVER['HTTP_CALLBACK'].'?'.json_encode($result_json));
                $return = json_decode($return);
                if($return->result=='ok'){
                    app::get('base')->model('rpcpoll')->delete(array('process_id'=>$process_id,'type'=>'response'));
                }
            }
        }else{
            app::get('base')->model('rpcpoll')->delete(array('process_id'=>$process_id,'type'=>'response'));
        }
        echo json_encode($result_json);
    }

    private function connection_aborted(){
        $return = connection_aborted();
        if(!$return){
            if(is_numeric($_SERVER['HTTP_CONNECTION']) && $_SERVER['HTTP_CONNECTION']>0){
                if(time()-$this->start_time>=$_SERVER['HTTP_CONNECTION']){
                    $return = true;
                }
            }
        }
        return $return;
    }

    public function async_result_handler($params){

        base_logger::begin(__FUNCTION__);

        $result = new base_rpc_result($_POST);
        $row = app::get('base')->model('rpcpoll')->getlist('callback,callback_params',array('id'=>$params['id'],'type'=>'request'),0,1);
        if($row){
            list($class,$method) = explode(':',$row[0]['callback']);
            if($class && $method){
                $result->set_callback_params(unserialize($row[0]['callback_params']));
                kernel::single($class)->$method($result);
            }
        }
        $return = array(
            'rst'=>'ok',
            'id'=>$params['id'],
            'error'=>null,
        );
        app::get('base')->model('rpcpoll')->delete(array('id'=>$params['id'],'type'=>'request'));

        if(!$return){
            $return = array(
                'id'=>$request->id,
                'error'=>'bad request-id:'.$request->id,
            );
        }

        base_logger::end();

        header('Content-type: text/plain');
        echo json_encode($return);
    }
    
    function user_error_handle($error_code,$error_msg){
        echo $error_msg;
        exit;
    }

}
