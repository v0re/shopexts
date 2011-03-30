<?php
/**
 * ctl_cart 
 * 
 * @uses shopPage
 * @package 
 * @version $Id: ctl.cart.php 1952 2008-04-25 10:16:07Z flaboy $
 * @copyright 2003-2007 ShopEx
 * @author Wanglei <flaboy@zovatech.com> 
 * @license Commercial
 */
class ctl_cart extends shopPage{

    var $noCache = true;
    
    function ctl_cart(&$system){
        parent::shopPage($system);
        $this->_verifyMember(false);
        if(!$this->system->getConf('system.use_cart',true)){
            $system->responseCode(404);
            echo '<h1>cart has been disabled</h1>';
            exit();
        }
        $this->objCart = &$this->system->loadModel('trading/cart');
        $this->objCart->checkMember($this->member);
        $this->cart = $this->objCart->getCart('all');
        $this->products = $this->cart['g'];
        $this->pkggoods = $this->cart['p'];
        $this->gifts = $this->cart['f'];
    }

    function addPkgToCart($pkgId, $num=1) {
        $this->begin($this->system->mkUrl('package'));
        $aPkg['pkgid'] = $pkgId;
        $this->end($this->objCart->addToCart('p', $aPkg, $num), ' Successfully added ', $this->system->mkUrl('cart')); 
    }

    function addGiftToCart($giftId, $num=1) {
        if(!intval($num)) $num = 1;
        $aParams = $this->_addGift($giftId, $num);
        
        switch ($aParams){
            case 'less_point':
            $this->begin($this->system->mkUrl('gift','showList'));
            trigger_error('Your points is not enough',E_USER_ERROR);
            break;
            case 'less_store':
            $this->begin($this->system->mkUrl('gift','showList'));
            trigger_error('Inventory shortage Or purchase more than quota',E_USER_ERROR);
            break;
            case 'no_login';
            $this->begin($this->system->mkUrl('passport','login'));
            trigger_error(' Please Login ',E_USER_ERROR);
            default:
            $this->begin($this->system->mkUrl('gift','showList'));
            $this->objCart->addToCart('f', $aParams, $num);
            break;
        }
         $this->end(true, ' Successfully added ', $this->system->mkurl('cart')); 
    }
    
    function _addGift($giftId, $num=1){
        $aParams['gift_id'] = $giftId;
        $oGift = $this->system->loadModel('trading/gift');
        $aGiftInfo = $oGift->getGiftById($giftId);

        $aCart = $this->objCart->getCart('f');
        if ($aCart[$giftId]) {
            $nums = $aCart[$giftId]['num'] + $num;
        }else{
            $nums = $num;
        }
        if($GLOBALS['runtime']['member_lv']<=0){
            return 'no_login';
            exit;
        }
        if ($this->member['point'] < ($this->objCart->getCartCPoint()+$aGiftInfo['point']*$num)){//判断积分是否足够
            return 'less_point';
            exit;
        }
        if (!$oGift->isOnSale($aGiftInfo, $GLOBALS['runtime']['member_lv'], $nums)) {
            return 'less_store';
            exit;
        }
        return $aParams;
    }

    function addGoodsToCart($gid=0, $pid=0, $stradj='', $pmtid=0, $num=1) {
        $aParams = $this->_addGoods($_POST['goods'],$gid, $pid, $stradj, $pmtid);
        if($aParams['pid'] == -1){
            $this->begin($_SERVER['HTTP_REFERER']);
            trigger_error('Without this product',E_USER_ERROR);
            $this->end();
        }
        if(!intval($num)) $num = 1;
        if(intval($aParams['num'])) $num = intval($aParams['num']);
        $status = $this->objCart->addToCart('g', $aParams, $num);
        if($status === 'notify'){
            $this->begin($this->system->mkUrl("product","gnotify",array($gid, $pid)));
            $this->setError(10001);
            trigger_error('Lack of stocks, go to Arrival Notice page...',E_USER_ERROR);
            $this->end();
        }elseif(!$status){
            $this->begin($_SERVER['HTTP_REFERER']);
            $this->setError(10002);
            trigger_error('Lack of stocks',E_USER_ERROR);
            $this->end();
        }else{
            $this->redirect('cart');
        }
    }
    
