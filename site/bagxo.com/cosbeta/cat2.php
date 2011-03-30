<?php
//catid 	offerid 	pid 	cat
require '../include/mall_config.php';
require 'inc.php';
require 'class.mysql.php';


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

	//获取本级目录
	$SQL = "SELECT catid,offerid,pid,cat FROM ".CAT_TB." WHERE pid=".$pid ;
	$Mysql = new Mysql(DB_NAME);
	$Mysql->doQuery($SQL);
	$i= 0;
	echo "<div id='detailsubmenu'><ul>\n";
	while( $obj=$Mysql->GetData() ){
		if( $catid== $obj->catid ) $class="class='cur-menu'";
		else $class="";
		echo "<li ".$class."><a href='/index.php?gOo=goods_category.dwt&gcat=".$obj->catid."&sub=".$obj->catid."' >". $obj->cat."</li>\n";

		$i ++ ;

	}
	echo "</ul></div>";

	echo "<SCRIPT LANGUAGE=\"JavaScript\">
<!--
	var pele = 'menu_li_".$pid."';
	if(window.parent.document.getElementById(pele).innerHTML != null)
	window.parent.document.getElementById(pele).innerHTML = document.getElementById('detailsubmenu').innerHTML;
//-->
</SCRIPT>";

}
?>

<?php if( $_GET['pid'] != ''){

$SecondCat[0] = $_GET['pid'];
$SQL = "SELECT  pid  FROM ".CAT_TB." WHERE catid=".$_GET['pid'] ;
echo $SQL."</br>";
$Mysql = new Mysql(DB_NAME);
$Mysql->doQuery($SQL);
$obj=$Mysql->GetData();
$SecondCat[1] = $obj->pid;
//继续获取上级id
if( $SecondCat[1] != 0 ){
	$SQL = "SELECT  pid  FROM ".CAT_TB." WHERE catid=".$SecondCat[1];echo $SQL."</br>";
	$Mysql = new Mysql(DB_NAME);
	$Mysql->doQuery($SQL);
	$obj=$Mysql->GetData();
	$SecondCat[2] = $obj->pid;
	if( $SecondCat[2] != 0 ){
		$SQL = "SELECT  pid  FROM ".CAT_TB." WHERE catid=".$SecondCat[2] ;echo $SQL."</br>";
		$Mysql = new Mysql(DB_NAME);
		$Mysql->doQuery($SQL);
		$obj=$Mysql->GetData();
		$SecondCat[3] = $obj->pid;
		
	}
}
echo $SecondCat[0]."|".$SecondCat[1]."|".$SecondCat[2]."|";
if( $SecondCat[1] == 0 )die();	
 if( $SecondCat[2] != 0 ){
	echo "ssssssssssss";
	echo "<div id='menu'><ul id='catmenu'>\n";
	$SQL = "SELECT   catid,offerid ,cat  FROM ".CAT_TB." WHERE pid=".$SecondCat[2] ;
	$Mysql = new Mysql(DB_NAME);
	$Mysql->doQuery($SQL);
	while( $obj=$Mysql->GetData()){
		if( $SecondCat[1] == $obj->catid ) $class="class='cur-menu'";	else $class="";
		echo "<li ".$class."><a href='/index.php?gOo=goods_category.dwt&gcat=".$obj->catid."' >". $obj->cat."1</li>\n";

		//建立三级目录
		
		if( $SecondCat[1] == $obj->catid ){

			echo "<ul id='catmenu'>\n";
			$SQL2 = "SELECT   catid,offerid ,cat  FROM ".CAT_TB." WHERE pid=".$SecondCat[1] ;
			$Mysql2 = new Mysql(DB_NAME);
			$Mysql2->doQuery($SQL2);
			while( $obj2=$Mysql2->GetData()){
				if( $_GET['pid'] == $obj2->catid ) {$class="class='cur-menu'";}
				else $class="";
				echo "<li ".$class."><a href='/index.php?gOo=goods_category.dwt&gcat=".$obj2->catid."&sub=".$obj2->catid."' >". $obj2->cat."</li>\n";


			}
			echo "</ul>";

		}

	}

}
else if( $SecondCat[1] != 0 ){
	echo "<div id='menu'><ul id='catmenu'>\n";
	$SQL = "SELECT   catid,offerid ,cat  FROM ".CAT_TB." WHERE pid=".$SecondCat[1] ;
	$Mysql = new Mysql(DB_NAME);
	$Mysql->doQuery($SQL);
	while( $obj=$Mysql->GetData()){
		if( $SecondCat[1] == $obj->catid ) $class="class='cur-menu'";	else $class="";
		echo "<li ".$class."><a href='/index.php?gOo=goods_category.dwt&gcat=".$obj->catid."' >". $obj->cat."</li>\n";

		//建立三级目录
		
		if( $_GET['pid'] == $obj->catid ){

			echo "<ul id='catmenu'>\n";
			$SQL2 = "SELECT   catid,offerid ,cat  FROM ".CAT_TB." WHERE pid=".$obj->catid;
			$Mysql2 = new Mysql(DB_NAME);
			$Mysql2->doQuery($SQL2);
			while( $obj2=$Mysql2->GetData()){
				if( $_GET['pid'] == $obj2->catid ) $class="class='cur-menu'";
				else $class="";
				echo "<li ".$class."><a href='/index.php?gOo=goods_category.dwt&gcat=".$obj2->catid."&sub=".$obj2->catid."' >". $obj2->cat."</li>\n";


			}
			echo "</ul>";

		}

	}

} 
else{
}
}//end of if pid
?>

<SCRIPT LANGUAGE="JavaScript">
<!--
	if(window.parent.document.getElementById('car_tree')!= null)
	window.parent.document.getElementById('car_tree').innerHTML = document.getElementById('catmenu').innerHTML;
//-->
</SCRIPT>