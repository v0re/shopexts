<?php
if(isset($_GET['password'])) {
	require("config/config.php");
	$link=mysql_connect(DB_HOST,DB_USER,DB_PASSWORD); 
	mysql_select_db(DB_NAME);
	$username=trim($_GET['username']);
	$password=md5(trim($_GET['password']));
	$query="update ".DB_PREFIX."operators set username='".$username."', userpass='".$password."' where op_id='1'" ;
	if($rs=mysql_query($query,$link)){
			print "<br>�ѽ�����Ա�޸�Ϊ <font color=red>$username</font> ,�����Ϊ <font color=red>".$_GET[password]."</font>";				
		}
		else{
			print mysql_error();
		}

	mysql_close($link);
}
?>
<form >
<table>
	<tr><td>���û�����</td><td><input type="text" name="username"></td></tr>
	<tr><td>�����룺</td><td><input type="text" name="password"></td></tr>
	<tr><td colspan=2 align='center'><input type="submit" name="submit")> </td></tr>
</table>
</form>