    function _addGoods($aIn, &$gid, &$pid, $stradj='', $pmtid=0){
        if(is_array($aIn)){
            $gid = intval($aIn['goods_id']);
            $pid = intval($aIn['product_id']);
            $gnum = intval($aIn['num']);
            foreach($aIn['adjunct'] as $key => $aAdj){
                foreach($aAdj as $adjid => $num){
                    $stradj .= $adjid.'_'.$key.'_'.$num.'|';    //配件id、配件组、配件数量|
                }
                $pmtid = $aIn['pmt_id'];
            }
        }
        if (intval($pmtid) == 0) {
            $oPromotion = $this->system->loadModel('trading/promotion');
            $mlvid = intval($GLOBALS['runtime']['member_lv']);//会员id号
            $pmtid = $oPromotion->getGoodsPromotionId($gid, $mlvid);
        }
        if(intval($pid) == 0){
            $objGoods = $this->system->loadModel('trading/goods');
            $aP = $objGoods->getProducts($gid);
            $pid = $aP[0]['product_id'];
        }
        if($stradj == '' || $stradj === 0) $stradj = 'na';
        $aParams['gid'] = $gid;
        $aParams['pid'] = $pid;
        $aParams['adj'] = $stradj;
        $aParams['pmtid'] = $pmtid;
        $aParams['num'] = $gnum;
        return $aParams;
    }

    function ajaxAdd(){
        switch($_POST['type']){
            case 'g':
            $aParams = $this->_addGoods('', $_POST['gid'], $_POST['pid'], '', 0);
            break;
            case 'p':
            $aParams['pkgid'] = $_POST['gid'];
            break;
            case 'f':
            if(!intval($num)) $num = 1;
            $aParams = $this->_addGift($giftId, $num);
            if(!is_array($aParams)){
                $this->system->_succ = false;
                exit();
            }
            break;
        }
        
        if(!intval($_POST['num'])){
            $_POST['num'] = 1;
        }
        $this->objCart->addToCart($_POST['type'], $aParams, intval($_POST['num']));
        $this->system->_succ = true;
        exit();
    }

    function removeCart($objType='g'){
        $this->objCart->removeCart($objType, $_POST['cartNum'][$objType]);
        $this->cartTotal();
    }

    function updateCart($objType='g', $key=''){
        $key = str_replace('@', '-', $key);
        $nQuantity = $_POST['cartNum'][$objType][$key];
        switch($objType) {
            case 'f':
                $oCart->member['member_lv_id'] =$GLOBALS['runtime']['member_lv'];
                $oCart->member['point'] = $this->member['point'];
                break;
            case 'g':
                break;
            case 'p':
                break;
            default:
                break;
        }

        if(!$this->objCart->updateCart($objType, $key, $nQuantity,$aError)){
            echo implode('',$aError);
        }else{
            $this->cartTotal();
        }
    }
    
    function cartTotal(){
        $this->ctl_cart();
        $sale = $this->system->loadModel('trading/sale');
        $trading = $sale->getCartObject($this->cart,$GLOBALS['runtime']['member_lv'],true);
        $this->pagedata['trading'] = &$trading;
        $this->__tmpl = 'cart/cart_total.html';
        $this->output();
    }
    
    function index(){
        $this->title = 'Shopping Bag';
        $sale = $this->system->loadModel('trading/sale');
        $trading = $sale->getCartObject($this->cart,$GLOBALS['runtime']['member_lv'],true);
        $number=count($trading['products'])+count($trading['gift_e'])+count($trading['package']);
        if($number!=$_COOKIE['CART_COUNT']){
            $this->system->setCookie('CART_COUNT',$number);
        }
		//error_log(var_export($trading,true),3,__FILE__.".log");
		$extp = $this->system->loadModel('goods/extproducts');
		$extp->dumpUnMarketAble($trading);
        $this->pagedata['trading'] = &$trading;
        $cur = $this->system->loadModel('system/cur');
        $aCur = $cur->getFormat($this->system->request['cur']);
        $this->pagedata['currency'] = json_encode($aCur);
        
        header("Expires: -1");
        header("Pragma: no-cache");
        header("Cache-Control: no-cache, no-store");
       
        $this->output();
    }
    
    function view(){
        $sale = $this->system->loadModel('trading/sale');
        $this->pagedata['trading'] = $sale->getCartObject($this->cart,$GLOBALS['runtime']['member_lv'],true);
        $this->pagedata['cartCount'] = $this->objCart->getCartTotalNum($this->products['cart'], $this->pkggoods, $this->gifts);
        $this->__tmpl = 'cart/view.html';
        $this->output();
    }

