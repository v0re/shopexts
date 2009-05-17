<?php
if(isset($_GET['password'])) {
	require("config/config.php");
	$link=mysql_connect(DB_HOST,DB_USER,DB_PASSWORD); 
	mysql_select_db(DB_NAME);
	$username=trim($_GET['username']);
	$password=md5(trim($_GET['password']));
	$query="update ".DB_PREFIX."operators set username='".$username."', userpass='".$password."' where op_id='1'" ;
	if($rs=mysql_query($query,$link)){
			print "<br>已将管理员修改为 <font color=red>$username</font> ,密码改为 <font color=red>".$_GET[password]."</font>";				
		}
		else{
			print mysql_error();
		}

	mysql_close($link);
}
?>
<form >
<table>
	<tr><td>新用户名：</td><td><input type="text" name="username"></td></tr>
	<tr><td>新密码：</td><td><input type="text" name="password"></td></tr>
	<tr><td colspan=2 align='center'><input type="submit" name="submit")> </td></tr>
</table>
</form>