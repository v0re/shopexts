<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
/**
 * 订单促销规则 item/aggregator 的测试用例
 * lib/sales/order/item/xxx.php
 * lib/sales/order/aggregator/xxx.php
 * $ 2010-05-21 10:36 $
 *
 * // 主要是validate的处理
 */
class sales_order extends PHPUnit_Framework_TestCase
{
    function setUp(){
        // 载入测试数据
        require(dirname(__FILE__)."/sales_order_data.php");
        $this->cart_objects = $data['cart_objects'];
    }

    /**
     * 订单信息
     * lib/sales/order/item/order.php
     *
     */
    function test_item_order() {
        $this->markTestSkipped('item order');
        /*
         * order_subtotal
         * order_subtotal_weight
         * order_subtotal_gain_score
         * order_subtotal_consume_score
         * order_items_quantity
         * order_items_count
         */
        $oCond = kernel::single('b2c_sales_order_item_order');

        // 订单总金额大于100
        $aCondition = array(
                        'type'=>'b2c_sales_order_item_order',
                        'attribute'=>'order_subtotal',
                        'operator'=>'>',
                        'value'=>100,
                      );

        $this->assertTrue($oCond->validate($this->cart_objects,$aCondition),'验证失败');

        // 订单总金额小于100
        $aCondition = array(
                        'type'=>'b2c_sales_order_item_order',
                        'attribute'=>'order_subtotal',
                        'operator'=>'<',
                        'value'=>100,
                      );

        $this->assertFalse($oCond->validate($this->cart_objects,$aCondition),'验证失败');

        // 订单总重量小于150
        $aCondition = array(
                        'type'=>'b2c_sales_order_item_order',
                        'attribute'=>'order_subtotal_weight',
                        'operator'=>'<',
                        'value'=>150,
                      );

        $this->assertTrue($oCond->validate($this->cart_objects,$aCondition),'验证失败');
    }

    /**
     * 优惠券 (item coupon)
     * lib/sales/order/item/coupon.php
     *
     */
    function test_item_coupon() {
        $this->markTestSkipped('item coupon');
    }

    /**
     * 商品信息
     * lib/sales/order/item/goods.php
     *
     */
    function test_item_goods() {
        $this->markTestSkipped('item goods');
        $oCond = kernel::single('b2c_sales_order_item_goods');
        $aData = $this->cart_objects;

        /**
         * 一些商品的属性
         */

        // 商品出售价小于100
        $aCondition = array(
                        'type'=>'b2c_sales_order_item_goods',
                        'attribute'=>'goods_buy_price',
                        'operator'=>'<',
                        'value'=>100,
                      );

        $this->assertTrue($oCond->validate($aData['object']['goods'][0],$aCondition),'验证失败');

        // 商品出售价大于等于50
        $aCondition = array(
                        'type'=>'b2c_sales_order_item_goods',
                        'attribute'=>'goods_buy_price',
                        'operator'=>'>=',
                        'value'=>50,
                      );
        $this->assertTrue($oCond->validate($aData['object']['goods'][0],$aCondition),'验证失败');

        // 商品名称开头包含'just'
        $aCondition = array(
                        'type'=>'b2c_sales_order_item_goods',
                        'attribute'=>'goods_name',
                        'operator'=>'#()',
                        'value'=>'just',
                      );
        $this->assertTrue($oCond->validate($aData['object']['goods'][0],$aCondition),'验证失败');

        // 商品货号不包含'baa'
        $aCondition = array(
                        'type'=>'b2c_sales_order_item_goods',
                        'attribute'=>'goods_bn',
                        'operator'=>'!()',
                        'value'=>'baa',
                      );
        $this->assertTrue($oCond->validate($aData['object']['goods'][0],$aCondition),'验证失败');

        // 商品(goods_id)属于 一定集合 array(1,2,3)
        $aCondition = array(
                        'type'=>'b2c_sales_order_item_goods',
                        'attribute'=>'goods_goods_id',
                        'operator'=>'{}',
                        'value'=>array(1,2,3),
                      );
        $this->assertTrue($oCond->validate($aData['object']['goods'][0],$aCondition),'验证失败');
    }

