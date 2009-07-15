<?php

define('ROOT',dirname(__FILE__));
define('PLUGINSDIR',ROOT."/plugins");
define('VARDIR',ROOT."/var");

include ROOT."/functions.php";

if(!isallowed()){
	die('您不允许使用该工具');
}

#base on shopex 48
$configfile = ROOT."/../config/config.php";
if(file_exists($configfile)){
	define('SHOPEXVER',48);
	include $configfile;
}else{
	$configfile = ROOT."/../include/mall_config.php";
	#convert to shopex48 format
	if(file_exists($configfile)){
		define('SHOPEXVER',47);
		include $configfile;
		define('DB_USER', $dbName);     
		define('DB_PASSWORD', $dbPass);
		define('DB_NAME', $dbName);   
		define('DB_HOST', $dbHost);    
		define('DB_PREFIX', $_tbpre);
		define('DB_CHARSET',MYSQL_CHARSET_NAME);
	}else{
		msg("找不到数据库配置文件");
		die();
	}
}

if(!defined(DB_PORT)){
	define('DB_PORT','3306');
}
if(!defined(DB_CHARSET)){
	define('DB_CHARSET','utf8');
}

mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die(mysql_error());
mysql_select_db(DB_NAME);
mysql_query("set names ".DB_CHARSET);

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
		$label = $classvars['label'];
		$plugins[$label]['classname'] = $classname;
		$plugins[$label]['classfilename'] = $classfilename;
		$plugins[$label]['classvars'] = $classvars;

	}
}
$d->close();
#
$module = $_REQUEST['module'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>ShopEx工具箱</title>
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
	<a href="<?php echo $_SERVER['PHP_SELF']; ?>"><img id=ae-logo src="statics/logo.gif" width=153 height=47></a>
</div>
<div id=ae-appbar-lrg class=g-section>
<h1>ShopEx Tools Console(47/48版本适用)</h1></div></div>
<!-- end //-->
<div id=bd class=g-section>
<div class="g-section g-tpl-160">
<!-- left navigator //-->
<div id=ae-lhs-nav class="g-unit g-first">
<div id=ae-nav class=g-c>
<ul id=menu>
<?php

	ksort($plugins);
	foreach($plugins as $label=>$iterator) {
		if($module == $iterator['classname']){
			echo "<li><a class='ae-nav-selected' href='?module=".$iterator['classname']."'>".$label."</a> </li>";
		}else{
			echo "<li><a href='?module=".$iterator['classname']."'>".$label."</a> </li>";
		}
	}
?>
</ul>
</div>
</div>
<!-- end //-->
<div id=ae-content class=g-unit>
<?php

if($module){
	#类文件包含进来过了，这里可以直接new一个实例	
	$instance = new $module;
	$instance->run();
}
?>
</div>
<!-- end //-->
</div>
<!-- foot //-->
<div id=ft>
<p>Do something for better life ©2009  </p></div>


</div></div></body></html>

