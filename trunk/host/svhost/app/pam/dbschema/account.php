<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['account'] = array(
    'columns'=>array(
        'account_id'=>array('type'=>'number','pkey'=>true,'extra' => 'auto_increment',),
        'account_type'=>array('type'=>'varchar(30)'),
        'login_name'=>array('type'=>'varchar(100)','is_title'=>true, ),
        'login_password'=>array('type'=>'varchar(32)'),
        'disabled'=>array('type'=>'bool','default'=>'false'),
        'createtime'=>array('type'=>'time'),
    ),
  'index' => array (
    'account' => array ('columns' => array ('account_type','login_name'),'prefix' => 'UNIQUE'),
  ),
);
