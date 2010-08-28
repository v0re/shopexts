<?php

$db['serverlist']=array (
  'columns' =>
  array (
    'server_id' =>
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
    'name' =>
    array (
      'type' => 'varchar(50)',
      'sdfpath'=>'server/name',
      'label' => '服务器名',
      'width' => 180,
      'is_title' => true,
      'required' => true,
      'comment' => '服务器名',
      'editable' => true,
      'searchtype' => 'has',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'ip' =>
    array (
      'type' => 'varchar(255)',
      'sdfpath'=>'server/ip',
      'label' => 'IP',
      'width' => 350,
      'comment' => 'IP',
      'editable' => true,
      'searchtype' => 'has',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'farm' =>
    array (
      'type' => 'varchar(255)',
      'sdfpath'=>'server/farm',
      'comment' => '所处机房',
      'editable' => true,
      'label' => '所处机房',
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
      'sdfpath'=>'server/ordernum',
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
