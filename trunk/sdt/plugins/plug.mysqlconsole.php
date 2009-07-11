<?php

class mysqlconsole {
	var $label = 'MySQL控制台';

	var $sql;
	var $logfile;

	function mysqlconsole() {
		$this->logfile = MEAT.'/log/sql.log';
	}

	function run(){
		switch($_REQUEST['action']){

			case "query":
				$this->sql = trim($_POST['sql']);
				if(get_magic_quotes_gpc()){
					$this->sql = stripslashes($this->sql);
				}
				$this->showForm();		
				$this->wlog();
				$rs = mysql_query($this->sql) or die(mysql_error());
				if( in_array(strtolower(substr($this->sql,0,4)),array('sele','desc','show')) ){
					echo "<p><table border=1>";
					$i = 0;
					while($row = mysql_fetch_array( $rs, MYSQL_ASSOC )){
						if($i == 0 ){
							echo "<tr>";
							foreach(array_keys($row) as $val){
								echo "<td>".$val."</td>";
							}	
							echo "<tr>";
						}

						echo "<tr>";
						foreach($row as $val){
								echo "<td>".htmlspecialchars($val)."</td>";			
						}
						echo "</tr>";	
						$i++;		
					}
					echo "</table></p>";
				}else{
					if($rs){
						echo "命令执行成功";
					}else{
						echo "命令执行失败<br><p>".mysql_error()."</p>";
					}
				}
			break;
			
			default:
				$this->showForm();
			break;
		}
	}

	function showForm() {

		$mysqluri = "mysql://".DB_USER.":".DB_PASSWORD."@".DB_HOST.":".DB_PORT."/".DB_NAME;
		echo <<<EOF
当前位置:{$mysqluri}
<form method=post>
<input type=hidden name=module value=mysqlconsole>
<input type=hidden name=action value=query>
<textarea name="sql" rows=9 cols=100 >{$this->sql}</textarea>
<br>
<input type=submit value='提交'>

</form>

EOF;
	}

	function wlog(){
		$log = $this->sql;
		$log = $log."\r\n";
		error_log($log,3,$this->logfile);
	}

	function rlog(){
		
	}
}

?>