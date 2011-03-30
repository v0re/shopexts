<?php
/********************************************************************************************
购物车管理

COOKIE Array
[goods][cart][gid-pid-adj]=num
[goods][pmt][gid] = pmt
@g.gid-pid-adj-num,gid-pid-adj-num;gid-pmtid,gid-pmtid@f.@p.@c.
//说明:@g.(购物车内容)gid-pid-adj-num,gid-pid-adj-num;(PMT内容)gid-pmtid,gid-pmtid
//adj = 配件id_配件组_配件数量|
['gift']
g.gift_id-na-num_pmtid

['c'] pmt_id type:o订单/g商品

**********************************************************************************************/

class mdl_cart extends modelFactory{

    var $cookiesName = 'CART';
    var $memberLogin = false;
    function checkMember($aMember){
        $this->memInfo = $aMember;
        if($aMember['member_id'] && $aMember['uname']){
            $this->memberLogin = true;
        }
    }
    
    function _checkStore($pid, $num=1){
        $objProduct = $this->system->loadModel('goods/products');
        $aStore = $objProduct->getFieldById($pid);
        if(!is_null($aStore['store'])){
            $gStore = intval($aStore['store']) - intval($aStore['freez']);
            if($gStore < $num){
                return false;
                exit;
            }
        }
        return true;
    }
    
    function _checkGoodsStore($pid, $num=1){
        $objGoods = $this->system->loadModel('trading/goods');
        $aStore = $objGoods->getFieldById($pid);
        if(!is_null($aStore['store'])){
            $gStore = intval($aStore['store']) - intval($aStore['freez']);
            if($gStore < $num){
                return false;
                exit;
            }
        }
        return true;
    }
    
    function _verifyObjType($objType) {
        $_allObjType = array('g', 'p', 'f', 'c');
        return in_array($objType, $_allObjType);
    }

    function addToCart($objType='g', &$aParams, $quantity=1){
        switch($objType){
            case 'g':
                if($aParams['gid'] > 0 && $aParams['pid'] > 0 && $quantity > 0){
                    $cartKey = $aParams['gid'].'-'.$aParams['pid'].'-'.$aParams['adj'];
                    $aCart = $this->getCart('g');
                    if (isset($aCart['cart'][$cartKey])){
                        $aCart['cart'][$cartKey] += $quantity;
                        $buyStatus = 1;
                    }else{
                        $aCart['cart'][$cartKey] = $quantity;
                        $buyStatus = 0;
                    }
                    if($aParams['pmtid'] > 0) $aCart['pmt'][$aParams['gid']] = $aParams['pmtid'];

                    if(!$this->_checkStore($aParams['pid'], $aCart['cart'][$cartKey])){
                        if($buyStatus == 1){
                            $this->setError(10001);
                            trigger_error(' Lack of stocks ',E_USER_NOTICE);
                            return false;
                        }else{
                            return 'notify';
                        }
                        exit;
                    }
        
                    if($stradj != 'na'){
                        $aAdj = explode('|', $aParams['adj']);
                        foreach($aAdj as $val){
                            $adjItem = explode('_', $val);
                            if($adjItem[0]>0 && $adjItem[2]>0){
                                if(!$this->_checkStore($adjItem[0], $adjItem[2]*$aCart['cart'][$cartKey])){
                                    $this->setError(10001);
                                    trigger_error('Lack of matching stocks',E_USER_NOTICE);
                                    return false;
                                }
                            }
                        }
                    }
                    return $this->save('g', $aCart);
                }else{
                    $this->setError(10001);
                    trigger_error('参数错误!',E_USER_NOTICE);
                    return false;
                }
            break;
            case 'p':
                if($aParams['pkgid']){
                    $aCart = $this->getCart('p');
                    $aCart[$aParams['pkgid']]['num'] += $quantity;
                    if (!$this->_checkGoodsStore($aParams['pkgid'], $aCart[$aParams['pkgid']]['num'])) {
                        $this->setError('10000');
                        trigger_error(' Lack of package stocks ',E_USER_ERROR);
                        return false;
                    }
                    return $this->save('p', $aCart);
                }else{
                    $this->setError(10001);
                    trigger_error('参数错误!',E_USER_NOTICE);
                    return false;
                }
            break;
            case 'f':
                if ($aParams['gift_id']) {
                    $aCart = $this->getCart('f');
                    $aCart[$aParams['gift_id']]['num'] += $quantity;
                    return $this->save('f', $aCart);
                }else{
                    $this->setError(10001);
                    trigger_error('参数错误!',E_USER_NOTICE);
                    return false;
                }
            break;
            case 'c':
                //todo判断coupon 的有效性
                //暂时强行规定一个购物车，有且只使用一张优惠券
                if (is_array($aParams)&&count($aParams==1)) {
                    foreach ($aParams as $k=>$c) {
                        $cart_c[$k] = array('type' => $c['type'],
                                            'pmt_id' => $c['pmt_id']);
                    }
                    return $this->save('c', $cart_c);
                }else{
                    $this->setError(10001);
                    trigger_error('参数错误!',E_USER_NOTICE);
                    return false;
                }
            break;
        }
    }

