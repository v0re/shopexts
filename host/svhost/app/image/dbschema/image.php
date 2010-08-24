<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['image']=array (
  'columns' => 
  array (
    'image_id' => 
    array (
      'type' => 'char(32)',
      'label'=>'图片Id',
      'required' => true,
      'pkey' => true,
      'width'=>250,
      'in_list'=>true,
      'default_in_list'=>true,
    ),
    'image_name'=>array(
      'label'=>'图片名称',
      'type' => 'varchar(50)',
      'required' => false,
      'width'=>100,
      'default_in_list'=>true,
    ),
    'storage'=>array(
      'label'=>'存储引擎',
      'type' => 'varchar(50)',
      'default' => 'filesystem',
      'required' => true,
      'in_list'=>true,
      'width'=>100,
      'default_in_list'=>true,
    ),
    'ident'=>array(
      'type' => 'varchar(200)',
      'required' => true,
    ),
    'url'=>array(
      'label'=>'网址',
      'type'=>'varchar(100)',
      'required' => true,
      'width'=>300,
      'in_list'=>true,
    ),
    'l_ident'=>array(
      'type' => 'varchar(200)',
    ),
    'l_url'=>array(
      'type' => 'varchar(200)',
    ),
    'm_ident'=>array(
      'type' => 'varchar(200)',
    ),
    'm_url'=>array(
      'type' => 'varchar(200)',
    ),
    's_ident'=>array(
      'type' => 'varchar(200)',
    ),
    's_url'=>array(
      'type' => 'varchar(200)',
    ),    
    'width'=>array(
       'label'=>'宽度',
      'type' => 'number',
      'in_list'=>true,
      'default_in_list'=>true,
    ),
    'height'=>array(
      'label'=>'高度',
      'type' => 'number',
      'in_list'=>true,
      'default_in_list'=>true,
    ),
    'watermark'=>array(
        'type'=>'bool',
        'label'=>'有水印',
        'in_list'=>true,
        'default_in_list'=>true,
    ),
    'last_modified' => array (
      'label'=>'更新时间',
      'type' => 'last_modify',
      'width'=>180,
      'required' => true,
      'default' => 0,
      'editable' => false,
      'in_list'=>true,
      'default_in_list'=>true,
    ),
  ),
  'engine' => 'innodb',
  'version' => '$Rev: 40912 $',
);
