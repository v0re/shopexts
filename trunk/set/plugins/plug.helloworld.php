<?php

class helloworld {
	var $label = '您好世界';

	function run(){
		switch($_REQUEST['action']){

			case "show":
				echo "您好 $_POST[name] ";
			break;
			
			default:
				$this->showForm();
			break;
		}
	}

	function showForm() {
		echo <<<EOF
<form name='hello' id='hello' method=post>
<input type=hidden name=module value=helloworld>
<input type=hidden name=action value=show>
请输入你的名字<input type=text name='name' >
<input type=submit value='提交'>

</form>

EOF;
	}

}

?>