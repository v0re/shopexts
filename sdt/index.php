<?php

define('MEAT',dirname(__FILE__));

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

$plugins = loadPlugins();
#
$module = $_REQUEST['module'];
if($module){
	#loadPlugins函数中已经把类文件包含进来了，这里可以直接new一个实例
	$instance = new $module;
	$instance->run();
}else{
#
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
<script type=text/javascript>
//<![cdata[
	function updatecontentpage(result) {
		contentpage = document.getElementById('ae-content');
		contentpage.innerHTML = result.responseText;
	}
	
	function loadmodule(name){
		var url="index.php";//url地址
		var pars="module="+name;
		var myajax = new ajax(url,pars,updatecontentpage);
		myajax.get();
	}

	function postdata(){
		var url="index.php";//url地址
		var pars="module="+name;
		var myajax = new ajax(url,pars,updatecontentpage);
		myajax.post();
	}

//]]>
</script>
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
		echo "<li><a class='handshape' onclick=loadmodule('".$classname."')>".$iterator['classvars']['label']."</a> </li>";
	}
?>
</ul>
</div>
</div>
<!-- end //-->
<div id=ae-content class=g-unit></div>
<!-- end //-->
</div>
<!-- foot //-->
<div id=ft>
<p>©2009  Do something for better life </p></div>

<script type=text/javascript>
  //<![cdata[

	function s() {
		if(document.body.scrollheight>document.body.clientheight-30) {
			scroll(0,document.body.scrollheight-document.body.clientheight+30);
		}
	}

  //]]>
  </script>
</div></div></body></html>

<?php

}

?>
