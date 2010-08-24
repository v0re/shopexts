<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

/**
 * rpc service request class
 * 统一请求接口
 */
class b2c_api_rpc_request
{
    /**
     * app object
     */
    protected $app;
    
    /**
     * 构造方法
     * @param object app object
     * @return null
     */
    public function __construct($app)
    {
        $this->app = $app;
    }
    
    /**
     * 整理和验证请求的数据
     * @param string method
     * @param array parameters
     * @param array callback array
     * @param string request title
     * @param int shop id
     * @param int time out type
     * @return null
     */
    protected function request($method, $params, $callback=array(), $title, $shop_id=NULL, $time_out=1)
    {
        if($callback && $callback['class'] && $callback['method']){
            $rpc_callback = array('class' => $callback['class'], 'method' => $callback['method'], 'params' => $callback['params']);
        }else{
            $rpc_callback = array('class' => 'b2c_api_callback_app', 'method' => 'callback');
        }
        
        $this->write_log($title,$rpc_callback[0],'rpc_request',array($method,$params,$rpc_callback));        
        $this->rpc_request($method,$params,$rpc_callback,$time_out);
    }
    
    /**
     * rpc方式发送请求
     * @param string method name
     * @param array parameters array
     * @param array callback
     * @param int timeout type
     */
    private function rpc_request($method,$params,$callback,$time_out=1)
    {
        // 取到连接对方的信息
        $obj_shop = $this->app->model('shop');
        $arr_shop = $obj_shop->dump(array('status' => 'bind', 'node_type' => 'ecos.ome'));
        
        if ($arr_shop)
        {        
            $params = array_merge(array('node_type' => $arr_shop['node_type'], 'to_node_id' => $arr_shop['node_id']), $params);
            
            $callback_class = $callback['class'];
            $callback_method = $callback['method'];
            $callback_params = (isset($callback['params'])&&$callback['params'])?$callback['params']:array();
            $rst = $this->app->matrix()->set_callback($callback_class,$callback_method,$callback_params)
                ->set_timeout($time_out)
                ->call($method,$params);
        }        
    }
    
    private function write_log($title='', $class_name='b2c_api_callback_app', $request_method_name='rpc_request', $data=array())
    {
        if ($data)
        {
            $log_file = DATA_DIR.'/logs/b2c/request/{date}/api_request.php';
            $logfile = str_replace('{date}', date("Ymd"), $log_file);
            //$logfile = DATA_DIR.'/logs/b2c/request/api_request.xml';
            
            if(!file_exists($logfile))
            {
                if(!is_dir(dirname($logfile)))  utils::mkdir_p(dirname($logfile));
                
                $fs = fopen($logfile, 'w');
                $str_xml = "<?php exit(0);";
                $str_xml .= "'<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
                $str_xml .= "<request>";
                $str_xml .= "</request>';";
                //error_log(print_r($str_xml,true),3,$logfile);
                fwrite($fs, $str_xml);
                fclose($fs);
            }
            
            $arr_data = array(
                'method' => $data[0],
                'params' => $data[1],
                'rpc_callback' => $data[2],
            );
            
            // 记录api日志            
            if (filesize($logfile))
            {
                $fs = fopen($logfile, 'a+');
                $str_xml = fread($fs, filesize($logfile));
                $str_xml = substr($str_xml, 0, strlen($str_xml) - 12);
                fclose($fs);
            }
            $fs = fopen($logfile, 'w');
            $str_xml .= "<query>";
            $str_xml .= "<method>" . $arr_data['method'] . "</method>";
            $str_xml .= "<params>" . print_r($arr_data['params'], true) . "</params>";
            $str_xml .= "<rpc_callback>";
            foreach ($arr_data['rpc_callback'] as $key=>$value)
            {
                $str_xml .= "<$key>" . $value . "</$key>";
            }
            $str_xml .= "</rpc_callback></query>";
            $str_xml .= "</request>';";

            fwrite($fs, $str_xml);
            fclose($fs);
            //error_log(print_r($str_xml,true),3,$logfile);
        }
    }
}