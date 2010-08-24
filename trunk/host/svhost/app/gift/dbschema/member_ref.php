<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['member_ref']=array (
  'columns' => 
  array (
    'goods_id' => 
    array (
      'type' => 'table:goods@b2c',
      'required' => true,
      'pkey' =>  true, 
      'label' => '赠品ID',
      'width' => 110,
      'editable' => false,
    ),
   'member_lv_ids' => 
    array (
      'type' => 'varchar(255)',
      'label' => '会员等级ID',
      'width' => 110,
      'editable' => false,
      'default' => '',
    ),
    
    'ifrecommend' => 
    array (
      'type' => 'bool',
      'default' => 'false',
      'label' => '是否作为推荐赠品',
      'width' => 110,
      'editable' => false,
    ),
    
  ),
  'index' =>
  array (
    'index_gift' =>
    array (
      'columns' =>
      array (
        0 => 'goods_id',
      ),
    ),
   'index_lv' =>
    array (
      'columns' =>
      array (
        0 => 'goods_id',
      ),
    ),
   'index_status' =>
    array (
      'columns' =>
      array (
        0 => 'ifrecommend',
      ),
    ),
  ),
  'comment' => '赠品关联表',
  'version' => '$Rev: 41329 $',
);
