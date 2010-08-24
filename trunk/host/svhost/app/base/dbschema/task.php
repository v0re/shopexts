<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['task']=array (
  'columns' => 
  array (
    'task' => array('type'=>'varchar(100)','pkey'=>true),
    'minute' => array('type'=>'time'),
    'hour' => array('type'=>'time'),
    'day' => array('type'=>'time'),
    'week' => array('type'=>'time'),
    'month' => array('type'=>'time'),
  ),
  'version' => '$Rev: 41137 $',
);
