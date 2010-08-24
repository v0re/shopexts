<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['goods_type_props_value']=array (
  'columns' => 
  array (
    'props_value_id' => 
    array (
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'label' => '属性值序号',
      'width' => 110,
      'editable' => false,
      'in_list' => true,
  ),
    'props_id' => 
    array (
      'type' => 'table:goods_type_props',
      'required' => true,
      'pkey' => true,
      'label' => '属性序号',
      'width' => 110,
      'editable' => false,
      'in_list' => true,
      'default_in_list' => true,
  ),
    'name' => 
    array (
      'type' => 'varchar(100)',
      'required' => true,
      'default' => '',
      'label' => '类型名称',
      'is_title' => true,
      'width' => 150,
      'editable' => true,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'alias' => 
    array (
      'type' => 'longtext',
      'editable' => false,
    ),
    'lastmodify' => 
    array (
      'label' => '供应商最后更新时间',
      'width' => 150,
      'type' => 'time',
      'hidden' => 1,
      'in_list' => false,
    ),
  ),
  'comment' => '商品类型表',
  'index' => 
  array (
    'ind_props_id' => 
    array (
      'columns' => 
      array (
        0 => 'props_id',
      ),
    ),
  ),
  'version' => '$Rev: 40654 $',
);
