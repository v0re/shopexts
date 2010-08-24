<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['link']=array (
    'columns' =>
    array (
        'link_id' =>
        array (
            'type' => 'number',
            'required' => true,
            'pkey' => true,
            'extra' => 'auto_increment',
        ),
        'link_name' =>
        array (
            'type' => 'varchar(128)',
            'required' => true,
            'default' => '',
            'label'=>'链接名称',
            'width'=>100,
            'default_in_list'=>true,
            'in_list'=>true,
        ),
        'href' =>
        array (
            'type' => 'varchar(255)',
            'required' => true,
            'default' => '',
            'label'=>'链接地址',
            'width'=>180,
            'default_in_list'=>true,
            'in_list'=>true,
        ),
        'image_url' =>
        array (
            'type' => 'varchar(255)',
            'label'=>'图片地址',
            'width'=>120,
            'default_in_list'=>true,
            'in_list'=>true,
        ),
        'orderlist' =>
        array (
            'type' => 'number',
            'default' => 0,   
            'label'=>'排序',
            'required' => true,
            'default_in_list'=>true,
            'in_list'=>true,
        ),
        'hidden' =>
        array (
            'type' => array('true'=>'是', 'false'=>'否'),
            'label'=>'隐藏',
            'required' => true,
            'default' => 'false',
            'default_in_list'=>true,
            'in_list'=>true,
        ),
    ),
);
