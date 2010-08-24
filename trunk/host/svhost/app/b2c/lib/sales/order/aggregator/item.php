<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
/**
 * order aggregator(action_conditions的处理) 不用加入b2c_sales_order_aggregator_apps中去
 * $ 2010-05-17 13:16 $
 */
class b2c_sales_order_aggregator_item extends b2c_sales_order_aggregator
{
    public $default = 'b2c_sales_order_aggregator_item';
    public function getItem() {
        // 其实一个aggregator 只有一条记录的哈
        return array(
                   'b2c_sales_order_aggregator_item' => array(
                                       'name'=>__('条件组合'),
                                       'object'=>'b2c_sales_order_aggregator_item',
                                       'support'=>array(
                                                     'aggregator'=>'all',
                                                     'item'=>array(
                                                                'goods'=>__('-----商品属性-----'),
                                                                'subgoods'=>__('-----商品扩展属性-----'),
                                                             )
                                                  ),
                                      )
               );
    }
}
?>
