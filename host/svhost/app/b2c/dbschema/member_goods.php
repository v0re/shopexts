<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['member_goods']=array (
  'columns' => 
  array (
    'gnotify_id' => array (
       'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'label' => 'ID',
      'width' => 110,
      'editable' => false,
      'default_in_list' => true,
      'id_title' => true,
    ),
    'goods_id' => array (
      'type' => 'table:goods',
      'required' => true,
      'label' => '缺货商品名称',
      'in_list' => true,
    ),
    'member_id' => array(
        'type'=>'table:members',
        'in_list' => true,
         'label' => '会员用户名',
       'default_in_list' => true,
    ),
    'product_id' => array (
      'type' => 'table:products',
      'default' => null,
    ),
    'email' => array(
        'type'=>'varchar(100)',
        'in_list' => true,
         'label' => 'Email',
       'default_in_list' => true,
    ),
    'status' => array (
      'type' => "enum('ready', 'send', 'progress')",
      'required' => true,
    ),
    'send_time' => 
     array (
      'type' => 'time',
      'label' => '发送时间',
      'width' => 110,
      'editable' => false,
      'filtertype' => 'time',
      'filterdefault' => true,
      'in_list' => true,
    ),
    'create_time' => 
    array (
      'type' => 'time',
      'label' => '申请时间',
      'width' => 110,
      'editable' => false,
      'filtertype' => 'time',
      'filterdefault' => true,
      'in_list' => true,
    ),
    'disabled' => array (
      'type' => 'bool',
      'default'=>'false',
    ),
    'remark' => array (
      'type' => 'longtext',
      'default'=>'false',
    ),
    'type' =>array(
        'type' =>  "enum('fav', 'sto')",
        )
  ),
  'comment' => '信息表',
   'engine' => 'innodb',
   'version' => '$Rev$',
);
