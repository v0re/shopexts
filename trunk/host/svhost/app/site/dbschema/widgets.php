<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

$db['widgets'] = array(
    'columns' => array(
        'id' => array(
            'type' => 'int unsigned',
            'required' => true,
            'pkey' => true,
            'extra' => 'auto_increment',
            'editable' => false,
        ),
        'app' => array (
            'type' => 'varchar(20)',
            'required' => true,
            'default' => '',
            'editable' => false,
        ),
        'name' => array (
            'type' => 'varchar(20)',
            'required' => true,
            'default' => '',
            'editable' => false,
        )
    ),
);
