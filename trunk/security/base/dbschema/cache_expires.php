<?php
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
