<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['return_product']=array (
  'columns' => 
  array (
    'order_id' => 
    array (
      'type' => 'table:orders@b2c',
      'default' => '0',
      'required' => true,
      'default' => 0,
      'editable' => false,
      'in_list' => true,
      'default_in_list' => true,	  	  
      'searchtype' => 'has',
      'filtertype' => 'yes',
      'label' => '订单号',
    ),
    'member_id' => 
    array (
      'type' => 'table:members@b2c',
      'default' => '0',
      'required' => true,
      'editable' => false,
      'in_list' => true,
      'default_in_list' => true,
      'label' => '申请人',
    ),
    'return_id' => 
    array (
      'type' => 'bigint(20)',
      'required' => true,
      'pkey' => true,
      'editable' => false,
      'in_list' => true,	  
      'searchtype' => 'has',
      'filtertype' => 'yes',
	  'default_in_list' => true,
      'label' => '退货记录流水号',
    ),
    'return_bn' =>
    array (
      'type' => 'varchar(32)',
      'required' => false,
      'label' => '退货记录流水号标识',
      'comment' => '退货记录流水号标识',
      'editable' => false,
      'in_list' => false,
      'default_in_list' => false,
      'is_title' => true,
    ),
    'title' => 
    array (
      'type' => 'varchar(200)',
      'required' => true,
      'width' => 110,
      'editable' => false,
      'in_list' => true,
      'default_in_list' => true,	  	  
      'searchtype' => 'has',
      'filtertype' => 'yes',
      'label' => '售后服务标题',
    ),
    'content' =>
    array(
        'type' => 'longtext',
        'editable' => false,
        'label' => '退货内容',
    ),
    'status' => 
    array (
      'type' => 
      array(
        '1' => '申请中',
        '2' => '审核中',
        '3' => '接受申请',
        '4' => '完成',
        '5' => '拒绝',
        '6' => '已收货',
        '7' => '已质检',
        '8' => '补差价',
        '9' => '已拒绝退款',
      ),
      'default' => '1',
      'required' => true,
      'comment' => '退货记录状态',
      'editable' => false,
      'in_list' => true,
      'default_in_list' => true,
      'label' => '售后服务状态',
    ),
    'image_file' =>
    array(
        'type' => 'varchar(255)',
        'label' => '附件',
        'width' => 75,
        'hidden' => true,
        'editable' => false,
        'in_list' => true,
    ),
    'product_data' =>
    array(
        'type' => 'longtext',
        'editable' => false,
        'label' => '退货货品记录',
    ),
    'comment' =>
    array(
        'type' => 'longtext',
        'editable' => false,
        'label' => '管理员备注',
    ),
    'add_time' =>
    array(
        'type' => 'time',
        'depend_col' => 'marketable:true:now',
        'label' => '售后处理时间',
        'width' => 110,
        'editable' => false,
        'in_list' => true,
        'default_in_list' => true,
    ),
    'disabled' =>
    array (
      'type' => 'bool',
      'default' => 'false',
      'required' => true,
      'editable' => false,
    ),
  ),
  'engine' => 'innodb',
  'version' => '$Rev: 40912 $',
);
