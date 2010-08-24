<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class desktop_ctl_passport extends desktop_controller{
    
    var $login_times_error=3;

    function index(){
        //TODO 模拟升级脚本
        if(!(app::get('desktop')->getConf('upgreade') === "YES")){
            pam_account::register_account_type('desktop','shopadmin','后台管理系统');
            pam_account::register_account_type('b2c','member','前台会员系统');
            app::get('desktop')->setConf('upgreade','YES');
        }
        $auth = pam_auth::instance(pam_account::get_account_type($this->app->app_id));
        $auth->set_redirect_url($_GET['url']);
        $pagedata['pam_passport_basic_uname'] = $_COOKIE['pam_passport_basic_uname'];
        foreach(kernel::servicelist('passport') as $k=>$passport){
            if($auth->is_module_valid($k)){
                $this->pagedata['passports'][] = array(
                        'name'=>$auth->get_name($k)?$auth->get_name($k):$passport->get_name(),
                        'html'=>$passport->get_login_form($auth,'desktop','basic-login.html',$pagedata),     
                    );
            }
        }
        $this->display('login.html');
    }
    function gen_vcode(){
        $vcode = kernel::single('base_vcode');
        $vcode->length(4);
        $vcode->verify_key('DESKTOPVCODE');
        $vcode->display();;
    }
    function logout(){
        $this->user->login();
        $this->user->logout();
        unset($_SESSION['account'][pam_account::get_account_type($this->app->app_id)]);
        unset($_SESSION['last_error']);
        header('Location: index.php');
    }

}
