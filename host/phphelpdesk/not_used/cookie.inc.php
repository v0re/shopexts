<?php

// Modified by Andrew Walker (ajwalker@home.com)
//$time = mktime()+$g_cookietimeout;
//$date = date("l, d-M-y H:i:s", ($time));
$date = time()+$g_cookietimeout;
if (isset($authentication)) {
//echo "subroutine,";
//echo $status, $date, $authentication;
//echo "<br>";
    setcookie("status", $status, $date);
    setcookie("user", $user, $date);
    setcookie("group", $group, $date);
    //setcookie("authentication", $authentication, $date);
    setcookie("laston", $laston, $date);
}
else {
    setcookie("status", "Logged In", $date);
    setcookie("user", $txtUsername, $date);
    setcookie("group", $row[4], $date);
    setcookie("authentication", "YES", $date);
    setcookie("laston", $row[6], $date);
}

?>