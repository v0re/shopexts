<?php	// connect to database

if( isset( $CONNECT ) ) {
    return;
}

$CONNECT=1;

if ($db_type == "mysql") {
  $mysql_link = mysql_connect("$db_server", "$db_username", "$db_password");
  if (!$mysql_link) {
    print "<B>$l_error</B> <I>$l_cannotconnecttodatabase</I>\n";
    exit;
  }
  mysql_select_db($db_db, $mysql_link);  
}

?>
