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
		################################################################################################################
		$aOut = $this->objCart->getCheckout($this->cart, $this->member, $this->system->request['cur'],$_POST['area']);//增加一个参数是收获地区的ID
		
        $payment = $this->system->loadModel('trading/payment');
		
        $payment->showPayExtendCon($aOut['payments']);
        $this->pagedata['payments'] = $aOut['payments'];//支付方式
		###############################################################################################################
        $this->pagedata['shippings'] = $aShippings;
        $this->__tmpl='cart/checkout_shipping.html';
        $this->output();
		
    }