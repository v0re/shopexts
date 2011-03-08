<?php   //Logout script created by Seek3r

/*
setcookie("status", "",time()+$g_cookietimeout, "$g_base_path", "$g_base_url", 0);
setcookie("user", "", time()+$g_cookietimeout, "$g_base_path", "$g_base_url", 0);
setcookie("group", "", time()+$g_cookietimeout, "$g_base_path", "$g_base_url", 0);
setcookie("authentication", "",time()+$g_cookietimeout, "$g_base_path", "$g_base_url", 0);
setcookie("laston", "", time()+$g_cookietimeout, "$g_base_path", "$g_base_url", 0);
*/

/*
setcookie("status");
setcookie("user");
setcookie("group");
setcookie("authentication");
setcookie("laston");
*/

/*
setcookie("status", "", time(), "$g_base_path", "$g_base_url", 0);
setcookie("user", "", time(), "$g_base_path", "$g_base_url", 0);
setcookie("group", "", time(), "$g_base_path", "$g_base_url", 0);
setcookie("authentication", "", time(), "$g_base_path", "$g_base_url", 0);
setcookie("laston", "", time(), "$g_base_path", "$g_base_url", 0);
*/

setcookie("status", "",time()+$g_cookietimeout);
setcookie("user", "", time()+$g_cookietimeout);
setcookie("group", "", time()+$g_cookietimeout);
setcookie("authentication", "",time()+$g_cookietimeout);
setcookie("laston", "", time()+$g_cookietimeout);

unset($status);
unset($user);
unset($authentication);
unset($laston);
unset($logout);
?>
