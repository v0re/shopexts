<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['member_lv']=array (
  'columns' => 
  array (
    'member_lv_id' => 
    array (
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'label' => 'ID',
      'width' => 110,
      'editable' => false,
      'in_list' => true,
      'default_in_list' => false,
    ),
    'name' => 
    array (
      'type' => 'varchar(100)',
      'is_title' => true,
      'required' => true,
      'default' => '',
      'label' => '等级名称',
      'width' => 110,
      'editable' => true,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'dis_count' => 
    array (
      'type' => 'decimal(5,2)',
      'default' => '1',
      'required' => true,
      'label' => '会员折扣率',
      'width' => 110,
      'match' => '[0-9\\.]+',
      'editable' => true,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'pre_id' => 
    array (
      'type' => 'mediumint',
      'editable' => false,
    ),
    'default_lv' => 
    array (
      'type' => 'intbool',
      'default' => 0,
      'required' => true,
      'label' => '是否默认',
      'width' => 110,
      'editable' => false,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'deposit_freeze_time' => 
    array (
      'type' => 'int',
      'default' => 0,
      'editable' => false,
    ),
    'deposit' => 
    array (
      'type' => 'int',
      'default' => 0,
      'editable' => false,
    ),
    'more_point' => 
    array (
      'type' => 'int',
      'default' => 1,
      'editable' => false,
    ),
    'point' => 
    array (
      'type' => 'number',
      'default' => 0,
      'required' => true,
      'label' => '所需积分',
      'width' => 110,
      'editable' => false,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'lv_type' => 
    array (
      'type' => 
      array (
        'retail' => '零售',
        'wholesale' => '批发',
        'dealer' => '代理',
      ),
      'default' => 'retail',
      'required' => true,
      'label' => '等级类型',
      'width' => 110,
      'editable' => false,
      'in_list' => true,
      'default_in_list' => false,
    ),
    'disabled' => 
    array (
      'type' => 'bool',
      'default' => 'false',
      'editable' => false,
    ),
    'show_other_price' => 
    array (
      'type' => 'bool',
      'default' => 'true',
      'required' => true,
      'editable' => false,
    ),
    'order_limit' => 
    array (
      'type' => 'tinyint(1)',
      'default' => 0,
      'required' => true,
      'editable' => false,
    ),
    'order_limit_price' => 
    array (
      'type' => 'money',
      'default' => '0.000',
      'required' => true,
      'editable' => false,
    ),
    'lv_remark' => 
    array (
      'type' => 'text',
      'editable' => false,
    ),
    'experience' => 
    array (
      'label' => '经验值',
      'type' => 'int(10)',
      'default' => 0,
      'required' => true,
      'editable' => false,
      'in_list' => true,
    ),
  ),
  'index' => 
  array (
    'ind_disabled' => 
    array (
      'columns' => 
      array (
        0 => 'disabled',
      ),
    ),
    'ind_name' => 
    array (
      'columns' => 
      array (
        0 => 'name',
      ),
      'prefix' => 'UNIQUE',
    ),
  ),
  'engine' => 'innodb',
  'version' => '$Rev: 44523 $',
);
