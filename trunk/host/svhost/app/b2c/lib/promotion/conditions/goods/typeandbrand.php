<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_promotion_conditions_goods_typeandbrand{
    var $tpl_name = "商品类型+商品品牌";
    var $tpl_type = 'config';

    function getConfig($aData = array()) {
        return  array(
                  'type'=> 'b2c_sales_goods_aggregator_combine',
                  'aggregator'=> 'all',
                  'conditions'=> array(
                                   0 => array(
                                           'type' =>'b2c_sales_goods_item_goods',
                                           'attribute' => 'goods_type_id'
                                         ),
                                   1 => array(
                                           'type' =>'b2c_sales_goods_item_goods',
                                           'attribute' => 'goods_brand_id'
                                         )
                                 )
                );
    }
}
?>
