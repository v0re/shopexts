<?php

$db['http']=array (
  'columns' =>
  array (
    'http_id' =>
    array (
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'label' => 'httpd id',
      'width' => 150,
      'comment' => 'httpd id',
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
    'http_name' =>
    array (
      'type' => 'enum(\'nginx\',\'apache\',\'lighthttp\')',
      'sdfpath' => 'http/setting/name',
      'comment' => 'http服务器',
      'editable' => true,
      'label' => 'http服务器',
       'in_list' => true,
    ),
    'http_htdocs' =>
    array (
      'type' => 'varchar(255)',
      'sdfpath' => 'http/setting/htdocs',
      'comment' => '根目录',
      'editable' => true,
      'label' => '根目录',
       'in_list' => true,
      'default_in_list' => true,
    ),
    'http_conf' =>
    array (
      'type' => 'varchar(255)',
      'sdfpath' => 'http/setting/conf',
      'comment' => '配置文件目录',
      'editable' => true,
      'label' => '配置文件目录',
       'in_list' => true,
      'default_in_list' => true,
    ),
    'http_user' =>
    array (
      'type' => 'varchar(255)',
       'sdfpath' => 'http/setting/user',
      'comment' => '运行用户',
      'editable' => true,
      'label' => '运行用户',
       'in_list' => true,
      'default_in_list' => true,
    ),
    'http_group' =>
    array (
      'type' => 'varchar(255)',
       'sdfpath' => 'http/setting/group',
      'comment' => '运行组',
      'editable' => true,
      'label' => '运行组',
       'in_list' => true,
      'default_in_list' => true,
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
  'comment' => 'http服务列表',
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
