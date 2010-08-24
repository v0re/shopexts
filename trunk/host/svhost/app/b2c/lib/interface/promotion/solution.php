<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
/**
 * 优惠方案接口
 * $ 2010-05-04 14:43 $
 */
interface b2c_interface_promotion_solution
{
    public function config($config=array());
    public function apply(&$object,$config,&$cart_object=null);
    public function apply_order(&$object, &$config, &$cart_object=null);
    public function getString();
    public function setString($aData);
    public function get_status();
}
?>
