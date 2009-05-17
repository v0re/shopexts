<?php
if(file_exists('syssite'))
{
	include_once("./syssite/home/shop/1/shop.cache.php");
	include_once("./include/class.HttpDown.php");
}
elseif(file_exists('shopadmin'))
{
	include_once("./home/shop/1/shop.cache.php");
	include_once("../include/class.HttpDown.php");
}
else
{
	include_once("shop.cache.php");
	include_once("../../../include/class.HttpDown.php");
}

$url='http://www.ebtea.com/index.php?gOo=shop.dwt';
$filename='testmakehtml.html';


MakeHtml($url,$filename,$tempstring);

echo "<hr>done!";

function MakeHtml($pageurl,$filename,$tempstring)//输入要生成静态的URL及保存的目标文件名即可生成静态
{
	$wget = new HttpDown;
	$wget->OpenUrl($pageurl); //打开页面
	$html=$wget->GetHtml();//取得主页面内容
	var_export($wget);
	$wget->Close();
	
	var_dump($html);

	if(strlen(trim($html)) == 0)
	{
		$html = "make ".$pageurl."<br> unsuccessful";
	}
	$fp = fopen($filename,"wb");
	fwrite($fp,$html.$tempstring);
	fclose($fp);
	@chmod($filename,0666);
}
?>