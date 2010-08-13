<?php

require "shell.php";

function test_is_exception(){
	$directory = "./home/cache/front_tmpl";
	$file = "03a9f8c100af014be78fe9a9c5f87faf.php";
	$ret = is_exception($directory,$file);
	var_dump($ret);	
	$directory = "./core/admin/controller";
	$file = "ctl.template.php";
	$ret = is_exception($directory,$file);
	var_dump($ret);	
}


test_is_exception();