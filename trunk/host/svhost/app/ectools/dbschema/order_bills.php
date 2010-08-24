<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['order_bills']=array (
  'columns' => 
  array (
    'rel_id' => 
    array (
      'type' => 'bigint unsigned',
      'required' => true,
      'pkey' => true,
      'default' => 0,
      'editable' => false,
    ),
    'bill_type' => 
    array (
      'type' => 
      array (
        'payments' => '付款单',
        'refunds' => '退款单',
      ),
      'default' => 'payments',
      'required' => true,
      'label' => '单据类型',
      'width' => 75,
      'editable' => false,
      'filtertype' => 'yes',
      'filterdefault' => true,
      'in_list' => true,
    ),
    'pay_object' => 
    array (
      'type' => 
      array (
        'order' => '订单支付',
        'recharge' => '预存款充值',
        'joinfee' => '加盟费',
      ),
      'default' => 'order',
      'required' => true,
      'label' => '支付类型',
      'width' => 110,
      'editable' => false,
      'filtertype' => 'yes',
      'filterdefault' => true,
      'in_list' => true,
    ),
    'bill_id' => 
    array (
      'type' => 'varchar(20)',
      'pkey' => true,
      'required' => true,
      'label' => '关联单号',
      'width' => 110,
      'editable' => false,
      'searchtype' => 'has',
      'filtertype' => 'yes',
      'filterdefault' => true,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'money' => 
    array (
      'type' => 'money',
      'editable' => false,
    ),
  ),
  'engine' => 'innodb',
  'version' => '$Rev: 40912 $',
);
