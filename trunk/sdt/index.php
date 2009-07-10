<?php

define('MEAT',dirname(__FILE__));
define('PLUGINSDIR',MEAT."/plugins");

include MEAT."/functions.php";

#base on shopex 48
$configfile = MEAT."/../config/config.php";
if(file_exists($configfile)){
	include $configfile;
}else{
	$configfile = MEAT."/../include/mall_config.php";
	#convert to shopex48 format
	if(file_exists($configfile)){
		include $configfile;
		define('DB_USER', $dbName);     
		define('DB_PASSWORD', $dbPass);
		define('DB_NAME', $dbName);   
		define('DB_HOST', $dbHost);    
		define('DB_CHARSET',MYSQL_CHARSET_NAME);
	}else{
		msg("找不到数据库配置文件");
	}
}

mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die(mysql_error());
mysql_select_db(DB_NAME);

#装载全部的插件

$d = dir(PLUGINSDIR);
$plugins = array();
while (false !== ($iterator = $d->read())) {
	if(@preg_match("/^plug\.(.+)\.php$/",$iterator,$rent)){
		$classfilename = $rent[0];
		$classname = $rent[1];
		include(PLUGINSDIR."/$classfilename");
		$instance = new $classname();
		$classvars = get_class_vars(get_class($instance));
		$plugins[$classname]['classfilename'] = $classfilename;
		$plugins[$classname]['classvars'] = $classvars;
	}
}
$d->close();

?>

<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>ShopEx工具箱</title>
<meta content="text/html; charset=utf-8" http-equiv=content-type>
<style type=text/css>
	@import url( statics/css.css );
</style>
<script type="text/javascript" src="statics/ajax.js"></script>
<script type="text/javascript" src="statics/functions.js"></script>
</head>
<body>

<div class=g-doc>
<!-- header //-->
<div id=hd class=g-section>
<div class=g-section>
	<a href="/"><img id=ae-logo src="statics/logo.gif" width=153 height=47></a>
</div>
<div id=ae-appbar-lrg class=g-section>
<h1>ShopEx Tools Console</h1></div></div>
<!-- end //-->
<div id=bd class=g-section>
<div class="g-section g-tpl-160">
<!-- left navigator //-->
<div id=ae-lhs-nav class="g-unit g-first">
<div id=ae-nav class=g-c>
<ul id=menu>
<?php
	foreach($plugins as $classname=>$iterator) {
		echo "<li><a href='?module=".$classname."'>".$iterator['classvars']['label']."</a> </li>";
	}
?>
</ul>
</div>
</div>
<!-- end //-->
<div id=ae-content class=g-unit>
<?php

$module = $_REQUEST['module'];
if($module){
	#loadPlugins函数中已经把类文件包含进来了，这里可以直接new一个实例	
	$instance = new $module;
	$instance->run();
}
?>
</div>
<!-- end //-->
</div>
<!-- foot //-->
<div id=ft>
<p>©2009  Do something for better life </p></div>


</div></div></body></html>

