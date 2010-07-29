<?php
$db['app_content']=array (
  'columns' => 
  array (
    'content_id'=>array(
      'type' => 'number',
      'pkey' => true,
      'extra' => 'auto_increment',
    ),
    'content_type' => 
    array (
      'type' => 'varchar(80)',
      'required' => true,
      'width' => 100,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'app_id' => 
    array (
      'type' => 'table:apps',
      'required' => true,
      'width' => 100,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'content_name'=>array(
        'type'=>'varchar(80)',
    ),
    'content_title'=>array(
        'type'=>'varchar(100)',
        'is_title'=>true,
    ),
    'content_path'=>array(
        'type'=>'varchar(255)',
    ),
    'disabled'=>array(
        'type'=>'bool',
        'default'=>'false'
    )
  ),
  'version' => '$Rev: 44008 $',
);
