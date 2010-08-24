<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class pam_passport_basic implements pam_interface_passport{

    function __construct(){
        kernel::single('base_session')->start();
        $this->init();
    }
    
    function init(){
        if(!app::get('pam')->getConf('passport.'.__CLASS__)){
                $ret = $this->get_setting();
                $ret['passport_id']['value'] = __CLASS__;
                $ret['passport_name']['value'] = $this->get_name();
                $ret['passport_status']['value'] = 'true';
                $ret['passport_version']['value'] = '1.5';
                app::get('pam')->setConf('passport.'.__CLASS__,$ret);
        }
    }

    function get_name(){
        return '用户登陆';
    }

    function get_login_form($auth, $appid, $view, $ext_pagedata=array()){
        $render = app::get('pam')->render();
        $render->pagedata['callback'] = $auth->get_callback_url(__CLASS__);
        if($auth->is_enable_vcode()){
            $render->pagedata['show_varycode'] = 'true';
            $render->pagedata['type'] = $auth->type;
        }
        if(isset($_SESSION['last_error']) && ($auth->type == $_SESSION['type'])){
            $render->pagedata['error_info'] = $_SESSION['last_error'];
            unset($_SESSION['last_error']);
            unset($_SESSION['type']);
        }
        if($ext_pagedata){
            foreach($ext_pagedata as $key => $v){
                $render->pagedata[$key] = $v;
            }
        }
        return $render->fetch($view,$appid);
    }

    function login($auth,&$usrdata){
        if($auth->is_enable_vcode()){
            if($auth->type == 'shopadmin'){
                $key = "DESKTOPVCODE";
            }
            else{
                $key = "MEMBERVCODE";
            }            
            if(!base_vcode::verify($key,intval($_POST['verifycode']))){
                $usrdata['log_data'] = __('用户').$_POST['uname'].__('验证码不正确！');
                $_SESSION['error'] = __('用户').$_POST['uname'].__('验证码不正确！');
                return false;
            }
        }
        $rows = app::get('pam')->model('account')->getList('*',array(
            'login_name'=>$_POST['uname'],
            'login_password'=>md5($_POST['password']),
            'account_type' => $auth->type,
            'disabled' => 'false',
            ),0,1);   

        if($rows[0]){
            if($_POST['remember'] === "true"){
                setcookie('pam_passport_basic_uname',$_POST['uname'],time()+365*24*3600,'/');
            }
            else{
                setcookie('pam_passport_basic_uname','',0,'/');
            }
            $usrdata['log_data'] = __('用户').$_POST['uname'].__('验证成功！');
            return $rows[0]['account_id'];
        }else{
            $usrdata['log_data'] = __('用户').$_POST['uname'].__('验证失败！');
            $_SESSION['error'] = __('用户名或密码错误');
            return false;
        }
    }

    function get_data(){
    }

    function get_id(){
    }

    function get_expired(){
    }
    
    function get_config(){
        if($ret = app::get('pam')->getConf('passport.'.__CLASS__)){
            return $ret;
        }else{
            $ret = $this->get_setting();
            $ret['passport_id']['value'] = __CLASS__;
            $ret['passport_name']['value'] = $this->get_name();
            $ret['passport_status']['value'] = 'true';
            $ret['passport_version']['value'] = '1.5';
            app::get('pam')->setConf('passport.'.__CLASS__,$ret);
            return $ret;        
        }
    }
    
    function set_config(&$config){
        $save = app::get('pam')->getConf('passport.'.__CLASS__);
        if(count($config))
            foreach($config as $key=>$value){
                if(!in_array($key,array_keys($save))) continue;
                $save[$key]['value'] = $value;
            }
        return app::get('pam')->setConf('passport.'.__CLASS__,$save);
    }

    function get_setting(){
        return array(
            'passport_id'=>array('label'=>'通行证id','type'=>'text','editable'=>false),
            'passport_name'=>array('label'=>'通行证','type'=>'text','editable'=>false),
            'passport_status'=>array('label'=>'开启','type'=>'bool','editable'=>false),
            'passport_version'=>array('label'=>'版本','type'=>'text','editable'=>false),
        );
    }
    


}
