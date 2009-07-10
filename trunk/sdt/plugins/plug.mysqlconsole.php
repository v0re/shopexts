<?php

class mysqlconsole {
	var $label = 'MySQL控制台';

	var $sql;

	function run(){
		switch($_REQUEST['action']){

			case "query":
				$this->sql = $_POST['sql'];
				$this->showForm();				
				mysql_query($this->sql);
			break;
			
			default:
				$this->showForm();
			break;
		}
	}

	function showForm() {
		echo <<<EOF
<form method=post>
<input type=hidden name=module value=mysqlconsole>
<input type=hidden name=action value=query>
<textarea name="sql" rows=9 cols=100 >{$this->sql}</textarea>
<br>
<input type=submit value='提交'>

</form>

EOF;
	}

}

?>