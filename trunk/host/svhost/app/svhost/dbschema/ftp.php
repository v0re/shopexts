<?php

$db['ftp']=array (
  'columns' =>
  array (
    'ftp_id' =>
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
    'ftp_name' =>
    array (
      'type' => 'enum(\'proftpd\',\'pureftpd\',\'vsftpd\')',
      'sdfpath' => 'ftp/name',
      'comment' => 'ftp服务器',
      'editable' => true,
      'label' => 'ftp服务器',
       'in_list' => true,
    ),
    'ftp_root' =>
    array (
      'type' => 'varchar(255)',
       'sdfpath' => 'ftp/root',
      'comment' => '根目录',
      'editable' => true,
      'label' => '根目录',
       'in_list' => true,
      'default_in_list' => true,
    ),
    'ftp_conf' =>
    array (
      'type' => 'varchar(255)',
       'sdfpath' => 'ftp/conf',
      'comment' => '配置文件目录',
      'editable' => true,
      'label' => '配置文件目录',
       'in_list' => true,
      'default_in_list' => true,
    ),
    'ftp_user' =>
    array (
      'type' => 'varchar(255)',
       'sdfpath' => 'ftp/user',
      'comment' => '运行用户',
      'editable' => true,
      'label' => '运行用户',
       'in_list' => true,
      'default_in_list' => true,
    ),
    'ftp_group' =>
    array (
      'type' => 'varchar(255)',
       'sdfpath' => 'ftp/group',
      'comment' => '运行组',
      'editable' => true,
      'label' => '运行组',
       'in_list' => true,
      'default_in_list' => true,
    ),
    'ftp_db' =>
    array (
      'type' => 'varchar(255)',
       'sdfpath' => 'ftp/db/name',
      'comment' => '帐号数据库',
      'editable' => true,
      'label' => '帐号数据库',
       'in_list' => true,
      'default_in_list' => false,
    ),
    'database_host' =>
    array (
      'type' => 'varchar(255)',
      'sdfpath' => 'ftp/db/host',
      'comment' => '服务地址',
      'editable' => true,
      'label' => '服务地址',
       'in_list' => true,
      'default_in_list' => false,
    ),
    'database_port' =>
    array (
      'type' => 'varchar(255)',
      'sdfpath' => 'ftp/db/port',
      'comment' => '服务端口',
      'editable' => true,
      'label' => '服务端口',
       'in_list' => true,
      'default_in_list' => false,
    ),
    'database_root' =>
    array (
      'type' => 'varchar(255)',
      'sdfpath' => 'ftp/db/user',
      'comment' => '管理员',
      'editable' => true,
      'label' => '管理员',
       'in_list' => true,
      'default_in_list' => false,
    ),
    'database_password' =>
    array (
      'type' => 'varchar(255)',
      'sdfpath' => 'ftp/db/password',
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
  'comment' => '服务器列表',
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
