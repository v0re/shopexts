<?php
//catid 	offerid 	pid 	cat
require '../include/mall_config.php';
require 'inc.php';
require 'class.mysql.php';
$global_navi;
?>
<SCRIPT LANGUAGE="JavaScript">
<!--
	window.parent.document.getElementById('car_tree').style.display = '';
//-->
</SCRIPT>
<?php
if(( $_GET['goodsid'] == '') 
	&& ($_GET['pid'] == '')  )
	{//new arrival 显示所有的列表
		$P_NAV ="<a href='/'>YouTubuy</a> &gt;&gt; New Arrival" ;
		
		$P_NAV_seach ="<a href='/'>YouTubuy</a> &gt;&gt; Advance Search" ;
		
		$P_NAV_recommend ="<a href='/'>YouTubuy</a> &gt;&gt; Recommend" ;
?>
<div id="cosbetanav"><span class="cattitle">Catalogue</span><?php echo $P_NAV;?></div>
<div id="cosbeta_ADV_SEARCH"><span class="cattitle">Catalogue</span><?php echo $P_NAV_seach;?></div>
<div id="cosbetanav"><span class="cattitle">Catalogue</span><?php echo $P_NAV;?></div>

<div id="cosbeta_recommend"><span class="cattitle">Catalogue</span><?php echo $P_NAV_recommend;?></div>

<div id="cosbeta_hot"><span class="cattitle">Catalogue</span><a href='/'>YouTubuy</a> &gt;&gt; Hot Sale</div>

<div id="cosbeta_special"><span class="cattitle" >Catalogue</span><a href='/'>YouTubuy</a> &gt;&gt;What's hot style</div>	

<div id="cosbeta_sdfrd"><span class="cattitle" >Catalogue</span><a href='/'>YouTubuy</a> &gt;&gt;Tell a friend</div>	

	<SCRIPT LANGUAGE="JavaScript">
<!--
	var topmenu = 'top-new';
	var img = '/cosbeta/images/cos_new.gif';
	if(window.parent.document.getElementById(topmenu)  != null)
	window.parent.document.getElementById(topmenu).src = img;
	
	if(window.parent.document.getElementById('productNav')  != null)
	window.parent.document.getElementById('productNav').innerHTML = document.getElementById('cosbetanav').innerHTML ;
	var current_url =window.parent.location+" ";
	if(current_url.indexOf("goods_search_more") != -1 )
	window.parent.document.getElementById('productNav').innerHTML = document.getElementById('cosbeta_ADV_SEARCH').innerHTML ;
	if(current_url.indexOf("recommend") != -1 )
		window.parent.document.getElementById('productNav').innerHTML = document.getElementById('cosbeta_recommend').innerHTML ;
	if(current_url.indexOf("=hot") != -1 )
		window.parent.document.getElementById('productNav').innerHTML = document.getElementById('cosbeta_hot').innerHTML ;
	if(current_url.indexOf("=special") != -1 )
		window.parent.document.getElementById('productNav').innerHTML = document.getElementById('cosbeta_special').innerHTML ;
	if(current_url.indexOf("sendtofriend") != -1 )
		window.parent.document.getElementById('productNav').innerHTML = document.getElementById('cosbeta_sdfrd').innerHTML ;
	
	
	//-->
</SCRIPT>
<?php
	die();
}


