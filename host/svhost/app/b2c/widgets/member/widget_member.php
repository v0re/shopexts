<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
function widget_member($setting,&$smarty){
    #return; //todo 修正widget
    $member_id = $_SESSION['account'][app::get('site')->getConf('account.type')];
    $member = app::get('b2c')->model('members');
    $member_data = $member->dump($member_id,'*',array(':account@pam'=>array('login_name')));
    $member_data['valideCode'] = app::get('b2c')->getConf('site.login_valide');
    return $member_data;
}
function instance_loginplug($data){
    //var_dump($data);
    //if(!class_exists('app')) require('app.php');
    $path = APP_DIR.'/'.$data['app_id'].'/passport.'.$data['app_id'].'.php';
    //echo $path;
    if(file_exists($path)){
        require_once($path);
        $classname = 'passport_'.$data['plugin_ident'];
        $object = new $classname;
        return $object;
    }else{
        return false;
    }
}
?>
