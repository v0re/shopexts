<?php
define("BASE_DIR","core");
define("__ADMIN__","admin");
//define("__SHOP__","shop");
define("SYSTEM_CACHE_DIR","syssite/home/cache/system/");
define("USER_CACHE_DIR","syssite/home/cache/1/");

include_once(BASE_DIR.'/lib/smarty/Smarty.class.php');
$smarty = new smarty();
$smarty->compile_check = true;
$smarty->debug = true;

if(defined('__ADMIN__')){
	$smarty->plugins_dir[] = BASE_DIR.'/'.__ADMIN__.'/smartyplugin/';
	$smarty->template_dir = BASE_DIR.'/'.__ADMIN__.'/view/';
	$smarty->compile_dir = SYSTEM_CACHE_DIR;
}elseif(defined('__SHOP__')){
	$smarty->plugins_dir[] = BASE_DIR.'/'.__SHOP__.'/smartyplugin';
	$smarty->template_dir = BASE_DIR.'/'.__SHOP__.'/view/';
	$smarty->compile_dir = USER_CACHE_DIR;
}
$smarty->left_delimiter='<{';
$smarty->right_delimiter='}>';

error_log(var_export($smarty,1),3,__FILE__.".smarty.log");


$tt =  $smarty->fetch("login.html");

var_export($tt);


echo "<br><font color=red>---------------------------------The End------------------------------------------</font>";

?>