if( $_GET['goodsid']){/*
	//查询本产品的类别
	$SQL = "SELECT  catid  FROM ".GOODS_TB." WHERE gid=".$_GET['goodsid'] ;echo $SQL;
	$Mysql = new Mysql(DB_NAME);
	$Mysql->doQuery($SQL);
	$obj=$Mysql->GetData();
	$cur_p_cat = $obj->catid;//当前类别的ID
*/
	$SQL = "SELECT  ".CAT_TB.".pid pid ,".CAT_TB.".catid catid  FROM ".CAT_TB.",".GOODS_TB." WHERE ".GOODS_TB.".catid = ".CAT_TB.".catid  AND ".GOODS_TB.".gid=".$_GET['goodsid']  ;

	$Mysql = new Mysql(DB_NAME);
	$Mysql->doQuery($SQL);
	$obj=$Mysql->GetData();
	$pid = $obj->pid;//当前类别的pid
	$catid = $obj->catid;//当前类别的id
	$currentcatpid = $obj->pid;

	//获取本级目录
	$SQL = "SELECT catid,offerid,pid,cat FROM ".CAT_TB." WHERE pid=".$pid ;
	$Mysql = new Mysql(DB_NAME);
	$Mysql->doQuery($SQL);
	$i= 0;
	$curl = "<ul>\n";
	while( $obj=$Mysql->GetData() ){
		if( $catid== $obj->catid ) $class="class='cur-menu'";
		else $class="";
		$curl .= "<li ".$class."><a href='/index.php?gOo=goods_category.dwt&gcat=".$obj->catid."&sub=".$obj->catid."' >". $obj->cat."</li>\n";
		$lastpid = $obj->pid;
		$i ++ ;

	}
	$curl .="</ul>";
	//
	//继续获取最上级目录
	//
	$SQL2 = "SELECT pid FROM ".CAT_TB." WHERE catid=".$lastpid ;

	$Mysql2 = new Mysql(DB_NAME);
	$Mysql2->doQuery($SQL2);
	$obj2=$Mysql2->GetData();
	$topnav = $obj2->pid; 
	//
   //获取二级目录
   //
	$SQL = "SELECT catid,offerid,pid,cat FROM ".CAT_TB." WHERE pid=".$topnav ;
	$Mysql = new Mysql(DB_NAME);
	$Mysql->doQuery($SQL);
	$i= 0;
	echo "<div id='catmenu'>";
	while( $obj=$Mysql->GetData() ){
		if( $currentcatpid == $obj->catid ) $class="class='cur-menu'";
		else $class="";
		echo   "<li ".$class."><a href='/cosbeta/cat.php?pid=".$obj->catid."&sub=".$obj->catid."' target='submenu'>". $obj->cat."</a></li>\n";
        if( $obj->catid == $currentcatpid ) {
			//获取导航条
			echo $curl;
		}
		$i ++ ;

	}
		echo   "<li class='wthot'><a href='/index.php?gOo=goods_search_list.dwt&gtype=special'>What's hot style</a></li>\n";
	echo "</div>";



	echo "<SCRIPT LANGUAGE=\"JavaScript\">
<!--
	var topmenu = 'top-".$topnav."';
	var img = '/cosbeta/images/' + topmenu + '.gif';
	var pele = 'car_tree';
	var cur_menu = 'menu-a-".$lastpid."';
	if(window.parent.document.getElementById(pele)  != null)
	window.parent.document.getElementById(pele).innerHTML = document.getElementById('catmenu').innerHTML;
	if(window.parent.document.getElementById(topmenu)  != null)
	window.parent.document.getElementById(topmenu).src = img;//set cur image

//-->
</SCRIPT>";
die();
}
?>

