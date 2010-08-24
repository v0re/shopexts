<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_promotion_conditions_goods_allgoods{
    public $tpl_name = "所有商品";
    public $tpl_type = 'html';

    function __construct($app){ $this->app = $app; }

    function getConfig($aData = array()) {
        return <<<EOF
<h4 align="center">所有商品</h4>
<input type="hidden" name="conditions[type]" value="b2c_sales_goods_aggregator_combine" />
<input type="hidden" name="conditions[aggregator]" value="all" />
<input type="hidden" name="conditions[value]" value="1" />

<input type="hidden" name="conditions[conditions][0][type]" value="b2c_sales_goods_item_goods" />
<input type="hidden" name="conditions[conditions][0][attribute]" value="goods_goods_id" />
<input type="hidden" name="conditions[conditions][0][operator]" value=">=" />
<input type="hidden" name="conditions[conditions][0][value]" value="1" />
EOF;
    }
}
?>
