<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['rpcpoll']=array (
  'columns' => array (
        'id'=>array('type'=>'varchar(32)'),
        'process_id'=>array('type'=>'varchar(32)'),
        'type'=>array('type'=>array(
                    'request'=>'call in',
                    'response'=>'call out',
                )),
        'calltime'=>array('type'=>'time'),
        'network'=>array('type'=>'table:network'),
        'method'=>array('type'=>'varchar(100)'),
        'params'=>array('type'=>'text'),
        'callback'=>array('type'=>'varchar(200)'),
        'callback_params'=>array('type'=>'text'),
        'result'=>array('type'=>'text'),
    ),
  'engine' => 'innodb',
  'version' => '$Rev: 40912 $',
);
