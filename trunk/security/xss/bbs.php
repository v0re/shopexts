<?php
session_start();
setcookie('account','belong ton me');
require("../share.php");
if ($_POST) {
	$sql = "insert into xss_bbs (name,comment) values ('".$_POST[name]."','".$_POST[comment]."')";
	mysql_query($sql) or die($sql);
}
$sql = "select * from xss_bbs";
$rs = mysql_query($sql) or die(mysql_error());
while($row = mysql_fetch_array($rs,MYSQL_ASSOC)){
	echo "<b>".$row['name']."</b> say :<br/>";
	echo $row['comment']."<br/><hr/>";
}

?>
 <form action="bbs.php" method="POST" />

  <p>Name: <input type="text" name="name" /><br />

  Comment: <textarea name="comment" rows="10" cols="60"></textarea><br />

  <input type="submit" value="Add Comment" /></p>

  </form>
