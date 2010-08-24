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
class b2c_promotion_solutions_addscore implements b2c_interface_promotion_solution
{
    public $name = "赠送积分"; // 名称
    public $type = 'goods'; //默认goods
    public $desc_pre = '赠送积分';
    public $desc_post = '';
    public $score_add = true;
    private $description = '';
    
    
    /**
     * 优惠方案模板
     * @param array $aConfig // 设置信息(修改的时候传入)
     * @return string // 返回要输出的模板html
     */
    public function config($aData = array()) {
        if( !$this->get_status() ) return '<p class="red">积分已取消!如使用积分请修改商店设置-》积分</p>';
        return <<<EOF
{$this->desc_pre}<input name="action_solution[b2c_promotion_solutions_addscore][gain_score]" vtype='required&&unsigned' value="{$aData['gain_score']}" />{$this->desc_post}
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
            $object['obj_items']['products'][0]['gain_score'] += $aConfig['gain_score'];
        } else {// 购物车里的处理
            $object['sales_score'] += $aConfig['gain_score'];
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
        $object['sales_score_order'] += $aConfig['gain_score'];
        $this->setString($aConfig);
    }
    
    
    public function setString($aData) {
        $this->description = $this->desc_pre . $aData['gain_score'] . $this->desc_post;
    }
    
    public function getString() {
        return $this->description;
    }
    
    
    
    public function get_status() {
        if(app::get('b2c')->getConf('site.get_policy.method')==1) return false;
        return true;
    }
    
    
}

