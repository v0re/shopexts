<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class pam_auth{

    private $account;
    static $instance = array();

    function __construct($type){
        $this->type = $type;
    }

    static function instance($type){
        if(!isset(self::$instance[$type])){
            self::$instance[$type] = new pam_auth($type);
        }
        return self::$instance[$type];
    }

    function account(){
        if(!$this->account){
            $this->account = new pam_account($this->type);
        }
        return $this->account;
    }

    function get_name($module){
        return app::get('pam')->getConf('module.name.'.$module);
    }

    function is_module_valid($module){
        $config = app::get('pam')->getConf('passport.'.$module);
        return $config['passport_status']['value'] == 'true' ?  true : false;
    }

    function get_callback_url($module){
        return kernel::api_url('api.pam_callback','login',array('module'=>$module,'type'=>$this->type,'redirect'=>$this->redirect_url));
    }

    function set_redirect_url($url){
        $this->redirect_url = $url;
    }
    
    function is_enable_vcode(){
        //todo 分前后台
        #return true;
        if($this->type == 'shopadmin'){
            return app::get('desktop')->getConf('shopadminVcode') == 'true' ? true : false;
        }
        if($this->type == 'member'){
             return app::get('b2c')->getConf('site.login_valide') == 'true' ? true : false;
        }
        return false;
    }

}
