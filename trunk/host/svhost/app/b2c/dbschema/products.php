<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['products']=array (
  'columns' =>
  array (
    'product_id' =>
    array (
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'label' => '货品ID',
      'width' => 110,
      'editable' => false,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'goods_id' =>
    array (
      'type' => 'table:goods',
      'default' => 0,
      'required' => true,
      'label' => '商品ID',
      'width' => 110,
      'editable' => false,
      'in_list' => true,
    ),
    'barcode' =>
    array (
      'type' => 'varchar(128)',
      'label' => '条码',
      'width' => 110,
      'editable' => false,
      'in_list' => true,
    ),
    'title' =>
    array (
      'type' => 'varchar(255)',
      'sdfpath' => 'title',
      'label' => '标题',
      'width' => 110,
      'editable' => false,
      'in_list' => true,
    ),
    'bn' =>
    array (
      'type' => 'varchar(30)',
      'label' => '货号',
      'width' => 75,
      'filtertype' => 'normal',
      'filterdefault' => true,
      'editable' => false,
      'in_list' => true,
    ),
    'price' =>
    array (
      'type' => 'money',
      'sdfpath' => 'price/price/price',
      'default' => '0',
      'required' => true,
      'label' => '销售价格',
      'width' => 75,
      'filtertype' => 'number',
      'filterdefault' => true,
      'editable' => false,
      'in_list' => true,
    ),
    'cost' =>
    array (
      'type' => 'money',
      'sdfpath' => 'price/cost/price',
      'default' => '0',
      'label' => '成本价',
      'required' => true,
      'width' => 110,
      'filtertype' => 'number',
      'editable' => false,
      'in_list' => true,
    ),
    'mktprice' =>
    array (
      'type' => 'money',
      'sdfpath' => 'price/mktprice/price',
      'label' => '市场价',
      'default' => '0',
      'required' => true,
      'width' => 75,
      'filtertype' => 'number',
      'editable' => false,
      'in_list' => true,
    ),
    'name' =>
    array (
      'type' => 'varchar(200)',
//      'sdfpath' => 'title',
      'required' => true,
      'default' => '',
      'label' => '货品名称',
      'width' => 180,
      'filtertype' => 'custom',
      'filterdefault' => true,
      'editable' => false,
      'in_list' => true,
      'default_in_list' => true,
      'is_title' => true,
    ),
    'weight' =>
    array (
      'type' => 'decimal(20,3)',
      'label' => '单位重量',
      'width' => 110,
      'filtertype' => 'number',
      'filterdefault' => true,
      'editable' => false,
      'in_list' => true,
    ),
    'unit' =>
    array (
      'type' => 'varchar(20)',
      'label' => '单位',
      'width' => 110,
      'filtertype' => 'normal',
      'editable' => false,
      'in_list' => true,
    ),
    'store' =>
    array (
      'type' => 'decimal(20,2)',
      'label' => '库存',
      'width' => 30,
      'filtertype' => 'number',
      'filterdefault' => true,
      'editable' => false,
      'in_list' => true,
    ),
    'store_place' =>
    array (
      'type' => 'varchar(255)',
      'label' => __('库位'),
      'width' => 30,
      'editable' => false,
      'hidden'=>true,
    ),
    'freez' =>
    array (
      'type' => 'number',
      'sdfpath' => 'freez',
      'label' => '冻结库存',
      'width' => 110,
      'hidden' => true,
      'editable' => false,
      'in_list' => true,
    ),
    'spec_info' =>
    array (
      'type' => 'longtext',
      'label' => '物品描述',
      'width' => 110,
      'filtertype' => 'normal',
      'editable' => false,
      'in_list' => true,
      'default_in_list' => true,

    ),
    'spec_desc' =>
    array (
      'type' => 'serialize',
      'label' => '规格值,序列化',
      'width' => 110,
      'editable' => false,
      'in_list' => true,
    ),
    'uptime' =>
    array (
      'type' => 'time',
      'label' => '录入时间',
      'width' => 110,
      'editable' => false,
      'in_list' => true,
    ),
    'last_modify' =>
    array (
      'type' => 'last_modify',
      'label' => '最后修改时间',
      'width' => 110,
      'editable' => false,
      'in_list' => true,
    ),
    'disabled' =>
    array (
      'type' => 'bool',
      'default' => 'false',
      'editable' => false,
    ),
    'marketable' =>
    array (
      'type' => 'bool',
      'sdfpath' => 'status',
      'default' => 'true',
      'required' => true,
      'label' => '上架',
      'width' => 30,
      'filtertype' => 'yes',
      'editable' => false,
      'in_list' => true,
    ),
  ),
  'comment' => '货品表',
  'index' =>
  array (
    'ind_disabled' =>
    array (
      'columns' =>
      array (
        0 => 'disabled',
      ),
    ),
    'ind_bn' =>
    array (
      'columns' =>
      array (
        0 => 'bn',
      ),
      'prefix' => 'UNIQUE',
    ),
  ),
  'engine' => 'innodb',
  'version' => '$Rev: 42376 $',
);
