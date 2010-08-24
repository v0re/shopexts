<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['dly_center']=array (
  'columns' => 
  array (
    'dly_center_id' => 
    array (
      'type' => 'int(10)',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'editable' => false,
      'in_list' => false,
      'label' => '出货点id',
    ),
    'name' => 
    array (
      'type' => 'varchar(50)',
      'default' => '0',
      'required' => true,
      'editable' => false,
      'in_list' => true,
      'default_in_list' => true,
      'label' => '出货点名称',
    ),
    'address' => 
    array (
      'type' => 'varchar(200)',
      'required' => true,
      'editable' => false,
      'in_list' => true,
      'default_in_list' => true,
      'label' => '出货点地址',
    ),
    'region' =>
    array(
      'type' => 'varchar(100)',
      'editable' => false,
      'in_list' => true,
      'default_in_list' => true,
      'label' => '地区',
    ),
    'zip' => 
    array (
      'type' => 'varchar(20)',
      'editable' => false,
      'in_list' => true,
      'default_in_list' => false,
      'label' => '邮编',
    ),
    'phone' =>
    array(
      'type' => 'varchar(100)',
      'editable' => false,
      'in_list' => true,
      'default_in_list' => true,
      'label' => '电话',
    ),
    'uname' =>
    array(
      'type' => 'varchar(100)',
      'editable' => false,
      'in_list' => true,
      'default_in_list' => true,
      'label' => '姓名',
    ),
    'cellphone' =>
    array(
      'type' => 'varchar(100)',
      'editable' => false,
      'in_list' => false,
      'default_in_list' => false,
      'label' => '手机',
    ),
    'sex' => 
    array (
      'type' => 
      array(
        'female' => '女性',
        'male' => '男性',
      ),
      'default' => 'male',
      'editable' => false,
      'in_list' => true,
      'default_in_list' => true,
      'label' => '性别',
    ),
    'memo' =>
    array(
        'type' => 'longtext',
        'editable' => false,
        'label' => '备注',
    ),
    'disabled' =>
    array (
      'type' => 'bool',
      'default' => 'false',
      'required' => true,
      'editable' => false,
    ),
  ),
  'engine' => 'innodb',
  'version' => '$Rev: 50831 $',
);
