<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['cart_objects']=array (
  'columns' => 
  array (
    'obj_ident' => 
    array (
      'type' => 'varchar(255)',
      'pkey' => true,
      'required' => true,
      'label' => '对象ident',
      'editable' => false,
      'in_list' => true,
      'default_in_list' => true,
      'is_title' => true,
    ),
    'member_ident' => 
    array (
      'type' => 'varchar(50)',
      'pkey' => true,
      'required' => true,
      'label' => '会员ident',
      'editable' => false,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'member_id' => 
    array (
      'type' => 'int(8) ',
      'pkey' => true,
      'required' => true,
      'label' => '会员 id',
      'editable' => false,
      'default' => -1,
    ),
    'obj_type' => 
    array (
      'type' => 'varchar(20)',
      'required' => true,
      'label' => '购物车对象类型',
      'editable' => false,
      'in_list' => true,
    ),
    'params' => 
    array (
      'type' => 'serialize',
      'required' => true,
      'label' => '购物车对象参数',
      'editable' => false,
      'in_list' => true,
    ),
    'quantity' => 
    array (
      'type' => 'float unsigned',
      'required' => true,
      'label' => '数量',
      'editable' => false,
      'in_list' => true,
    ),
  ),
  'engine' => 'innodb',
  'version' => '$Rev: 40912 $',
);
