<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
/**
* @table order_coupon_user;
*
* @package Schemas
* @version $
* @copyright 2010 ShopEx
* @license Commercial
*/

$db['order_coupon_user']=array (
  'columns' =>
  array (
    'order_id' =>
    array (
      'type' => 'table:orders',
      'required' => true,
      'default' => 0,
      'pkey' => true,
      'comment' => __('应用订单号'),
      'editable' => false,
    ),
    'cpns_id' =>
    array (
      'type' => 'number',
      'required' => true,
      'default' => 0,
      'pkey' => true,
      'comment' => __('优惠券方案ID'),
      'editable' => false,
    ),
    'cpns_name' =>
    array (
      'type' => 'varchar(255)',
      'comment' => __('优惠券方案名称'),
      'editable' => false,
    ),
    'usetime' => 
    array (
      'type' => 'time',
      'label' => '使用时间',
      'width' => 110,
      'editable' => false,
      'filtertype' => 'time',
      'filterdefault' => true,
      'in_list' => true,
    ),
    'memc_code' =>
    array (
      'type' => 'varchar(255)',
      'comment' => __('使用的优惠券号码'),
      'editable' => false,
    ),
    'cpns_type' =>
    array (
      'type' =>
      array (
        0 => 0,
        1 => 1,
        2 => 2,
      ),
      'comment' => __('优惠券类型0全局 1用户 2外部优惠券'),
      'editable' => false,
    ),
  ),
  'comment' => '优惠券使用记录',
);
