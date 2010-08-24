<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
/**
 * subtotal goods_items(cart)
 * $ 2010-05-08 19:38 $
 */
class b2c_sales_order_item_subgoods extends b2c_sales_order_item
{
    public function getItem() {
        return array(
                    // cart_item -> /
                    'subgoods_quantity'=> array(
                                            'name'=>__('商品订购数量'),
                                            'path'=>'quantity',
                                            'type'=>'subgoods',
                                            'object'=>'b2c_sales_order_item_subgoods',
                                            'operator'=>array('equal','equal1'),'vtype'=>'digits'
                                        ),
                    // price
                    'subgoods_subtotal'=> array(
                                            'name'=>__('商品订购总价'),
                                            'path'=>'subtotal',
                                            'type'=>'subgoods',
                                            'object'=>'b2c_sales_order_item_subgoods',
                                            'operator'=>array('equal','equal1'),'vtype'=>'unsigned'
                                        ),
                    'subgoods_subtotal_weight'=> array(
                                                'name'=>__('商品订购总重量'),
                                                'path'=>'subtotal_weight',
                                                'type'=>'subgoods',
                                                'object'=>'b2c_sales_order_item_subgoods',
                                                'operator'=>array('equal','equal1'),'vtype'=>'unsigned'
                                            ),
                    // point
                    'subgoods_subtotal_consume_score'=> array(
                                                        'name'=>__('商品消费积分总数'),
                                                        'path'=>'subtotal_consume_score',
                                                        'type'=>'subgoods',
                                                        'object'=>'b2c_sales_order_item_subgoods',
                                                        'operator'=>array('equal','equal1'),'vtype'=>'unsigned'
                                                     ),
                    'subgoods_subtotal_gain_score'=> array(
                                                    'name'=>__('商品获得积分总数'),
                                                    'path'=>'subtotal_gain_score','type'=>'subgoods',
                                                    'object'=>'b2c_sales_order_item_subgoods',
                                                    'operator'=>array('equal','equal1'),'vtype'=>'unsigned'
                                                )
        );
    }
}
?>
