<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class b2c_api_callback_app implements b2c_api_callback_interface_app
{
    public function __construct($app)
    {
        $this->app = $app;
    }
    
    public function callback($result)
    {
        $log_file = DATA_DIR.'/logs/b2c/callback/{date}/api_result.php';
        $logfile = str_replace('{date}', date("Ymd"), $log_file);
        //$logfile = DATA_DIR.'/logs/b2c/callback/api_result.xml';
        
        if(!file_exists($logfile))
        {
            if(!is_dir(dirname($logfile)))  utils::mkdir_p(dirname($logfile));
            
            $fs = fopen($logfile, 'w');
            $str_xml .= "<?php exit(0);";
            $str_xml .= "'<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
            $str_xml .= "<response>";
            $str_xml .= "</response>';";
            //error_log(print_r($str_xml,true),3,$logfile);
            fwrite($fs, $str_xml);
            fclose($fs);
        }
        
        // 记录api日志
        if (filesize($logfile))
        {
            $fs = fopen($logfile, 'a+');
            $str_xml = fread($fs, filesize($logfile));
            $str_xml = substr($str_xml, 0, strlen($str_xml) - 13);
            fclose($fs);
        }
        $fs = fopen($logfile, 'w');    
        $str_xml .= "<query>";
        foreach ($result->response as $key=>$value)
        {
            $str_xml .= "<$key>" . $value . "</$key>";
        }
        $str_xml .= "</query>";
        $str_xml .= "</response>';";
        
        fwrite($fs, $str_xml);
        fclose($fs);
        
        $arr_callback_params = $result->get_callback_params();        
        include_once(ROOT_DIR.'/app/b2c/lib/api/rpc/request_api_method.php');
        
        $message = $arr_apis[$arr_callback_params['method']] . (($result->response['rsp'] == 'succ') ? '成功，' : '失败，') . '单号：' . $arr_callback_params['tid'];
        
        return $message;
    }
}