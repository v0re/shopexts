<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
/**
 * 订单促销规则 item/aggregator 的测试用例数据
 * $ 2010-05-21 11:03 $
 */
///////////////////////////////////////////  购物车商品信息 /////////////////////////////////////////////////
$data['cart_objects']['object']['goods'] = array(
    0 => array(
            'obj_ident' => 'goods_1_1_na', // 购物车商品标识 $obj_type_$goods_id_$product_id_(na| $product_id_$group_id|...) // na为没有配件商品  否则配件以 货品编号(?)_
            'obj_type'  => 'goods',        // 商品类型 goods|package|gift  goods:加入购物车的商品 package:捆绑商品 gift:赠品
            'obj_items' => array(
                    'products' => array(
                        0 => array(
                              'bn'    => 'abaccc',  // 货品货号
                              'price' => array(
                                            'price'           => '80.000', // 商品基本价格
                                            'cost'            => '50.000', // 成本价
                                            'member_lv_price' => 80,       // 会员等级价(会员独立设置 或按 会员等级折扣*商品基本价格)
                                            'buy_price'       => 80,       // 买入的价格 (经过了goods_promotion 的处理得到的值)
                                        ),
                             'product_id' => '1',
                             'goods_id' => '1',
                             'consume_score' => 0,          // 单个货品要消耗的积分
                             'gain_score' => 0,             // 单个货品能得到的积分
                             'type_setting' => false,       // ?
                             'type_id' => '1',              // ?
                             'spec_info' => 'fefe,fefe',
                             'spec_desc' => false,
                             'weight' => '100.000',        // 单个货品重量
                             'quantity' => 1,              // 购买数量
                             'default_image' => array(
                                                  'thumbnail' => NULL,
                                                  'small'     => NULL,
                                                  'big'       => NULL,
                                                ),
                             'name' => 'just a goods',
                             'subtotal_consume_score' => 0, // ?
                             'subtotal_gain_score' => 0,    // ?
                             'subtotal' => 80,              // ?
                             'subtotal_weight' => 100,      //
                            ),
                        ),
                    ),
           'quantity' => '12',    // 下单数量
           'params' => array(
                          'goods_id' => '1',
                          'product_id' => '1',
                          'adjunct' => array(
                                       ),
                       ),
            'subtotal_consume_score' => 0,  // 本商品的总消费积分(和quantity,obj_items[products][0][subtotal_consume_score] 相关)
            'subtotal_gain_score' => 0,     // 本商品的总获得积分(和quantity,obj_items[products][0][subtotal_gain_score] 相关)
            'subtotal' => 960,              // 本商品的总重量(和quantity,obj_items[products][0][subtotal] 相关)
            'subtotal_weight' => 1200,      // 本商品的总重量(和quantity,obj_items[products][0][subtotal_weight] 相关)
            'discount_amount' => 0,         //
            'adjunct' => array(
                             ),
          ),
     1 => array (
                    'obj_ident' => 'goods_2_2_na',
                    'obj_type'  => 'goods',
                    'obj_items' => array(
                              'products' => array(
                                              0 =>  array(
                                                     'bn'    => 'abaccc',
                                                     'price' => array(
                                                                   'price' => '80.000',
                                                                   'cost' => '50.000',
                                                                   'member_lv_price' => 80,
                                                                   'buy_price' => 80,
                                                                ),
                                                     'product_id' => '2',
                                                     'goods_id' => '2',
                                                     'consume_score' => 0,
                                                     'gain_score' => 0,
                                                     'type_setting' => false,
                                                     'type_id' => '2',
                                                     'spec_info' => 'fefe,fefe',
                                                     'spec_desc' => false,
                                                     'weight' => '100.000',
                                                     'quantity' => 1,
                                                     'default_image' => array(
                                                                          'thumbnail' => NULL,
                                                                          'small' => NULL,
                                                                          'big' => NULL,
                                                                        ),
                                                     'name' => 'just a goods',
                                                     'subtotal_consume_score' => 0,
                                                     'subtotal_gain_score' => 0,
                                                     'subtotal' => 80,
                                                     'subtotal_weight' => 100,
                                              ),
                                          ),
                     ),
                     'quantity' => '9',
                     'params' => array (
                                    'goods_id' => '2',
                                    'product_id' => '2',
                                    'adjunct' => array(
                                                  ),
                                 ),
                     'subtotal_consume_score' => 0,
                     'subtotal_gain_score' => 0,
                     'subtotal' => 720,
                     'subtotal_weight' => 900,
                     'discount_amount' => 0,
                     'adjunct' => array(
                                   ),
                 ),
            2 => array(
                   'obj_ident' => 'goods_6_6_na',
                   'obj_type' => 'goods',
                   'obj_items' => array(
                                   'products' => array(
                                                   0 => array(
                                                          'bn' => 'abaccc',
                                                          'price' => array(
                                                                       'price' => '180.000',
                                                                       'cost' => '50.000',
                                                                       'member_lv_price' => 180,
                                                                       'buy_price' => 180,
                                                                     ),
                                                          'product_id' => '6',
                                                          'goods_id' => '6',
                                                          'consume_score' => 0,
                                                          'gain_score' => 0,
                                                          'type_setting' => false,
                                                          'type_id' => '3',
                                                          'spec_info' => 'fefe,fefe',
                                                          'spec_desc' => false,
                                                          'weight' => '100.000',
                                                          'quantity' => 1,
                                                          'default_image' => array(
                                                                                'thumbnail' => NULL,
                                                                                'small' => NULL,
                                                                                'big' => NULL,
                                                                             ),
                                                          'name' => 'just a goods',
                                                          'subtotal_consume_score' => 0,
                                                          'subtotal_gain_score' => 0,
                                                          'subtotal' => 180,
                                                          'subtotal_weight' => 100,
                                                     ),
                                                  ),
                                     ),
                'quantity' => '3',
                'params' => array(
                              'goods_id' => '6',
                              'product_id' => '6',
                              'adjunct' => array(
                                           ),
                           ),
                'subtotal_consume_score' => 0,
                'subtotal_gain_score' => 0,
                'subtotal' => 540,
                'subtotal_weight' => 300,
                'discount_amount' => 0,
                'adjunct' => array (
                             ),
         ),
);

///////////////////////////////////////////  购物车信息(coupon) /////////////////////////////////////////////////
$data['cart_objects']['obejct']['coupon'] = array();

///////////////////////////////////////////  购物车信息(order) /////////////////////////////////////////////////
$data['cart_objects']['subtotal_consume_score'] = 0;
$data['cart_objects']['subtotal_gain_score'] = 500;
$data['cart_objects']['items_quantity'] = 25;
$data['cart_objects']['items_count'] = 5;
$data['cart_objects']['subtotal_weight'] = 100.2;
$data['cart_objects']['subtotal'] = 500.1;
?>
