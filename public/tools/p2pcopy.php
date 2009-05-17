<?PHP
	header("Content-Type:text/html;charset=utf-8");
	include_once("../include/mall_config.php");
	include("mysql_db.php");
	set_time_limit(0);
	ini_set("display_errors",1);
	error_reporting(E_ALL^E_NOTICE);
	define("SYS_DEBUGING",1); 
	$db=new MYSQL_DB;
	function p2pcopy($record=0){
		global $db;
		$size=10;
		$sql="select count(gid) as recordcount from ".$GLOBALS['_tbpre']."mall_goods where ifobject<>3";
		$db->query($sql);
		$recordend=0;
		if ($db->next_record())
			$recordcount=$db->f('recordcount');
		echo "<h5>总共".$recordcount."个商品，分为".ceil($recordcount/$size)."卷处理，每卷".$size."个商品，当前第".($record+1)."卷。</h5>";
		if (intval(($record+1)*$size)>=$recordcount){
			 $sqlString="select gid,goods,smallimgremote,bigimgremote,multi_image from ".$GLOBALS['_tbpre']."mall_goods where ifobject<>3"." order by gid asc limit ".$size*$record.",".$size;
			 $recordend=1;
			  echo "<h5><font color=green>全部商品处理完毕！！！</font></h5>";
		 }
		 else{
			 $sqlString.="select gid,goods,smallimgremote,bigimgremote,multi_image from ".$GLOBALS['_tbpre']."mall_goods where ifobject<>3"." order by gid asc limit ".$size*$record.",".$size;
			 echo "<h5><font color=red>正在处理商品图片，请稍候......</font></h5>";	
		 }
		 $record = $record + 1;
		 $db->query($sqlString);
		 while($db->next_record()){
			 $smallimg=$db->f("smallimgremote");
			 $bigimg=$db->f("bigimgremote");
			 $multiimg=$db->f("multi_image");
			 $gid=$db->f("gid");
			  echo "<font style=font-size:12px;color:#999;>正在处理&nbsp;&nbsp;[".$db->f("goods")."]&nbsp;&nbsp;的图片....</font><br>";
			  unset($tmp_gid);
			  if (strlen($gid)>=4){
				$dir1=substr($gid,-4,2);
				$dir2=substr($gid,-2);
			  }
			  else{
				for($i=4-intval(strlen($gid));$i>0;$i--){
					$tmp_gid.="0";
				}
				$tmp_gid.=$gid;
				$dir1=substr($tmp_gid,0,2);
				$dir2=substr($tmp_gid,2,2);
			 }
			 if(!empty($dir1)&&!empty($dir2)){
				copy_file($smallimg,$bigimg,$multiimg,$dir1,$dir2, $gid);
			 } 
		 }
		 echo "</ul>";
		if (!$recordend){
			usleep(500);
			jsjmp($_SERVER['PHP_SELF']."?record=".$record);
		}
	}
	function copy_file($smallpic,$bigpic,$multipic,$dir1,$dir2,$goodsid){
		 if (!empty($smallpic)||!empty($bigpic)||!empty($multipic)){
			$smalpic= $smallpic;
			$bigpic = $bigpic;
			$mulpic = $multipic;
			copy_image($smalpic,$bigpic,$multipic,$dir1,$dir2,$goodsid);
		 }										  
	}
	function copy_image($smallimg,$bigimg,$multiimg,$dir1,$dir2,$goodsid){
		global $dbHost,$dbName,$dbUser,$dbPass,$_tbpre;
		$args['smallimg']=$smallimg;
		$args['bigimg']=$bigimg;
		$args['goodsid']=$goodsid;
		$arg=array($args);
		ini_set("memory_limit","30M");
		if (!is_object($ImgCore)){
			include_once('GoodsImgCore.php');
			$ImgCore=new GoodsImgCore();
		} 
		$ImgCore->run('goodsimage','save',1,$arg);
		if (!empty($multiimg))
			copymultiimg($multiimg,$dir1,$dir2,$goodsid);
	}
	function copymultiimg($mulpic,$dir1,$dir2,$goodsid){
		if (!is_dir("../images/goods/".$dir1)){
			@mkdir("../images/goods/".$dir1);
		}
		if (!is_dir("../images/goods/".$dir1."/".$dir2))
			@mkdir("../images/goods/".$dir1."/".$dir2);
		$basicdir="../images/goods/";
		$resbigdir="../syssite/home/shop/1/pictures/productsimg/big/";
		$imgbasicdir=$basicdir.$dir1."/".$dir2."/";
		if (!empty($mulpic)){
			$img_ary=explode("&&&",$mulpic);
			if (is_array($img_ary)){
				foreach($img_ary as $imk => $imv){
					if (!empty($imv)){
						 if (substr($imv,0,7)=="http://"||substr($imv,0,8)=="https://")
							 continue;
						 else{
							 $mulname = substr($imv,0,strrpos($imv,"."));
							 $mulstyle = strtolower(strrchr($imv,"."));
							 if ($mulstyle==".jpeg")
								 $mulstyle = ".jpg";	
							 if (file_exists($resbigdir.$imv)){
								$mdname=md5($mulname."_mul");
								if (is_dir($imgbasicdir)){
									@copy($resbigdir.$imv,$imgbasicdir."gpic_".$mdname.$mulstyle);
									@copy($resbigdir.$imv,$imgbasicdir."gpic_".$mdname."_big".$mulstyle);
									@copy($resbigdir.$imv,$imgbasicdir."gpic_".$mdname."_small".$mulstyle);
									@copy($resbigdir.$imv,$imgbasicdir."gpic_".$mdname."_thumbnail".$mulstyle);
								}
							 }
						 }
					}
				}
			}
		}
	}
	function jsjmp($url){			
		echo <<<EOF
		<script language="JavaScript">
		<!--
					location= '{$url}';
		//-->
		</script>
EOF;
	}
	if ($_GET['record'])
		$record = intval($_GET['record']);
	else
		$record = 0;
	p2pcopy($record);
?>