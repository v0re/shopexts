<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['table']=array (
  'columns' => 
  array (
    'tbl_id' => 
    array (
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra'=>'auto_increment',
      'width' => 100,
      'label' => '表id',
      'hidden' => 1,
      'editable' => false,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'tbl_name' => 
    array (
      'type' => 'varchar(100)',
      'width' => 100,
      'label' => '名字',
      'editable' => false,
      'in_list' => true,
       'default_in_list' => true,
    ),
    'tbl_value' => 
    array (
      'type' => 'varchar(255)',
      'default' => 'false',
      'width' => 100,
      'label' => '值',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'tbl_status' => 
    array (
      'label' => '状态',
      'width' => 200,
      'default' => 'wait',
      'type' => 
      array (
        'run' => '运行',
        'hangup' => '挂起',
        'sleep' => '休眠',
        'wait' => '等待',
      ),
      'in_list' => true,
      'default_in_list' => true,
    ),
  ),
  'index' => 
  array (
    'ind_status' => 
    array (
      'columns' => 
      array (
        0 => 'tbl_status',
      ),
    ),
  ),
'comment' => '品牌表',
  'version' => '$Rev: 44008 $',
);