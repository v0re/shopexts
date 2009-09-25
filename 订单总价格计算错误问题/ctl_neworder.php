<?php
function newtotal()
	{
        if(!empty($_POST['username'])){
			
            $objMember = $this->system->loadModel('member/member');
            $aUser = $objMember->getMemberByUser($_POST['username']);
            if(empty($aUser['member_id'])){
               echo __('<script>alert("不存在的会员名称!")</script>');
                exit;
            }else{
                $aHidden['aMember[member_id]'] = $aUser['member_id'];
                $aHidden['aMember[member_lv_id]'] = $aUser['member_lv_id'];
                $aHidden['aMember[uname]'] = $aUser['uname'];
            }
            $_SESSION['order_add_userid'] = $aUser['member_id'];
            $_SESSION['order_user'] = $aUser;
        }else{
            $aUser = array('member_id' => 0, 'member_lv_id' => 0);
        }

        if($_POST['goods']){
			$type='goods';                                                       //设置添加是普通商品
            $aTmp['product_id'] = $_POST['goods'];
            $objPdt = $this->system->loadModel('goods/finderPdt');
            $aPdt = $objPdt->getList('goods_id, product_id', $aTmp, 0, count($_POST['goods']));
			
            unset($aTmp);
			$objPro = $this->system->loadModel('goods/products');
            foreach($aPdt as $key => $row){
                $num = ceil($_POST['goodsnum'][$aPdt[$key]['product_id']]);
				
                if($num > 0){
					$result_goods=$objPro->checkInventory($aPdt[$key]['product_id'],$num,$type);
					if($result_goods){                                                   //如果存在商品库存不不足就设置状态值
                     $flag_goods=true;
					}
					
                    $cart['g']['cart'][$row['goods_id'].'-'.$aPdt[$key]['product_id'].'-na'] = $num;
                    $aHidden['aCart[g][cart]['.$row['goods_id'].'-'.$aPdt[$key]['product_id'].'-na]'] = $num;

                    $oPromotion = $this->system->loadModel('trading/promotion');
                    if($pmtid = $oPromotion->getGoodsPromotionId($row['goods_id'], $aUser['member_lv_id'])){
                        $cart['g']['pmt'][$row['goods_id']] = $pmtid;
                        $aHidden['aCart[g][pmt]['.$row['goods_id'].']'] = $pmtid;
                    }
                }
            }
       
		}
        if($_POST['package']){
			$type='package';                                              //设置添加是捆绑商品
            $aTmp['goods_id'] = $_POST['package'];
            $oPackage = $this->system->loadModel('trading/package');
            $aPkg = $oPackage->getList('goods_id', $aTmp, 0, count($_POST['package']));
			
            unset($aTmp);
			$objPac = $this->system->loadModel('goods/products');
            foreach($aPkg as $key => $row){
                $num = ceil($_POST['pkgnum'][$aPkg[$key]['goods_id']]);
                if($num > 0){
					$result_package=$objPac->checkInventory($aPkg[$key]['goods_id'],$num,$type);
					if($result_package){                                                   //如果存在商品库存不不足就设置状态值
                     $flag_package=true;
					}
                    $cart['p'][$row['goods_id']]['num'] = $num;
                    $aHidden['aCart[p]['.$row['goods_id'].'][num]'] = $num;
                }
            }
        }
       if(!$cart){
            echo __('<script>alert("没有购买商品或者购买数量为0!")</script>');
            exit;
        }
		if($flag_goods||$flag_package){
			echo __('<script>alert("某商品库存不足，请检查")</script>');
            exit;
		}
		//序列化添加商品的信息,并写入到文件
		file_put_contents(md5($_SERVER['SERVER_NAME'])."aUser",serialize($aUser));
		file_put_contents(md5($_SERVER['SERVER_NAME'])."aHidden",serialize($aHidden));
		file_put_contents(md5($_SERVER['SERVER_NAME'])."cart",serialize ($cart));
	    //global $my_var=$cart;
		//this->Cart=$cart;
        $objCart = $this->system->loadModel('trading/cart');
        $aOut = $objCart->getCheckout($cart, $aUser, '');
		$this->pagedata['trading'] = $aOut['trading'];
        $this->setView('shop:cart/checkout_total.html');
        $this->output();

		
	}
		#########################################################################################

		#########################################################################################填写收获信息
	function newcreate(){
		    //反序列化添加的商品信息
			$aUser=unserialize (file_get_contents(md5($_SERVER['SERVER_NAME'])."aUser"));
			$aHidden=unserialize (file_get_contents(md5($_SERVER['SERVER_NAME'])."aHidden"));
			$cart=unserialize (file_get_contents(md5($_SERVER['SERVER_NAME'])."cart"));
			
        if(!$cart){
            echo __('<script>alert("购买数量为0霍超出商品库存!")</script>');
            exit;
        }

        $this->pagedata['hiddenInput'] = $aHidden;
        //error_log(print_r($cart,true),3,'VV.log');
        $objCart = $this->system->loadModel('trading/cart');
        $aOut = $objCart->getCheckout($cart, $aUser, '');
		 //error_log(print_r($aOut['trading']['totalPrice'],true),3,'VVV.log');
        $aOut['trading']['admindo'] = 1;
        $this->pagedata['has_physical'] = $aOut['has_physical'];
        $this->pagedata['minfo'] = $aOut['minfo'];
        $this->pagedata['areas'] = $aOut['areas'];
        $this->pagedata['currencys'] = $aOut['currencys'];
        $this->pagedata['currency'] = $aOut['currency'];
        $payment = $this->system->loadModel('trading/payment');
        $payment->showPayExtendCon($aOut['payments']);
        $this->pagedata['payments'] = $aOut['payments'];
        $this->pagedata['payments'] = $aOut['payments'];
        $this->pagedata['trading'] = $aOut['trading'];
       
        if($aUser['member_id']){
            $member = $this->system->loadModel('member/member');
            $addrlist = $member->getMemberAddr($aUser['member_id']);
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
        }
		

        $this->setView('order/order_create.html');
         $this->output();
		}
?>