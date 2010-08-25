<?php

$db['database']=array (
  'columns' =>
  array (
    'database_id' =>
    array (
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'label' => '服务器ID',
      'width' => 150,
      'comment' => '服务器ID',
      'editable' => false,
      'in_list' => false,
      'default_in_list' => false,
    ),
    'server_id' =>
    array (
      'type' => 'table:serverlist',
      'required' => true,
      'label' => '服务器ID',
      'width' => 150,
      'comment' => '服务器ID',
      'editable' => false,
      'in_list' => false,
      'default_in_list' => false,
    ),
    'database_name' =>
    array (
      'type' => 'enum(\'mysql\',\'pgsql\',\'sqlite\')',
      'sdfpath' => 'database/name',
      'comment' => 'database服务器',
      'editable' => true,
      'label' => 'database服务器',
       'in_list' => true,
    ),
    'database_datadir' =>
    array (
      'type' => 'varchar(255)',
      'sdfpath' => 'database/datadir',
      'comment' => '数据存放目录',
      'editable' => true,
      'label' => '数据存放目录',
       'in_list' => true,
      'default_in_list' => true,
    ),
    'database_conf' =>
    array (
      'type' => 'varchar(255)',
      'sdfpath' => 'database/conf',
      'comment' => '配置文件目录',
      'editable' => true,
      'label' => '配置文件目录',
       'in_list' => true,
      'default_in_list' => true,
    ),
    'database_user' =>
    array (
      'type' => 'varchar(255)',
      'sdfpath' => 'database/user',
      'comment' => '运行用户',
      'editable' => true,
      'label' => '运行用户',
       'in_list' => true,
      'default_in_list' => true,
    ),
    'database_group' =>
    array (
      'type' => 'varchar(255)',
      'sdfpath' => 'database/group',
      'comment' => '运行组',
      'editable' => true,
      'label' => '运行组',
       'in_list' => true,
      'default_in_list' => true,
    ),
    'database_host' =>
    array (
      'type' => 'varchar(255)',
      'sdfpath' => 'database/host',
      'comment' => '服务地址',
      'editable' => true,
      'label' => '服务地址',
       'in_list' => true,
      'default_in_list' => false,
    ),
    'database_port' =>
    array (
      'type' => 'varchar(255)',
      'sdfpath' => 'database/port',
      'comment' => '服务端口',
      'editable' => true,
      'label' => '服务端口',
       'in_list' => true,
      'default_in_list' => false,
    ),
    'database_root' =>
    array (
      'type' => 'varchar(255)',
      'sdfpath' => 'database/root',
      'comment' => '管理员',
      'editable' => true,
      'label' => '管理员',
       'in_list' => true,
      'default_in_list' => false,
    ),
    'database_password' =>
    array (
      'type' => 'varchar(255)',
      'sdfpath' => 'database/password',
      'comment' => '管理员密码',
      'editable' => true,
      'label' => '管理员密码',
       'in_list' => true,
      'default_in_list' => false,
    ),
    'disabled' =>
    array (
      'type' => 'bool',
      'default' => 'false',
      'comment' => '失效',
      'editable' => false,
      'label' => '失效',
      'in_list' => false,
      'deny_export' => true,
    ),
    'ordernum' =>
    array (
      'type' => 'number',
      'label' => '排序',
      'width' => 150,
      'comment' => '排序',
      'editable' => true,
      'in_list' => true,
    ),
  ),
  'comment' => '数据服务列表',
  'index' =>
  array (
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
  'version' => '$Rev: 40654 $',
);
