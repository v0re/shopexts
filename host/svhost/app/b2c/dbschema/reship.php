<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['reship']=array (
  'columns' => 
  array (
    'reship_id' => 
    array (
      'type' => 'bigint unsigned',
      'required' => true,
      'pkey' => true,
      'label' => '发货单号',
      'extra' => 'auto_increment',
      'comment' => '配送流水号',
      'editable' => false,
      'searchtype' => 'has',
      'filtertype' => 'yes',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'reship_bn' => 
    array (
      'type' => 'varchar(32)',
      'required' => false,
      'label' => '中心退货单号',
      'comment' => '退货流水号',
      'editable' => false,
      'width' =>140,
      'searchtype' => 'has',
      'filtertype' => 'yes',
      'filterdefault' => true,
      'in_list' => false,
      'default_in_list' => false,
      'is_title' => false,
    ),
    'order_id' => 
    array (
      'type' => 'varchar(100)',
      'label' => '订单号',
      'comment' => '订单号',
      'editable' => false,
      'searchtype' => 'tequal',
      'filtertype' => 'normal',
      'filterdefault' => true,
      'in_list' => true,
    ),
    'member_id' => 
    array (
      'type' => 'table:members',
      'label' => '会员用户名',
      'comment' => '订货会员ID',
      'editable' => false,
      'filtertype' => 'yes',
      'filterdefault' => true,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'money' => 
    array (
      'type' => 'money',
      'required' => true,
      'default' => 0,
      'label' => '物流费用',
      'comment' => '配送费用',
      'editable' => false,
      'filtertype' => 'number',
      'in_list' => true,
    ),
    'is_protect' => 
    array (
      'type' => 'bool',
      'default' => 'false',
      'required' => true,
      'label' => '是否保价',
      'comment' => '是否保价',
      'editable' => false,
      'filtertype' => 'yes',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'delivery' => 
    array (
      'type' => 'varchar(20)',
      'label' => '配送方式',
      'comment' => '配送方式(货到付款、EMS...)',
      'editable' => false,
      'filtertype' => 'normal',
      'filterdefault' => true,
      'in_list' => true,
      'is_title' => true,
      'default_in_list' => true,
    ),
    'logi_id' => 
    array (
      'type' => 'varchar(50)',
      'comment' => '物流公司ID',
      'editable' => false,
      'label' => '物流公司ID',
      'in_list' => true,
    ),
    'logi_name' => 
    array (
      'type' => 'varchar(100)',
      'label' => '物流公司',
      'comment' => '物流公司名称',
      'editable' => false,
      'filtertype' => 'normal',
      'filterdefault' => true,
      'in_list' => true,
    ),
    'logi_no' => 
    array (
      'type' => 'varchar(50)',
      'label' => '物流单号',
      'comment' => '物流单号',
      'editable' => false,
      'searchtype' => 'tequal',
      'filtertype' => 'normal',
      'filterdefault' => true,
      'in_list' => true,
    ),
    'ship_name' => 
    array (
      'type' => 'varchar(50)',
      'label' => '收货人',
      'comment' => '收货人姓名',
      'editable' => false,
      'searchtype' => 'tequal',
      'filtertype' => 'normal',
      'filterdefault' => true,
      'in_list' => true,
    ),
    'ship_area' => 
    array (
      'type' => 'region',
      'label' => '收货地区',
      'comment' => '收货人地区',
      'editable' => false,
      'filtertype' => 'normal',
      'filterdefault' => true,
      'in_list' => true,
    ),
    'ship_addr' => 
    array (
      'type' => 'varchar(100)',
      'label' => '收货地址',
      'comment' => '收货人地址',
      'editable' => false,
      'filtertype' => 'normal',
      'filterdefault' => true,
      'in_list' => true,
    ),
    'ship_zip' => 
    array (
      'type' => 'varchar(20)',
      'label' => '收货邮编',
      'comment' => '收货人邮编',
      'editable' => false,
      'filtertype' => 'normal',
      'in_list' => true,
    ),
    'ship_tel' => 
    array (
      'type' => 'varchar(30)',
      'label' => '收货人电话',
      'comment' => '收货人电话',
      'editable' => false,
      'filtertype' => 'normal',
      'in_list' => true,
    ),
    'ship_mobile' => 
    array (
      'type' => 'varchar(50)',
      'label' => '收货人手机',
      'comment' => '收货人手机',
      'editable' => false,
      'filtertype' => 'normal',
      'filterdefault' => true,
      'in_list' => true,
    ),
    'ship_email' => 
    array (
      'type' => 'varchar(150)',
      'label' => '收货人Email',
      'comment' => '收货人Email',
      'editable' => false,
      'filtertype' => 'normal',
      'in_list' => true,
    ),
    't_begin' => 
    array (
      'type' => 'time',
      'label' => '单据创建时间',
      'comment' => '单据生成时间',
      'editable' => false,
      'filtertype' => 'time',
      'in_list' => true,
      'default_in_list' => true,
    ),
    't_send' => 
    array (
      'type' => 'time',
      'comment' => '单据结束时间',
      'editable' => false,
      'label' => '单据结束时间',
      'in_list' => true,
    ),
    't_confirm' => 
    array (
      'type' => 'time',
      'comment' => '确认时间',
      'editable' => false,
      'label' => '确认时间',
      'in_list' => true,
    ),
    'op_name' => 
    array (
      'type' => 'varchar(50)',
      'label' => '操作员',
      'comment' => '操作者',
      'editable' => false,
      'searchtype' => 'tequal',
      'filtertype' => 'normal',
      'in_list' => true,
    ),
    'status' => 
    array (
      'type' => 
      array (
        'succ' => '成功到达',
        'failed' => '发货失败',
        'cancel' => '已取消',
        'lost' => '货物丢失',
        'progress' => '运送中',
        'timeout' => '超时',
        'ready' => '准备发货',
      ),
      'default' => 'ready',
      'required' => true,
      'comment' => '状态',
      'editable' => false,
      'label' => '状态',
      'in_list' => true,
    ),
    'memo' => 
    array (
      'type' => 'longtext',
      'label' => '备注',
      'comment' => '备注',
      'editable' => false,
      'filtertype' => 'normal',
      'in_list' => true,
    ),
    'disabled' => 
    array (
      'type' => 'bool',
      'default' => 'false',
      'comment' => '无效',
      'editable' => false,
      'label' => '无效',
      'in_list' => true,
    ),
  ),
  'comment' => '发货/退货单表',
  'index' => 
  array (
    'ind_disabled' => 
    array (
      'columns' => 
      array (
        0 => 'disabled',
      ),
    ),
    'ind_logi_no' => 
    array (
      'columns' => 
      array (
        0 => 'logi_no',
      ),
    ),
  ),
  'version' => '$Rev: 40654 $',
);
