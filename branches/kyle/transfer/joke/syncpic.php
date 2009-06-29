<?php
set_time_limit(0);
error_reporting(E_ALL^E_NOTICE);
?>
<html>
<head>
<title>shopex安装</title>
<META HTTP-EQUIV="content-type" CONTENT="text/html; charset=gb2312">
</head>
<body style="font-size:16px;">
<?php
	require("../include/mall_config.php");
	$link=mysql_pconnect($dbHost,$dbUser,$dbPass); 
	mysql_select_db($dbName);	
	$prefix='../syssite/home/shop/1/pictures/productsimg/';
	$query="select gid,smallimgremote,bigimgremote from sdb_mall_goods";
	$rs=mysql_query($query,$link) or die(mysql_error());
	$ok=0;
	$fail=0;
	while($row=mysql_fetch_array($rs))
	{
		//print_r($row);
		$small="../../".$row['smallimgremote'];
		$big="../../".$row['bigimgremote'];
		$news=$prefix."small/".$row['gid'].".jpg";
		$newb=$prefix."big/".$row['gid'].".jpg";
		if(file_exists($news)) unlink($news);
		if(@copy($small,$news))
		{
				$query="update sdb_mall_goods set smallimgremote='".$row['gid'].".jpg' where gid='".$row['gid']."'";
				mysql_query($query) or die(mysql_error());
				msg( "将".$small."改为".$news."成功","ok");
				$ok++;
		}
		else 
		{
				msg( "将".$small."改为".$news."失败","fail");
				$fail++;
		}
		if(file_exists($newb)) unlink($newb);
		if(@copy($big,$newb))
		{
				$query="update sdb_mall_goods set bigimgremote='".$row['gid'].".jpg' where gid='".$row['gid']."'";
				mysql_query($query) or die(mysql_error());
				msg( "将".$big."改为".$newb."成功","ok");
				$ok++;
		}
		else 
		{
				msg( "将".$big."改为".$newb."失败","fail");	
				$fail++;
		}
	}
	print "<hr color=red><br>";
	print "总共转换了".($ok+$fail)."个图片文件,其中成功".$ok."个，失败".$fail."个";
	
	function msg($str,$state)
	{
		if($state=="ok")
		{
			$str="<font color=\"green\" size=\"3\">".$str."</font><br>";
		}
		if($state=="fail")
		{
			$str="<font color=\"red\" size=\"3\">".$str."</font><br>";
		}
		$str.=<<<MSG
		<script>
			if(document.body.scrollHeight>document.body.clientHeight-30)
		 	{
				 scroll(0,document.body.scrollHeight-document.body.clientHeight+30);
			}
		</script>
MSG;

		flush();
		echo $str;

	}
?>

</body>
</html>