<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['explorers']=array (
    'columns' =>
    array (
        'id' =>
        array (
            'type' => 'int unsigned',
            'required' => true,
            'pkey' => true,
            'extra' => 'auto_increment',
        ),
        'app' =>
        array (
            'type' => 'varchar(50)',
            'required' => true,
            'default' => '',
            'label' => '程序目录',
            'width'=>80,
            'default_in_list'=>true,
            'in_list'=>true,
        ),
        'title' =>
        array (
            'type' => 'varchar(100)',
            'required' => true,
            'default' => '',
            'label'=>'名称',
            'width'=>120,
            'default_in_list'=>true,
            'in_list'=>true,
        ),
        'path' =>
        array (
            'type' => 'varchar(100)',
            'required' => true,
            'default' => '',
            'label'=>'目录',
            'width'=>120,
            'default_in_list'=>true,
            'in_list'=>true,
        ),
    ),
);
