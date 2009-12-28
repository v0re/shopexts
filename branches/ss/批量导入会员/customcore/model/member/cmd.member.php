<?php
class cmd_member extends mdl_member {
    function checkImportData($aData=array(), $aFile=array()){
        if($aData['type']=='csv'){
            if(substr($aFile['upload']['name'],-4)!='.csv'){
                trigger_error(__('文件格式有误'),E_USER_ERROR);
                exit;
            }
            $content = file_get_contents($aFile['upload']['tmp_name']);
            if(substr($content,0,3)=="\xEF\xBB\xBF"){
                $content = substr($content,3);    //去BOM头
                $handle = fopen($aFile['upload']['tmp_name'],'wb');
                fwrite($handle,$content);
                fclose($handle);
            }
            $handle = fopen($aFile['upload']['tmp_name'],'r');
        }elseif(substr($aData['type'],0,4)=='site'){
            $handle['url'] = $aData['url'];
            $handle['count'] = 0;
        }

        $dataio = $this->system->loadModel('system/dataio');
        while($data = $dataio->import_row($aData['type'],$handle)){
            $goMark = true;
            foreach($data as $v){
                if(trim($v)){
                    $goMark = false;
                    break;
                }
            }
            if($goMark){
                continue;
            }

			$aData['uname'] = $data[0];
			$advance = $data[1];
			$mlevel = $this->system->loadModel('member/level');
			$aData['member_lv_id'] = $mlevel->getDefauleLv();
			$aData['password'] = md5('jfshop');
			$aData['regtime'] = time();
			$aData['reg_ip'] = remote_addr();
			$oAdv = $this->system->loadModel('member/advance');
			if($row=$this->getMemberByUser($aData['uname'])){
				if($advance > 0){
					$oAdv->add($row['member_id'],$advance,'后台代充',__('修改成功！'), '', '' ,'' ,'店主代充');
				}else{
					$oAdv->add($row['member_id'],$advance,'后台代扣',__('修改成功！'), '', '' ,'' ,'店主代充');
				}
			}else{
				$rs = $this->db->exec('SELECT * FROM sdb_members WHERE 0=1');
				$sql = $this->db->getInsertSQL($rs, $aData);
				if($sql && !$this->db->exec($sql)){
					trigger_error('SQL Error:'.$sql,E_USER_NOTICE);
					exit;
				}
				$member_id = $this->db->lastInsertId();
				
				if($advance > 0){
					$oAdv->add($member_id,$advance,'后台代充',__('修改成功！'), '', '' ,'' ,'店主代充');
				}else{
					$oAdv->add($member_id,$advance,'后台代扣',__('修改成功！'), '', '' ,'' ,'店主代扣');
				}
			}

            $iLoop++;
            usleep(20);
        }

        return true;
    }
}
?>
