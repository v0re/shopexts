<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['recycle']=array (
  'columns' => 
  array (
    'item_id' => 
    array (
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'editable' => false,
    ),
    'item_title' => 
    array (
      'type' => 'varchar(200)',
      'label'=>'名称',
      'required' => false,
      'is_title'=>true,
      'in_list'=>true,
      'width'=>200,
      'filtertype' => 'yes',
      'filterdefault' => true,
      'default_in_list'=>true,
    ),
    'item_type'=>array(
      'label'=>'类型',
      'type' => 'varchar(80)',
      'required' => true,
      'in_list'=>true,
      'width'=>100,
      'filtertype' => 'yes',
      'filterdefault' => true,

      'default_in_list'=>true,
    ),
    'app_key'=>array(
      'label'=>'应用',
      'type' => 'varchar(80)',
      'required' => true,
      'in_list'=>true,
      'width'=>100,
      'default_in_list'=>true,
    ),
    'drop_time'=>array(
      'type' => 'time',
      'label'=>'删除时间',
      'required' => true,
      'in_list'=>true,
      'width'=>150,
      'filtertype' => 'yes',
      'filterdefault' => true,
      'default_in_list'=>true,
    ),
    'item_sdf'=>array(
      'type' => 'serialize',
      'required' => true,
    ),
  ),
  'engine' => 'innodb',
  'version' => '$Rev: 40912 $',
);

//需要id从大到小的执行
