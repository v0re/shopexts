<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
/**
* @table coupons;
*
* @package dbschema
* @version $v1
* @copyright 2010 ShopEx
* @license Commercial
*/

$db['coupons']=array (
  'columns' =>
  array (
    'cpns_id' =>
    array (
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'label' => __('id'),
      'width' => 110,
      'comment' => __('优惠券方案id'),
      'editable' => false,
    ),
    'cpns_name' =>
    array (
      'type' => 'varchar(255)',
      'label' => __('优惠券名称'),
      'searchable' => true,
      'width' => 110,
      'comment' => __('优惠券名称'),
      'editable' => false,
      'in_list' => true,
      'default_in_list' => true,
      'filterdefault'=>true,
    ),
    'pmt_id' =>
    array (
      'type' => 'number',
      'comment' => __('*暂时废弃'),
      'editable' => false,
    ),
    'cpns_prefix' =>
    array (
      'type' => 'varchar(50)',
      'required' => true,
      'default' => '',
      'label' => __('优惠券号码'),
      'width' => 110,
      'comment' => __('生成优惠券前缀/号码(当全局时为号码)'),
      'editable' => false,
      'in_list' => true,
      'default_in_list' => true,
      'filterdefault'=>true,
    ),
    'cpns_gen_quantity' =>
    array (
      'type' => 'number',
      'default' => 0,
      'required' => true,
      'label' => __('总数量'),
      'width' => 110,
      'comment' => __('总数量'),
      'editable' => false,
      'in_list' => true,
      'default_in_list' => true,
      'filterdefault'=>true,
    ),
    'cpns_key' =>
    array (
      'type' => 'varchar(20)',
      'required' => true,
      'default' => '',
      'width' => 110,
      'comment' => __('优惠券生成的key'),
      'editable' => false,
    ),
    'cpns_status' =>
    array (
      'type' => 'intbool',
      'default' => 1,
      'required' => true,
      'label' => __('是否启用'),
      'width' => 110,
      'comment' => __('优惠券方案状态'),
      'editable' => false,
      'in_list' => true,
      'default_in_list' => true,
      'filterdefault'=>true,
    ),
    'cpns_type' =>
    array (
      'type' =>
      array (
        0 => __('一张无限使用'),
        1 => __('多张使用一次'),
        //2 => __('外部优惠券'),
      ),
      'default' => 0,
      'required' => true,
      'label' => __('优惠券类型'),
      'width' => 110,
      'comment' => __('优惠券类型'),
      'editable' => false,
      'in_list' => true,
      'default_in_list' => true,
      'filterdefault'=>true,
    ),
    'cpns_point' =>
    array (
      'type' => 'number',
      'default' => NULL,
      'label' => __('兑换所需积分'),
      'width' => 110,
      'comment' => __('兑换优惠券积分'),
      'editable' => false,
      'in_list' => true,
    ),
    'rule_id'=>
        array(
          'type' => 'table:sales_rule_order',
          'sdfpath' => 'rule/rule_id',
          'default' => NULL,
          'comment' => __('相关的订单促销规则ID'), // rule_type ='C'
          'editable' => false,
        ),
  ),

  'comment' => __('优惠券表'),
  'index' =>
  array (
    'ind_cpns_prefix' =>
    array (
      'columns' =>
      array (
        0 => 'cpns_prefix',
      ),
    ),
  ),
);
