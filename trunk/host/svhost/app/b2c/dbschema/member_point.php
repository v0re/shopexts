<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['member_point']=array (
  'columns' => 
  array (
    'id' => 
    array (
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'editable' => false,
    ),
    'member_id' => 
    array (
      'type' => 'table:members',
      'required' => true,
      'default' => 0,
      'editable' => false,
    ),
    'point' => 
    array (
      'type' => 'int(10)',
      'required' => true,
      'default' => 0,
      'editable' => false,
    ),
    'addtime' => 
    array (
      'type' => 'time',
      'required' => true,
      'default' => 0,
      'editable' => false,
    ),
    'expiretime' => 
    array (
      'type' => 'time',
      'required' => true,
      'default' => 0,
      'editable' => false,
    ),
    'reason' => 
    array (
      'type' => 'varchar(50)',
      'required' => true,
      'default' => '',
      'editable' => false,
      'is_title' => true,
    ),
    'remark' => 
    array (
      'type' => 'varchar(100)',
      'required' => false,
      'default' => '',
      'editable' => false,
      'is_title' => true,
    ),
    'related_id' => 
    array (
      'type' => 'bigint unsigned',
      'editable' => false,
    ),
    'type' => 
    array (
      'type' => 'tinyint(1)',
      'required' => true,
      'default' => 1,
      'editable' => false,
    ),
    'operator' => 
    array (
      'type' => 'varchar(50)',
      'editable' => false,
    ),
  ),
  'engine' => 'innodb',
  'comment' => '积分历史',
  'version' => '$Rev: 43105 $',
);
