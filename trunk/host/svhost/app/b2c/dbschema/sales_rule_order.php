<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$db['sales_rule_order'] = array(
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
            'in_list' => true, 
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
            'required' => true,
            'label' => '会员级别集合',
            'editable' => false,
            ),
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
        'action_conditions' =>
        array (
            'type' => 'serialize',
            'default' => '',
            'label' => '动作执行条件',
            'editable' => false,
            ),
        'stop_rules_processing' =>
        array (
            'type' => 'bool',
            'default' => 'false',
            'required' => true,
            'label' => '排斥其他规则',
            'editable' => true,
            'filterdefault'=>true,
            'in_list' => true,
            ),
        'sort_order' =>
        array (
            'type' => 'int(10) unsigned',
            'default' => '0',
            'required' => true,
            'label' => '排序',
            'editable' => true,
            'in_list' => true,
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
            'type' =>array(
                    0=>'免运费',
                    1=>'满足过滤条件的商品免运费',
                    2=>'全场免运费'
             ),
            'default' => '0',
            'label' => '免运费',
            'editable' => false,
            'filterdefault'=>true,
            'in_list' => true,
            ),
       'rule_type' =>
            array (
            'type' => array (
                'N' => '普通规则',
                'C' => '优惠券规则',
            ),
            'default' => 'N',
            'required' => true,
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
            'type' => 'varchar(255)',
            'label' => '优惠方案模板',
            'editable' => false,
            ),
        ),
    'label' => '订单促销规则',
    );
