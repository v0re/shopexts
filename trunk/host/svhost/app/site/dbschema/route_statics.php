<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['route_statics']=array (
    'columns' =>
    array (
        'id' =>
        array (
            'type' => 'int unsigned',
            'required' => true,
            'pkey' => true,
            'extra' => 'auto_increment',
        ),
        'static' =>
        array (
            'type' => 'varchar(255)',
            'required' => true,
            'label'=>'静态规则',
            'width'=>300,
            'default_in_list'=>true,
            'in_list'=>true,
            'searchtype' => 'has',
        ),
        'url' =>
        array (
            'type' => 'varchar(255)',
            'required' => true,
            'label' => '目标链接',
            'width'=>300,
            'default_in_list'=>true,
            'in_list'=>true,
            'searchtype' => 'has',
        ),
        'enable' =>
        array (
            'type' => 'bool',
            'required' => true,
            'default'=>'true',
            'label'=>'启用',
            'width'=>80,
            'default_in_list'=>true,
            'in_list'=>true,
        ),
    ),
);

