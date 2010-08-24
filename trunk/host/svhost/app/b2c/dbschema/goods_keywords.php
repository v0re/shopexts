<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['goods_keywords']=array (
  'columns' => 
  array (
    'goods_id' => 
    array (
      'type' => 'table:goods',
      'required' => true,
      'default' => 0,
      'pkey' => true,
      'editable' => false,
    ),
    'keyword' => 
    array (
      'type' => 'varchar(40)',
      'default' => '',
      'required' => true,
      'pkey' => true,
      'editable' => false,
      'is_title' => true,
    ),
    'refer' => 
    array (
      'type' => 'varchar(255)',
      'default' => '',
      'required' => false,
      'editable' => false,
    ),
    'res_type' => 
    array (
      'type' => 'enum(\'goods\',\'article\')',
      'default' => 'goods',
      'required' => true,
      'pkey' => true,
      'editable' => false,
    ),
  ),
  'version' => '$Rev: 40654 $',
);
