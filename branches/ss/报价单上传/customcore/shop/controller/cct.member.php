<?php
class cct_member extends ctl_member{
    function index() {
        $oMem = $this->system->loadModel('member/member');
        $oAsk = $this->system->loadModel('goods/askpric');
        $aInfo = $oMem->getMemberInfo($this->member['member_id']);
        $this->pagedata['mem'] = $aInfo;

        $wInfo = $oMem->getWelcomeInfo($this->member['member_id']);
        $this->pagedata['wel'] = $wInfo;

		if($this->member['member_lv_id'] == '3'){
			$askInfo = $oAsk->getAskInfo();
			$this->pagedata['askInfo'] = $askInfo;
		}

        $this->_output();
    }
}
?>
