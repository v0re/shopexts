<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
/**
 * 购物车项处理(优惠券)
 * $ 2010-05-25 14:09 $
 */
class b2c_cart_object_coupon implements b2c_interface_cart_object{

    private $app;
    private $member_ident; // 用户标识
    private $oCartObject;

    /**
     * 构造函数
     *
     * @param $object $app  // service 调用必须的
     */
    public function __construct() {
        $this->app = app::get('b2c');
        
        $this->arr_member_info = kernel::single('b2c_frontpage')->get_current_member();
        $this->member_ident = kernel::single("base_session")->sess_id();
        
        $this->oCartObjects = $this->app->model('cart_objects');
    }
    
    
    
    public function get_type() {
        return 'coupon';
    }

    /**
     * 添加购物车项(coupon)
     *
     * @param array $aData // array(
     *                          'goods_id'=>'xxxx',   // 商品编号
     *                          'product_id'=>'xxxx', // 货品编号
     *                          'adjunct'=>'xxxx',    // 配件信息
     *                          'quantity'=>'xxxx',   // 购买数量
     *                        )
     * @return array //
     */
    public function add($aData) {
        if(!$this->_check($aData)) return false;
        $objIdent = $this->_generateIdent($aData);
        $aCouponRule = $this->app->model('coupons')->getCouponByCouponCode($aData['coupon']);
        $aSave = array(
                   'obj_ident'    => $objIdent,
                   'member_ident' => $this->member_ident,
                   'obj_type'     => 'coupon',
                   'params'       => array(
                                        'name'  =>  $aData['coupon'],
                                        'rule_id'   => $aCouponRule[0]['rule_id'],
                                        'cpns_id'   => $aCouponRule[0]['cpns_id'],
                                        ),
                   'quantity'     => 1,  // 一张优惠券只能使用一次不能叠加
                 );
        
        if(kernel::single("b2c_cart_object_goods")->get_cart_status()) {
            $this->coupon_object[$aSave['obj_ident']] = $aSave;
            return $aSave;
            //todo
        }; //no database
        
        $this->oCartObjects->save($aSave);
        return $aSave;
    }

    // 优惠券没有更新这一说
    public function update($sIdent,$quantity) {
        return false;
    }

    /**
     * 指定的购物车优惠券
     *
     * @param string $sIdent
     * @param boolean $rich        // 是否只取cart_objects中的数据 还是完整的sdf数据
     * @return array
     */
    public function get($sIdent = null,$rich = false) {
        if(empty($sIdent)) return $this->getAll($rich);
        
        $aResult = $this->oCartObjects->getList('*',array(
                                           'obj_ident' => $sIdent,
                                           'member_ident'=> $this->member_ident,
                                        ));
        if(empty($aResult)) return array();
        if($rich) {
            $aResult = $this->_get($aResult);
            $aResult = $aResult[0];
        }
        
        return $aResult;
    }

    public function _get($aData){
        // todo要从数据库中取出对应用的优惠券的描述
        foreach($aData as $row) {
            $params = $row['params'];
            $aResult[] = array(
                            'obj_ident' => $row['obj_ident'],
                            'obj_type' => 'coupon',
                            'quantity' => 1,
                            'description' => '',
                            'coupon'=>$params['name'],
                            'rule_id' => $params['rule_id'],
                            'cpns_id'=> $params['cpns_id'],
                            'used' => false // 是否使用 order conditions时处理
                        );
        }
        return $aResult;
    }

    // 购物车里的所有优惠券
    public function getAll($rich = false) {
        
        if(kernel::single("b2c_cart_object_goods")->get_cart_status()) {
            $aResult = $this->coupon_object;
        } else {
            $aResult= $this->oCartObjects->getList('*',array(
                                               'obj_type' => 'coupon',
                                               'member_ident'=> $this->member_ident,
                                           ));
        }
        if(empty($aResult)) return array();
        if(!$rich) return $aResult;
        return $this->_get($aResult);
    }

    // 删除购物车中指定优惠券
    public function delete($sIdent = null) {
        if(empty($sIdent)) return $this->deleteAll();
        // todo 如果dbeav中有delete方法邓 再悠修改下面
        return $this->oCartObjects->delete(array('member_ident'=>$this->member_ident, 'obj_ident'=>$sIdent, 'obj_type'=>'coupon'));
    }

    // 清空购物车中优惠券数据
    public function deleteAll() {
        return $this->oCartObjects->delete(array('member_ident'=>$this->member_ident, 'obj_type'=>'coupon'));
    }

    // 统计购物车中优惠券数据
    public function count(&$aData) {}

    // todo 优惠券添加到购物车中的数据检测在这里处理
    // 优惠券的正确性 类型 是否已使用
    private function _check(&$aData) {
        if(empty($aData) || empty($aData['coupon'])) return false;
        return $this->app->model("coupons")->verify_coupons($aData);
        
        // 通过 $aData['coupon'] 验证coupon的有效性

        return true;
    }

    private function _generateIdent($aData) {
        return "coupon_".$aData['coupon'];# .'_'. ( $this->arr_member_info['member_id'] ? $this->arr_member_info['member_id'] : 0 );
    }
}
?>
