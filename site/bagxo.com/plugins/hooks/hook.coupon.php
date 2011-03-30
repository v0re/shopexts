<?php
class hook_coupon {
    function hook_coupon() {
        $this->system = &$GLOBALS['system'];
        $this->db = &$this->system->database();
    }
    function toPayed($PARA) {
        if ($PARA['pay_status']==1 || $PARA['pay_status']==2) {
            $orderId = $PARA['order_id'];
            $sSql = 'select count(*) as count from sdb_member_coupon where memc_gen_orderid=\''.$orderId.'\'';
            $aData = $this->db->selectRow($sSql);
            //如果没有此订单没生成过优惠券,则生成.否则置之不理
            if ($aData['count']==0) {
                $oOrder = $this->system->loadModel('trading/order');
                $aOrder = $oOrder->getFieldById($orderId, array('member_id'));
                $memberId = $aOrder['member_id'];
                $oCoupon = $this->system->loadModel('trading/coupon');
                
                //赠送优惠券按
                $sSql = 'select cpns_id,nums from sdb_coupons_p_items where order_id=\''.$orderId.'\'';
                $c_p_items = $this->db->select($sSql);
                if ($c_p_items) {
                    foreach ($c_p_items as $items) {
                        $oCoupon->generateCoupon($items['cpns_id'], $memberId, $items['nums'], $orderId);
                    }
                }
            }
            //使用优惠券,挪到下单后就进行
        }
        return true;
    }

    //todo退款处理
    function toRefund($PARA) {
    }

    function toCancel($PARA) {
    }

    function toDelete($PARA) {
        $this->toCancel($PARA);
    }
}

?>