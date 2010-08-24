<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['apps']=array (
  'columns' => 
  array (
    'app_id' => 
    array (
      'type' => 'varchar(32)',
      'required' => true,
      'default' => '',
      'pkey' => true,
      'width' => 100,
      'label' => '程序目录',
      'hidden' => 1,
      'editable' => false,
      'in_list' => true,
      'default_in_list' => false,
    ),
    'app_name' => array ('type' => 'varchar(50)','width' => 150,'label' => '应用程序','is_title'=>1, 'in_list' => true,'default_in_list' => 1 ),
    'debug_mode' => array ('type' => 'bool','default' => 'false','width' => 100,'label' => '调试模式', 'in_list' => true,'default_in_list' => false ),
    'app_config' => array ('type' => 'text'),
    'status' =>  array (
      'label' => '状态',
      'width' => 100,
      'default' => 'uninstalled',
      'type' => 
      array (
        'installed' => '已安装, 未启动',
        'resolved' => '已配置',
        'starting' => '正在启动',
        'active' => '运行中',
        'stopping' => '正在关闭',
        'uninstalled' => '尚未安装',
        'broken' => '已损坏',
      ),
      'in_list' => true,
      'default_in_list' => true,
    ),
    'webpath'=>array('type'=>'varchar(20)'),
    'description'=>array('type'=>'varchar(255)','width' => 300,'label' => '说明','in_list' => true,'default_in_list' => 1),
    'local_ver'=>array('type'=>'varchar(20)','width' => 100,'label' => '当前版本','in_list' => true,'default_in_list' => 1),
    'remote_ver'=>array('type'=>'varchar(20)','width' => 100,'label' => '最新版本','in_list' => true,'default_in_list' => false),
    'author_name'=>array('type'=>'varchar(100)'),
    'author_url'=>array('type'=>'varchar(100)'),
    'author_email'=>array('type'=>'varchar(100)'),
    'dbver'=>array('type'=>'varchar(32)'),
    'remote_config'=>array('type'=>'serialize')
  ),
  'version' => '$Rev: 44008 $',
);
