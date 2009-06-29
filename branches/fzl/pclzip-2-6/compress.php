<?php
	
	//http://www.majormoves.net/post/270/   pclzip基本参数和用法  不指定
	//http://www.ccvita.com/59.html			PclZip简介与使用
	//http://www.ccvita.com/330.html		PclZip:强大的PHP压缩与解压缩zip类
	header("Content-type: text/html; charset=UTF-8");
	$src = '../back0609/';
	require_once('pclzip.lib.php');
	$pclZip = new PclZip('compress.zip');
	if ($pclZip->create($src) == 0) 
	{
		//當有錯誤的時候，可以用這個顯示錯誤訊息
		die("Error : ".$pclZip->errorInfo(true));
	}
	 else
	 {
		echo 'OK!压缩'.$src.'成功!';
	 }

?>