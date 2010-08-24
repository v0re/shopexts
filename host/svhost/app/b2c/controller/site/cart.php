<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
/**
 * ctl_cart
 *
 * @uses b2c_frontpage
 * @package
 * @version $Id: ctl.cart.php 1952 2008-04-25 10:16:07Z flaboy $
 * @copyright 2003-2007 ShopEx
 * @author Wanglei <flaboy@zovatech.com>
 * @license Commercial
 */
class b2c_ctl_site_cart extends b2c_frontpage{

    var $customer_template_type='cart';
    var $noCache = true;

    public function __construct(&$app) {
        
        parent::__construct($app);
        $this->set_tmpl('cart');
        $this->mCart = $this->app->model('cart');
        $this->_response->set_header('Cache-Control', 'no-store');
        //$this->arr_member_info = $this->get_current_member();
        
        $this->member_status = $this->check_login();
        $this->objMath = kernel::single("ectools_math");
    }

    public function index(){
        if( !isset($this->guest_enabled) )
            $this->guest_enabled = $this->app->getConf('security.guest.enabled');
        $this->pagedata['guest_enabled'] = $this->guest_enabled;
        $this->_common();
        $this->_response->set_header('Cache-Control','no-store');
        $this->page('site/cart/index.html');
    }

    // 添加
    public function addToCart() {
        $aData = $this->getData();
        
        //快速购买
        if(isset($aData[1]) && $aData[1] == 'quick' && empty($this->member_status)) $this->redirect(array('app'=>'b2c','ctl'=>'site_cart','act'=>'checkout'));
        
        
        if(isset($aData[0]) && !empty($aData[0])) {
            $oCartObject = $this->app->model('cart_objects');
            $status = $oCartObject->add_object($aData,$aData[0]);

            if(!$status){
                $this->begin(array('app'=>'b2c', 'ctl'=>'site_cart', 'act'=>'index'));
                if($_POST['mini_cart']){
                    $this->_response->set_http_response_code(404);
                } else {
                    if($aData[0]=='coupon') {
                        $msg = __('优惠券无效！');
                    } else {
                        $msg = __('加入购物车失败: 商品库存不足！！');
                    }
                    //trigger_error(__('加入购物车失败: 商品库存不足或者提交参数错误！'),E_USER_ERROR);
                    $this->end(false, $msg);
                }
            } else {
                
                if(isset($aData[1]) && $aData[1] == 'quick') {
                    if(!$this->member['member_id'] && !$_COOKIE['ST_ShopEx-Anonymity-Buy']){
                        $this->page('site/cart/loginbuy_fast.html', true);
                        return;
                    }
                    
                    $this->checkout();
                }else{
                    if($_POST['mini_cart']){
                        $arr = $this->app->model("cart")->get_objects();
                        $temp = $arr['_cookie'];
                        
                        $this->pagedata['cartCount']      = $temp['CART_COUNT'];
                        $this->pagedata['cartNumber']     = $temp['CART_NUMBER'];
                        $this->pagedata['cartTotalPrice'] = $temp['CART_TOTAL_PRICE'];
                        $this->page('site/cart/mini_cart.html', true);
                        return;
                    }
                    $this->redirect(array('app'=>'b2c', ctl=>'site_cart'));
                }
            }
        
        
        }
        // 快速购买
        //if(isset($aData[1]) && $aData[1] == 'quick') $this->redirect('b2c','site_cart','checkout');
        //$this->redirect('b2c','site_cart');
        //$this->redirect('b2c','site_cart_mini_cart');
        //echo $this->fetch('site/cart/mini_cart.html');
    }


