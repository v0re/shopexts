<?php

class helloworld {
	var $label = '您好世界';

	function run(){
		switch($_REQUEST['action']){

			case "show":
				print_r($_POST);				
			break;
			
			default:
				$this->showForm();
			break;
		}
	}

	function showForm() {
		echo <<<EOF
<form name='hello' id='hello'>
<input type=hidden name=action value=show>
请输入你的名字<input type=text name='name' >
<input type=botton onclick=postdata(this,'helloword') value='提交'>

</form>

EOF;
	}

}

?>