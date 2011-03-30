<?php
class hook_gift {
    function hook_gift() {
        $this->system = &$GLOBALS['system'];
        $this->db = &$this->system->database();
    }

    function toConsign(&$PARA) {
        $orderId = $PARA['order_id'];
        $oGift = $this->system->loadModel('trading/gift');
        $PARA['gift_send'] = &$PARA['delivery']['gift_send'];

        if (is_array($PARA['gift_send'])) {
            $aOrderItems = $oGift->getOrderItemsList($orderId, array_keys($PARA['gift_send']));
            $flowMark = true;
        } else {
            $aOrderItems = $oGift->getOrderItemsList($orderId);
            $flowMark = false;
        }

        $nTotalSendNum = 0;
        $i = 0;
        $sError = '';

        //判断库存是否全
        if ($aOrderItems){
            foreach($aOrderItems as $aItem) {
                $sendNum = ($flowMark ? intval($PARA['gift_send'][$aItem['gift_id']]) : ($aItem['nums'] - $aItem['send_num']));
                if ($sendNum>0) {
                    if (!$oGift->checkStock($aItem['gift_id'], -$sendNum)) {
                        $sError = ','.$aItem['name'];
                    }else{
                        $nTotalSendNum += $sendNum;
                        $consignItems[$i] = array(
                                'order_id'=>$orderId,
                                'item_type'=>'gift',
                                'product_id'=>$aItem['gift_id'],
                                'product_bn'=>'',
                                'product_name'=>$aItem['name'],
                                'number'=>$sendNum
                        );
                        $i++;
                    }
                }
            }
        }
        
        if ($nTotalSendNum > 0) {
            $objShipping = $this->system->loadModel('trading/delivery');
            $oGift = $this->system->loadModel('trading/gift');
            if ($PARA['delivery']['delivery_id']) {
                $deliveryId = $PARA['delivery']['delivery_id'];
            }else{
                $deliveryId = $objShipping->toCreate($PARA['delivery']);
            }
            
            foreach($consignItems as $aItem) {
                $aItem['delivery_id'] = $deliveryId;   //对应主表ID
                $itemId = $objShipping->toInsertItem($aItem);
                $oGift->toConsign($orderId, $aItem['product_id'], $aItem['number']);
            }
        }
        if (!empty($sError)) {
            array_push($PARA['message'], $sError);
            array_push($PARA['ship_status_o'], 2);//部分发货
        }else{
            if($i > 0) array_push($PARA['ship_status_o'], 1);//全部发货
            else array_push($PARA['ship_status_o'], -1);//没有赠品发货
        }

        return true;
    }

    function toCancel($PARA){
        $oOrder = $this->system->loadModel('trading/order');
        $oMemberPoint = $this->system->loadModel('trading/memberPoint');

        $orderId = $PARA['order_id'];
        $aData = $oOrder->getFieldById($orderId, array('member_id'));
        $memberId = intval($aData['member_id']);

//        if ($PARA['pay_status']==1 || $PARA['pay_status']==2) {
            $oMemberPoint->cancelOrderRefundConsumePoint($aMemberId['member_id'], $orderId);
//        }

        $oGift = $this->system->loadModel('trading/gift');
        $aOrderItems = $oGift->getOrderItemsList($PARA['order_id']);
        if($aOrderItems) {
            foreach($aOrderItemds as $aItem) {
                $oGift->toCancel($orderId, $aItem['gift_id']);                
            }
        }
        return true;
    }

    function toRemove($PARA) {
        $this->toCancel($PARA);
        $orderId = $PARA['order_id'];
        $this->db->exec('delete from sdb_gift_items where orderId='.intval($orderId));
        $orderId = $PARA['order_id'];
    }
/*
    function toPayed($PARA){
        $oOrder = $this->system->loadModel('trading/order');
        $oMemberPoint = $this->system->loadModel('trading/memberPoint');

        $orderId = $PARA['order_id'];
        $aData = $oOrder->getFieldById($orderId, array('member_id');
        $memberId = intval($aData['member_id']);

        $oMemberPoint = $this->system->loadModel('member/account');
        $aPoint = $oMember->getMemberById($memberId);
        //只有当全额付款时才做处理
        if ($memberId) {
            if ($PARA['pay_status']==1 || $PARA['pay_status']==2) {
                $oMemberPoint->payAllConsumePoint($memberId, $orderId);
            }
        }
        return true;
    }
*/
    //赠品没有退款操作
/*
    function toRefund($IN, &$OUT, &$MSG){
        //
        $oOrder = $this->system->loadModel('trading/order');
        $oMemberPoint = $this->system->loadModel('trading/memberPoint');

        $orderId = null;
        $aData = $oOrder->getFieldById($orderId, array('member_id');
        $memberId = intval($aData['member_id']);

        $oMemberPoint = $this->system->loadModel('member/account');
        $aPoint = $oMember->getMemberById($aUserid['userid']);
        //只有当全额付款时才做处理
        if ($memberId) {
            if ($OUT['pay_status']==5) {
                $oMemberPoint->refundAllConsumePoint($memberId, $orderId);
            }
        }
        return true;
    }*/




}
?>