    function updateCart($objType='g', $cartKey, $quantity, &$aMsg){
        
        $quantity = intval($quantity);
        if($quantity < 1){
            $aMsg[] = 'The quantity is invalid, please modify.';
//            trigger_error('输入更新数量不合法',E_USER_NOTICE);
            return false;
        }
        
        switch($objType){
            case 'g':
                $aTemp = explode('-', $cartKey);
                $goodsid = $aTemp[0];
                $productid = $aTemp[1];
                $stradj = $aTemp[2];
                if($goodsid > 0 && $productid > 0 && $quantity > 0){
                    $aCart = $this->getCart($objType);
                    $aCart['cart'][$cartKey] = $quantity;
                    if(!$this->_checkStore($productid, $aCart['cart'][$cartKey])){
                        $aMsg[] = ' Lack of   stocks ';
//                        $this->setError(10001);
//                        trigger_error('库存不足',E_USER_NOTICE);
                        return false;
                    }
        
                    if($stradj != 'na'){
                        $aAdj = explode('|', $stradj);
                        foreach($aAdj as $val){
                            $adjItem = explode('_', $val);
                            if($adjItem[0]>0 && $adjItem[2]>0){
                                if(!$this->_checkStore($adjItem[0], $adjItem[2]*$aCart['cart'][$cartKey])){
                                    $aMsg[] = 'Lack of matching stocks';
//                                    $this->setError(10001);
//                                    trigger_error('配件库存不足',E_USER_NOTICE);
                                    return false;
                                }
                            }
                        }
                    }
                    return $this->save($objType, $aCart);
                }else{
                    $aMsg[] = '参数错误';
//                    $this->setError(10001);
//                    trigger_error('参数错误!',E_USER_NOTICE);
                    return false;
                }
                break;
            case 'p':
                if($quantity > 0){
                    $aCart = $this->getCart('p');
                    $aCart[$cartKey]['num'] = $quantity;
                    if (!$this->_checkGoodsStore($cartKey, $aCart[$cartKey]['num'])) {
                        $aMsg[] = 'Lack of package stocks';
//                        $this->setError('10000');
//                        trigger_error('捆绑商品数量不足',E_USER_ERROR);
                        return false;
                    }
                    return $this->save('p', $aCart);
                }else{
//                    $this->setError(10001);
                    $aMsg[] = '参数错误';
//                    trigger_error('参数错误!',E_USER_NOTICE);
                    return false;
                }
            break;
            case 'f':
                $oGift = $this->system->loadModel('trading/gift');
                $aGiftInfo = $oGift->getGiftById($cartKey);
                
                if (intval($cartKey)>0) {
                    if ($oGift->isOnSale($aGiftInfo, $this->memInfo['member_lv_id'], $quantity)) {
                        if ($this->memInfo['point']>=$aGiftInfo['point']*$quantity){//判断积分是否足够
                            $aCart = $this->getCart($objType);
                            $aCart[$cartKey]['num'] = $quantity;
                            return $this->save($objType, $aCart);
                        }else{
                            $aMsg[] = 'Your points is not enough';
//                            trigger_error('用户积分不足',E_USER_ERROR);
                            return false;
                        }
                    }else{
                        $aMsg[] = 'Inventory shortage Or purchase more than quota';
//                        trigger_error('库存不足/购买数量超过限定数量/过期/超过最大购买限额',E_USER_ERROR);
                        return false;
                    }
                }
            break;
        }
    }

