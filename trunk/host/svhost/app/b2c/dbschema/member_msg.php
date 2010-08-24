<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['member_msg']=array (
  'columns' => 
  array (
    'msg_id' => array (
       'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'label' => 'ID',
      'width' => 110,
      'editable' => false,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'for_id' => array(
        'type'=>'int',
        'label' =>'回复哪个信件',
        'default' => 0,
    ),
    'from_id' => array (
      'type' => 'table:members',
      'required' => true,
    ),
    'from_uname' => array(
        'type'=>'varchar(100)',
        'label' =>'发信者',
        'in_list' => true,
       'default_in_list' => true,
    ),
    'from_type' => array(
        'type'=>'int',
        'label' =>'发信类型',
        'default' => 0,
    ),
    'to_id' => array (
      'type' => 'table:members',
      'default' =>0,
      'required' => true,
    ),
    'to_uname' => array(
        'type'=>'varchar(100)',
       'default_in_list' => true,
    ),
    'subject' => array (
      'type' => 'varchar(100)',
       'label' => '消息主题',
        'in_list' => true,
        'is_title' =>true,
       'default_in_list' => true,
      'required' => true,
    ),
    'content' => array (
      'type' => 'text',
      'label' => '内容',
      'required' => true,
    ),
     'order_id' => array (
      'type' => 'bigint(20)',
      'label' => '订单ID',
      'default' =>0,
    ),
    'create_time' => 
    array (
      'type' => 'time',
      'label' => '发送时间',
      'width' => 110,
      'editable' => false,
      'filtertype' => 'time',
      'filterdefault' => true,
      'in_list' => true,
    ),
    'to_time' => 
    array (
      'type' => 'time',
      'label' => '发送时间',
      'width' => 110,
      'editable' => false,
      'filtertype' => 'time',
      'filterdefault' => true,
      'in_list' => true,
    ),
    'has_read' => array (
      'type' => 'bool',
      'label' => '是否已读',
      'default'=>'false',
    ),
    'keep_unread' => array (
      'type' => 'bool',
        'label' => '保持未读',
      'default'=>'false',
    ),
     'has_star' => array (
      'type' => 'bool',
        'label' => '是否打上星标',
      'default'=>'false',
    ),
    'has_sent' => array (
      'type' => 'bool',
      'label' => '是否发送',
      'default'=>'true',
    ),
  ),
   'index' => 
    array (
    'ind_to_id' => 
    array (
      'columns' => 
      array (
        0 => 'to_id',
        1 => 'has_read',
        2 => 'has_sent',
      ),
    ),
    'ind_from_id' => 
    array (
      'columns' => 
      array (
        0 => 'from_id',
        1 => 'has_read',
        2 => 'has_sent',
      ),
    ),
  ),
  'comment' => '信息表',
   'engine' => 'innodb',
   'version' => '$Rev$',
);
