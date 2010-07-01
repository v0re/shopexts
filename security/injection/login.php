<?php
require("../share.php");
if($_POST || $_GET){
	if($_GET['action'] == 'reset'){
		$sql = "truncate injection_login";
		mysql_query("insert into injection_login (username,password) values('admin','admin')");
		echo "reset ok";
	}elseif($_GET['action'] == 'show'){
		$sql = "select * from injection_login where id=".$_GET['id'];
		$rs = mysql_query($sql) or die(mysql_error());
		$row = mysql_fetch_array($rs,MYSQL_ASSOC);
		echo "data:<br>";
		var_dump($row);
		echo "<hr/>";
	}else{
		$username = $_POST['username'];
		$password = $_POST['password'];
		
		$sql = "select * from injection_login where username={$username} and password={$password}";
		$rs = mysql_query($sql) or die(mysql_error());
		if ( $row = mysql_fetch_array($rs,MYSQL_ASSOC) ){
			echo "<font color=green>welcome<b><a href=?action=show&id={$row[id]} target=_blank> $row[username] </a> </b></font><hr/>";
		}else{
			echo "<font color=red>login fail!</font><hr/>";
		}	
		
	}
}
?>

<form method=post>
user:<input type=text name=username>
<br/>
pass:<input type=text name=password>
<br/>
<input type=submit value='submit'>
</form>

<?php

if($sql){
	echo "<hr/>sql executed:<br/><font size=8>$sql </font>"	;
}

?>