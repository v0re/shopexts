<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['tag']=array (
  'columns' => 
  array (
    'tag_id' => 
    array (
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'editable' => false,
    ),
    'tag_name' => 
    array (
      'type' => 'varchar(20)',
      'required' => true,
      'default' => '',
      'label' => '标签名',
      'width' => 200,
      'editable' => true,
      'in_list' => true,
      'default_in_list' => true,
      'is_title' => true,
    ),
    'tag_mode' => 
    array (
      'type' => 
      array (
        'normal' => '普通标签',
        'filter' => '自动标签',
      ),
      'default' => 'normal',
      'label' => '标签类型',
      'required' => true,
      'editable' => false,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'app_id' => 
    array (
      'type' => 'varchar(32)',
      'label' => '应用',
      'required' => true,
      'width' => 100,
      'in_list' => true,
    ),
    'tag_type' => 
    array (
      'type' => 'varchar(20)',
      'required' => true,
      'default' => '',
      'label' => '标签对象',
      'editable' => false,
      'in_list' => true,
    ),
    'tag_abbr' => 
    array (
      'type' => 'varchar(6)',
      'required' => true,
      'default' => '',
      'label' => '标签缩写',
      'editable' => false,
      'in_list' => true,
    ),
    'tag_bgcolor' => 
    array (
      'type' => 'varchar(7)',
      'required' => true,
      'default' => '',
      'label' => '标签背景颜色',
      'editable' => false,
      'in_list' => true,
    ),
    'tag_fgcolor' => 
    array (
      'type' => 'varchar(7)',
      'required' => true,
      'default' => '',
      'label' => '标签字体颜色',
      'editable' => false,
      'in_list' => true,
    ),
    'tag_filter' => 
    array (
      'type' => 'varchar(255)',
      'required' => true,
      'default' => '',
      'label' => '标签条件',
      'editable' => false,
      'in_list' => true,
    ),
    'rel_count' => 
    array (
      'type' => 'number',
      'default' => 0,
      'required' => true,
      'editable' => false,
    ),
  ),
  'index' => 
  array (
    'ind_type' => 
    array (
      'columns' => 
      array (
        0 => 'tag_type',
      ),
    ),
    'ind_name' => 
    array (
      'columns' => 
      array (
        0 => 'tag_name',
      ),
    ),
  ),
  'version' => '$Rev: 42201 $',
);
