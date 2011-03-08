<?php	//check txtUsername and txtPassword and set variables
// CONNECT TO THE DATABASE SERVER
$query = "SELECT * FROM security ";
// query checks if user is in DB and if the passwords suit
$query .= "WHERE s_user='$txtUsername' AND s_password='$txtPassword';";
$mysql_result = query($query);
$row = mysql_fetch_row($mysql_result);
if ($row) {
  $authentication = "YES";
  $status = "Logged In";
  $user = $txtUsername;
  $laston = $row[4];

  $query = "SELECT d_name FROM userdepartments ";
  $query .= "WHERE s_user='$user';";
  $mysql_result = query($query);
  $row = mysql_fetch_row($mysql_result);
  $departments[0] = "$row[0]";
  for ($i=1;$row = mysql_fetch_row($mysql_result); $i++) {
    $departments[$i] = "$row[0]";  
  }

  //update timestamp for laston in security;
  $current_date = date("ymdHi"); 
  $query = "UPDATE security SET s_timestamp_laston='$current_date' WHERE s_user='$user';";
  $mysql_result = query($query);
}
else {
  $wronginfomsg = "<B>$l_error </B><I>$l_usernameorpasswordareincorrect</I><BR>\n";
  session_destroy(); // clear session to unset the status variable
}

?>
