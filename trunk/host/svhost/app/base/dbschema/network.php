<?php
$db['network']=array (
  'columns' => 
  array (
    'node_id' => array (
      'type' => 'number',
      'label' => 'id',
      'required' => true,
      'width' => 100,
      'in_list' => true,
      'default_in_list' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
    ),
    'node_name' => 
    array (
      'type' => 'varchar(255)',
      'label' => '名称',
      'required' => true,
      'width' => 150,
      'in_list' => true,
      'default_in_list' => true,
      'is_title' => true,
    ),
    'node_url' => 
    array (
      'type' => 'varchar(100)',
      'label' => '网址',
      'width' => 150,
      'required' => true,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'node_api' => 
    array (
      'type' => 'varchar(100)',
      'label' => 'api地址',
      'width' => 150,
      'required' => true,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'link_status' => 
    array (
      'type' => 
      array (
        'active' => '正常',
        'group' => '维护',
        'wait' => '等待对方确认...',
      ),
      'default' => 'wait',
      'width' => 100,
      'label' => '关联类型',
      'required' => true,
      'in_list' => true,
    ),
    'node_detail' => 
    array (
      'type' => 'varchar(255)',
      'label' => '说明',
      'width' => 300,
    ),
    'token' => 
    array (
      'type' => 'varchar(32)',
      'label' => '验证玛',
    ),
  ),
  'version' => '$Rev: 41137 $',
);