    function removeCart($objType='all', $aGoods){
        switch($objType){
            case 'g':
            if (is_array($aGoods) && !empty($aGoods)) {
                $aCart = $this->getCart($objType);
                foreach ($aCart['cart'] as $strKey => $v){
                    if(!$aGoods[$strKey]) unset($aCart['cart'][$strKey]);
                }
            }else{
                $aCart=array();
            }
            return $this->save('g', $aCart);
            break;
            case 'p':
            if (is_array($aGoods) && !empty($aGoods)) {
                $aCart = $this->getCart('p');
                foreach ($aCart as $goodsId => $v) {
                    if(!$aGoods[$goodsId]) unset($aCart[$goodsId]);
                }
            }else{
                $aCart=array();
            }
            return $this->save('p', $aCart);
            break;
            case 'f':
                //todo 需要bryant调整
                if (is_array($aGoods) && !empty($aGoods)) {
                    $aCart = $this->getCart('f');
                    foreach ($aCart as $giftId => $v) {
                        if(!$aGoods[$giftId]) unset($aCart[$giftId]);
                    }
                }else{
                    $aCart=array();
                }
                return $this->save('f', $aCart);
            break;
            case 'c':
                $aCart = $this->getCart();
                unset($aCart['c']);
                $this->save('all', $aCart);
            break;
            case 'all':
                return $this->clearAll();
            break;
        }
    }

    function clearAll() {
        if($this->memberLogin){
            $oMember = $this->system->loadModel('member/member');
            $oMember->saveCart($this->memInfo['member_id'], '');    //插入用户数据库addon['cart']
        }else{
            $this->system->setcookie($this->cookiesName,'');
        }
        $this->setCartNum($nullCart);
    }

    function getCartCPoint() {
        $aCart = $this->getCart('f');
        $giftIds = array_keys($aCart);
        $nums = array_item($aCart, 'num');
        $count = 0;
        if ($giftIds) {
            $oGift = $this->system->loadModel('trading/gift');
            $aGift = $oGift->getGiftByIds($giftIds);
            foreach($aGift as $k => $item) {
                $count += $item['point'] * $nums[$k];
            }
        }
        return $count;
    }
    
    function getCartTotalNum($gCart=array(),$pCart=array(),$fCart=array()){
        $tNum = 0;
        foreach($gCart as $num){
            $tNum += $num;
        }
        foreach($pCart as $item){
            $tNum += $item['num'];
        }
        foreach($fCart as $item){
            $tNum += $item['num'];
        }
        return $tNum;
    }
    
    function setCartNum(&$aCart){
        $sale = $this->system->loadModel('trading/sale');
        $trading = $sale->getCartObject($aCart,$GLOBALS['runtime']['member_lv'],true);
        $number=count($trading['products'])+count($trading['gift_e'])+count($trading['package']);
    
        if($number!=$_COOKIE['CART_COUNT']){
            $this->system->setCookie('CART_COUNT',$number);
        }
    }

    function getCart($objType='all'){
        $aCart = $this->_getCart();
        $this->setCartNum($aCart);
        if ($objType == 'all') {
            return $aCart;
        }else {
            if ($this->_verifyObjType($objType)) {
                if (is_array($aCart[$objType])) {
                    return $aCart[$objType];
                }
            }
        }
    }

    function _getCart(){
        $aCart = array();
        if($this->memberLogin){
            $oMember = $this->system->loadModel('member/member');
            $sCookie = $oMember->getCart($this->memInfo['member_id']);
        }else{
            $sCookie = $_COOKIE[$this->cookiesName];
        }
        $aType = explode("@", $sCookie);
        unset($aType[0]);
        foreach($aType as $sType){
            if (!empty($sType)){
                $aItems = explode(".", $sType);
                $sCurObj = $aItems[0];
                $sItem = $aItems[1];
                switch ($sCurObj) {
                    case 'g'://商品
                        $aTmp = null;
                        $aSplit = explode(";", $sItem);    //拆分购物车和PMT
                        $sCart = $aSplit[0];
                        $sPmt = $aSplit[1];
                        if(!empty($sCart)){
                            $aRow = explode(",", $sCart);
                            foreach($aRow as $sRow){
                                $aTmp = explode("-", $sRow);
                                $aCart['g']['cart'][$aTmp[0].'-'.$aTmp[1].'-'.$aTmp[2]] = $aTmp[3];
                            }
                            $aRow = explode(",", $sPmt);
                            foreach($aRow as $sRow){
                                $aTmp = explode("-", $sRow);
                                if($aTmp[0]) $aCart['g']['pmt'][$aTmp[0]] = $aTmp[1];
                            }
                        }else{
                            $aCart['g']['cart'] = array();
                        }
                        break;
                    case 'p'://捆绑
                        $aTmp = null;                        
                        $aRow = explode(",",$sItem);
                        foreach ($aRow as $sRow) {
                            $aTmp = explode('-', $sRow);
                            $aCart['p'][$aTmp[0]]['num'] = $aTmp[1];
                        }
                        break;
                    case 'f'://赠品
                        $aTmp = null;                        
                        $aRow = explode(",",$sItem);
                        foreach ($aRow as $sRow) {
                            $aTmp = explode('-', $sRow);
                            $aCart['f'][$aTmp[0]]['num'] = $aTmp[1];
                        }
                        break;
                    case 'c'://优惠券
                        /*
                        $aTmp = null;
                        $aRow = explode("-",$sItem);
                        $aTmp[$aRow[0]]['pmt_id'] = $aRow[1];                        
                        switch ($aRow[2]) {
                            case 'o':
                                $aRow[2] = 'order';
                                break;
                            case 'g':
                                $aRow[2] = 'goods';
                                break;
                        }
                        $aTmp[$aRow[0]]['type'] = $aRow[2];
                        $aCart['c'] = $aTmp;*/
                        $aTmp = null;
                        $aRow = explode(',', $sItem);
                        foreach($aRow as $sRow) {
                            $aTmp = explode("-",$sItem);
                            $aCart['c'][$aTmp[0]]['pmt_id'] = $aTmp[1];                        
                            switch ($aTmp[2]) {
                                case 'o':
                                    $aTmp[2] = 'order';
                                    break;
                                case 'g':
                                    $aTmp[2] = 'goods';                            
                                    break;
                            }
                            $aCart['c'][$aTmp[0]]['type'] = $aTmp[2];
                        }
                        break;
                }
            }
        }
        return $aCart;
    }
    
