<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['themes']=array (
  'columns' => 
  array (
    'theme' => 
    array (
      'type' => 'varchar(50)',
      'required' => true,
      'default' => '',
      'pkey' => true,
      'editable' => false,
      'is_title' => true,
    ),
    'name' => 
    array (
      'type' => 'varchar(50)',
      'editable' => false,
      'is_title'=>true,
      'label'=>'模板名称',
      'width'=>'200',
      'in_list'=>true,
      'default_in_list'=>true,
    ),
    'stime' => 
    array (
      'type' => 'int unsigned',
      'editable' => false,
    ),
    'author' => 
    array (
      'type' => 'varchar(50)',
      'editable' => false,
      'label'=>'作者',
      'width'=>'100',
      'in_list'=>true,
      'default_in_list'=>true,
    ),
    'site' => 
    array (
      'type' => 'varchar(100)',
      'editable' => false,
      'label'=>'网址',
      'width'=>'200',
      'in_list'=>true,
      'default_in_list'=>true,
    ),
    'version' => 
    array (
      'type' => 'varchar(50)',
      'editable' => false,
      'label'=>'版本',
      'width'=>'80',
      'in_list'=>true,
      'default_in_list'=>true,
    ),
    'info' => 
    array (
      'type' => 'varchar(255)',
      'editable' => false,
    ),
    'config' => 
    array (
      'type' => 'serialize',
      'editable' => false,
    ),
    'update_url' => 
    array (
      'type' => 'varchar(100)',
      'editable' => false,
    ),
    'is_used' =>
    array (
      'type' => 'bool',
      'editable' => false,
      'default' => 'false',
    ),
  ),
  'version' => '$Rev: 40918 $',
);
