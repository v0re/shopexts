<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['order_delivery']=array (
  'columns' => 
  array (
    'order_id' => 
    array (
      'type' => 'table:orders',
      'required' => true,
      'pkey' => true,
      'default' => 0,
      'editable' => false,
    ),
    'dlytype' => 
    array (
      'type' => 
      array (
        'delivery' => '发货单',
        'reship' => '退货单',
      ),
      'default' => 'delivery',
      'required' => true,
      'label' => '单据类型',
      'width' => 75,
      'editable' => false,
      'filtertype' => 'yes',
      'filterdefault' => true,
      'in_list' => true,
    ),
    'dly_id' => 
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
    'items' => 
    array (
      'type' => 'text',
      'label' => '货品明细',
      'editable' => false,
    ),
  ),
  'engine' => 'innodb',
  'version' => '$Rev: 41996 $',
);