<?php if( $_GET['pid'] != ''){

$SecondCat[0] = $_GET['pid'];
$SQL = "SELECT  pid,cat  FROM ".CAT_TB." WHERE catid=".$_GET['pid'] ;
$Mysql = new Mysql(DB_NAME);
$Mysql->doQuery($SQL);
$obj=$Mysql->GetData();
$SecondCat[1] = $obj->pid;
$navname = $obj->cat;
if(  $obj->pid == 0 ){
//直接二级分类
	echo "<div id='menu'><ul  id='catmenu'>\n";
		$SQL = "SELECT   catid,offerid ,cat  FROM ".CAT_TB." WHERE pid=".$_GET['pid'] ;
		$Mysql = new Mysql(DB_NAME);
		$Mysql->doQuery($SQL);
		while( $obj=$Mysql->GetData()){
			if( $SecondCat[1] == $obj->catid ) $class="class='cur-menu'";	else $class="";
			echo "<li ".$class."><a href='/cosbeta/cat.php?pid=".$obj->catid."'  target='submenu' >". $obj->cat."</li>\n";
		}
		
		echo   "<li class='wthot'><a href='/index.php?gOo=goods_search_list.dwt&gtype=special'>What's hot style</a></li>\n";
		echo "</ul>";

	$P_NAV ="<a href='/'>YouTubuy</a> &gt;&gt; " .$navname;
?>
<div id="cosbetanav"><span class="cattitle">Catalogue</span><?php echo $P_NAV;?></div>

<SCRIPT LANGUAGE="JavaScript">
<!--
	if( (window.parent.document.getElementById('car_tree')!= null) && ( document.getElementById('catmenu') != null))
	window.parent.document.getElementById('car_tree').innerHTML = document.getElementById('catmenu').innerHTML;
	var topmenu = 'top-<?php echo $_GET['pid'] ;?>';
	var img = '/cosbeta/images/' + topmenu + '.gif';
	if(window.parent.document.getElementById(topmenu)  != null)
	window.parent.document.getElementById(topmenu).src = img;

	if(window.parent.document.getElementById('productNav')  != null)
	window.parent.document.getElementById('productNav').innerHTML = document.getElementById('cosbetanav').innerHTML ;

//-->
</SCRIPT>
<?php
	die();
}
//直接二级分类完璧
//继续获取上级id
if( $SecondCat[1] != 0 ){
	$SQL = "SELECT  pid  FROM ".CAT_TB." WHERE catid=".$SecondCat[1];
	$Mysql = new Mysql(DB_NAME);
	$Mysql->doQuery($SQL);
	$obj=$Mysql->GetData();
	$SecondCat[2] = $obj->pid;
	if( $SecondCat[2] != 0 ){
		$SQL = "SELECT  pid  FROM ".CAT_TB." WHERE catid=".$SecondCat[2] ;
		$Mysql = new Mysql(DB_NAME);
		$Mysql->doQuery($SQL);
		$obj=$Mysql->GetData();
		$SecondCat[3] = $obj->pid;
		
	}
}
if( $SecondCat[1] == 0 )die();	
 if( $SecondCat[2] != 0 ){
	echo "<div id='menu'><ul id='catmenu'>\n";
	$SQL = "SELECT   catid,offerid ,cat  FROM ".CAT_TB." WHERE pid=".$SecondCat[2] ;
	$Mysql = new Mysql(DB_NAME);
	$Mysql->doQuery($SQL);
	while( $obj=$Mysql->GetData()){
		if( $SecondCat[1] == $obj->catid ) $class="class='cur-menu'";	else $class="";
		echo "<li ".$class."><a href='/cosbeta/cat.php?pid=".$obj->catid."'  target='submenu'>". $obj->cat."</li>\n";

		//建立三级目录
		
		if( $SecondCat[1] == $obj->catid ){

			echo "<ul id='catmenu'>\n";
			$SQL2 = "SELECT   catid,offerid ,cat  FROM ".CAT_TB." WHERE pid=".$SecondCat[1] ;
			$Mysql2 = new Mysql(DB_NAME);
			$Mysql2->doQuery($SQL2);
			while( $obj2=$Mysql2->GetData()){
				if( $_GET['pid'] == $obj2->catid ) {$class ="class='cur-menu'";}
				else $class="";
				echo "<li ".$class."><a href='/index.php?gOo=goods_category.dwt&gcat=".$obj2->catid."&sub=".$obj2->catid."' >".$obj2->cat."</li>\n";
			}
			echo "</ul>";

		}

	}
	
		echo   "<li class='wthot'><a href='/index.php?gOo=goods_search_list.dwt&gtype=special'>What's hot style</a></li>\n";
	echo "</div>";

}
else if( $SecondCat[1] != 0 ){
	echo "<div id='menu'><ul id='catmenu'>\n";
	$SQL = "SELECT   catid,offerid ,cat  FROM ".CAT_TB." WHERE pid=".$SecondCat[1] ;
	$Mysql = new Mysql(DB_NAME);
	$Mysql->doQuery($SQL);
	while( $obj=$Mysql->GetData()){
		if( $_GET['pid'] == $obj->catid ) $class="class='cur-menu'";	else $class="";
		echo "<li ".$class."><a href='/cosbeta/cat.php?pid=".$obj->catid."'  target='submenu'>". $obj->cat."</li>\n";

		//建立三级目录
		
		if( $_GET['pid'] == $obj->catid ){

			echo "<ul id='catmenu'>\n";
			$SQL2 = "SELECT   catid,offerid ,cat  FROM ".CAT_TB." WHERE pid=".$obj->catid;
			$Mysql2 = new Mysql(DB_NAME);
			$Mysql2->doQuery($SQL2);
			while( $obj2=$Mysql2->GetData()){
				if( $_GET['pid'] == $obj2->catid ) $class="class='cur-menu'";
				else $class="";
				echo "<li ".$class."><a href='/index.php?gOo=goods_category.dwt&gcat=".$obj2->catid."&sub=".$obj2->catid."' >".$class. $obj2->cat."</li>\n";


			}
			echo "</ul>";

		}

	}
	
		echo   "<li class='wthot'><a href='/index.php?gOo=goods_search_list.dwt&gtype=special'>What's hot style</a></li>\n";
	echo "</div>";

} 
else{
}
if( $SecondCat[2] == 0 )
	$thetop = $SecondCat[1];
else 
	$thetop = $SecondCat[2];

}//end of if pid

