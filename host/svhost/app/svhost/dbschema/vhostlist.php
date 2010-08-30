<?php

$db['vhostlist']=array (
  'columns' =>
  array (
    'vhost_id' =>
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
      'default' => 0,
      'required' => true,
      'editable' => false,
    ),
    
    'domain' =>
    array (
      'type' => 'varchar(50)',
      'sdfpath'=>'domain',
      'label' => '域名',
      'width' => 180,
      'is_title' => true,
      'required' => true,
      'comment' => '域名',
      'editable' => true,
      'searchtype' => 'has',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'ip' =>
    array (
      'type' => 'varchar(255)',
      'sdfpath'=>'ip',
      'label' => 'IP',
      'width' => 100,
      'comment' => 'IP',
      'editable' => true,
      'searchtype' => 'has',
      'in_list' => true,
      'default_in_list' => true,
    ),

    'dbhost' =>
    array (
      'type' => 'varchar(255)',
      'width' => 100,
      'sdfpath'=>'db/host',
      'comment' => '数据库地址',
      'editable' => false,
      'label' => '数据库地址',
       'in_list' => true,
      'default_in_list' => true,
    ),
    'dbport' =>
    array (
      'type' => 'varchar(255)',
        'width' => 80,
      'sdfpath'=>'db/port',
      'comment' => '数据库端口',
      'editable' => true,
      'label' => '数据库端口',
       'in_list' => true,
      'default_in_list' => true,
    ),
    'dbname' =>
    array (
      'type' => 'varchar(255)',
      'sdfpath'=>'db/name',
      'comment' => '数据库名',
      'editable' => false,
      'label' => '数据库名',
       'in_list' => true,
      'default_in_list' => true,
    ),
    'dbuser' =>
    array (
      'type' => 'varchar(255)',
      'sdfpath'=>'db/user',
      'comment' => '数据库用户',
      'editable' => false,
      'label' => '数据库用户',
       'in_list' => true,
      'default_in_list' => true,
    ),
    'dbpassword' =>
    array (
      'type' => 'varchar(255)',
      'sdfpath'=>'db/password',
      'comment' => '数据库密码',
      'editable' => true,
      'label' => '数据库密码',
       'in_list' => true,
      'default_in_list' => false,
    ),
    'ftpuser' =>
    array (
      'type' => 'varchar(255)',
      'sdfpath'=>'ftp/user',
      'comment' => 'FTP用户',
      'editable' => true,
      'label' => 'FTP用户',
       'in_list' => true,
      'default_in_list' => true,
    ),
    'ftppassword' =>
    array (
      'type' => 'varchar(255)',
      'sdfpath'=>'ftp/password',
      'comment' => 'FTP密码',
      'editable' => true,
      'label' => 'FTP密码',
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
      'sdfpath'=>'ordernum',
      'label' => '排序',
      'width' => 150,
      'comment' => '排序',
      'editable' => true,
      'in_list' => true,
    ),
  ),
  'comment' => '虚拟空间列表',
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