    // 修改
    public function updateCart() {
        $aParams = $this->_request->get_params(true);
        $mCartObject = $this->app->model('cart_objects');
        if($aParams['modify_quantity']){
            
            $aCart = $this->mCart->get_basic_objects();
            foreach($aCart as $row){
                // 值不同的才修改咯
                if( isset($aParams['modify_quantity'][$row['obj_ident']]) ) {
                    $temp = $aParams['modify_quantity'][$row['obj_ident']];
                    $flag = $this->_v_cart_object($temp, $row);
                    if(!$flag) {
                        $_flag = $mCartObject->update_object($row['obj_type'],  $row['obj_ident'],$aParams['modify_quantity'][$row['obj_ident']]);
                        if( !$_flag ) {
                            // || (isset($flag['end']['status']) && $flag['end']['status']===false)
                            $this->_response->set_http_response_code(404);return;
                        } else if ( isset($_flag['status']) && $_flag['status']===false ) {
                           // echo json_encode( $_flag );return;
                        }
                    }
                }
            }// foreach
        }
        //cart页面 提示 无 
        //exit;
        $this->_cart_main();
    }
    
    
    private function _v_cart_object ($temp, $row) {
        if( $temp['quantity'] == $row['quantity'] ) {
            $flag = true;
            if( isset($row['params']['adjunct']) && is_array($row['params']['adjunct']) ) {
                foreach( $row['params']['adjunct'] as $adjunct ) {
                    if( !isset($adjunct['adjunct']) || !is_array($adjunct['adjunct']) ) continue;
                    foreach( $adjunct['adjunct'] as $p_id => $p_quantity ) {
                        if($temp[$adjunct['group_id']][$p_id]['quantity']!=$p_quantity) $flag = false;
                    }
                }
            }
        }
        return $flag;
    }
    
    

    // 删除&清空
    public function removeCart() {
        $aParams = $this->_request->get_params(true);
        $mCartObject = $this->app->model('cart_objects');
        $this->ajax_html = true;  //用于返回页面识别。当无商品是跳转至cart_empty
        // 清空购物车
        if($aParams[0] == 'all' || empty($aParams['modify_quantity'])) {
            $obj_type = null;
            $mCartObject->remove_object(); // 不带入参清空所有的
        } else {
            if($aParams['modify_quantity']){
                $aCart = $this->mCart->get_basic_objects();
                foreach($aCart as $row){
                    if(!isset($aParams['modify_quantity'][$row['obj_ident']])) {
                        $mCartObject->remove_object($row['obj_type'], $row['obj_ident']);
                    } else if( isset($aParams['modify_quantity'][$row['obj_ident']]) ) {
                        $temp = $aParams['modify_quantity'][$row['obj_ident']];
                        $flag = $this->_v_cart_object($temp, $row);
                        if(!$flag) {
                            if(!$mCartObject->update_object($row['obj_type'],  $row['obj_ident'],$aParams['modify_quantity'][$row['obj_ident']])) {
                                $this->_response->set_http_response_code(404);return;
                            }
                        }
                    }
                }// foreach
            }
        }
        $this->_cart_main();
    }

    private function _cart_main() {
       $this->_common();
       $this->page('site/cart/cart_main.html', true);
    }

    private function _common() {
        // 购物车数据信息
        
        $aCart = $this->mCart->get_objects($this->_request->get_params(true));
        $this->pagedata['aCart'] = $aCart;
        //print_r($aCart);exit;

        
        // 购物车是否为空
        $this->pagedata['is_empty'] = $this->mCart->is_empty($aCart);
        //ajax_html 删除单个商品是触发
        if($this->ajax_html && $this->mCart->is_empty($aCart)) {
            $this->page('site/cart/cart_empty.html', true);
            return ;
        }
        
        // 购物车数据项的render
        $this->pagedata['item_section'] = $this->mCart->get_item_render();
        
        // 购物车数据项的render
        $this->pagedata['item_goods_section'] = $this->mCart->get_item_goods_render();
        
        // 购物车数据项的render
        $this->pagedata['item_other'] = $this->mCart->get_item_other_render();
        //print_r($this->mCart->get_item_other_render());exit;
        
        // 优惠信息项render
        $this->pagedata['solution_section'] = $this->mCart->get_solution_render();
    }

    /**
     * checkout
     * 切记和admin/order:create保持功能上的同步
     *
     * @access public
     * @return void
     */
    public function checkout($isfastbuy=0)
    {
        // 判断顾客登录方式.
        $login_type = $this->app->getConf('site.login_type');
        $is_member_buy = $this->app->getConf('security.guest.enabled');
        $arrMember = $this->get_current_member();
        
        if (($login_type == 'href' && !$arrMember['member_id'] && $_COOKIE['S']['ST_ShopEx-Anonymity-Buy'] != 'true') || $is_member_buy != 'true')
            $this->redirect(array('app'=>'b2c','ctl'=>'site_cart','act'=>'loginbuy','arg0'=>'1'));
        $this->_common();
        $this->begin(array('app'=>'b2c','ctl'=>'site_cart','act'=>'index'));
        
        // 购物车是否为空
        if ($this->pagedata['is_empty'])
        {
            $this->end(false, '购物车为空！');
            //$this->redirect($this->app->app_id, 'site_cart', 'index');
        }
        // 删除cookie.
        setcookie("S[ST_ShopEx-Anonymity-Buy]", "false", time() - 3600);
        
        // 购物是否满足起订量和起订金额
        if ((isset($this->pagedata['aCart']['cart_status']) && $this->pagedata['aCart']['cart_status'] == 'false') && (isset($this->pagedata['aCart']['cart_error_html']) && $this->pagedata['aCart']['cart_error_html'] != ""))
        {
            $this->end(false, $this->pagedata['aCart']['cart_error_html']);
        }
        
        //$this->end(true, '开始结账！', array('app'=>'b2c','ctl'=>'site_cart','act'=>'checkout_result'));
        $this->checkout_result();
    }
    
