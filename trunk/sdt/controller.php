<?php

switch($_REQUEST['action']){
	case "backupdb":
		$dumper = new Mysqldumper($dbHost, $dbUser, $dbPass, $dbName);
		$dumper->setDroptables(true);
		$dumper->nodata = array();
		if(!$_REQUEST['inited']){
			$fileid = 0;
			$tableid = 0;
			$startid = -1;
			$dumper->setBackupTable();
			rptout("��ʼ���ݿⱸ��...\n");
		}else{
			$dumper->tableid = $_REQUEST['tableid'];
			$dumper->startid = $_REQUEST['startid'];
			$fileid = $_REQUEST['fileid'];
		}
		$volsize = $_REQUEST['volsize'] ? $_REQUEST['volsize'] : 1024;
		$finished = $dumper->multiDump($filename,$fileid,$volsize);
		$fileid++;
		rptout("���ݿⱸ���ļ� data_".$fileid.".sql ������\n");
		$tableid = $dumper->tableid;
		$startid = $dumper->startid;
		if($finished){
			rptout("<hr><font color=green>���ݿⱸ�����</font>\n");
		}else{
			jsjmp($_SERVER['PHP_SELF']."?action=backupdb&fileid={$fileid}&tableid={$tableid}&startid={$startid}&inited=1");
		}
	break;
}

?>