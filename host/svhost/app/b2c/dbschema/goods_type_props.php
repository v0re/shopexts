<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['goods_type_props']=array (
  'columns' => 
    array (
      'props_id' => array(
        'type' => 'number',
        'required' => true,
        'extra' => 'auto_increment',
        'label' => '属性序号',
        'width' => 110,
        'editable' => false,
        'pkey' => true,
        'in_list' => true,
        'default_in_list' => true,
    ),
    'type_id' => 
    array (
      'type' => 'table:goods_type',
      'required' => true,
      'label' => '类型序号',
      'width' => 110,
      'editable' => false,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'type'=>array(
        'type'=>'varchar(20)',
        'required' =>true,
        'label' => '展示类型'
    ),
    'search'=>array(
        'type'=>'varchar(20)',
        'required' => true,
        'label' =>'搜索方式',
        'default' => 'select'
    ),
    'show' => array(
        'type' => 'varchar(10)',
        'required' => true,
        'default' => '',
        'in_list' => true,
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
    'goods_p'=>array(
        'type' => 'smallint',
        'label' => '商品位置'
    ),
    'lastmodify' => 
    array (
      'label' => '供应商最后更新时间',
      'width' => 150,
      'type' => 'last_modify',
      'hidden' => 1,
      'in_list' => false,
    ),
  ),
  'comment' => '商品类型表',
    'index' => 
  array (
    'ind_type_id' => 
    array (
      'columns' => 
      array (
        0 => 'type_id',
      ),
    ),
  ),
  'version' => '$Rev: 40654 $',
);
