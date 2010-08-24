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
class gift_promotion_solutions_gift implements b2c_interface_promotion_solution
{
    public $name = "送赠品"; // 名称
    public $type = 'goods'; //默认goods
    public $desc_pre = '获得赠品';
    public $desc_post = '';
    public $score_add = true;
    private $description = '';

    /**
     * 优惠方案模板
     * @param array $aConfig // 设置信息(修改的时候传入)
     * @return string // 返回要输出的模板html
     */
    public function config($aData = array()) {
        $render = app::get('gift')->render();
        ob_start();
        $render->pagedata['value'] = $aData['gain_gift'];
        $render->pagedata['object'] = 'goods';
        $render->pagedata['name'] = 'action_solution[gift_promotion_solutions_gift][gain_gift]';
        $render->display('admin/sales/dialog.html');
        return $this->desc_pre . ob_get_clean();
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
        if(!isset($aConfig['gain_gift']) || empty($aConfig['gain_gift'])) return false;
        $arr = kernel::single("gift_cart_object_goods")->_get_products($aConfig['gain_gift']);
		foreach( $aConfig['gain_gift'] as $_gift_id ) {
			$tmp[] = $arr[$_gift_id];
		}
		$arr = $tmp;
		$tmp = null;
		
        if(is_null($cart_object)) { // 商品预过滤
            $object['gift'] = $arr;
        } else {// 购物车里的处理
            $cart_object['object']['gift']['order'] = $arr;
        }
		$this->setString($arr);
        
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
        if(!isset($aConfig['gain_gift']) || empty($aConfig['gain_gift'])) return false;
        $arr = kernel::single("gift_cart_object_goods")->_get_products($aConfig['gain_gift']);
		foreach( $aConfig['gain_gift'] as $_gift_id ) {
			$tmp[] = $arr[$_gift_id];
		}
		$arr = $tmp;
		$tmp = null;
		
        $this->setString($arr);
        $cart_object['object']['gift']['order'] = $arr;
		
    }
    
    
    public function setString($aData) {
        if( is_array($aData) ) {
            foreach( $aData as $row ) {
                $a[] = $row['name'];
            }
            $str = implode('、', $a);
        }
		
        $this->description = $this->desc_pre . $str;
    }
    
    public function getString($aData=array()) {
        return $this->description;
    }
    
    public function get_status() {
        return true;
    }
    
}
?>
