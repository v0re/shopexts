<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['modules']=array (
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
            'default' => '',
            'required' => true,
            'label' => '程序目录',
            'width'=>80,
            'default_in_list'=>true,
            'in_list'=>true,
        ),
        'ctl' =>
        array (
            'type' => 'varchar(50)',
            'default' => '',
            'required' => true,
            'label' => '控制器',
            'width'=>80,
            'default_in_list'=>true,
            'in_list'=>true,
        ),
        'path' => 
        array (
            'type' => 'varchar(50)',
            'default' => '',
            'required' => true,
            'label' => '路径标识',
            'width'=>80,
            'default_in_list'=>true,
            'in_list'=>true,
        ),
        'title' =>
        array (
            'type' => 'varchar(50)',
            'default' => '',
            'required' => true,
            'label' => '名称',
            'width' => 100,
            'default_in_list'=>true,
            'in_list'=>true,
        ),
        'allow_menus'=>
        array (
            'type' => 'varchar(255)',
            'default' => '',
            'required' => true,
            'label' => '允许菜单',
            'width' => 200,
            'default_in_list'=>true,
            'in_list'=>true,
        ),
        'is_native'=>
        array (
            'type' => 'bool',
            'required' => true,
            'default'=>'false',
            'label'=>'原生模块',
            'width'=>80,
            'default_in_list'=>true,
            'in_list'=>true,
        ),
        'enable' =>
        array (
            'type' => 'bool',
            'required' => true,
            'default'=>'false',
            'label'=>'启用',
            'width'=>80,
            'default_in_list'=>true,
            'in_list'=>true,
        ),
        'update_modified' => 
        array (
          'type' => 'time',
          'editable' => false,
        ),
    ),
);
