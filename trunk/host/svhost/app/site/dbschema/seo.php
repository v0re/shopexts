<?php
$db['seo']=array (
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
        'act' => 
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
        'config' =>
        array (
            'type' => 'serialize',
            'default' => '',
            'label' => '配置',
        ),
        'param' =>
        array (
            'type' => 'serialize',
            'default' => '',
            'label' => '参数',
        ),
        'update_modified' => 
        array (
          'type' => 'time',
          'editable' => false,
        ),
    ),
);
