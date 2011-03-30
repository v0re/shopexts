<?php
class hook_getPoint {
    function hook_getPoint() {
        $this->system = &$GLOBALS['system'];
        $this->db = &$this->system->database();
    }

    function toPayed(&$PARA){
        $oOrder = $this->system->loadModel('trading/order');
        $orderId = $PARA['order_id'];
        $aMemberId = $oOrder->getFieldById($orderId, array('member_id'));
        $memberId = intval($aMemberId['member_id']);
        if ($memberId) {
            if ($PARA['pay_status']==1 || $PARA['pay_status']==2) {
                $oMemberPoint = $this->system->loadModel('trading/memberPoint');
                if (!$oMemberPoint->payAllGetPoint($memberId, $orderId)) {
                    return false;
                }
            }
        }
        return true;
    }

    function toRefund($PARA){
        $oOrder = $this->system->loadModel('trading/order');
        $orderId = $PARA['order_id'];
        $aMemberId = $oOrder->getFieldById($orderId, array('member_id'));
        $memberId = intval($aMemberId['member_id']);
        if ($memberId) {
            //$PARA['pay_status'] 4为部分到款,5为全额退款
            $oMemberPoint = $this->system->loadModel('trading/memberPoint');
            if (isset($PARA['return_score'])){
                $oMemberPoint->refundPartGetPoint($memberId, $orderId, (0-$PARA['return_score']));
            }else{
                $oMemberPoint->refundAllGetPoint($memberId, $orderId);
            }
        }
        return true;
    }
}
?>