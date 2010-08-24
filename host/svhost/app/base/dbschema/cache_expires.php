<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['cache_expires']=array (
  'columns' => 
  array (
    'type' => array(
        'type' => 'varchar(20)',
        'pkey' => true,
        'required' => true,
    ),
    'name' => array(
        'type'=>'varchar(255)',
        'pkey' => true,
        'required'=>true,
    ),
    'expire' => array(
        'type'=>'time',
        'required' => true,
    ),
  ),
  'engine' => 'innodb',
  'version' => '$Rev: 41137 $',
  'ignore_cache' => true,
);
