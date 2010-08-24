<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class base_rpc_caller{

    var $timeout = 10;

    function __construct(&$app,$node_id){
        $this->network_id = $node_id;
        $this->app = $app;
    }

    private function begin_transaction($method,$params){

        $rpc_id = rand(0,time());

        $data = array(
            'id'=>$rpc_id,
            'network'=>$this->network_id,
            'calltime'=>time(),
            'method'=>$method,
            'params'=>$params,
            'type'=>'request',
            'callback'=>$this->callback_class.':'.$this->callback_method,
            'callback_params'=>$this->callback_params,
        );

        app::get('base')->model('rpcpoll')->insert($data);
        return $rpc_id;
    }

    private function get_url($node){
        $row = app::get('base')->model('network')->getlist('node_url,node_api', array('node_id'=>$this->network_id));
        if($row){
            if(substr($row[0]['node_url'],-1,1)!='/'){
                $row[0]['node_url'] = $row[0]['node_url'].'/';
            }
            if($row[0]['node_api']{0}=='/'){
                $row[0]['node_api'] = substr($row[0]['node_api'],1);
            }
            $url = $row[0]['node_url'].$row[0]['node_api'];
        }

        return $url;
    }

    public function call($method,$params){
        $rpc_id = $this->begin_transaction($method,$params);

        $headers = array(
            'Connection'=>$this->timeout,
        );

        $query_params = array(
            'app_id'=>'ecos.'.$this->app->app_id,
            'method'=>$method,
            'date'=>date('Y-m-d H:i:s'),
            'callback_url'=>kernel::api_url('api.rpc_callback','async_result_handler',array('id'=>$rpc_id)),
            'format'=>'json',
            'certi_id'=>base_certificate::certi_id(),
            'v'=>$this->api_version($method),
			'from_node_id' => base_shopnode::node_id($this->app->app_id),
        );
		
		$query_params['node_id'] = $params['to_node_id'];

        $query_params = array_merge((array)$params,$query_params);
        $query_params['sign'] = base_certificate::gen_sign($query_params);

        $url = $this->get_url($this->network_id);
		
        $core_http = kernel::single('base_httpclient');
        $response = $core_http->post($url,$query_params,$headers);
		
        if($response===HTTP_TIME_OUT){
            $headers = $core_http->responseHeader;
            kernel::log('Request timeout, process-id is '.$headers['process-id']);
            app::get('base')->model('rpcpoll')->update(array('process_id'=>$headers['process-id'])
                ,array('id'=>$rpc_id,'type'=>'request'));
            $this->status = RPC_RST_RUNNING;
            return false;
        }else{
            kernel::log('Response: '.$response);
            $result = json_decode($response);
            if($result){
                $this->error = $response->error;
                switch($result->rst){
                case 'running':
                    $this->status = RPC_RST_RUNNING;
                    return false;

                case 'succ':
                    app::get('base')->model('rpcpoll')->delete(array('id'=>$rpc_id,'type'=>'request'));
                    $this->status = RPC_RST_FINISH;
                    $method = $this->callback_method;
                    kernel::single($this->callback_class)->$method($result->data);
                    return $result->data;

                case 'fail':
                    $this->error = 'Bad response';
                    $this->status = RPC_RST_ERROR;
                    return false;
                }
            }else{
                //error 解码失败
            }
        }
    }

    public function set_callback($callback_class,$callback_method,$callback_params=null){
        $this->callback_class = $callback_class;
        $this->callback_method = $callback_method;
        $this->callback_params = $callback_params;
        return $this;
    }

    public function set_timeout($timeout){
        $this->timeout = $timeout;
        return $this;
    }

    private function api_version($method){ return 1; }

}
