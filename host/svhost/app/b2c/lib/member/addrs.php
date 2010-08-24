<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_member_addrs{
    function get_receive_addr(&$controller, $addr_id=0){
        $member_addrs = &$controller->app->model('member_addrs');
        $arr_member_addr = $member_addrs->dump($addr_id, '*');
        $controller->pagedata['addr'] = $arr_member_addr;
        $arrMember = $controller->get_current_member();
        $controller->pagedata['address'] = $arrMember['member_id'];
        
        return $controller->fetch("site/common/rec_addr.html");
    }
}
?>