    /**
     * 商品扩展信息
     * lib/sales/order/item/subgoods.php
     *
     */
    function test_item_subgoods() {
        $this->markTestSkipped('item subgoods');
        $oCond = kernel::single('b2c_sales_order_item_subgoods');
        $aData = $this->cart_objects;

        /**
         * subgoods_quantity
         * subgoods_subtotal
         * subgoods_subtotal_weight
         * subgoods_subtotal_gain_score
         * subgoods_subtotal_consume_score
         */

        // 商品购买数量小于等于12
        $aCondition = array(
                        'type'=>'b2c_sales_order_item_subgoods',
                        'attribute'=>'subgoods_quantity',
                        'operator'=>'<=',
                        'value'=>12,
                      );

        $this->assertTrue($oCond->validate($aData['object']['goods'][0],$aCondition),'验证失败');


        // 商品总价大于 800
        $aCondition = array(
                        'type'=>'b2c_sales_order_item_subgoods',
                        'attribute'=>'subgoods_subtotal',
                        'operator'=>'>',
                        'value'=>800,
                      );

        $this->assertTrue($oCond->validate($aData['object']['goods'][0],$aCondition),'验证失败');
    }

    /**
     * 集合器(found) 用于conditions
     * 商品属性组合
     */
    function test_aggregator_found() {
        $this->markTestSkipped('aggregator found');
        $oCond = kernel::single('b2c_sales_order_aggregator_found');
        $aData = $this->cart_objects;

        /// 商品属性组合 /////////////////////////////////
        /**
         * 80 12
         * 80 9
         * 180 3
         */
        // 1 购物车都得满足的条件 true
        $aCondition = array(
                        'type'=>'b2c_sales_order_aggregator_found',
                        'aggregator'=>'all',
                        'value'=>1,
                        'conditions'=>array(
                                        0 => array(
                                            'type'=>'b2c_sales_order_item_subgoods',
                                            'attribute'=>'subgoods_quantity',
                                            'operator'=>'>=',
                                            'value'=>3,
                                        ),
                                        1 => array(
                                            'type'=>'b2c_sales_order_item_goods',
                                            'attribute'=>'goods_price',
                                            'operator'=>'>=',
                                            'value'=>80,
                                        )
                        )
                      );
       $this->assertTrue($oCond->validate($aData,$aCondition),'验证失败');

       // 2 购物车中有一件满足即可 true
       $aCondition['aggregator'] = 'any';
       $aCondition['conditions'][1]['value'] = 120;
       $this->assertTrue($oCond->validate($aData,$aCondition),'验证失败');

       // 3 购物车中有一件不满足即可 true
       $aCondition['aggregator'] = 'any';
       $aCondition['value'] = 0;
       $aCondition['conditions'][1]['value'] = 200;
       $this->assertTrue($oCond->validate($aData,$aCondition),'验证失败');

       // 4 购物车中商品都不满足即可 true
       $aCondition['aggregator'] = 'all';
       $aCondition['value'] = 0;
       $aCondition['conditions'][0]['value'] = 100;
       $aCondition['conditions'][1]['value'] = 200;
       $this->assertTrue($oCond->validate($aData,$aCondition),'验证失败');
    }

    /**
     * 集合器(subselect) 用于conditions
     * 商品子查询
     */
    function test_aggregator_subselect() {
        $this->markTestSkipped('aggregator subselect');
        $oCond = kernel::single('b2c_sales_order_aggregator_subselect');
        $aData = $this->cart_objects;

        //
        /**
         * 80 12
         * 80 9
         * 180 3
         */
        $aCondition = array(// 数量小于12并且价格小于等于80 只有第二件商品满足
                        'type'=>'b2c_sales_order_aggregator_subselect',
                        'attribute'=>'subgoods_quantity',
                        'operator'=>'>=',
                        'value'=>9,
                        'conditions'=>array(
                                        array(
                                            'type'=>'b2c_sales_order_item_subgoods',
                                            'attribute'=>'subgoods_quantity',
                                            'operator'=>'<',
                                            'value'=>12,
                                        ),
                                        array(
                                            'type'=>'b2c_sales_order_item_goods',
                                            'attribute'=>'goods_price',
                                            'operator'=>'<=',
                                            'value'=>80,
                                        )
                        )
                      );
       $this->assertTrue($oCond->validate($aData,$aCondition),'验证失败');

       // 验证没有通过 false
       $aCondition['value'] = 12;
       $this->assertFalse($oCond->validate($aData,$aCondition),'验证失败');
    }