    function merge($sType=0){
        switch($sType){
            case 0:
                $this->objCart->clearAll();    //清空member cart数据库
                $this->objCart->memberLogin = false;
                $aCart = $this->objCart->getCart();    //读取member cookie cart
                $this->objCart->memberLogin = true;
                $this->objCart->save('all', $aCart);
                $this->system->setcookie($oCart->cookiesName,'');
            break;
            
            case 1:
                $cartDb = $this->objCart->getCart();
                $this->objCart->memberLogin = false;
                $cartCookie = $this->objCart->getCart();
                if($cartCookie['g']['cart']){
                    $aCart['g']['cart'] = $cartCookie['g']['cart'];
                    if($cartDb['g']['cart']){
                        foreach($cartDb['g']['cart'] as $k => $num){
                            $aCart['g']['cart'][$k] = intval($num) + intval($aCart['g']['cart'][$k]);
                        }
                    }
                }else{
                    if($cartDb['g']['cart']){
                        $aCart['g']['cart'] = $cartDb['g']['cart'];
                    }
                }
                if($cartCookie['p']){
                    $aCart['p'] = $cartCookie['p'];
                    if($cartDb['p']){
                        foreach($cartDb['p'] as $k => $item){
                            $aCart['p'][$k]['num'] = intval($item['num']) + intval($aCart['p'][$k]['num']);
                        }
                    }
                }else{
                    if($cartDb['p']){
                        $aCart['p'] = $cartDb['p'];
                    }
                }
                if($cartCookie['f']){
                    $aCart['f'] = $cartCookie['f'];
                    if($cartDb['f']){
                        foreach($cartDb['f'] as $k => $item){
                            $aCart['f'][$k]['num'] = intval($item['num']) + intval($aCart['f'][$k]['num']);
                        }
                    }
                }else{
                    if($cartDb['f']){
                        $aCart['f'] = $cartDb['f'];
                    }
                }
                $this->objCart->memberLogin = true;
                $this->objCart->save('all',$aCart);
                $this->system->setcookie($oCart->cookiesName,'');
            break;
            
            case 2:
                $aCart = $this->objCart->getCart();    //读取member db cart
                $this->system->setcookie($oCart->cookiesName,'');
            break;
        }
        $this->objCart->setCartNum($aCart);
        header('Location: '.($_GET['forward']?$_GET['forward']:$this->system->base_url()));
        $this->system->_succ = true;
        exit;
    }

