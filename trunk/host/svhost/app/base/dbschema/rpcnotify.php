<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['rpcnotify']=array (
    'columns' => 
    array (
    'id' => 
    array (
      'type' => 'bigint unsigned',
      'extra' => 'auto_increment',
      'pkey' => true,
      'label' => 'ID',
      'required' => true,
      'editable' => false,
      'in_list' => true,
      'default_in_list' => true,
      'width'=>40,
    ),
    'callback' => 
    array (
      'type' => 'varchar(200)',
      'label'=>'API',
      'required' => true,
      'in_list'=>true,
      'width'=>100,
    ),
    'rsp' =>
    array(
      'type' => array(
            'succ' => '成功',
            'fail' => '失败',
      ),
      'label' => '状态',
      'required' => true,
      'width' => 100,
      'in_list' =>true,
      'default_in_list' => true,
    ),
    'msg'=>array(
      'type' => 'varchar(255)',
      'required' => true,
      'width'=>200,
      'label' => '信息',
      'in_list' =>true,
      'default_in_list' => true,
    ),
    'notifytime' => 
    array (
      'type' => 'time',
      'label' => '通知时间',
      'required' => true,
      'width' => 140,
      'in_list'=> true,
      'default_in_list'=> true,
    ),
    ),
    'engine' => 'innodb',
    'version' => '$Rev: 40912 $',
);

