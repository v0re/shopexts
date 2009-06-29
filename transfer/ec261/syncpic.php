<?php
	require("../include/mall_config.php");
	set_time_limit(0);
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
		$small="../".$row['smallimgremote'];
		$big="../".$row['bigimgremote'];
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

	$prefix='../syssite/home/shop/1/pictures/brandimg/';
	$query="select brand_id,brand_logo from sdb_mall_brand";
	$rs=mysql_query($query,$link) or die(mysql_error());
	$ok=0;
	$fail=0;

	while($row=mysql_fetch_array($rs))
	{
		$old_brand = "../".$row['brand_logo'];
		$new_brand=$prefix."brand_".$row['brand_id'].".jpg";
		if(file_exists($news)) unlink($news);
		if(@copy($old_brand,$new_brand))
		{
				$query="update sdb_mall_brand set brand_logo='brand_".$row['brand_id'].".jpg' where brand_id='".$row['brand_id']."'";
				mysql_query($query) or die(mysql_error());
				msg( "将".$old_brand."改为".$new_brand."成功","ok");
				$ok++;
		}
		else 
		{
				
				msg( "将".$old_brand."改为".$new_brand."失败","fail");
				$fail++;
		}
	}
	
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