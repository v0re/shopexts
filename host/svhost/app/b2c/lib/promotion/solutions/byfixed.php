<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
/**
 * 单商品减固定价格购买
 * $ 2010-05-04 16:51 $
 */
class b2c_promotion_solutions_byfixed implements b2c_interface_promotion_solution
{
    public $name = "减固定价格购买"; // 名称
    public $type = 'goods'; //默认goods
    public $desc_pre = '价格优惠';
    public $desc_post = '出售';
    private $description = '';
    

    /**
     * 优惠方案模板
     * @param array $aConfig // 设置信息(修改的时候传入)
     * @return string // 返回要输出的模板html
     */
    public function config($aData = array()) {
        return <<<EOF
{$this->desc_pre}<input name="action_solution[b2c_promotion_solutions_byfixed][total_amount]" vtype='required&&unsigned' value="{$aData['total_amount']}" />{$this->desc_post}
EOF;
    }

    /**
     * 优惠方案应用
     *
     * @param array $object  // 引用的一个商品信息
     * @param array $aConfig // 优惠的设置
     * @param array $cart_object // 购物车信息(预过滤的时候这个为null)
     * @return void // 引用处理了,没有返回值
     */
    public function apply(&$object,$aConfig,&$cart_object = null) {
        if(is_null($cart_object)) { // 商品预过滤
            
            $object['obj_items']['products'][0]['price']['buy_price'] = $object['obj_items']['products'][0]['price']['member_lv_price'] - $aConfig['total_amount'];
            $object['obj_items']['products'][0]['price']['buy_price'] = max(0,$object['obj_items']['products'][0]['price']['buy_price']);
        } else {// 购物车里的处理
            
            $productQuantity = $object['obj_items']['products'][0]['quantity'];
            $goodsQuantity = $object['quantity'];
            $qty = $productQuantity * $goodsQuantity;
            $productPrice = $object['obj_items']['products'][0]['price']['buy_price'];
            $discountAmount    = $qty * $aConfig['total_amount'];
            $object['discount_amount_order'] += max(0, $discountAmount);
            //$this->desc_pre = '总价格优惠';
        }
        
        $this->setString($aConfig);
    }
    
    
    
    
    /**
     * 优惠方案应用
     *
     * @param array $object  // 引用的一个商品信息
     * @param array $aConfig // 优惠的设置
     * @param array $cart_object // 购物车信息(预过滤的时候这个为null)
     * @return void // 引用处理了,没有返回值
     */
    public function apply_order(&$object, &$aConfig,&$cart_object = null) {
        if(is_null($cart_object)) return false;
        
        $object['discount_amount_order'] += $aConfig['total_amount'];
        $this->desc_pre = '订单总价格优惠';
        $this->setString($aConfig);
    }
    
    
    public function setString($aData) {
        $this->description = $this->desc_pre . kernel::single('ectools_mdl_currency')->changer($aData['total_amount']) . $this->desc_post;
    }
    
    public function getString() {
        return $this->description;
    }
    
    
    
    public function get_status() {
        return true;
    }
    
    
}
