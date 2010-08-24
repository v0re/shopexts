<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

$db['order_pmt']=array (
  'columns' => 
  array (
    'pmt_id' => 
    array (
      'type' => 'int(8)',
      'required' => true,
      'label' => '促销规则id',
      'pkey' => true,
      'editable' => false,
    ),
    'order_id' => 
    array (
      'type' => 'table:orders',
      'required' => true,
      'pkey' => true,
      'label' => '订单id',
      'editable' => false,
    ),
    'pmt_type' => 
    array (
      'type' => 
      array (
        'order' => '订单',
        'goods' => '商品',
        'coupon' => '优惠券',
      ),
      'default' => 'goods',
      'required' => true,
      'comment' => '优惠规则类型',
      'pkey' => true,
      'editable' => false,
      'label' => '优惠规则类型',
    ),
    'pmt_amount' => 
    array (
      'type' => 'money',
      'default' => '0',
      'required' => true,
      'editable' => false,
    ),
    'pmt_memo' => 
    array (
      'type' => 'longtext',
      'editable' => false,
    ),
    'pmt_describe' => 
    array (
      'type' => 'longtext',
      'editable' => false,
    ),
  ),
  'comment' => '订单与商品促销规则的关联表',
  'version' => '$Rev: 48882 $',
);
