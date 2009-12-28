<?php
include_once('objectPage.php');
class ctl_askpric extends objectPage{
	var $name='咨询报价';
	var $workground = 'member';
	var $object = 'member/askpric';
	var $disableGridEditCols = "ask_id";
	var $disableColumnEditCols = "ask_id";
	var $disableGridShowCols = "ask_id";

	function index(){
		parent::index();
	}


    function detail($aid){
        $askObj = $this->system->loadModel('member/askpric');
		$asks=$askObj->getFieldById($aid,array('ask_id','member_id','goods_id','ask_status'));
		$this->pagedata['asks'] = &$asks;
		$mid = $asks['member_id'];
		$gid = $asks['goods_id'];
		
        $memObj = $this->system->loadModel('member/member');
		$members=$memObj->getBasicInfoById($mid);
		$this->pagedata['members'] = &$members;

        $o = $this->system->loadModel('trading/goods');
        $goods=$o->getFieldById($gid, array('mktprice','price','thumbnail_pic','disabled','marketable','pdt_desc','rank_count'));
		
		$mPrice = $o->getMemberPrice($gid, $product['product_id'], $levelid);
		
		$this->pagedata['mprice'] = $mPrice['mprice'][$members['member_lv_id']];
        $this->pagedata['goods'] = &$goods;
        $this->pagedata['is_pub'] = ($goods['marketable']!='false' && $goods['disabled']!='true');
        $this->pagedata['url'] = $this->system->realUrl('product','index',array($gid),null,$this->system->base_url());

        $this->setView('member/askpric/detail.html');
        $this->output();
    }

	function toShow($askid){
        if(!$askid) $askid = $_POST['ask_id'];
        else $_POST['ask_id'] = $askid;

        $_POST['opid'] = $this->op->opid;
        $_POST['opname'] = $this->op->loginName;
        $this->begin('index.php?ctl=member/askpric&act=detail&p[0]='.$askid);
        $objAsk = $this->system->loadModel('member/askpric');
        $objAsk->op_id = $this->op->opid;
        $objAsk->op_name = $this->op->loginName;
        if($objAsk->toShow($_POST, true)){    //处理订单收款
            $this->setError(10001);
            trigger_error('报价成功',E_USER_NOTICE);
        }else{
            $this->setError(10002);
            trigger_error('报价失败',E_USER_ERROR);
        }
        $this->end();
    }
}
?>