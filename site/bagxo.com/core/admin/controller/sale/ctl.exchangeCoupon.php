<?php
include_once('objectPage.php');
class ctl_exchangeCoupon extends objectPage{

    var $name = '优惠券兑换';
    var $workground = 'sale';
    var $object = 'trading/exchangeCoupon';
    var $actionView = 'sale/coupon/exchange/finder_action.html'; //默认的动作html模板,可以为null
    var $actions= array(
                'showAddExchange'=>'编辑',
            );
    var $noRecycle = true;
    var $disableGridShowCols = "cpns_key";

    function addExchange(){
        
        if(empty($_POST['cpns_id']) || $_POST['cpns_id']=='undefined'){
            $this->splash('failed', 'index.php?ctl=sale/exchangeCoupon&act=index', "优惠券名称不能为空");
        }
        $oExchangeCoupon = $this->system->loadModel('trading/exchangeCoupon');
        if(!$oExchangeCoupon->saveExchange($this->in)) {
            $this->splash('failed', 'index.php?ctl=sale/exchangeCoupon&act=index', $oExchangeCoupon->message);
        }
        $this->splash('success', 'index.php?ctl=sale/exchangeCoupon&act=index');
    }

    function showAddExchange($cpnsId=null){
        $oCoupon = $this->system->loadModel('trading/coupon');
        $aList = $oCoupon->getUserCouponArr();
        $this->pagedata['cpns_list'] = $aList;
        if ($cpnsId) {
            $this->pagedata['cpns'] = $oCoupon->getCouponById($cpnsId);
        }else{
            $this->pagedata['cpns']['cpns_id'] = $aList[0][0]['cpns_id'];
        }
        $this->page('sale/coupon/exchange/addExchange.html');
    }
    function delete() {
        $oExchangeCoupon = $this->system->loadModel('trading/exchangeCoupon');
        if ($oExchangeCoupon->recycle($_POST)) {
            echo '删除成功';
        } else {
            echo '删除失败';            
        }
    }
    
}
?>
