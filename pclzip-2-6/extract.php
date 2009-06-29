<?php
	
	//http://www.majormoves.net/post/270/   pclzip基本参数和用法  不指定
	//http://www.ccvita.com/59.html			PclZip简介与使用
	//http://www.ccvita.com/330.html		PclZip:强大的PHP压缩与解压缩zip类
	header("Content-type: text/html; charset=UTF-8");
	$src = 'phpMyAdmin-2.11.4-all-languages.zip';
	require_once('pclzip.lib.php');
	$pclZip = new PclZip($src);
	if ($pclZip->extract() == 0) 
	{
		//當有錯誤的時候，可以用這個顯示錯誤訊息
		die("Error : ".$pclZip->errorInfo(true));
	}
	 else
	 {
		echo 'OK!解压'.$src.'成功!';
	 }

?>