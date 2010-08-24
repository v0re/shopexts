<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

// 快递单模版管理表
class express_mdl_print_tmpl extends dbeav_model
{    
    public function getElements(){
        $elements = array(
            'ship_name'=>__('收货人-姓名'),

            'ship_area_0'=>__('收货人-地区1级'),
            'ship_area_1'=>__('收货人-地区2级'),
            'ship_area_2'=>__('收货人-地区3级'),

            'ship_addr'=>__('收货人-地址'),
            'ship_tel'=>__('收货人-电话'),
            'ship_mobile'=>__('收货人-手机'),
            'ship_zip'=>__('收货人-邮编'),
            'dly_name'=>__('发货人-姓名'),

            'dly_area_0'=>__('发货人-地区1级'),
            'dly_area_1'=>__('发货人-地区2级'),
            'dly_area_2'=>__('发货人-地区3级'),

            'dly_address'=>__('发货人-地址'),
            'dly_tel'=>__('发货人-电话'),
            'dly_mobile'=>__('发货人-手机'),
            'dly_zip'=>__('发货人-邮编'),
            'date_y'=>__('当日日期-年'),
            'date_m'=>__('当日日期-月'),
            'date_d'=>__('当日日期-日'),
            'order_print'=>'订单条码',
            'order_id'=>__('订单-订单号'),
            'order_price'=>__('订单总金额'),
            'order_weight'=>__('订单物品总重量'),
            'order_count'=>__('订单-物品数量'),
            'order_memo'=>__('订单-备注'),
            'ship_time'=>__('订单-送货时间'),
            'shop_name'=>__('网店名称'),
            'tick'=>__('对号 - √'),
            'text'=>__('自定义内容'),
        );
        return $elements;
    }
}