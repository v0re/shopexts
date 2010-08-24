<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
/**
 * @table regions;
 *
 * @package Schemas
 * @version $
 * @copyright 2003-2009 ShopEx
 * @license Commercial
 */

$db['regions']=array (
    'columns' =>
    array (
        'region_id' =>
        array (
            'type' => 'int unsigned',
            'required' => true,
            'pkey' => true,
            'extra' => 'auto_increment',
            'editable' => false,
        ),
        'local_name' =>
        array (
            'type' => 'varchar(50)',
            'required' => true,
            'default' => '',
            'label'=>'当地名称',
            'width'=>100,
            'default_in_list'=>true,
            'in_list'=>true,
            'editable' => false,
        ),
        'package' =>
        array (
            'type' => 'varchar(20)',
            'required' => true,
            'default' => '',
            'label'=>'数据包',
            'width'=>100,
            'default_in_list'=>true,
            'in_list'=>true,
            'editable' => false,
        ),
        'p_region_id' =>
        array (
            'type' => 'int unsigned',
            'editable' => false,
        ),
        'region_path' =>
        array (
            'type' => 'varchar(255)',
            'width'=>300,
            'editable' => false,
        ),
        'region_grade' =>
        array (
            'type' => 'number',
            'editable' => false,
        ),
        'p_1' =>
        array (
            'type' => 'varchar(50)',
            'editable' => false,
        ),
        'p_2' =>
        array (
            'type' => 'varchar(50)',
            'editable' => false,
        ),
        'ordernum' =>
        array (
            'type' => 'number',
            'editable' => true,
        ),
        'disabled' =>
        array (
            'type' => 'bool',
            'default' => 'false',
            'editable' => false,
        ),
    ),
);
