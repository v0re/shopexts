<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['dlycorp']=array (
  'columns' => 
  array (
    'corp_id' => 
    array (
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'label' => '物流公司ID',
      'width' => 110,
      'editable' => false,
      'hidden' => true,
    ),
    'type' => 
    array (
      'type' => 'varchar(6)',
      'editable' => false,
      'is_title' => true,
    ),
    'name' => 
    array (
      'type' => 'varchar(200)',
      'label' => '物流公司',
      'width' => 180,
      'editable' => true,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'disabled' => 
    array (
      'type' => 'bool',
      'default' => 'false',
      'editable' => false,
    ),
    'ordernum' => 
    array (
      'type' => 'smallint(4) unsigned',
      'label' => '排序',
      'width' => 180,
      'editable' => true,
      'in_list' => true,
    ),
    'website' => 
    array (
      'type' => 'varchar(200)',
      'label' => '网址',
      'width' => 180,
      'editable' => true,
      'default_in_list' => true,
      'in_list' => true,
    ),
    'request_url' => 
    array (
      'type' => 'varchar(200)',
      'label' => '物流公司网址',
      'width' => 180,
      'hidden'=>false,
      'editable' => true,
      'in_list' => true,
    ),
  ),
  'comment' => '物流公司表',
  'index' => 
  array (
    'ind_type' => 
    array (
      'columns' => 
      array (
        0 => 'type',
      ),
    ),
    'ind_disabled' => 
    array (
      'columns' => 
      array (
        0 => 'disabled',
      ),
    ),
    'ind_ordernum' => 
    array (
      'columns' => 
      array (
        0 => 'ordernum',
      ),
    ),
  ),
  'version' => '$Rev$',
);
