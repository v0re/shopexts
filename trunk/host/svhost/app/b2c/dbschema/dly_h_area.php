<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['dly_h_area']=array (
  'columns' => 
  array (
    'dha_id' => 
    array (
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'editable' => false,
    ),
    'dt_id' => 
    array (
      'type' => 'number',
      'editable' => false,
    ),
    'area_id' => 
    array (
      'type' => 'mediumint(6) unsigned',
      'default' => 0,
      'editable' => false,
    ),
    'price' => 
    array (
      'type' => 'varchar(100)',
      'default' => 0,
      'editable' => false,
      'is_title' => true,
    ),
    'has_cod' => 
    array (
      'type' => 'tinyint(1) unsigned',
      'default' => 0,
      'required' => true,
      'editable' => false,
    ),
    'areaname_group' => 
    array (
      'type' => 'longtext',
      'editable' => false,
    ),
    'areaid_group' => 
    array (
      'type' => 'longtext',
      'editable' => false,
    ),
    'config' => 
    array (
      'type' => 'varchar(255)',
      'editable' => false,
    ),
    'expressions' => 
    array (
      'type' => 'varchar(255)',
      'editable' => false,
    ),
    'ordernum' => 
    array (
      'type' => 'smallint(4) unsigned',
      'editable' => true,
    ),
  ),
  'comment' => '配送地区运费配置表',
  'version' => '$Rev$',
);