    /**
     * 集合器(combine) 用于conditions
     * 商品子查询
     */
    function test_aggregator_combine() {
        $this->markTestSkipped('aggregator combine');
        $oCond = kernel::single('b2c_sales_order_aggregator_combine');
        $aData = $this->cart_objects;

        //// 只有订单属性 ///////////////////////////////////////////
        // 1 true
        $aCondition = array(
                        'type'=>'b2c_sales_order_aggregator_combine',
                        'aggregator'=>'all',
                        'value'=>1,
                        'conditions'=>array(
                                         0=>array(
                                            'type'=>'b2c_sales_order_item_order',
                                            'attribute'=>'order_subtotal',
                                            'operator'=>'>=',
                                            'value'=>12,
                                         ),
                                         1=>array(
                                            'type'=>'b2c_sales_order_item_order',
                                            'attribute'=>'order_items_quantity',
                                            'operator'=>'<=',
                                            'value'=>25,
                                         ),
                                      )
                      );
       $this->assertTrue($oCond->validate($aData,$aCondition),'验证失败');
       // 2 false
       $aCondition['conditions'][1]['value'] = 20;
       $this->assertFalse($oCond->validate($aData,$aCondition),'验证失败');

       // 3 any 1
       $aCondition['aggregator'] = 'any';
       $this->assertTrue($oCond->validate($aData,$aCondition),'验证失败');

       // 4 any 0
       $aCondition['value'] = '0';
       $this->assertTrue($oCond->validate($aData,$aCondition),'验证失败');

       //// 带aggregator 的复杂条件 //////////////////////////////////
    }


    /**
     * 集合器(item) 用于action_conditions
     *
     */
    function test_aggregator_item() {
        //$this->markTestSkipped('aggregator item');
        $oCond = kernel::single('b2c_sales_order_aggregator_combine');
        $aData = $this->cart_objects;

        // 1 true
        $aCondition = array(
                        'type'=>'b2c_sales_order_aggregator_item',
                        'aggregator'=>'all',
                        'value'=>1,
                        'conditions'=>array(
                                         0=>array(
                                            'type'=>'b2c_sales_order_item_goods',
                                            'attribute'=>'goods_price',
                                            'operator'=>'>=',
                                            'value'=>80,
                                         ),
                                         1=>array(
                                            'type'=>'b2c_sales_order_item_subgoods',
                                            'attribute'=>'subgoods_quantity',
                                            'operator'=>'>=',
                                            'value'=>9,
                                         ),
                                      )
                      );
        $this->assertTrue($oCond->validate($aData['object']['goods'][0],$aCondition),'验证失败'); // 12
        $this->assertTrue($oCond->validate($aData['object']['goods'][1],$aCondition),'验证失败'); // 9
        $this->assertFalse($oCond->validate($aData['object']['goods'][2],$aCondition),'验证失败'); // 3


        // 2 item--item
        $aCondition = array(
                        'type'=>'b2c_sales_order_aggregator_item',
                        'aggregator'=>'all',
                        'value'=>1,
                        'conditions'=>array(
                                         0=>array(
                                            'type'=>'b2c_sales_order_item_goods',
                                            'attribute'=>'goods_price',
                                            'operator'=>'>=',
                                            'value'=>80,
                                         ),
                                         1=>array(
                                            'type'=>'b2c_sales_order_aggregator_item',
                                            'aggregator'=>'all',
                                            'value'=>1,
                                            'conditions'=>array(
                                                             0=>array(
                                                                    'type'=>'b2c_sales_order_item_subgoods',
                                                                    'attribute'=>'subgoods_quantity',
                                                                    'operator'=>'>=',
                                                                    'value'=>9,
                                                             ),
                                                             1=>array(
                                                                    'type'=>'b2c_sales_order_item_goods',
                                                                    'attribute'=>'goods_price',
                                                                    'operator'=>'>=',
                                                                    'value'=>60,
                                                             ),
                                                         )

                                         ),
                                      )
                      );
        $this->assertTrue($oCond->validate($aData['object']['goods'][0],$aCondition),'验证失败'); // 12
        $this->assertTrue($oCond->validate($aData['object']['goods'][1],$aCondition),'验证失败'); // 9
        $this->assertFalse($oCond->validate($aData['object']['goods'][2],$aCondition),'验证失败'); // 3
    }
}
?>
