<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class pam_callback{

    function login($params){
        $auth = pam_auth::instance($params['type']);
        if($params['module']){
            if($passport_module = kernel::single($params['module'])){
                if($passport_module instanceof pam_interface_passport){
                    $module_uid = $passport_module->login($auth,$auth_data);
                    if($module_uid){
                        $auth->account()->update($params['module'], $module_uid, $auth_data);   
                    }                    
                    $log = array(
                        'event_time'=>time(),
                        'event_type'=>$auth->type,
                        'event_data'=>$auth_data['log_data'],
                    );
                    app::get('pam')->model('log')->insert($log);
                    $_SESSION['last_error'] = $auth_data['log_data'];
                    $_SESSION['type'] = $auth->type;
                    $_SESSION['login_time'] = time();
                    $url = '';
                    if($params['mini']) {
                        $url = '?mini=1';
                    }
                    /**
                     * appעļ
                     */
                    $params['member_id'] = $module_uid;
                    $params['uname'] = $_POST['uname'];
                    foreach(kernel::servicelist('pam_login_listener') as $service)
                    {
                        $service->listener_login($params);
                    }
                    
                    header('Location:' .base64_decode(urldecode($params['redirect'])). $url);         
                }
            }else{
               
            }
        }
    }

}
