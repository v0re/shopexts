<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['user_flow']=array (
  'columns' => 
  array (
    'user_id' => array (
      'type' => 'table:users',
      'required' => true,
      'pkey' => true,
    ),
    'flow_id' => array (
      'type' => 'table:flow',
      'required' => true,
      'pkey' => true,
    ),
    'unread' => array (
      'type' => 'bool',
      'required' => true,
      'default'=>'true',
    ),
    'note' => array (
      'type' => 'varchar(50)',
      'default'=>'',
    ),
    'has_star' => array (
      'type' => 'bool',
      'required' => true,
      'default'=>'false',
    ),
    'keep_unread' => array (
      'type' => 'bool',
      'required' => true,
      'default'=>'false',
    ),
  ),
  'comment' => '信息表',
  'version' => '$Rev$',
);
