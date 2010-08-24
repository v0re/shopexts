<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['meta_value_text']=array (
  'columns' => 
  array (
    'mr_id' => 
    array (
      'type' => 'number',
      'required' => true,
      'comment' => '关联表的id id',
    ),
    'pk' => 
    array (
      'type' => 'number',
      'required' => true, 
      'comment' => '查询结果集定位基准', 
    ),
    'value' => 
    array (
      'type' => 'text NOT NULL',
      'required' => true,
      'comment' => 'meta值',
    ),
  ),
  'comment' => 'meta系统text类型存值表',
  'index' => 
  array (
    'ind_mr_pk' => 
    array (
      'columns' => 
      array (
        0 => 'mr_id',
        1 => 'pk',
      ),
    ),
  ),
  'engine' => 'innodb',
  'version' => '$Rev: 40912 $',
);
