<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['link']=array (
 'columns' => array (
    'refer_id' =>
        array (
          'type' => 'varchar(50)',
          'label' => __('首次来源ID'),
          'width' => 75,
          'pkey' => true,
          'in_list' => true,
        ),
    'refer_url' =>
        array (
          'type' => 'varchar(200)',
          'label' => __('首次来源URL'),
          'width' => 150,
          'in_list' => true,
        ),
    'refer_time' =>
        array (
          'type' => 'time',
          'label' => __('首次来源时间'),
          'width' => 110,
          'in_list' => true,
        ),
    'c_refer_id' =>
        array (
          'type' => 'varchar(50)',
          'label' => __('本次来源ID'),
          'width' => 75,
          'in_list' => true,
        ),
    'c_refer_url' =>
        array (
          'type' => 'varchar(200)',
          'label' => __('本次来源URL'),
          'width' => 150,
          'in_list' => true,
        ),
    'c_refer_time' =>
        array (
          'type' => 'time',
          'label' => __('本次来源时间'),
          'width' => 110,
          'in_list' => true,
        ),
    'target_id' => 
        array(
          'type' => 'varchar(32)',
          'required' => true,
          'width' => 100,
          'pkey' => true,
        ),
    'target_type' => 
        array(
          'type' => 'varchar(50)',
          'required' => true,
          'label' => __('类型'),
          'width' => 100,
          'pkey' => true,
          'in_list' => true,
          'default_in_list' => true,
        ),
   ),
  'version' => '$Rev: 41137 $',
  'engine' => 'innodb',
);