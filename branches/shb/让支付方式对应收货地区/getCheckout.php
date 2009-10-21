 function getCheckout(&$aCart, $aMember, $currency,$areaid){###################################增加一个参数（具体地区的ID）
	
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
        $aOut['payments'] = $payment->getByCur($currency['cur_code'],$type,$areaid);###################增加一个参数（具体地区的ID）
        $aOut['trading'] = $trading;
        return $aOut;
    }