    //cookies
    function save($objType, $aPara) {
        if ($objType == 'all') {
            $aRet = $aPara;
        }else {
            if ($this->_verifyObjType($objType)) {
                $aRet = $this->getCart();
                $aRet[$objType] = $aPara;
            }else{
                return false;
            }
        }
        $this->setCartNum($aRet);
        $sRet = $this->_save($aRet);
        if($this->memberLogin){
            $oMember = $this->system->loadModel('member/member');
            $oMember->saveCart($this->memInfo['member_id'], $sRet);    //插入用户数据库addon['cart']
        }else{
            $this->system->setcookie($this->cookiesName, $sRet);
        }
        return true;
    }

    function _save(&$aRet) {
        $sRet = '';
        foreach($aRet as $sObj => $aObj) {
            if (is_array($aObj)&&!empty($aObj)) {
                $sRet .= '@';
                switch ($sObj){
                    case 'g':
                        $sRet .= $sObj.'.';
                        //@g.gid-pid-adj-num,gid-pid-adj-num;pmt:gid-pmtid,gid-pmtid
                        $iLoop = 0;
                        foreach($aObj['cart'] as $item => $num){
                            if($item && $num){
                                if($iLoop > 0) $sRet .= ',';
                                $sRet .= $item.'-'.$num;
                                $iLoop++;
                            }
                        }
                        
                        $iLoop = 0;
                        $sRet .= ';';
                        foreach($aObj['pmt'] as $gid => $pmtid){
                            if($gid && $pmtid){
                                if($iLoop > 0) $sRet .= ',';
                                $sRet .= $gid.'-'.$pmtid;
                                $iLoop++;
                            }
                        }
                        break;
                    case 'p':
                        $sRet .= $sObj.'.';
                        $aComponents  = array();
                        foreach($aObj as $gid => $aProduct){
                            $aComponents[]  = $gid.'-'.$aProduct['num'];
                        }
                        $sRet .= implode(',', $aComponents );
                        break;
                    case 'f':
                        $sRet .= $sObj.'.';
                        $aComponents  = array();
                        foreach($aObj as $gid => $aProduct){
                            $aComponents[]  = $gid.'-'.$aProduct['num'];
                        }
                        $sRet .= implode(',', $aComponents );
                        break;
                    case 'c':
                        $sRet .= $sObj.'.';
                        $aComponents  = array();
                        foreach($aObj as $code => $aCoupon){
                            switch ($aCoupon['type']) {
                                case 'order':
                                    $aCoupon['type'] = 'o';
                                    break;
                                case 'goods':
                                    $aCoupon['type'] = 'g';
                                    break;
                            }
                            //$sRet .= '.'.$code.'-'.$aCoupon['pmt_id'].'-'.$aCoupon['type'];
                            $aComponents[] = $code.'-'.$aCoupon['pmt_id'].'-'.$aCoupon['type'];
                        }
                        $sRet .= implode(',', $aComponents);
                        break;
                }
            }
        }    
        return $sRet;
    }

