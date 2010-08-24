<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['tag_rel']=array (
  'columns' => 
  array (
    'tag_id' => 
    array (
      'type' => 'table:tag',
      'sdfpath' => 'tag/tag_id',
      'required' => true,
      'default' => 0,
      'pkey' => true,
      'editable' => false,
    ),
    'rel_id' => 
    array (
      'type' => 'varchar(32)',
      'required' => true,
      'default' => 0,
      'pkey' => true,
      'editable' => false,
    ),
    'app_id' => 
    array (
      'type' => 'varchar(32)',
      'label' => '应用',
      'required' => true,
      'width' => 100,
      'in_list' => true,
    ),
    'tag_type' => 
    array (
      'type' => 'varchar(20)',
      'required' => true,
      'default' => '',
      'label' => '标签对象',
      'editable' => false,
      'in_list' => true,
    ),
  ),
  'version' => '$Rev$',
);
