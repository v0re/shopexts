<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['goods_type_spec']=array (
  'columns' => 
  array (
    'spec_id' => 
    array (
      'type' => 'table:specification',
      'pkey' => true,
      'default' => 0,
      'editable' => false,
    ),
    'type_id' => 
    array (
      'type' => 'table:goods_type',
      'default' => 0,
      'pkey' => true,
      'editable' => false,
    ),
    'spec_style' => 
    array (
      'type' => 
      array (
        'select' => '下拉',
        'flat' => '平面',
        'disabled' => '禁用',
      ),
      'default' => 'flat',
      'required' => true,
      'editable' => false,
    ),
  ),
  'comment' => '类型 规格索引表',
  'version' => '$Rev: 40912 $',
);
