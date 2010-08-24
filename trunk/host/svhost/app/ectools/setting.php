<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$setting = array(
'site.decimal_digit.count'=>array('type'=>SET_T_ENUM,'default'=>2,'desc'=>__('金额运算精度保留位数'),'options'=>array(0=>__('整数取整'),1=>__('取整到1位小数'),2=>__('取整到2位小数'),3=>__('取整到3位小数'))),//WZP
'site.decimal_type.count'=>array('type'=>SET_T_ENUM,'default'=>1,'desc'=>__('金额运算精度取整方式'),'options'=>array('1'=>__('四舍五入'),'2'=>__('向上取整'),'3'=>__('向下取整'))),//WZP
'site.decimal_digit.display'=>array('type'=>SET_T_ENUM,'default'=>2,'desc'=>__('金额显示保留位数'),'options'=>array(0=>__('整数取整'),1=>__('取整到1位小数'),2=>__('取整到2位小数'),3=>__('取整到3位小数'))),//WZP
'site.decimal_type.display'=>array('type'=>SET_T_ENUM,'default'=>1,'desc'=>__('金额显示取整方式'),'options'=>array('1'=>__('四舍五入'),'2'=>__('向上取整'),'3'=>__('向下取整'))),
'system.area_depth'=>array('type'=>SET_T_INT,'default'=>'3','desc'=>__('地区级数')),
);
