<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['orders']=array (
  'columns' => 
  array (
    'order_id' => 
    array (
      'type' => 'bigint unsigned',
      'required' => true,
      'default' => 0,
      'pkey' => true,
      'label' => '订单号',
      'is_title' => true,
      'width' => 110,
      'searchtype' => 'has',
      'editable' => false,
      'filtertype' => 'custom',
      'filterdefault' => true,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'member_id' => 
    array (
      'type' => 'table:members',
      'label' => '会员用户名',
      'width' => 75,
      'editable' => false,
      'filtertype' => 'yes',
      'filterdefault' => true,
      'in_list' => true,
    ),
    'confirm' => 
    array (
      'type' => 'tinybool',
      'default' => 'N',
      'required' => true,
      'label' => '确认状态',
      'width' => 75,
      'hidden' => true,
      'editable' => false,
      'in_list' => false,
    ),
    'status' => 
    array (
      'type' => 
      array (
        'active' => '活动订单',
        'dead' => '已作废',
        'finish' => '已完成',
      ),
      'default' => 'active',
      'required' => true,
      'label' => '订单状态',
      'width' => 75,
      'hidden' => true,
      'editable' => false,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'pay_status' => 
    array (
      'type' => 
      array (
        0 => '未支付',
        1 => '已支付',
        2 => '处理中',
        3 => '部分付款',
        4 => '部分退款',
        5 => '全额退款',
      ),
      'default' => 0,
      'required' => true,
      'label' => '付款状态',
      'width' => 75,
      'editable' => false,
      'filtertype' => 'yes',
      'filterdefault' => true,
      'in_list' => true,
    ),
    'ship_status' => 
    array (
      'type' => 
      array (
        0 => '未发货',
        1 => '已发货',
        2 => '部分发货',
        3 => '部分退货',
        4 => '已退货',
      ),
      'default' => 0,
      'required' => true,
      'label' => '发货状态',
      'width' => 75,
      'editable' => false,
      'filtertype' => 'yes',
      'filterdefault' => true,
      'in_list' => true,
    ),
    'is_delivery' => 
    array (
      'type' => 'tinybool',
      'default' => 'Y',
      'required' => true,
      'editable' => false,
    ),
    'shipping_id' => 
    array (
      'type' => 'table:dlytype',
      'label' => '配送方式',
      'filtertype' => 'yes',
      'filterdefault' => true,
      'editable' => false,
      'sdfpath' => 'shipping/shipping_id',
      'in_list' => true,
    ),
    'shipping' => 
    array (
      'type' => 'varchar(100)',
      'label' => '配送方式',
      'width' => 75,
      'editable' => false,
      'sdfpath' => 'shipping/shipping_name',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'payment' => 
    array (
      'type' => 'varchar(100)',
      'label' => '支付方式',
      'width' => 75,
      'editable' => false,
      'filtertype' => 'yes',
      'filterdefault' => true,
      'sdfpath' => 'payinfo/pay_app_id',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'weight' => 
    array (
      'type' => 'money',
      'editable' => false,
    ),
    'tostr' => 
    array (
      'type' => 'longtext',
      'editable' => false,
      'sdfpath' => 'title',
    ),
    'itemnum' => 
    array (
      'type' => 'number',
      'editable' => false,
    ),
    'createtime' => 
    array (
      'type' => 'time',
      'label' => '下单时间',
      'width' => 110,
      'editable' => false,
      'filtertype' => 'time',
      'filterdefault' => true,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'last_modified' => 
    array (
      'label' => '最后更新时间',
      'type' => 'last_modify',
      'width' => 110,
      'editable' => false,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'ip' => 
    array (
      'type' => 'varchar(15)',
      'editable' => false,
    ),
    'ship_name' => 
    array (
      'type' => 'varchar(50)',
      'label' => '收货人',
      'sdfpath' => 'consignee/name',
      'width' => 75,
      'searchtype' => 'head',
      'editable' => false,
      'filtertype' => 'normal',
      'filterdefault' => true,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'ship_area' => 
    array (
      'type' => 'region',
      'label' => '收货地区',
      'width' => 180,
      'editable' => false,
      'filtertype' => 'yes',
      'sdfpath' => 'consignee/area',
      'in_list' => true,
    ),
    'ship_addr' => 
    array (
      'type' => 'varchar(100)',
      'label' => '收货地址',
      'searchtype' => 'has',
      'width' => 180,
      'editable' => false,
      'filtertype' => 'normal',
      'sdfpath' => 'consignee/addr',
      'in_list' => true,
    ),
    'ship_zip' => 
    array (
      'type' => 'varchar(20)',
      'editable' => false,
      'sdfpath' => 'consignee/zip',
    ),
    'ship_tel' => 
    array (
      'type' => 'varchar(30)',
      'label' => '收货人电话',
      'searchtype' => 'has',
      'width' => 75,
      'editable' => false,
      'filtertype' => 'normal',
      'filterdefault' => true,
      'sdfpath' => 'consignee/telephone',
      'in_list' => true,
    ),
    'ship_email' => 
    array (
      'type' => 'varchar(150)',
      'editable' => false,
      'sdfpath' => 'consignee/email',
    ),
    'ship_time' => 
    array (
      'type' => 'varchar(50)',
      'editable' => false,
      'sdfpath' => 'consignee/r_time',
    ),
    'ship_mobile' => 
    array (
      'label' => '收货人手机',
      'hidden' => true,
      'searchtype' => 'has',
      'type' => 'varchar(50)',
      'editable' => false,
      'sdfpath' => 'consignee/mobile',
      'in_list' => true,
    ),
    'cost_item' => 
    array (
      'type' => 'money',
      'default' => '0',
      'required' => true,
      'editable' => false,
    ),
    'is_tax' => 
    array (
      'type' => 'bool',
      'default' => 'false',
      'required' => true,
      'editable' => false,
    ),
    'cost_tax' => 
    array (
      'type' => 'money',
      'default' => '0',
      'required' => true,
      'editable' => false,
    ),
    'tax_company' => 
    array (
      'type' => 'varchar(255)',
      'editable' => false,
      'sdfpath' => 'tax_title',
    ),
    'cost_freight' => 
    array (
      'type' => 'money',
      'default' => '0',
      'required' => true,
      'label' => '配送费用',
      'width' => 75,
      'editable' => false,
      'filtertype' => 'number',
      'sdfpath' => 'shipping/cost_shipping',
      'in_list' => true,
    ),
    'is_protect' => 
    array (
      'type' => 'bool',
      'default' => 'false',
      'required' => true,
      'editable' => false,
      'sdfpath' => 'shipping/is_protect',
    ),
    'cost_protect' => 
    array (
      'type' => 'money',
      'default' => '0',
      'required' => true,
      'editable' => false,
      'sdfpath' => 'shipping/cost_protect',
    ),
    'cost_payment' => 
    array (
      'type' => 'money',
      'editable' => false,
      'sdfpath' => 'payinfo/cost_payment',
    ),
    'currency' => 
    array (
      'type' => 'varchar(8)',
      'editable' => false,
    ),
    'cur_rate' => 
    array (
      'type' => 'decimal(10,4)',
      'default' => '1.0000',
      'editable' => false,
    ),
    'score_u' => 
    array (
      'type' => 'money',
      'default' => '0',
      'required' => true,
      'editable' => false,
    ),
    'score_g' => 
    array (
      'type' => 'money',
      'default' => '0',
      'required' => true,
      'editable' => false,
    ),
    'discount' => 
    array (
      'type' => 'money',
      'default' => '0',
      'required' => true,
      'editable' => false,
    ),
    'total_amount' => 
    array (
      'type' => 'money',
      'default' => '0',
      'required' => true,
      'label' => '订单总额',
      'width' => 75,
      'editable' => false,
      'filtertype' => 'number',
      'filterdefault' => true,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'final_amount' => 
    array (
      'type' => 'money',
      'default' => '0',
      'required' => true,
      'editable' => false,
      'sdfpath' => 'cur_amount',
    ),
    'pmt_goods' => 
    array (
      'type' => 'money',
      'editable' => false,
    ),
    'pmt_order' => 
    array (
      'type' => 'money',
      'editable' => false,
    ),
    'payed' => 
    array (
      'type' => 'money',
      'default' => '0',
      'editable' => false,
    ),
    'memo' => 
    array (
      'type' => 'longtext',
      'editable' => false,
    ),
    'mark_text' => 
    array (
      'type' => 'longtext',
      'label' => '订单备注',
      'width' => 50,
      'editable' => false,
      'searchtype' => 'has',
      'filtertype' => 'normal',
      'in_list' => true,
    ),
    'disabled' => 
    array (
      'type' => 'bool',
      'default' => 'false',
      'editable' => false,
    ),
    'mark_type' => 
    array (
      'type' => 'varchar(2)',
      'default' => 'b1',
      'required' => true,
      'label' => '订单备注图标',
      'hidden' => true,
      'width' => 150,
      'editable' => false,
      'in_list' => true,
    ),
    'extend' => 
    array (
      'type' => 'varchar(255)',
      'default' => 'false',
      'editable' => false,
    ),
    'order_refer' => 
    array (
      'type' => 'varchar(20)',
      'required' => true,
      'default' => 'local',
      'hidden' => true,
    ),
  ),
  'index' => 
  array (
    'ind_ship_status' => 
    array (
      'columns' => 
      array (
        0 => 'ship_status',
      ),
    ),
    'ind_pay_status' => 
    array (
      'columns' => 
      array (
        0 => 'pay_status',
      ),
    ),
    'ind_status' => 
    array (
      'columns' => 
      array (
        0 => 'status',
      ),
    ),
    'ind_disabled' => 
    array (
      'columns' => 
      array (
        0 => 'disabled',
      ),
    ),
  ),
  'engine' => 'innodb',
  'version' => '$Rev: 42376 $',
);
