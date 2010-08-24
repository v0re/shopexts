<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['flow']=array (
  'columns' => 
  array (
    'flow_id' => 
    array (
      'label' => '序号',
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'editable' => false,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'flow_from' => 
    array (
      'label' => '发送者',
      'type' => 
      array (
        'user' => '管理员',
        'system' => '系统',
        'internet' => '站外',
        'user' => '用户',
      ),
      'default' => 'system',
      'required' => true,
      'in_list' => true,
    ),
    'from_id' => 
    array (
      'type' => 'number',
      'default' => 0,
      'editable' => false,
    ),
    'subject' => 
    array (
      'label' => '消息标题',
      'type' => 'varchar(50)',
      'required' => true,
      'default' => '',
      'editable' => false,
      'in_list' => true,
      'is_title' => true,
    ),
    'flow_desc' => 
    array (
      'label' => '消息描述',
      'type' => 'varchar(100)',
      'required' => true,
      'default' => '',
      'editable' => false,
      'in_list' => true,
    ),
    'body' => 
    array (
      'label' => '内容本体',
      'type' => 'text',
      'required' => true,
      'default' => '',
      'editable' => false,
      'in_list' => true,
    ),
    'flow_ip' => 
    array (
      'type' => 'varchar(20)',
      'default' => '',
      'required' => true,
      'editable' => false,
    ),
    'send_mode'=>array(
        'type'=>array(
            'direct'=>'直送',
            'broadcast'=>'广播',
            'fetch'=>'收取',
        ),
        'default' => 'direct',
        'required' => true,
    ),
    'flow_type' => 
    array (
      'type' => 'varchar(32)',
      'default' => 'default',
      'required' => true,
      'editable' => false,
    ),
    'send_time'=>array(
        'type'=>'time',
        'required' => true,
    )
  ),
  'comment' => '信息表',
  'version' => '$Rev$',
);