    /**
     * checkout 结果页面
     * @params int 
     * @return null
     */
    public function checkout_result($isfastbuy=0)
    {
        // 初始化购物车数据
        $this->_common();
        
        $this->pagedata['checkout'] = 1;
        
        // 如果会员已登录，查询会员的信息
        $arrMember = $this->get_current_member();
        $obj_member_addrs = $this->app->model('member_addrs');
        $addr = array();
        $member_point = 0;
        if ($arrMember['member_id'])
        {
            $addrMember = array(
                'member_id' => $arrMember['member_id'],
            );
            $addrlist = $obj_member_addrs->getList('*',array('member_id'=>$arrMember['member_id']));
            foreach($addrlist as $rows)
            {
                if(empty($rows['tel'])){
                    $str_tel = __('手机：').$rows['mobile'];
                }else{
                    $str_tel = __('电话：').$rows['tel'];
                }
                $addr[] = array('addr_id'=> $rows['addr_id'],'def_addr'=>$rows['def_addr'],'addr_region'=> $rows['area'],
                                'addr_label'=> $rows['addr'].__(' (收货人：').$rows['name'].' '.$str_tel.__(' 邮编：').$rows['zip'].')');

            }
            
            // 得到当前会员的积分
            $obj_members = $this->app->model('members');
            $arr_member = $obj_members->dump($arrMember['member_id'], 'point');
            $member_point = $arr_member['point'];
        }
        
        $this->pagedata['addrlist'] = $addr;
        $this->pagedata['address']['member_id'] = $arrMember['member_id'];

        $currency = app::get('ectools')->model('currency');
        $this->pagedata['currencys'] = $currency->getList('cur_id,cur_code,cur_name');
        $arrDefCurrency = $currency->getDefault();
        $strDefCurrency = $arrDefCurrency['cur_code'];
        $aCur = $currency->getcur($strDefCurrency);
        $this->pagedata['current_currency'] = $strDefCurrency;

        $obj_payments = new b2c_payment_getlist();
        $this->pagedata['payment_html'] = $obj_payments->get_view($this);
   
        // 得到税金的信息
        $this->pagedata['trigger_tax'] = $this->app->getConf("site.trigger_tax");
        $this->pagedata['tax_ratio'] = $this->app->getConf("site.tax_ratio");
        
        $demical = $this->app->getConf('system.money.operation.decimals');
        
        $total_item = $this->objMath->number_minus(array($this->pagedata['aCart']["subtotal"], $this->pagedata['aCart']['discount_amount_prefilter']));
        // 取到商店积分规则
        $policy_method = $this->app->getConf("site.get_policy.method");
        switch ($policy_method)
        {
            case '1':
                $subtotal_consume_score = 0;
                $subtotal_gain_score = 0;
                $totalScore = 0;
                break;
            case '2':
                $subtotal_consume_score = round($this->pagedata['aCart']['subtotal_consume_score']);
                $policy_rate = $this->app->getConf('site.get_rate.method');
                $subtotal_gain_score = round($this->objMath->number_plus(array(0, $this->pagedata['aCart']['subtotal_gain_score'])));
                $totalScore = round($this->objMath->number_minus(array($subtotal_gain_score, $subtotal_consume_score)));
                break;
            case '3':
                $subtotal_consume_score = round($this->pagedata['aCart']['subtotal_consume_score']);
                $subtotal_gain_score = round($this->pagedata['aCart']['subtotal_gain_score']);
                $totalScore = round($this->objMath->number_minus(array($subtotal_gain_score, $subtotal_consume_score)));
                break;
            default:
                $subtotal_consume_score = 0;
                $subtotal_gain_score = 0;
                $totalScore = 0;
                break;
        }
            
        $total_amount = $this->objMath->number_minus(array($this->pagedata['aCart']["subtotal"], $this->pagedata['aCart']['discount_amount']));
        if ($total_amount < 0)
            $total_amount = 0;
        // 得到cart total支付的信息
        $this->pagedata['order_detail'] = array(
            'cost_item' => $total_item,
            'total_amount' => $total_amount,
            'currency' => $this->app->getConf('site.currency.defalt_currency'),
            'pmt_amount' => $this->pagedata['aCart']['discount_amount'],
            'totalConsumeScore' => $subtotal_consume_score,
            'totalGainScore' => $subtotal_gain_score,
            'totalScore' => $totalScore,
            'cur_code' => $strDefCurrency,
            'cur_display' => $strDefCurrency,
            'cur_rate' => $aCur['cur_rate'],
            'final_amount' => $currency->changer($total_amount, $this->app->getConf("site.currency.defalt_currency"), true),
        );
        
        if ($arrMember['member_id'])
        {
            $this->pagedata['order_detail']['totalScore'] = $member_point;
        }
        else
        {
            $this->pagedata['order_detail']['totalScore'] = 0;
        }
        
        $odr_decimals = $this->app->getConf('system.money.decimals');
        $total_amount = $this->objMath->get($this->pagedata['order_detail']['total_amount'], $odr_decimals);        
        $this->pagedata['order_detail']['discount'] = $this->objMath->number_minus(array($this->pagedata['order_detail']['total_amount'], $total_amount));
        $this->pagedata['order_detail']['total_amount'] = $total_amount;
        $this->pagedata['order_detail']['current_currency'] = $strDefCurrency;
        
        // 获得商品的赠品信息
        $arrM_info = array();
        foreach ($this->pagedata['aCart']['object']['goods'] as $arrGoodsInfo)
        {
            if (isset($arrGoodsInfo['gifts']) && $arrGoodsInfo['gifts'])
            {
                $this->pagedata['order_detail']['gift_p'][] = array(
                    'storage' => $arrGoodsInfo['gifts']['storage'],
                    'name' => $arrGoodsInfo['gifts']['name'],
                    'nums' => $arrGoodsInfo['gifts']['nums'],
                );
            }
            
            // 得到商品购物信息的必填项目
            $goods_id = $arrGoodsInfo['obj_items']['products'][0]['goods_id'];
            $product_id = $arrGoodsInfo['obj_items']['products'][0]['product_id'];
            // 得到商品goods表的信息
            $objGoods = $this->app->model('goods');
            $arrGoods = $objGoods->dump($goods_id, 'type_id');
            if (isset($arrGoods) && $arrGoods && $arrGoods['type']['type_id'])
            {
                $objGoods_type = $this->app->model('goods_type');
                $arrGoods_type = $objGoods_type->dump($arrGoods['type']['type_id'], '*');
                
                if ($arrGoods_type['minfo'])
                {
                    $arrM_info[$product_id]['name'] = $arrGoodsInfo['obj_items']['products'][0]['name'];
                    $arrM_info[$product_id]['nums'] = $arrGoodsInfo['obj_items']['products'][0]['quantity'];
                    foreach ($arrGoods_type['minfo'] as $arr_minfo)
                    {
                        $arrM_info[$product_id]['minfo'][] = $arr_minfo;
                    }
                    
                }
            }
        }
        $this->pagedata['minfo'] = $arrM_info;
        
        $this->page('site/cart/checkout.html');
    }

