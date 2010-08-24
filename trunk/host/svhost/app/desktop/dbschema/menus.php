<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['menus']=array (
  'columns' => 
  array (
    'menu_id'=>array(
      'type' => 'number',
      'pkey' => true,
      'extra' => 'auto_increment',
    ),
    'menu_type' => 
    array (
      'type' => 'varchar(80)',
      'required' => true,
      'width' => 100,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'app_id' => 
    array (
      'type' => 'table:apps@base',
      'required' => true,
      'width' => 100,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'workground'=>array(
        'type'=>'varchar(30)',
    ),
     'menu_group'=>array(
        'type'=>'varchar(30)',
    ),
    'menu_title'=>array(
        'type'=>'varchar(100)',
        'is_title'=>true,
    ),
    'menu_path'=>array(
        'type'=>'varchar(255)',
    ),
    'disabled'=>array(
        'type'=>'bool',
        'default'=>'false'
    ),
     'display'=>array(
        'type'=>"enum('true', 'false')",
        'default'=>'false'
    ),
    'permission'=>array(
        'type'=>'varchar(80)',
    ),
    'addon'=>array(
        'type'=>'text',
    ),
    'target'=>array(
        'type'=>'varchar(10)',
        'default'=>''
    ),
    'menu_order'=>array(
        'type' => 'number',
        'default'=>'0'
    ),
  ),
  'version' => '$Rev: 44008 $',
  'unbackup' => true,
);
