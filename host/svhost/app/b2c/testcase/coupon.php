<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
/**
 * 购物车测试用例
 * $ 2010-04-29 14:01 $
 */
class coupon extends PHPUnit_Framework_TestCase
{
    function setUp() {
        // 调用model
        $this->app = app::get('b2c');
        
    }
    
    
    function test_ctl_coupon() {
        $aData = Array ( 
                        'c_template' => 'b2c_promotion_conditions_order_allorderallgoods',
                        's_template' => 'b2c_promotion_solutions_byfixed',
                        'member_lv_ids' => 1, 
                        'description' => 'asdf',
                        'rule_id' => '10',
                        'status' => 'true' ,
                        'rule_type' => 'C' ,
                        'name' => '优惠劵规则-test' ,
                        'from_time' => 1275580800 ,
                        'to_time' => 1275580800 ,
                        'create_time' => 1275635725 ,
                        'conditions' => Array ( 
                            'type' => 'b2c_sales_order_aggregator_combine', 
                            'aggregator' => 'all',
                            'value' => '1', 
                            'conditions' => Array ( 
                                Array ( 
                                    'type' => 'b2c_sales_order_item_coupon', 
                                    'attribute' => 'coupon', 
                                    'operator' => '=', 
                                    'value' => ''
                                ), 
                                Array ( 
                                    'type' => 'b2c_sales_order_aggregator_combine', 
                                    'aggregator' => 'all', 
                                    'value' => '1', 
                                    'conditions' => Array ( 
                                        Array ( 
                                            'type' => 'b2c_sales_order_item_order', 
                                            'attribute' => 'order_subtotal', 
                                            'operator' => '>=', 
                                            'value' => '0', 
                                        ), 
                                    ), 
                                ), 
                            ), 
                        ),
                        'action_conditions' => Array ( 
                            'type' => 'b2c_sales_order_aggregator_item',
                            'aggregator' => 'all',
                            'value' => 1, 
                            'conditions' => Array ( 
                                '0' => Array ( 
                                    'type' => 'b2c_sales_order_item_goods',
                                    'attribute' => 'goods_buy_price',
                                    'operator' => '>=',
                                    'value' => 0,
                                    ),
                                ),
                        ),
                        'action_solution' => Array ( 
                            'b2c_promotion_solutions_byfixed' => Array ( 
                                        'type' => 'goods',
                                        'total_amount' => '12' 
                             ), 
                        ),
                   );
        
        $object = $this->app->controller("admin_sales_coupon");
        $object->toAdd($aData);
        exit;
    }
    

    function test_add_coupon() {
        $object = kernel::single("b2c_cart_object_coupon");
        $aData = Array ( 
                    'modify_quantity' => Array ( 
                                        'coupon_Btest54F2900001' => 1,
                                       ),
                    'coupon' => 'Btest6CA4A00002',
                    '0' => 'coupon',
                );
        $object->add($aData);
        
        
        //获取优惠券信息
        //$t = kernel::single("b2c_mdl_coupons")->getCouponByCouponCode('Btest54F2900001');
        //$t = kernel::single("b2c_mdl_cart")->get_objects(1);
        //print_R($t);exit;
        
        print_r($t);
        //print_r($aData);
    }

}
?>
