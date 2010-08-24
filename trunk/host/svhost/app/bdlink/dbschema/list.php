<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['list']=array (
 'columns' => array (
    'id' =>
         array (
          'type' => 'bigint unsigned',
          'required' => true,
          'pkey' => true,
          'extra' => 'auto_increment',
          'label' => 'ID',
          'width' => 110,
          'hidden' => true,
          'editable' => false,
          'in_list' => false,
        ),
    'generatecode' =>
        array (
          'type' => 'varchar(255)',
          'label' => __('投放链接'),
          'width' => 280,
          'in_list' => true,
          'default_in_list' => true,
        ),
    'time' =>
        array (
          'type' => 'time',
          'label' => __('创建时间'),
          'width' => 140,
          'in_list' => true,
          'default_in_list' => true,
        ),
    'user_id' =>
        array (
          'type' => 'number',
          'label' => __('创建用户id'),
          'width' => 75,
        ),
    'validtime' =>
        array (
          'type' => 'time',
          'label' => __('有效期'),
          'width' => 150,
        ),
   ),
  'version' => '$Rev: 41137 $',
  'engine' => 'innodb',
);