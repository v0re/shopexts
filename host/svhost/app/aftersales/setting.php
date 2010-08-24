<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$setting = array(
'site.is_open_return_product'=>array('type'=>SET_T_ENUM,'default'=>0,'desc'=>__('售后服务状态'),'options'=>array(0=>__('关闭'),1=>__('开启'))),//WZP
'site.return_product_comment'=>array('type'=>SET_T_TXT,'default'=>'','desc'=>__('服务须知')),//WZP
);