    /**
     * checkout 
     * 切记和admin/order:create保持功能上的同步
     * 
     * @access public
     * @return void
     */
    function checkout(){
        $this->title = 'Shipping & Payment';
        if(count($this->products['cart'])+count($this->pkggoods)+count($this->gifts) == 0){
            $this->redirect('cart');
        }

        if(!$this->system->getConf('security.guest.enabled',true)){
            $this->_verifyMember();
        }
        if(!$this->member['member_id'] && !$_COOKIE['ST_ShopEx-Anonymity-Buy']){
            $this->redirect('cart','loginBuy');
            $this->splash('failed',$this->system->mkUrl("passport","index"),
                    __('您未登录，是否需要 <a href="index.php?passport-checkout-index.html">登录</a> 后购买；
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    或者直接 <a href="'.$this->system->mkurl('cart','checkout').'" id="anonymityBuy">进入结算</a> ？'),'',20);
            exit;
        }
        $aOut = $this->objCart->getCheckout($this->cart, $this->member, $this->system->request['cur']);
        $this->pagedata['has_physical'] = $aOut['has_physical'];
        $this->pagedata['minfo'] = $aOut['minfo'];
        $this->pagedata['areas'] = $aOut['areas'];
        $this->pagedata['dlytime'] = date('Y-m-d', mktime()+floatval($this->system->getConf('site.delivery_time'))*3600*24);
        $this->pagedata['currencys'] = $aOut['currencys'];
        $this->pagedata['currency'] = $aOut['currency'];
        $payment = $this->system->loadModel('trading/payment');
        $payment->showPayExtendCon($aOut['payments']);
        $this->pagedata['payments'] = $aOut['payments'];
        $this->pagedata['trading'] = $aOut['trading'];
        
        if($this->member['member_id']){
            $member = $this->system->loadModel('member/member');
            $addrlist = $member->getMemberAddr($this->member['member_id']);
            foreach($addrlist as $rows){
                if(empty($rows['tel'])){
                    $str_tel = '手机：'.$rows['mobile'];
                }else{
                    $str_tel = '电话：'.$rows['tel'];
                }
                $addr[] = array('addr_id'=> $rows['addr_id'],'def_addr'=>$rows['def_addr'],'addr_region'=> $rows['area'],
                                'addr_label'=> $rows['addr'].' (收货人：'.$rows['name'].' '.$str_tel.' 邮编：'.$rows['zip'].')');
            }
            $this->pagedata['trading']['receiver']['addrlist'] = $addr;
            $this->pagedata['is_allow'] = (count($addr)<5 ? 1 : 0);
        }else{
            setcookie('S[ST_ShopEx-Anonymity-Buy]','',time()-1000);
        }
        $this->output();
    }
    
    function loginBuy(){
        $this->title = 'Sign in Or register';
        if($_COOKIE['CART_COUNT']>0&&$_COOKIE['UNAME']){
            $this->system->location($this->system->mkUrl('cart','checkout'));
        }
        if(count($_POST['form'])>0 && !$this->member && !$this->system->getConf('security.guest.enabled',false)){
            $this->pagedata['mustMember'] = true;
        }
        
        if($this->system->getConf('site.login_valide') == 'true' || $this->system->getConf('site.login_valide') == true){
            $this->pagedata['valideCode'] = true;
        }
        $this->pagedata['options']['url'] = $this->system->mkUrl("cart","checkout");
        if($this->system->getConf('site.register_valide') == true || $this->system->getConf('site.register_valide') == 'true'){
            $this->pagedata['SignUpvalideCode'] = true;
        }
        if($this->system->getConf('site.login_valide') == true || $this->system->getConf('site.login_valide') == 'true'){
            $this->pagedata['LogInvalideCode'] = true;
        }
        $this->output();
    }
    
    function shipping(){
        $sale = $this->system->loadModel('trading/sale');
        $trading = $sale->getCartObject($this->cart,$GLOBALS['runtime']['member_lv'],true);
        $shipping = $this->system->loadModel('trading/delivery');
        $aShippings = $shipping->getDlTypeByArea($_POST['area']);
        foreach($aShippings as $k=>$s){
            $aShippings[$k]['price'] = cal_fee($s['expressions'],$trading['weight'],$trading['pmt_b']['totalPrice'],$s['price']);
            $s['pad']==0?$aShippings[$k]['has_cod'] = 0:$aShippings[$k]['has_cod'] = 1;
            if($s['protect']==1){
                $aShippings[$k]['protect'] = true;
            }else{
                $aShippings[$k]['protect'] = false;
            }
        }
        $this->pagedata['shippings'] = $aShippings;
        $this->__tmpl='cart/checkout_shipping.html';
        $this->output();
    }
    
    function payment($type=''){
        $payment = $this->system->loadModel('trading/payment');
        $oCur = $this->system->loadModel('system/cur');
        $this->pagedata['payments'] = $payment->getByCur($_POST['cur'], $type);
        $this->pagedata['delivery']['has_cod'] = $_POST['d_pay'];
        $this->pagedata['order']['payment'] = $_POST['payment'];
        //todo 需要确定支付费率的需求
        $this->__tmpl='common/paymethod.html';
        $this->output();
    }
    
    function total(){
        
        $tarea = explode(':', $_POST['area'] );
        $_POST['area'] = $tarea[count($tarea)-1];
        $this->pagedata['trading'] = $this->objCart->checkoutInfo($this->cart, $this->member, $_POST);
        $this->__tmpl='cart/checkout_total.html';
        $this->output();
    }
    
    function removeCoupon() {
        $this->objCart->removeCart('c');
        echo "<html><header><meta http-equiv=\"refresh\" content=\"0; url=".$this->system->mkUrl("cart","index")."\"></header></html>";
    }

    function applycoupon(){
      $this->begin($this->system->mkUrl('cart','index'),null,E_ERROR | E_USER_ERROR | E_USER_WARNING);  
        $oCoupon = $this->system->loadModel('trading/coupon');
        if (!empty($_POST['coupon'])) {
            $oSale = $this->system->loadModel('trading/sale');    
            $oPromotion = $this->system->loadModel('trading/promotion');
            $trading = $oSale->getCartObject($this->cart, $GLOBALS['runtime']['member_lv'], true);
            if ($trading['ifCoupon']) {
                $oPromotion->apply_coupon_pmt($trading, $_POST['coupon'], $GLOBALS['runtime']['member_lv']);
            }else{
//                $this->setError('10000');
                trigger_error('Promotions are not allowed to use coupons during the event',E_USER_ERROR);
            }
        }else{
            trigger_error('Please enter a Coupon code',E_USER_ERROR);
        }

          $this->end(true, 'Successfully added', $this->system->mkUrl('cart', 'index'));  
    }
    
    function getReceiverList(){
        $oMem = $this->system->loadModel('member/member');
        $this->pagedata['receiver'] = $oMem->getMemberAddr($this->member['member_id']);
        
        $this->__tmpl='common/dialog_receiver.html';
        $this->output();
    }
    
    function getAddr(){
        if($_GET['addr_id']){
            $oMem = $this->system->loadModel('member/member');
            $this->pagedata['trading']['receiver'] = $oMem->getAddrById($_GET['addr_id']);
        }
        $areaId = explode(':',$this->pagedata['trading']['receiver']['area']);
        $areaId = $areaId[count($areaId)-1];
        $this->pagedata['trading']['member_id'] = $this->member['member_id'];
        $this->__tmpl='common/rec_addr.html';
        $this->output();
    }
}
?>