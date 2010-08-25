<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['servers']=array (
  'columns' =>
  array (
    'server_id' =>
    array (
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'label' => '品牌id',
      'width' => 150,
      'comment' => '品牌id',
      'editable' => false,
      'in_list' => false,
      'default_in_list' => false,
    ),
    'server_name' =>
    array (
      'type' => 'varchar(50)',
      'label' => '品牌名称',
      'width' => 180,
      'is_title' => true,
      'required' => true,
      'comment' => '品牌名称',
      'editable' => true,
      'searchtype' => 'has',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'server_url' =>
    array (
      'type' => 'varchar(255)',
      'label' => '品牌网址',
      'width' => 350,
      'comment' => '品牌网址',
      'editable' => true,
      'searchtype' => 'has',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'server_desc' =>
    array (
      'type' => 'longtext',
      'comment' => '品牌介绍',
      'editable' => false,
      'label' => '品牌介绍',
    ),
    'server_logo' =>
    array (
      'type' => 'varchar(255)',
      'comment' => '品牌图片标识',
      'editable' => false,
      'label' => '品牌图片标识',
    ),
    'server_keywords' =>
    array (
      'type' => 'longtext',
      'label' => '品牌别名',
      'width' => 150,
      'comment' => '品牌别名',
      'editable' => false,
      'searchtype' => 'has',
      'in_list' => true,
    ),
    'server_setting' =>
    array(
        'type' => 'serialize',
        'label' => '商品设置',
        'deny_export' => true,
    ),
    'disabled' =>
    array (
      'type' => 'bool',
      'default' => 'false',
      'comment' => '失效',
      'editable' => false,
      'label' => '失效',
      'in_list' => false,
      'deny_export' => true,
    ),
    'ordernum' =>
    array (
      'type' => 'number',
      'label' => '排序',
      'width' => 150,
      'comment' => '排序',
      'editable' => true,
      'in_list' => true,
    ),
  ),
  'comment' => '品牌表',
  'index' =>
  array (
    'ind_disabled' =>
    array (
      'columns' =>
      array (
        0 => 'disabled',
      ),
    ),
    'ind_ordernum' =>
    array (
      'columns' =>
      array (
        0 => 'ordernum',
      ),
    ),
  ),
  'version' => '$Rev: 40654 $',
);
