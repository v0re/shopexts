<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['order_log']=array (
  'columns' => 
  array (
    'log_id' => 
    array (
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'editable' => false,
    ),
    'rel_id' => 
    array (
      'type' => 'bigint unsigned',
      'required' => true,
      'default' => 0,
      'editable' => false,
    ),
    'op_id' => 
    array (
      'type' => 'number',//'table:users@desktop',
      'label' => '操作员',
      'width' => 110,
      'editable' => false,
      'filtertype' => 'normal',
      'in_list' => true,
    ),
    'op_name' => 
    array (
      'type' => 'varchar(100)',
      'label' => '操作人名称',
      'width' => 110,
      'editable' => false,
      'filtertype' => 'normal',
      'filterdefault' => true,
      'in_list' => true,
    ),
    'alttime' => 
    array (
      'type' => 'time',
      'label' => '操作时间',
      'width' => 110,
      'editable' => false,
      'filtertype' => 'time',
      'filterdefault' => true,
      'in_list' => true,
    ),
   'bill_type' => 
    array (
      'type' => 
      array (
        'order' => '订单支付',
        'recharge' => '预存款充值',
        'joinfee' => '加盟费',
      ),
      'default' => 'order',
      'required' => true,
      'label' => '支付类型',
      'width' => 110,
      'editable' => false,
      'filtertype' => 'yes',
      'filterdefault' => true,
      'in_list' => true,
    ),
    'behavior' => 
    array (
      'type' => 
      array (
        'creates' => '创建',
        'updates' => '修改',
        'payments' => '支付',
        'refunds' => '退款',
        'delivery' => '发货',
        'reship' => '退货',
        'finish' => '完成',
        'cancel' => '取消',
      ),
      'default' => 'payments',
      'required' => true,
      'label' => '操作行为',
      'width' => 110,
      'editable' => false,
      'filtertype' => 'yes',
      'filterdefault' => true,
      'in_list' => true,
    ),
    'result' => 
    array (
      'type' => 
      array (
        'SUCCESS' => '成功',
        'FAILURE' => '失败',
      ),
      'required' => true,
      'label' => '操作结果',
      'width' => 110,
      'editable' => false,
      'filtertype' => 'yes',
      'filterdefault' => true,
      'in_list' => true,
    ),
    'log_text' => 
    array (
      'type' => 'longtext',
      'editable' => false,
      'in_list' => true,
      'default_in_list' => false,
    ),
  ),
  'engine' => 'innodb',
  'version' => '$Rev: 46974 $',
);