    function getCheckout(&$aCart, $aMember, $currency){
        $trading = $this->checkoutInfo($aCart, $aMember);
        $gtype = $this->system->loadModel('goods/gtype');
        foreach($trading['products'] as $p){
            if($p['goods_id'] > 0) $aP[] = $p['goods_id'];
        }
        if(!empty($aP) || $trading['package'] || $trading['gift_e']){    //todo 需要处理捆绑商品的配送问题
            if(!empty($aP)){
                $deliverInfo = $gtype->deliveryInfo($aP);
                $aOut['has_physical'] = $deliverInfo['physical'];
            }else{
                $aOut['has_physical'] = 1;
            }
            foreach($trading['products'] as $product){
                if($deliverInfo['custom'][$product['type_id']]){
                    $aOut['minfo'][$product['product_id']] = array(
                            'goods_id' => $product['goods_id'],
                            'nums' => $product['nums'],
                            'name' => $product['name'],
                            'minfo' => &$deliverInfo['custom'][$product['type_id']]);
                }
            }
            $oDly = $this->system->loadModel('trading/delivery');
            if($area = $oDly->getDlAreaList()){
                foreach($area as $a){
                    $aOut['areas'][$a['area_id']]=$a['name']; 
                }
            }
        }
        
        $payment = $this->system->loadModel('trading/payment');
        $oCur = $this->system->loadModel('system/cur');
        $currency = $oCur->getcur($currency, true);

        $oMem = $this->system->loadModel('member/member');
        $trading['receiver'] = $oMem->getDefaultAddr($aMember['member_id']);
        $trading['receiver']['email'] = $aMember['email'];

        $aOut['currencys'] = $oCur->curAll();
        $aOut['currency'] = $currency['cur_code'];
        $aOut['payments'] = $payment->getByCur($currency['cur_code']);
        $aOut['trading'] = $trading;
        return $aOut;
    }

    function checkoutInfo(&$aCart, &$aMember, $aParam=null) {
        $sale = $this->system->loadModel('trading/sale');
        $trading = $sale->getCartObject($aCart,$aMember['member_lv_id'],true);
        $trading['total_amount'] = $trading['totalPrice'];
        if($aParam['shipping_id']){
            $shipping = $this->system->loadModel('trading/delivery');
            $aShip = $shipping->getDlTypeByArea($aParam['area'], 0, $aParam['shipping_id']);
            if($trading['exemptFreight'] == 1){
                $trading['cost_freight'] = 0;
            }else{
                $trading['cost_freight'] = cal_fee($aShip[0]['expressions'],$trading['weight'],$trading['pmt_b']['totalPrice'],$aShip[0]['price']);
            }
            $trading['shipping_id'] = $aParam['shipping_id'];
            if($aParam['is_protect'] == 'true' && $aShip[0]['protect']){
                $trading['is_protect'] = 1;
                $trading['cost_protect'] = max($aShip[0]['protect_rate']*$trading['totalPrice'], $aShip[0]['minprice']);
            }
            $trading['total_amount'] += $trading['cost_freight']+$trading['cost_protect'];
        }
        if($this->system->getConf('site.trigger_tax')){
            $trading['is_tax'] = 1;
            if(!isset($aParam['is_tax']) || $aParam['is_tax'] == 'true'){
                $trading['tax_checked'] = 'checked';
                $trading['cost_tax'] = $trading['totalPrice'] * $this->system->getConf('site.tax_ratio');
                $trading['total_amount'] += $trading['cost_tax'];
            }
            $trading['tax_rate'] = $this->system->getConf('site.tax_ratio');
        }

        if($aParam['payment']){
            $payment = $this->system->loadModel('trading/payment');
            $aPay = $payment->getPaymentById($aParam['payment']);
            $trading['cost_payment'] = $aPay['fee'] * $trading['total_amount'];
            $trading['total_amount'] += $trading['cost_payment'];
        }

        $trading['score_g'] = $trading['pmt_b']['totalGainScore'];
        $trading['pmt_amount'] = $trading['pmt_b']['totalPrice'] - $trading['totalPrice'];
        $trading['member_id'] = $aMember['member_id'];
        
        $order = $this->system->loadModel('trading/order');
        $newNum = $order->getOrderDecimal($trading['total_amount']);
        $trading['discount'] = $trading['total_amount'] - $newNum;
        $trading['total_amount'] = $newNum;
        $oCur = $this->system->loadModel('system/cur');
        $currency = $oCur->getcur($aParam['cur']);
        if($currency['cur_code']){
            $trading['cur_rate'] = $currency['cur_rate'];
        }else{
            $trading['cur_rate'] = 1;
        }
        $trading['final_amount'] = $newNum * $trading['cur_rate'];
        $trading['cur_sign'] = $currency['cur_sign'];
        $trading['cur_display'] = $this->system->request['cur'];
        $trading['cur_code'] = $currency['cur_code'];
        return $trading;
    }
}
?>