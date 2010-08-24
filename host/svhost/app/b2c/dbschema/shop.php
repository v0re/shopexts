<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['shop']=array (
  'columns' => 
  array (
    'shop_id' => 
    array (
        'type' => 'int(8)',
        'required' => true,
        'pkey' => true,
        'label' => 'id',
        'editable' => false,
        'extra' => 'auto_increment',
    ),
    'name' =>
    array (
      'type' => 'varchar(255)',
      'required' => true,
      'label' => '店铺名称',
      'editable' => false,
      'searchtype' => 'has',
      'filtertype' => 'normal',
      'filterdefault' => true,
      'in_list' => true,
      'default_in_list' => true,
      'is_title' => true,
    ),
    'node_id' =>
    array (
      'type' => 'varchar(32)',
      'label' => '对方节点id',
      'editable' => false,
      'in_list' => true,
      'default_in_list' => true,
      'is_title' => true,
    ),
    'node_type' =>
    array (
      'type' => 'varchar(128)',
      'label' => '对方节点类型',
      'editable' => false,
      'in_list' => true,
      'default_in_list' => true,
      'is_title' => true,
    ),
    'status' => 
    array(
      'type' => 
      array (
        'bind' => '绑定',
        'unbind' => '未绑定',
      ),
      'default' => 'unbind',
      'label' => '绑定状态',
      'editable' => false,
      'in_list' => true,
      'default_in_list' => true,
      'is_title' => true,
    ),
  ),
  'engine' => 'innodb',
  'version' => '$Rev:  $',
);