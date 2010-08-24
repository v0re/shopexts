<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
function widget_topbar(&$setting,&$app){

    #return; //todo widget
    $member_id = $_SESSION['account'][app::get('site')->getConf('account.type')];
    $member = app::get('b2c')->model('members');
    $member_data = $member->dump($member_id,'*',array(':account@pam'=>array('login_name')));
    $member_data['valideCode'] = app::get('b2c')->getConf('site.login_valide');
    
    return $member_data;
}

