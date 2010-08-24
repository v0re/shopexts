<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['member_systmpl']=array (
  'columns' => 
  array (
    'tmpl_name' => array (
       'type' => 'varchar(50)',
        'pkey' => true,
      'required' => true,
    ),
    'content' => array(
        'type'=>'longtext',
        'label' =>'内容',
        'default' => 0,
    ),
    'edittime' => array (
      'type' => 'int(10) ',
      'required' => true,
    ),
    'active' => array(
        'type'=>"enum('true', 'false')",
        'default' => 'true',      
    ),
   
  ),   
  'comment' => '信息表',
   'engine' => 'innodb',
   'version' => '$Rev$',
);
