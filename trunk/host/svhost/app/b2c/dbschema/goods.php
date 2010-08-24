<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['goods']=array (
  'columns' =>
  array (
    'goods_id' =>
    array (
      'type' => 'bigint unsigned',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'label' => 'ID',
      'width' => 110,
      'hidden' => true,
      'editable' => false,
      'in_list' => false,
    ),
    'cat_id' =>
    array (
      'type' => 'table:goods_cat',
      'required' => true,
      'sdfpath' => 'category/cat_id',
      'default' => 0,
      'label' => '分类',
      'width' => 75,
      'editable' => true,
      'filtertype' => 'yes',
      'filterdefault' => true,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'type_id' =>
    array (
      'type' => 'table:goods_type',
      'sdfpath' => 'type/type_id',
      'label' => '类型',
      'width' => 75,
      'editable' => false,
      'filtertype' => 'yes',
      'in_list' => true,
    ),
    'goods_type' =>
    array (
      'type' =>
      array (
        'normal' => '普通商品',
        'bind' => '捆绑商品',
        'gift' => '赠品',
      ),
      'sdfpath' => 'goods_type',
      'default' => 'normal',
      'required' => true,
      'label' => '销售类型',
      'width' => 110,
      'editable' => false,
      'hidden' => true,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'bn' =>
    array (
      'type' => 'varchar(200)',
      'label' => '商品编号',
      'width' => 110,
      'searchtype' => 'head',
      'editable' => true,
      'filtertype' => 'yes',
      'filterdefault' => true,
      'in_list' => true,
    ),
    'name' =>
    array (
      'type' => 'varchar(200)',
      'required' => true,
      'default' => '',
      'label' => '商品名称',
      'is_title' => true,
      'width' => 310,
      'searchtype' => 'has',
      'editable' => true,
      'filtertype' => 'custom',
      'filterdefault' => true,
      'filtercustom' =>
      array (
        'has' => '包含',
        'tequal' => '等于',
        'head' => '开头等于',
        'foot' => '结尾等于',
      ),
      'in_list' => true,
      'default_in_list' => true,
    ),
    'price' =>
    array (
      'type' => 'money',
      'sdfpath' => 'product[default]/price/price/price',
      'default' => '0',
      'required' => true,
      'label' => '销售价',
      'width' => 75,
      'editable' => false,
      'filtertype' => 'number',
      'filterdefault' => true,
      'in_list' => true,
    ),
    'mktprice' =>
    array (
      'type' => 'money',
      'sdfpath' => 'product[default]/price/mktprice/price',
      'label' => '市场价',
      'width' => 75,
      'required' => true,
      'editable' => false,
      'filtertype' => 'number',
      'default' => '0',
      'in_list' => true,
    ),
    'cost' =>
    array (
      'type' => 'money',
      'sdfpath' => 'product[default]/price/cost/price',
      'default' => '0',
      'required' => true,
      'label' => '成本价',
      'width' => 75,
      'editable' => false,
      'filtertype' => 'number',
      'in_list' => true,
    ),
    'brand_id' =>
    array (
      'type' => 'table:brand',
      'sdfpath' => 'brand/brand_id',
      'label' => '品牌',
      'width' => 75,
      'editable' => true,
      'hidden' => true,
      'filtertype' => 'yes',
      'filterdefault' => true,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'image_default_id' =>
    array (
      'type' => 'varchar(32)',
      'label' => '默认图片',
      'width' => 75,
      'hidden' => true,
      'editable' => false,
      'in_list' => true,
    ),
    'udfimg' =>
    array (
      'type' => 'bool',
      'default' => 'false',
      'label' => '是否用户自定义图',
      'width' => 110,
      'hidden' => true,
      'editable' => false,
      'in_list' => true,
    ),
    'thumbnail_pic' =>
    array (
      'type' => 'varchar(32)',
      'label' => '缩略图',
      'width' => 110,
      'hidden' => true,
      'editable' => false,
      'in_list' => true,
    ),
    'small_pic' =>
    array (
      'type' => 'varchar(255)',
      'editable' => false,
    ),
    'big_pic' =>
    array (
      'type' => 'varchar(255)',
      'editable' => false,
    ),
    'brief' =>
    array (
      'type' => 'varchar(255)',
      'label' => '商品简介',
      'width' => 110,
      'hidden' => false,
      'editable' => false,
      'filtertype' => 'normal',
      'in_list' => true,
    ),
    'intro' =>
    array (
      'type' => 'longtext',
      'sdfpath' => 'description',
      'label' => '详细介绍',
      'width' => 110,
      'hidden' => true,
      'editable' => false,
      'filtertype' => 'normal',
      'in_list' => true,
    ),
    'marketable' =>
    array (
      'type' => 'bool',
      'default' => 'true',
      'sdfpath' => 'status',
      'required' => true,
      'label' => '上架',
      'width' => 30,
      'editable' => true,
      'filtertype' => 'yes',
      'filterdefault' => true,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'weight' =>
    array (
      'type' => 'decimal(20,3)',
      'sdfpath' => 'product[default]/weight',
      'label' => '重量',
      'width' => 75,
      'editable' => false,
      'in_list' => true,
    ),
    'unit' =>
    array (
      'type' => 'varchar(20)',
      'sdfpath' => 'unit',
      'label' => '单位',
      'width' => 30,
      'editable' => false,
      'filtertype' => 'normal',
      'in_list' => true,
    ),
    'store' =>
    array (
      'type' => 'decimal(20,2)',
      'label' => '库存',
      'width' => 30,
      'editable' => false,
      'filtertype' => 'number',
      'filterdefault' => true,
      'in_list' => true,
    ),
    'store_place' =>
    array (
      'type' => 'varchar(255)',
      'label' => __('库位'),
      'sdfpath' => 'product[default]/store_place',
      'width' => 30,
      'editable' => false,
      'hidden'=>true,
    ),
    'min_buy' =>
    array (
      'type' => 'number',
      'label' => '起定量',
      'width' => 30,
      'editable' => false,
    ),
   'package_scale' =>
    array (
      'type' => 'decimal(20,2)',
      'label' => '打包比例',
      'width' => 30,
      'editable' => false,
    ),
   'package_unit' =>
    array (
      'type' => 'varchar(20)',
      'label' => '打包单位',
      'width' => 30,
      'editable' => false,
    ),
    'package_use' =>
    array (
      'type' => 'intbool',
      'label' => '是否开启打包',
      'width' => 30,
      'editable' => false,
    ),
    'score_setting' =>
    array (
      'type' =>
      array (
        'percent' => '百分比',
        'number' => '实际值',
      ),
      'default' => 'number',
      'editable' => false,
    ),
    'nostore_sell' =>
    array (
      'type' => 'intbool',
      'label' => '是否开启无库存销售',
      'width' => 30,
      'editable' => false,
    ),
    'score' =>
    array (
      'type' => 'number',
      'sdfpath' => 'gain_score',
      'label' => '积分',
      'width' => 30,
      'editable' => false,
      'in_list' => true,
    ),
    'spec_desc' =>
    array (
      'type' => 'serialize',
      'label' => '物品',
      'width' => 110,
      'hidden' => true,
      'editable' => false,
//      'in_list' => true,
    ),
    'params' =>
    array (
      'type' => 'serialize',
      'editable' => false,
    ),
    'uptime' =>
    array (
      'type' => 'time',
      'depend_col' => 'marketable:true:now',
      'label' => '上架时间',
      'width' => 110,
      'editable' => false,
      'in_list' => true,
    ),
    'downtime' =>
    array (
      'type' => 'time',
      'depend_col' => 'marketable:false:now',
      'label' => '下架时间',
      'width' => 110,
      'editable' => false,
      'in_list' => true,
    ),
    'last_modify' =>
    array (
      'type' => 'last_modify',
      'label' => '更新时间',
      'width' => 110,
      'editable' => false,
      'in_list' => true,
    ),
    'disabled' =>
    array (
      'type' => 'bool',
      'default' => 'false',
      'required' => true,
      'editable' => false,
    ),
    'notify_num' =>
    array (
      'type' => 'number',
      'default' => 0,
      'required' => true,
      'label' => '缺货登记',
      'width' => 110,
      'editable' => false,
      'in_list' => true,
    ),
    'rank' =>
    array (
      'type' => 'decimal(5,3)',
      'default' => '5',
      'editable' => false,
    ),
    'rank_count' =>
    array (
      'type' => 'int unsigned',
      'default' => 0,
      'editable' => false,
    ),
    'comments_count' =>
    array (
      'type' => 'int unsigned',
      'default' => 0,
      'required' => true,
      'editable' => false,
    ),
    'view_w_count' =>
    array (
      'type' => 'int unsigned',
      'default' => 0,
      'required' => true,
      'editable' => false,
    ),
    'view_count' =>
    array (
      'type' => 'int unsigned',
      'default' => 0,
      'required' => true,
      'editable' => false,
    ),
    'buy_count' =>
    array (
      'type' => 'int unsigned',
      'default' => 0,
      'required' => true,
      'editable' => false,
    ),
    'buy_w_count' =>
    array (
      'type' => 'int unsigned',
      'default' => 0,
      'required' => true,
      'editable' => false,
    ),
    'count_stat' =>
    array (
      'type' => 'longtext',
      'editable' => false,
    ),
    'p_order' =>
    array (
      'type' => 'number',
      'default' => 30,
      'required' => true,
      'label' => '排序',
      'width' => 110,
      'editable' => false,
      'hidden' => true,
      'in_list' => false,
    ),
    'd_order' =>
    array (
      'type' => 'number',
      'default' => 30,
      'required' => true,
      'label' => '排序',
      'width' => 30,
      'editable' => true,
      'in_list' => true,
    ),
    'p_1' =>
    array (
      'type' => 'number',
      'sdfpath' => 'props/p_1/value',
      'editable' => false,
    ),
    'p_2' =>
    array (
      'type' => 'number',
      'sdfpath' => 'props/p_2/value',
      'editable' => false,
    ),
    'p_3' =>
    array (
      'type' => 'number',
      'sdfpath' => 'props/p_3/value',
      'editable' => false,
    ),
    'p_4' =>
    array (
      'type' => 'number',
      'sdfpath' => 'props/p_4/value',
      'editable' => false,
    ),
    'p_5' =>
    array (
      'type' => 'number',
      'sdfpath' => 'props/p_5/value',
      'editable' => false,
    ),
    'p_6' =>
    array (
      'type' => 'number',
      'sdfpath' => 'props/p_6/value',
      'editable' => false,
    ),
    'p_7' =>
    array (
      'type' => 'number',
      'sdfpath' => 'props/p_7/value',
      'editable' => false,
    ),
    'p_8' =>
    array (
      'type' => 'number',
      'sdfpath' => 'props/p_8/value',
      'editable' => false,
    ),
    'p_9' =>
    array (
      'type' => 'number',
      'sdfpath' => 'props/p_9/value',
      'editable' => false,
    ),
    'p_10' =>
    array (
      'type' => 'number',
      'sdfpath' => 'props/p_10/value',
      'editable' => false,
    ),
    'p_11' =>
    array (
      'type' => 'number',
      'sdfpath' => 'props/p_11/value',
      'editable' => false,
    ),
    'p_12' =>
    array (
      'type' => 'number',
      'sdfpath' => 'props/p_12/value',
      'editable' => false,
    ),
    'p_13' =>
    array (
      'type' => 'number',
      'sdfpath' => 'props/p_13/value',
      'editable' => false,
    ),
    'p_14' =>
    array (
      'type' => 'number',
      'sdfpath' => 'props/p_14/value',
      'editable' => false,
    ),
    'p_15' =>
    array (
      'type' => 'number',
      'sdfpath' => 'props/p_15/value',
      'editable' => false,
    ),
    'p_16' =>
    array (
      'type' => 'number',
      'sdfpath' => 'props/p_16/value',
      'editable' => false,
    ),
    'p_17' =>
    array (
      'type' => 'number',
      'sdfpath' => 'props/p_17/value',
      'editable' => false,
    ),
    'p_18' =>
    array (
      'type' => 'number',
      'sdfpath' => 'props/p_18/value',
      'editable' => false,
    ),
    'p_19' =>
    array (
      'type' => 'number',
      'sdfpath' => 'props/p_19/value',
      'editable' => false,
    ),
    'p_20' =>
    array (
      'type' => 'number',
      'sdfpath' => 'props/p_20/value',
      'editable' => false,
    ),
    'p_21' =>
    array (
      'type' => 'varchar(255)',
      'sdfpath' => 'props/p_21/value',
      'editable' => false,
    ),
    'p_22' =>
    array (
      'type' => 'varchar(255)',
      'sdfpath' => 'props/p_22/value',
      'editable' => false,
    ),
    'p_23' =>
    array (
      'type' => 'varchar(255)',
      'sdfpath' => 'props/p_23/value',
      'editable' => false,
    ),
    'p_24' =>
    array (
      'type' => 'varchar(255)',
      'sdfpath' => 'props/p_24/value',
      'editable' => false,
    ),
    'p_25' =>
    array (
      'type' => 'varchar(255)',
      'sdfpath' => 'props/p_25/value',
      'editable' => false,
    ),
    'p_26' =>
    array (
      'type' => 'varchar(255)',
      'sdfpath' => 'props/p_26/value',
      'editable' => false,
    ),
    'p_27' =>
    array (
      'type' => 'varchar(255)',
      'sdfpath' => 'props/p_27/value',
      'editable' => false,
    ),
    'p_28' =>
    array (
      'type' => 'varchar(255)',
      'sdfpath' => 'props/p_28/value',
      'editable' => false,
    ),
  ),
  'comment' => '商品表',
  'index' =>
  array (
    'uni_bn' =>
    array (
      'columns' =>
      array (
        0 => 'bn',
      ),
      'prefix' => 'UNIQUE',
    ),
    'ind_p_1' =>
    array (
      'columns' =>
      array (
        0 => 'p_1',
      ),
    ),
    'ind_p_2' =>
    array (
      'columns' =>
      array (
        0 => 'p_2',
      ),
    ),
    'ind_p_3' =>
    array (
      'columns' =>
      array (
        0 => 'p_3',
      ),
    ),
    'ind_p_4' =>
    array (
      'columns' =>
      array (
        0 => 'p_4',
      ),
    ),
    'ind_p_23' =>
    array (
      'columns' =>
      array (
        0 => 'p_23',
      ),
    ),
    'ind_p_22' =>
    array (
      'columns' =>
      array (
        0 => 'p_22',
      ),
    ),
    'ind_p_21' =>
    array (
      'columns' =>
      array (
        0 => 'p_21',
      ),
    ),
    'ind_frontend' =>
    array (
      'columns' =>
      array (
        0 => 'disabled',
        1 => 'goods_type',
        2 => 'marketable',
      ),
    ),
  ),
  'engine' => 'innodb',
  'version' => '$Rev: 44513 $',
);
