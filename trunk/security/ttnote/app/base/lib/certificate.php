<?php
class base_certificate{

    static $certi= null;

    static function register($data=null){
        $sys_params = base_setup_config::deploy_info();
        $data = array(
            'certi_app'=>'open.reg',
            'app_id' => $sys_params['product_name'],
        );
        $http = kernel::single('base_httpclient');
        $http->timeout = 3;
        $result = $http->post(
            LICENSE_CENTER,
            $data);

        //todo: 声称获取一个唯一iD，发给飞飞
        $result = json_decode($result,1);
        if($result['res']=='succ'){
            $certificate = $result['info'];
            self::set_certificate($certificate);
        }else{
            return false;
        }
    }

    static function get($code='certificate_id'){
        
        if(!function_exists('get_certificate')){
            if(self::$certi===null){
                if(file_exists(ROOT_DIR.'/config/certi.php')){
                    require(ROOT_DIR.'/config/certi.php');
                    self::$certi = $certificate;
                }else{
                    self::$certi = array();
                }
            }
        }else{
            self::$certi = get_certificate();
        }
        
        return self::$certi[$code];
    }
    
    static function active(){
		if(self::get()){
			kernel::log('Using exists certificate: config/certi.php');
		}else{
			kernel::log('Request new certificate');
			self::register();
		}
    }
    
    
    static function set_certificate($certificate){
        if(!function_exists('set_certificate')){
            return file_put_contents(ROOT_DIR.'/config/certi.php'
                ,'<?php $certificate='.var_export($certificate,1).';');
        }else{
            return set_certificate($certificate);
        }
    }
    static function gen_sign($params){
        ksort($params);
        $sign = '';
        foreach($params as $key => $value){
            $sign .= $key.$value;
        }
        return strtoupper(md5(strtoupper(md5($sign)).self::token()));
    }
    
    static function certi_id(){ return self::get('certificate_id'); }
    
    static function token(){ return self::get('token'); }


}
