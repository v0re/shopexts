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
            //���û�д˶���û���ɹ��Ż�ȯ,������.������֮����
            if ($aData['count']==0) {
                $oOrder = $this->system->loadModel('trading/order');
                $aOrder = $oOrder->getFieldById($orderId, array('member_id'));
                $memberId = $aOrder['member_id'];
                $oCoupon = $this->system->loadModel('trading/coupon');
                
                //�����Ż�ȯ��
                $sSql = 'select cpns_id,nums from sdb_coupons_p_items where order_id=\''.$orderId.'\'';
                $c_p_items = $this->db->select($sSql);
                if ($c_p_items) {
                    foreach ($c_p_items as $items) {
                        $oCoupon->generateCoupon($items['cpns_id'], $memberId, $items['nums'], $orderId);
                    }
                }
            }
            //ʹ���Ż�ȯ,Ų���µ���ͽ���
        }
        return true;
    }

    //todo�˿��
    function toRefund($PARA) {
    }

    function toCancel($PARA) {
    }

    function toDelete($PARA) {
        $this->toCancel($PARA);
    }
}

?>