    public function getAddr(){
        $obj_addr = new b2c_member_addrs();
        $addr_id = intval($_GET['addr_id']);
        echo $obj_addr->get_receive_addr($this,$addr_id);exit;
    }

    public function shipping(){
        $this->_common();
        $area_id = ($_POST['area']);
        $obj_delivery = new b2c_order_dlytype();
        $sdf = array();
        $sdf = $this->pagedata['aCart'];
        echo $obj_delivery->select_delivery_method($this,$area_id,$sdf);exit;
    }

    public function payment(){
        //$this->_get_cart();
        $obj_payment_select = new ectools_payment_select();
        $sdf = $_POST;
        echo $obj_payment_select->select_pay_method($this, $sdf);exit;
    }

    public function total()
    {
        $this->_common();
        $obj_total = new b2c_order_total();
        $sdf_order = $_POST;
        $arrMember = $this->get_current_member();
        $sdf_order['member_id'] = $arrMember['member_id'];
        $arr_cart_object = $this->mCart->get_objects(true);
        //$sdf_order = array('payment'=>$_POST['payment'],'shipping_id'=>$_POST['shipping_id'],'is_protect'=>$_POST['is_protect'],'currency'=>$_POST['cur'], 'cart'=>$this->pagedata['aCart'], 'is_tax'=>$_POST['is_tax'],'tax_company'=>$_POST['tax_company'],'area'=>$_POST['area']);
        echo $obj_total->order_total_method($this,$arr_cart_object,$sdf_order);exit;
    }    
    
