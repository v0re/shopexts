<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['members']=array (
  'columns' => 
  array (
    'member_id' => 
    array (
      'type' => 'table:account@pam',
      'pkey' => true,
      'sdfpath' => 'pam_account/account_id',
      'label' => '用户名',
      'width' => 110,
      'editable' => false,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'member_lv_id' => 
    array (
      'required' => true,
      'default' => 0,
      'label' => '会员等级',
      'sdfpath' => 'member_lv/member_group_id',
      'width' => 75,
      'type' => 'table:member_lv',
      'editable' => true,
      'filtertype' => 'bool',
      'filterdefault' => 'true',
      'in_list' => true,
    ),
    'name' => 
    array (
      'type' => 'varchar(50)',
      'label' => '姓名',
      'width' => 75,
      'sdfpath' => 'contact/name',
      'searchtype' => 'has',
      'editable' => true,
      'filtertype' => 'normal',
      'filterdefault' => 'true',
      'in_list' => true,
      'is_title'=>true,
      'default_in_list' => true,
    ),
    'lastname' => 
    array (
      'sdfpath' => 'contact/lastname',
      'type' => 'varchar(50)',
      'editable' => false,
    ),
    'firstname' => 
    array (
      'sdfpath' => 'contact/firstname',
      'type' => 'varchar(50)',
      'editable' => false,
    ),
    'area' => 
    array (
      'label' => '地区',
      'width' => 110,
      'type' => 'region',
      'sdfpath' => 'contact/area',
      'editable' => false,
      'filtertype' => 'yes',
      'filterdefault' => 'true',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'addr' => 
    array (
      'type' => 'varchar(255)',
      'label' => '地址',
      'sdfpath' => 'contact/addr',
      'width' => 110,
      'editable' => true,
      'filtertype' => 'normal',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'mobile' => 
    array (
      'type' => 'varchar(30)',
      'label' => '手机',
      'width' => 75,
      'sdfpath' => 'contact/phone/mobile',
      'searchtype' => 'head',
      'editable' => true,
      'filtertype' => 'normal',
      'filterdefault' => 'true',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'tel' => 
    array (
      'type' => 'varchar(30)',
      'label' => '固定电话',
      'width' => 110,
      'sdfpath' => 'contact/phone/telephone',
      'searchtype' => 'head',
      'editable' => true,
      'filtertype' => 'normal',
      'filterdefault' => 'true',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'email' => 
    array (
      'type' => 'varchar(200)',
      'label' => 'EMAIL',
      'width' => 110,
      'sdfpath' => 'contact/email',
      'required' => 1,
      'searchtype' => 'has',
      'editable' => true,
      'filtertype' => 'normal',
      'filterdefault' => 'true',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'zip' => 
    array (
      'type' => 'varchar(20)',
      'label' => '邮编',
      'width' => 110,
      'sdfpath' => 'contact/zipcode',
      'editable' => true,
      'filtertype' => 'normal',
      'in_list' => true,
    ),

    'order_num' => 
    array (
      'type' => 'number',
      'default' => 0,
      'label' => '订单数',
      'width' => 110,
      'editable' => false,
      'hidden' => true,
      'in_list' => true,
    ),
    'refer_id' => 
    array (
      'type' => 'varchar(50)',
      'label' => '来源ID',
      'width' => 75,
      'editable' => false,
      'filtertype' => 'normal',
      'in_list' => false,
    ),
    'refer_url' => 
    array (
      'type' => 'varchar(200)',
      'label' => '推广来源URL',
      'width' => 75,
      'editable' => false,
      'filtertype' => 'normal',
      'in_list' => false,
    ),
    'b_year' => 
    array (
        'label' => '生年',
      'type' => 'smallint unsigned',
      'width' => 30,
      'editable' => false,
      'in_list'=>false,
    ),
    'b_month' => 
    array (
      'label' => '生月',
      'type' => 'tinyint unsigned',
      'width' => 30,
      'editable' => false,
      'hidden' => true,
      'in_list' => false,
    ),
    'b_day' => 
    array (
      'label' => '生日',
      'type' => 'tinyint unsigned',
      'width' => 30,
      'editable' => false,
      'hidden' => true,
      'in_list' => false,
    ),
    'sex' => 
    array (
      'type' => 
      array (
        0 => '女',
        1 => '男',
      ),
      'sdfpath' => 'profile/gender',
      'default' => 1,
      'required' => true,
      'label' => '性别',
      'width' => 30,
      'editable' => true,
      'filtertype' => 'yes',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'addon' => 
    array (
      'type' => 'longtext',
      'editable' => false,
    ),
    'wedlock' => 
    array (
      'type' => 'intbool',
      'default' => 0,
      'required' => true,
      'editable' => false,
    ),
    'education' => 
    array (
      'type' => 'varchar(30)',
      'editable' => false,
    ),
    'vocation' => 
    array (
      'type' => 'varchar(50)',
      'editable' => false,
    ),
    'interest' => 
    array (
      'type' => 'longtext',
      'editable' => false,
    ),
    'advance' => 
    array (
      'type' => 'money',
      'default' => '0.00',
      'required' => true,
      'label' => '预存款',
      'sdfpath' => 'advance/total',
      'width' => 110,
      'editable' => false,
      'filtertype' => 'number',
      'in_list' => true,
    ),
    'advance_freeze' => 
    array (
      'type' => 'money',
      'default' => '0.00',
      'sdfpath' => 'advance/freeze',
      'required' => true,
      'editable' => false,
    ),
    'point_freeze' => 
    array (
      'type' => 'number',
      'default' => 0,
      'required' => true,
      'sdfpath' => 'score/freeze',
      'editable' => false,
    ),
    'point_history' => 
    array (
      'type' => 'number',
      'default' => 0,
      'required' => true,
      'editable' => false,
    ),
    'point' => 
    array (
      'type' => 'number',
      'default' => 0,
      'required' => true,
      'sdfpath' => 'score/total',
      'label' => '积分',
      'width' => 110,
      'editable' => false,
      'filtertype' => 'number',
      'in_list' => true,
    ),
    'score_rate' => 
    array (
      'type' => 'decimal(5,3)',
      'editable' => false,
    ),
    'reg_ip' => 
    array (
      'type' => 'varchar(16)',
      'label' => '注册IP',
      'width' => 110,
      'editable' => false,
      'in_list' => true,
    ),
    'regtime' => 
    array (
      'label' => '注册时间',
      'width' => 75,
      'type' => 'time',
      'editable' => false,
      'filtertype' => 'time',
      'filterdefault' => true,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'state' => 
    array (
      'type' => 'tinyint(1)',
      'default' => 0,
      'required' => true,
      'label' => '验证状态',
      'width' => 110,
      'editable' => false,
      'in_list' => false,
    ),
    'pay_time' => 
    array (
      'type' => 'number',
      'editable' => false,
    ),
    'biz_money' => 
    array (
      'type' => 'money',
      'default' => '0',
      'required' => true,
      'editable' => false,
    ),
    'pw_answer' => 
    array (
      'label' => '回答',
      'type' => 'varchar(250)',
      'sdfpath' => 'account/pw_answer',
      'editable' => false,
    ),
    'pw_question' => 
    array (
      'label' => '安全问题',
      'type' => 'varchar(250)',
      'sdfpath' => 'account/pw_question',
      'editable' => false,
    ),
    'fav_tags' => 
    array (
      'type' => 'longtext',
      'editable' => false,
    ),
    'custom' => 
    array (
      'type' => 'longtext',
      'editable' => false,
    ),
    'cur' => 
    array (
      'sdfpath' => 'currency',
      'type' => 'varchar(20)',
      'label' => '货币',
      'width' => 110,
      'editable' => false,
      'in_list' => true,
    ),
    'lang' => 
    array (
      'type' => 'varchar(20)',
      'label' => '语言',
      'width' => 110,
      'editable' => false,
      'in_list' => true,
    ),
    'unreadmsg' => 
    array (
      'type' => 'smallint unsigned',
      'default' => 0,
      'required' => true,
      'label' => '未读信息',
      'width' => 110,
      'editable' => false,
      'filtertype' => 'number',
      'in_list' => true,
    ),
    'disabled' => 
    array (
      'type' => 'bool',
      'default' => 'false',
      'editable' => false,
    ),
    'remark' => 
    array (
      'label' => '备注',
      'type' => 'text',
      'width' => 75,
      'in_list' => true,
    ),
    'remark_type' => 
    array (
      'type' => 'varchar(2)',
      'default' => 'b1',
      'required' => true,
      'editable' => false,
    ),
    'login_count' => 
    array (
      'type' => 'int(11)',
      'default' => 0,
      'required' => true,
      'editable' => false,
    ),
    'experience' => 
    array (
      'label' => '经验值',
      'type' => 'int(10)',
      'default' => 0,
      'editable' => false,
      'in_list' => true,
    ),
    'foreign_id' => 
    array (
      'type' => 'varchar(255)',
    ),
    'member_refer' => 
    array (
      'type' => 'varchar(50)',
      'hidden' => true,
      'default' => 'local',
    ),
  ),
  'comment' => '商店会员表',
  'index' => 
  array (
    'ind_email' => 
    array (
      'columns' => 
      array (
        0 => 'email',
      ),
    ),
    'ind_regtime' => 
    array (
      'columns' => 
      array (
        0 => 'regtime',
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
  'version' => '$Rev: 42798 $',
);
