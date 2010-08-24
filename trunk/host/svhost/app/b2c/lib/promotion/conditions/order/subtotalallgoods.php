<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_promotion_conditions_order_subtotalallgoods{
    public $tpl_name = "当订单商品总价满X时,对所有商品优惠";
    public $whole = true;

    public function getConfig($aData = array()) {
        return <<<EOF
        订单金额满
    <input type="hidden" name="conditions[type]" value="b2c_sales_order_aggregator_combine" />
    <input type="hidden" name="conditions[aggregator]" value="all" />
    <input type="hidden" name="conditions[value]" value="1" />
    <input type="hidden" name="conditions[conditions][0][type]" value="b2c_sales_order_item_order" />
    <input type="hidden" name="conditions[conditions][0][attribute]" value="order_subtotal" />
    <input type="hidden" name="conditions[conditions][0][operator]" value=">=" />
    <input type="text" name="conditions[conditions][0][value]" size="3" vtype="required&&unsigned" value="{$aData['conditions']['conditions'][0]['value']}" />

    <input type="hidden" name="action_conditions[type]" value="b2c_sales_order_aggregator_item" />
    <input type="hidden" name="action_conditions[aggregator]" value="all" />
    <input type="hidden" name="action_conditions[value]" value="1" />
    <input type="hidden" name="action_conditions[conditions][0][type]" value="b2c_sales_order_item_goods" />
    <input type="hidden" name="action_conditions[conditions][0][attribute]" value="goods_price" />
    <input type="hidden" name="action_conditions[conditions][0][operator]" value=">=" />
    <input type="hidden" name="action_conditions[conditions][0][value]" value="0" />
EOF;
    }
}
?>
