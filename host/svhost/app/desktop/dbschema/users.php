<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['users']=array (
  'columns' => 
  array (
    'user_id' => 
    array (
      'type' => 'table:account@pam',
      'required' => true,
//      'sdfpath' => 'pam_account/account_id',
      'pkey' => true,
      'label' => '用户名',
      'width' => 110,
      'editable' => false,
      'hidden' => true,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'name' => 
    array (
      'type' => 'varchar(30)',
      'label' => '姓名',
      'width' => 110,
      'editable' => true,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'config' => 
    array (
      'type' => 'serialize',
      'editable' => false,
    ),
    'favorite' => 
    array (
      'type' => 'longtext',
      'editable' => false,
    ),
    'super' => 
    array (
      'type' => 'intbool',
      'default' => 0,
      'required' => true,
      'label' => '超级管理员',
      'width' => 75,
      'editable' => false,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'lastip' => 
    array (
      'type' => 'varchar(20)',
      'editable' => false,
    ),
    'logincount' => 
    array (
      'type' => 'number',
      'default' => 0,
      'required' => true,
      'label' => '登陆次数',
      'width' => 110,
      'editable' => false,
      'in_list' => true,
    ),
    'lastlogin' => 
    array (
      'type' => 'time',
      'default' => 0,
      'required' => true,
      'label' => '最后登陆时间',
      'width' => 110,
      'editable' => false,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'status' => 
    array (
      'type' => 'intbool',
      'default' => 0,
      'label' => '启用',
      'width' => 100,
      'required' => true,
      'editable' => true,
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
    'op_no' => 
    array (
      'type' => 'varchar(50)',
      'label' => '编号',
      'width' => 30,
      'editable' => true,
      'in_list' => true,
    ),
    'memo' => 
    array (
      'type' => 'text',
      'label' => '备注',
      'width' => 270,
      'editable' => false,
      'in_list' => true,
    ),
  ),
  'comment' => '商店后台管理员表',
  'index' => 
  array (   
    'ind_disabled' => 
    array (
      'columns' => 
      array (
        0 => 'disabled',
      ),
    ),
  ),
  'engine' => 'innodb',
  'version' => '$Rev: 40912 $',
);
