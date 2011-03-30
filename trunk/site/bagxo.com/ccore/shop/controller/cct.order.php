<?php
class cct_order extends ctl_order{

    function create(){
        $this->begin($this->system->mkUrl('cart', 'checkout'));
        $this->_verifyMember(false);

        $order = $this->system->loadModel('trading/order');
        $oCart = $this->system->loadModel('trading/cart');
        $oCart->checkMember($this->member);
        $cart = $oCart->getCart('all');
				$_POST['delivery']['ship_addr'] = $_POST['delivery']['ship_addr']." ".$_POST['delivery']['ship_addr1'];  
				$orderid = $order->create($cart, $this->member,$_POST['delivery'],$_POST['payment'],$_POST['minfo']);
        if($orderid){
            if($_POST['fromCart']){
                $oCart->clearAll();
            }
/*             $this->redirect('index','order',array($orderid)); */
        }else{
            trigger_error('对不起，订单创建过程中发生问题，请重新提交或稍后提交',E_USER_ERROR);            
        }
        $this->system->setcookie('ST_ShopEx-Order-Buy', md5($this->system->getConf('certificate.token').$orderid));
        
         $this->end_only(true, '订单建立成功', $this->system->mkUrl('order', 'index', array($orderid)));
        $this->redirect('order','index',array($orderid));
    }
}
?>
