<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
//商品促销规则表
$db['sales_rule_goods'] = array(
    'columns' =>
    array (
        'rule_id' =>
        array (
            'type' => 'int(8)',
            'required' => true,
            'pkey' => true,
            'label' => '规则id',
            'editable' => false,
            'extra' => 'auto_increment',
            ),
        'name' =>
        array (
            'type' => 'varchar(255)',
            'required' => true,
            'default' => '',
            'label' => '规则名称',
            'editable' => true,
            'in_list' => true,
            'default_in_list' => true,
            'filterdefault'=>true,
            'is_title' => true,
            ),
        'description' =>
        array (
            'type' => 'text',
            'label' => '规则描述',
            'required' => false,
            'default' => '',
            'editable' => false,
            'in_list' => true,
            'filterdefault'=>true,
            ),
        'create_time' =>
        array (
            'type' => 'time',
            'label' => '修改时间',
            'editable' => true,
            'in_list' => true,
            'default_in_list' => false,
            'filterdefault'=>true,
            ),
        'from_time' =>
        array (
            'type' => 'time',
            'label' => '起始时间',
            'default'=> 0,
            'editable' => true,
            'in_list' => true,
            'default_in_list' => true,
            'filterdefault'=>true,
            ),
        'to_time' =>
        array (
            'type' => 'time',
            'label' => '截止时间',
            'default'=> 0,
            'editable' => true,
            'in_list' => true,
            'default_in_list' => true,
            'filterdefault'=>true,
            ),
        'member_lv_ids' =>
        array (
            'type' => 'varchar(255)',
            'default' => '',
            'required' => false,
            'label' => '会员级别集合',
            'editable' => false,
            ),
            //status 标志是否使用该规则执行预过滤
        'status' =>
        array (
            'type' => 'bool',
            'default' => 'false',
            'required' => true,
            'label' => '开启状态',
            'in_list' => true,
            'editable' => false,
            'filterdefault'=>true,
            ),
        'conditions' =>
        array (
            'type' => 'serialize',
            'default' => '',
            'required' => true,
            'label' => '规则条件',
            'editable' => false,
            ),
        'stop_rules_processing' =>
        array (
            'type' => 'bool',
            'default' => 'false',
            'required' => true,
            'label' => '排斥其他规则',
            'in_list' => true,
            'editable' => true,
            'filterdefault'=>true,
            ),
        'sort_order' =>
        array (
            'type' => 'int(10) unsigned',
            'default' => '0',
            'required' => true,
            'label' => '排序',
            'in_list' => true,
            'editable' => true,
            ),
        'action_solution' =>
        array (
            'type' => 'serialize',
            'default' => '',
            'required' => true,
            'label' => '动作方案',
            'editable' => false,
            ),
        'free_shipping' =>
        array(
            'type' => 'tinyint(1) unsigned',
            'default' => '0',
            'label' => '免运费',
            'editable' => false,
            ),
        'c_template' =>
        array(
            'type' => 'varchar(100)',
            'label' => '过滤条件模板',
            'editable' => false,
            ),
        's_template' =>
        array(
            'type' => 'varchar(100)',
            'label' => '优惠方案模板',
            'editable' => false,
            ),
        'apply_time' =>
        array (
            'type' => 'time',
            'label' => '预过滤时间',
            'editable' => true,
            'in_list' => true,
            'default_in_list' => true,
            ),
        ),
    'label' => '商品促销规则',
    );
