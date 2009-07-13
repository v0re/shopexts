<?php

class mysqlconsole {
	var $label = 'MySQL控制台';

	var $sql;
	var $logfile;
	var $timecost;

	function mysqlconsole() {
		$this->logfile = VARDIR.'/sql.log';
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
				$t1=microtime(true);
				$rs = mysql_query($this->sql) or die(mysql_error());
				$this->timecost = microtime(true) - $t1;
				$this->timecost = number_format($this->timecost,3,'.','');
				#有查询结果用表格显示
				if( in_array(strtolower(substr($this->sql,0,4)),array('sele','desc','show')) ){
						$this->query($rs);
				}else{
					if($rs){
						echo "命令执行成功，花费了 {$this->timecost} ms";
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
		$sqllog = $this->rlog();
		echo <<<EOF
<form method=post >
当前位置:{$mysqluri} <input type=submit value='提交查询'> <br>
<input type=hidden name=module value=mysqlconsole>
<input type=hidden name=action value=query>
<textarea name="sql" rows=9 cols=70 >{$this->sql}</textarea><textarea rows=9 cols=70 >{$sqllog}</textarea>
</form>
EOF;
	}

	function query($rs){
		echo "<div class='iframelike' id='sqlquerycontent'>";
		echo "<table class='ae-table'>";
		$i = 0;
		while($row = mysql_fetch_array( $rs, MYSQL_ASSOC )){
			if($i == 0 ){
				echo "<thead>";
				foreach(array_keys($row) as $val){
					echo "<th>".$val."</th>";
				}	
				echo "</thead>";
			}
			echo "<tr>";
			foreach($row as $val){
				//$val = cnSubStr($val,10);
				echo "<td>".htmlspecialchars($val)."</td>";			
			}
			echo "</tr>";	
			$i++;		
		}
		echo "</table>";
		echo "</div>";
		echo "<script>AutoSizeDIV('sqlquerycontent');</script>";
	}

	function wlog(){
		if(strlen($this->sql) > 0){
			$log = $this->sql;
			$log = $log."\r\n";
			error_log($log,3,$this->logfile);
		}
	}

	function rlog(){
		/*读取最后三行，我们预期这个sql.log不会太大，所以直接用file，如果文件很大，要采用优化算法，可以参考这个：
		*http://pgregg.com/projects/php/code/tail-10.phps
		*/
		$aTmp = file($this->logfile);
		$aTmp = array_slice($aTmp,-5,5);
		$aTmp = array_reverse($aTmp); 
		$seperator = str_repeat('-',80)."\r\n";
		return implode($seperator,$aTmp);
	}
}

?>