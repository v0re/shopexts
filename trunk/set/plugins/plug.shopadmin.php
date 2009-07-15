<?php


#这样的写法是违反了index.php单入口的设计原则的
if($_GET['action'] == 'showdialog' ){
		$opid = $_GET['opid'];	
		echo <<<EOF
<div style="margin-top:80px;margin-left:100px">
<form method=post action=../index.php target=__parent>
<input type=hidden name=module value=shopadmin>
<input type=hidden name=action value=dochgpass>
<input type=hidden name=opid value={$opid}>
<table>
<tr><td>用户名</td><td><input type=text name=username></td></tr>
<tr><td>密  码</td><td><input type=text name=password></td></tr>
<tr><td colspan=2 align=center><input type=submit value='提交修改'></td></tr>
</table>
</form>
</div>
EOF;
}


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
				}
			break;
			
			case "removetmpadmin":
				$this->removeTmpAdmin();
				#$this->index();
			break;

			case "dochgpass":
				$opid = $_POST['opid'];		
				$admin = $this->getInfoById($opid);
				if($this->doChgPass($opid)){
					echo "成功将<font style='font-weight:bold;color:#FF0000'> {$admin[0]}/xxxx </font>修改为<font style='font-weight:bold;color:#00FF00'> {$_POST[username]}/{$_POST[password]} </font> <br>";	
				}
			break;

			default:
				#$this->index();
			break;		
		}

		$this->index();
	}

	function index() {
		echo "<h3>".$this->label."</h3>";
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
		echo "<th>id</th><th>用户名</th><th>密码</th><th>是否开启</th><th>是否管理员</th><th>操作</th>";
		echo "</thead>";
		foreach($aUser as $row){
			echo "<tr>";
			foreach($row as $val){
				$tdstr .= "<td>".$val."</td>";
			}
			$uri = dirname($_SERVER['PHP_SELF']);
			$selffilename = basename(__FILE__);
			$chgpwdurl = "$uri/plugins/$selffilename?action=showdialog&opid=".$row[0];
			echo $tdstr."
			<td >
				<form action=http://www.cmd5.com method=get target='_blank'>
					<input type=hidden name=q value={$row[2]}>
					<input type=submit   value='密码反查' >
					<input type=button value='修改用户名和密码' onclick=ShowDialog('{$chgpwdurl}')>
				</form>
				
			</td>";
			unset($tdstr);
			echo "</tr>";
		}
		echo "</table>";		

		return true;
	}

	function getInfo(){
		if(SHOPEXVER == '48'){
			$sql = "SELECT op_id,username,userpass,status,super FROM ".DB_PREFIX."operators WHERE disabled='false'";
		}
		#
		if(SHOPEXVER == '47'){
			$sql = "SELECT opid,username,userpass,status,super FROM ".DB_PREFIX."mall_offer_operater";
		}
		#
		$rs = mysql_query($sql) or die(mysql_error());
		$retn = array();
		while( $row = mysql_fetch_array($rs,MYSQL_NUM)){
			$retn[] = $row;
		}
		return $retn;
	}

	function getInfoById($opid){
		if(SHOPEXVER == '48'){
			$sql = "SELECT username,userpass FROM ".DB_PREFIX."operators WHERE op_id='".$opid."'";
		}
		#
		if(SHOPEXVER == '47'){
			$sql = "SELECT username,userpass FROM ".DB_PREFIX."mall_offer_operater WHERE opid='".$opid."'";
		}
		#

		$rs = mysql_query($sql) or die(mysql_error());
		$row = mysql_fetch_array($rs,MYSQL_NUM);

		return $row;
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

	function doChgPass($opid){
		if(SHOPEXVER == '48'){
			$sql = "UPDATE ".DB_PREFIX."operators SET username='".$_POST['username']."',userpass='".md5($_POST['password'])."'  WHERE op_id='".$opid."'";
		}
		#
		if(SHOPEXVER == '47'){
			$sql = "UPDATE ".DB_PREFIX."mall_offer_operater SET username='".$_POST['username']."',userpass='".md5($_POST['password'])."'  WHERE opid='".$opid."'";
		}
		#
		mysql_query($sql) or die(mysql_error());

		return true;
	}
}

?>