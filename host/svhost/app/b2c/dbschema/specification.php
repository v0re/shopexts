<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['specification']=array (
  'columns' => 
  array (
    'spec_id' => 
    array (
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'label' => '规格id',
      'width' => 150,
      'editable' => false,
      'in_list' => false,
    ),
    'spec_name' => 
    array (
      'type' => 'varchar(50)',
      'default' => '',
      'required' => true,
      'label' => '规格名称',
      'width' => 180,
      'editable' => true,
      'in_list' => true,
      'is_title' => true,
      'default_in_list' => true,
    ),
    'alias' => 
    array (
      'type' => 'varchar(255)',
      'default' => '',
      'label' => '规格别名',
      'width' => 180,
      'in_list' => true,
    ),
    'spec_show_type' => 
    array (
      'type' => 
      array (
        'select' => '下拉',
        'flat' => '平铺',
      ),
      'default' => 'flat',
      'required' => true,
      'label' => '显示方式',
      'width' => 75,
      'editable' => true,
      'in_list' => true,
    ),
    'spec_type' => 
    array (
      'type' => 
      array (
        'text' => '文字',
        'image' => '图片',
      ),
      'default' => 'text',
      'required' => true,
      'label' => '类型',
      'width' => 75,
      'editable' => false,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'spec_memo' => 
    array (
      'type' => 'varchar(50)',
      'default' => '',
      'required' => true,
      'label' => '规格备注',
      'width' => 350,
      'editable' => false,
      'in_list' => true,
    ),
    'p_order' => 
    array (
      'type' => 'number',
      'default' => 0,
      'required' => true,
      'editable' => false,
      'deny_export' => true,
    ),
    'disabled' => 
    array (
      'type' => 'bool',
      'default' => 'false',
      'required' => true,
      'editable' => false,
      'deny_export' => true
    ),
  ),
  'comment' => '商店中商品规格',
  'version' => '$Rev: 40654 $',
);