?>
<?php
//单独处理menu
$P_NAV = ""; 
$SQL = "SELECT  pid,cat,catid  FROM ".CAT_TB." WHERE catid=".$_GET['pid'];
$Mysql = new Mysql(DB_NAME);
$Mysql->doQuery($SQL);
$obj=$Mysql->GetData();
$P_NAV = "<a href='?gOo=goods_category.dwt&gcat=".$obj->catid."'>".$obj->cat."</a>";


$SQL = "SELECT  pid,cat,catid  FROM ".CAT_TB." WHERE catid=".$obj->pid;
$Mysql = new Mysql(DB_NAME);
$Mysql->doQuery($SQL);
$obj=$Mysql->GetData();
if( $obj->catid != '')
$P_NAV = "<a href='?gOo=goods_category.dwt&gcat=".$obj->catid."'>".$obj->cat."</a> &gt;&gt; " . $P_NAV;

 
$SQL = "SELECT  pid,cat,catid  FROM ".CAT_TB." WHERE catid=".$obj->pid;
$Mysql = new Mysql(DB_NAME);
$Mysql->doQuery($SQL);
$obj=$Mysql->GetData();
if( $obj->catid != '')
$P_NAV = "<a href='?gOo=goods_category.dwt&gcat=".$obj->catid."'>".$obj->cat."</a> &gt;&gt; " . $P_NAV;

$P_NAV ="<a href='/'>YouTubuy</a> &gt;&gt; " .$P_NAV;
?>
<div id="cosbetanav"><span class="cattitle">Catalogue</span><?php echo $P_NAV;?></div>
<SCRIPT LANGUAGE="JavaScript">
<!--
	if( (window.parent.document.getElementById('car_tree')!= null) && ( document.getElementById('catmenu') != null))
	window.parent.document.getElementById('car_tree').innerHTML = document.getElementById('catmenu').innerHTML;
	var topmenu = 'top-<?php echo $thetop ;?>';
	var img = '/cosbeta/images/' + topmenu + '.gif';
	if(window.parent.document.getElementById(topmenu)  != null)
	window.parent.document.getElementById(topmenu).src = img;
	
	if(window.parent.document.getElementById('productNav')  != null)
	window.parent.document.getElementById('productNav').innerHTML = document.getElementById('cosbetanav').innerHTML ;

//-->
</SCRIPT>