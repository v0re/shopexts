<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['reship_items']=array (
  'columns' => 
  array (
    'item_id' => 
    array (
      'type' => 'int unsigned',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'editable' => false,
    ),
    'reship_id' => 
    array (
      'type' => 'bigint unsigned',
      'required' => true,
      'default' => 0,
      'editable' => false,
    ),
    'item_type' => 
    array (
      'type' => 
      array (
        'goods' => '商品',
        'gift' => '赠品',
        'pkg' => '捆绑商品',
      ),
      'default' => 'goods',
      'required' => true,
      'editable' => false,
    ),
    'product_id' => 
    array (
      'type' => 'bigint unsigned',
      'required' => true,
      'default' => 0,
      'editable' => false,
    ),
    'product_bn' => 
    array (
      'type' => 'varchar(30)',
      'editable' => false,
      'is_title' => true,
    ),
    'product_name' => 
    array (
      'type' => 'varchar(200)',
      'editable' => false,
    ),
    'number' => 
    array (
      'type' => 'float',
      'required' => true,
      'default' => 0,
      'editable' => false,
    ),
  ),
  'comment' => '发货/退货单明细表',
  'version' => '$Rev: 40654 $',
);