    //widgets cart
    public function view(){
        $oCart = $this->app->model("cart_objects");
        $aData = $oCart->setCartNum();
        $this->pagedata['trading'] = $aData['trading'];
        $this->pagedata['cartCount'] = $aData['CART_COUNT'];
        $this->pagedata['cartNumber'] = $aData['CART_NUMBER'];
        $tpl = 'site/cart/view.html';
        $this->page($tpl, true);
    }
    
    
    
    
    function getData() {
        $data = $this->_request->get_params(true);
        if(!$data['goods']) {
            $data['goods']['goods_id'] = $data[1];
            unset($data[1]);
            $data['goods']['product_id'] = ($data[2]=='false' ? 0 : $data[2]);
            unset($data[2]);
            $data['goods']['num'] = $data[3];
            unset($data[3]);
            
            //未完待续 针对列表页。不知又没  配件
            //$data['goods']['adjunt'] = $daata[4];
        }
        return $data;
    }
    
    
    
    public function loginBuy($isfastbuy=0)
    {
        $this->pagedata['guest_enabled'] = $this->app->getConf('security.guest.enabled');
        $this->__tmpl = 'site/cart/loginbuy_fast.html';
        $this->pagedata['no_right'] = 1;
        $oAP = $this->app->controller('site_passport');
        $oAP->login(1);
        $this->pagedata['toUrl'] = base64_encode($this->gen_url(array('app'=>'b2c','ctl'=>'site_cart','act'=>'checkout')));
        
        if (!$isfastbuy)
            $this->page($this->__tmpl, true);
        else
            $this->page($this->__tmpl);
//        $this->title = __('用户登陆或注册');
//        if($_COOKIE['CART_COUNT']>0&&$_COOKIE['UNAME']){
//            //$this->system->location($this->system->mkUrl('cart','checkout'));
//        }
///**
//        if($this->system->getConf('site.login_valide') == 'true' || $this->system->getConf('site.login_valide') == true){
//            $this->pagedata['valideCode'] = true;
//        }
//        $this->pagedata['options']['url'] = $this->system->mkUrl("cart","checkout");
//        if($this->system->getConf('site.register_valide') == true || $this->system->getConf('site.register_valide') == 'true'){
//            $this->pagedata['SignUpvalideCode'] = true;
//        }
//        if($this->system->getConf('site.login_valide') == true || $this->system->getConf('site.login_valide') == 'true'){
//            $this->pagedata['LogInvalideCode'] = true;
//        }
//        $appmgr = $this->system->loadModel('system/appmgr');
//        $login_plugin = $appmgr->getloginplug();
//
//        $this->pagedata['mustMember'] = !$this->system->getConf('security.guest.enabled');
//        if($isfastbuy){
//            $this->pagedata['isfastbuy'] = $isfastbuy;
//        }
//        $_POST['isfastbuy'] = $isfastbuy;
//        $this->pagedata['to_buy'] = true;
////*/
//        //if($_GET['mini_passport']){
//            $this->__tmpl = 'site/cart/loginbuy_fast.html';
//            /**
//            foreach($login_plugin as $key =>$value){
//            $object = $appmgr->instance_loginplug($value);
//                if(method_exists($object,'getMiniHtml')){
//                    $this->pagedata['mini_login_content'][] = $object->getMiniHtml();
//                }
//            }
//            //*/
//        //}else{
//            /**
//            foreach($login_plugin as $key =>$value){
//            $object = $appmgr->instance_loginplug($value);
//            if(method_exists($object,'getCartHtml')){
//                $this->pagedata['cart_login_content'][] = $object->getCartHtml();
//            }
//            //*/
//        //}
//
//        //$this->pagedata['ref_url'] = $this->system->mkUrl('cart','checkout',array($isfastbuy));
    }
    
    
 


}
