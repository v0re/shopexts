<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
interface pam_interface_passport{

    function get_name();
    function get_login_form($auth,$appid,$view,$ext_pagedata=array());
    function login($auth,&$usrdata);
    function get_data();
    function get_id();
    function get_expired();

}
