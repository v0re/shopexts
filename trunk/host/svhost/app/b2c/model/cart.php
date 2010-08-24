<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
/**
 * mdl_cart 购物车model
 * $ 2010-04-28 20:03 $
 */

class b2c_mdl_cart extends dbeav_model{
    /**
     * 获取购物车数据(订单修改也走这个方法)
     *
     * @param array $aData   // $_GET $POST and so on
     * @param array $aResult // 传出的就是购物车的数据(所有的东东都打过折的 处理过的)
     * @param array $aConfig // 一些设置 (订单修改时可能要用到的当时下单的一些数据)
     * @return array(
     *            'object'=> array(
     *                          'goods'=>array(...),
     *                          'coupon'=>array(....),
     *                          ....
     *                       )
     *             'subtotal'=>'xxx',
     *             ...
     *         )
     */
    public function get_objects($aData=array(),$aResult = array(),$aConfig = array()) {
        foreach(kernel::servicelist('b2c_cart_process_apps') as $object) {
            if(!is_object($object)) continue;
            $tmp[$object->get_order()] = $object;
        }
        
        krsort($tmp);
        
        foreach($tmp as $object){
            $object->process($aData,$aResult,$aConfig);
        }
        app::get('b2c')->model('cart_objects')->setCartNum($aResult);
        return $aResult;
   }
   
   
   //后台
   public function set_cookie_cart_arr($arr_goods=array(), $member_ident='') {
       if(empty($member_ident)) return false;
       if(empty($arr_goods) || !is_array($arr_goods)) return false;
       return kernel::single("b2c_cart_object_goods")->set_cookie($member_ident, $arr_goods);
   }
   
   public function get_cookie_cart_arr($member_ident='') {
       if(empty($member_ident)) return false;
       return kernel::single("b2c_cart_object_goods")->get_cookie($member_ident);
   }
   
   public function del_cookie_cart_arr($member_ident='') {
       if(empty($member_ident)) return false;
       return kernel::single("b2c_cart_object_goods")->del_cookie($member_ident);
   }
   
   public function get_cart_object($arr_goods=array()) {
       if(empty($arr_goods) || !is_array($arr_goods)) return false;
       kernel::single("b2c_cart_object_goods")->no_database(true, $arr_goods, md5(rand().microtime()));
       foreach(kernel::servicelist('b2c_cart_process_apps') as $object) {
            if(!is_object($object)) continue;
            $tmp[$object->get_order()] = $object;
        }
        krsort($tmp);
        
        foreach($tmp as $object){
            $object->process($aData,$aResult,$aConfig);
        }
       kernel::single("b2c_cart_object_goods")->no_database(false);
       return $aResult;
   }

   public function get_basic_objects(){
       $aResult = array();
       $o = kernel::single("base_session");
       $o->start();
       $arr =  $this->app->model('cart_objects')->getList('*',array('member_ident'=>$o->sess_id()));
       return $arr;
       
       /**
       foreach(kernel::servicelist('b2c_cart_object_apps') as $object) {
            if(!is_object($object)) continue;
            $aResult = array_merge($aResult,$object->getAll(false)); // 只从数据库中取出
       }
       print_r($aResult);exit;
       return $aResult;
       */
   }

   /**
    * 购物车项总数据统计
    *
    * @param array $aData // cart_objects sdf
    */
   public function count_objects(&$aData) {
       $aData['subtotal_consume_score'] = 0;
       $aData['subtotal_gain_score'] = 0;
       $aData['subtotal'] = 0;
       $aData['subtotal_discount'] = 0;
       $aData['items_quantity'] = 0;
       $aData['items_count'] = 0;
       $aData['subtotal_weight'] = 0;
       $aData['discount_amount_prefilter'] = 0;
       $aData['discount_amount_order'] = 0;
       $aData['discount_amount'] = 0;

       foreach(kernel::servicelist('b2c_cart_object_apps') as $object) {
            if(!is_object($object)) continue;
           $aResult = $object->count($aData);

           if(empty($aResult)) continue;
           $aData['subtotal_consume_score'] += $aResult['subtotal_consume_score'];
           $aData['subtotal_gain_score'] += $aResult['subtotal_gain_score'];
           $aData['subtotal'] += $aResult['subtotal'];
           $aData['discount_amount'] += $aResult['discount_amount'];
           $aData['items_quantity'] += $aResult['items_quantity'];
           $aData['items_count'] += $aResult['items_count'];
           $aData['subtotal_weight'] += $aResult['subtotal_weight'];
           $aData['discount_amount_prefilter'] += $aResult['discount_amount_prefilter'];
           $aData['discount_amount_order'] += $aResult['discount_amount_order'];
           $aData['discount_amount'] = $aData['discount_amount_prefilter'] + $aData['discount_amount_order'];
       }

        if( $aData['cart_status']!=='false' ) {
            $sMinOrderAmount = app::get('b2c')->getConf('site.min_order_amount');
            if($sMinOrderAmount) {
                if($sMinOrderAmount > ($aData['subtotal']-$aData['discount_amount'])) {
                    $aData['cart_status'] = 'false';
                    $aData['cart_error_html'] = '订单未满起订金额！起订金额为：'. $sMinOrderAmount;
                }
            }
        }
        
   }

   // 购物车物品项render
   public function get_item_render() {
       $aResult = array();
       foreach(kernel::servicelist('b2c_cart_render_items_apps') as $object) {;
           if(!is_object($object)) continue;
           $aResult[$object->index] = (array) $object;
       }
       krsort($aResult);

       return $aResult;
   }
   
   
   // 购物车物品项render
   public function get_item_goods_render() {
       $aResult = array();
       foreach(kernel::servicelist('b2c_cart_render_items_goods_apps') as $object) {
           if(!is_object($object)) continue;
           $aResult[$object->index] = (array) $object;
       }
       krsort($aResult);

       return $aResult;
   }
   
   // 购物车物品项render
   public function get_item_other_render() {
       $aResult = array();
       foreach(kernel::servicelist('b2c_cart_render_items_other_apps') as $object) {  
           if(!is_object($object)) continue;    
           $aResult[$object->index] = (array) $object;
       }
       krsort($aResult);
       return $aResult;
   }

   // 优惠项render
   public function get_solution_render() {
       $aResult = array();
       foreach(kernel::servicelist('b2c_cart_render_solutions_apps') as $object) {

           $aResult[$object->index] = (array) $object;
       }
       
       arsort($aResult);
       return $aResult;
   }

   /**
    * 购物车是否为空
    *
    * @param array $aCart
    * @return boolean
    */
   public function is_empty($aCart) {
       if(!is_array($aCart)) return true;
       if(!isset($aCart['object'])) return true;
       if(empty($aCart['object'])) return true;
       $aKey = array_keys($aCart['object']);
       foreach($aKey as $key) {
           if(!empty($aCart['object'][$key])) return false;
       }
       return true;
   }

}
