<?php
class cct_member extends ctl_member {
	var $allowImport = true;

	function import(){
        $this->path[] = array('text'=>'批量导入');
        $dataio = &$this->system->loadModel('system/dataio');
        $dataio->privateImport = true;

        $this->page('member/import.html');
    }

    function importer(){
        $this->begin('index.php?ctl=member/member&act=import');
        $m = $this->system->loadModel('member/member');
        if(!$m->checkImportData($_POST, $_FILES)){
            $this->end(false);
        }else{
			$this->end(true,__('会员数据导入成功!'));
        }
    }
}
?>
