<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['order_items']=array (
  'columns' => 
  array (
    'item_id' => 
    array (
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'editable' => false,
    ),
    'order_id' => 
    array (
      'type' => 'table:orders',
      'required' => true,
      'default' => 0,
      'editable' => false,
    ),
    'obj_id' => 
    array (
      'type' => 'table:order_objects',
      'required' => true,
      'default' => 0,
      'editable' => false,
    ),
    'product_id' => 
    array (
      'type' => 'table:products',
      'required' => true,
      'default' => 0,
      'editable' => false,
      'sdfpath' => 'products/product_id'
    ),
    'goods_id' => 
    array (
      'type' => 'table:goods',
      'required' => true,
      'default' => 0,
      'editable' => false,
    ),
    'type_id' => 
    array (
      'type' => 'number',
      'editable' => false,
    ),
    'bn' => 
    array (
      'type' => 'varchar(40)',
      'editable' => false,
      'is_title' => true,
    ),
    'name' => 
    array (
      'type' => 'varchar(200)',
      'editable' => false,
    ),
    'cost' => 
    array (
      'type' => 'money',
      'editable' => false,
    ),
    'price' => 
    array (
      'type' => 'money',
      'default' => '0',
      'required' => true,
      'editable' => false,
    ),
    'amount' => 
    array (
      'type' => 'money',
      'editable' => false,
    ),
    'score' =>
    array (
      'type' => 'number',
      'label' => '积分',
      'width' => 30,
      'editable' => false,
    ),
    'weight' => 
    array (
      'type' => 'number',
      'editable' => false,
    ),
    'nums' => 
    array (
      'type' => 'float',
      'default' => 1,
      'required' => true,
      'editable' => false,
      'sdfpath' => 'quantity',
    ),
    'sendnum' => 
    array (
      'type' => 'float',
      'default' => 0,
      'required' => true,
      'editable' => false,
    ),
    'addon' => 
    array (
      'type' => 'longtext',
      'editable' => false,
    ),
    'item_type' => 
    array (
      'type' => 
      array (
        'product' => '商品',
        'pkg' => '捆绑商品',
        'gift' => '捆绑商品',
        'adjunct' => '捆绑商品',
      ),
      'default' => 'product',
      'required' => true,
      'editable' => false,
    ),
  ),
  'engine' => 'innodb',
  'version' => '$Rev: 44813 $',
);
