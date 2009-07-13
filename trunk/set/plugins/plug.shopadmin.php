<?php

class shopadmin {
	var $label = 'ShopEx后台密码管理';

	function run(){
		switch($_REQUEST['action']){

			case "save":
				echo "您好 $_POST[name] ";
			break;
			
			default:
				$this->index();
			break;
		}
	}

	function index() {
		echo <<<EOF
<form name='password' id='password' method=post>
<input type=hidden name=module value=shopadmin>
<input type=hidden name=action value=save>
请输入你的名字<input type=text name='name' >
<input type=submit value='提交'>

</form>

EOF;
	}

	function getPasswordHash(){
		if(SHOPEXVER == '48'){
			
		}

		if(SHOPEXVER == '47'){
			
		}
	}

}

?>