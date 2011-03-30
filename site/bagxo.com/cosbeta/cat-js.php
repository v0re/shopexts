<?php
//cat_id 	offerid 	pid 	cat
require '../config/config.php';
require 'inc.php';
require 'class.mysql.php';
define('HOME_URL','http://bagxp.com/');

define('NEW_CAT_TB','sdb_goods_cat');


	//获取本级目录
	$SQL = "SELECT cat_id,offerid,pid,cat FROM ".NEW_CAT_TB." WHERE pid=0 ORDER BY catord ASC" ;
	$Mysql = new Mysql(DB_NAME);
	$Mysql->doQuery($SQL);
	$i= 0;
	$curl = "<ul  class='car_tree'>\n";
	$curl .=  '<li class="title">Shop by Brand </li>';
	while( $obj=$Mysql->GetData() ){
		if( $cat_id== $obj->cat_id ) $class="class='cur-menu'";
		else $class="";
		$curl .= "<li  id='menu-p-".$obj->cat_id."' class='parli'>";
		$curl .= "<a  href='#'  onClick='showHid(".$obj->cat_id.",true);' id='menu-a-".$obj->cat_id."'";
		$curl .= "class='nohighlight' href='".HOME_URL."/index.php?gOo=goods_category.dwt&gcat=".$obj->cat_id."' >". $obj->cat."</a>";
		
			$SQL2 = "SELECT cat_id,offerid,pid,cat FROM ".NEW_CAT_TB." WHERE pid=".$obj->cat_id."  ORDER BY catord ASC" ;
			$Mysql2 = new Mysql(DB_NAME);
			$Mysql2->doQuery($SQL2);
			
			$curl .= "<ul  id='menu-sub-".$obj->cat_id."' class='childli' style='display:none;'>\n";
			$ulid_list .=$obj->cat_id.",";
			while( $obj2 = $Mysql2->GetData() ){
				$curl .= "<li   class='class-2'  id='menu-a-".$obj->cat_id."' >";
				$curl .= "<a  id='li-a-".$obj2->cat_id."' href='".HOME_URL."/index.php?gOo=goods_category.dwt&gcat=".$obj2->cat_id."' >".$obj2->cat."</a>";
			}
		$curl .= "</ul>";
		$curl .= "</li>\n";
		$lastpid = $obj->pid;
		$i ++ ;

	}
	$curl .="<li class='menu-bt'></li>";
	$curl .="</ul>";

	echo $curl;
if( $_GET['goodsid'] != '' ){
	$SQL = "SELECT cat_id  FROM ".GOODS_TB." WHERE gid=".$_GET['goodsid'] ;
	$Mysql = new Mysql(DB_NAME);
	$Mysql->doQuery($SQL);
	$obj=$Mysql->GetData();
	$goodscat = $obj->cat_id;

	
	$SQL = "SELECT pid  FROM ".NEW_CAT_TB." WHERE cat_id=".$goodscat."  ORDER BY catord ASC" ;
	$Mysql = new Mysql(DB_NAME);
	$Mysql->doQuery($SQL);
	$obj=$Mysql->GetData();
	$pid = $obj->pid;
	
	echo "<div id='cat_ul_list' style='display:none'>".$pid."</div>";
}
 echo "<div id='ul_list' style='display:none'>".$ulid_list."</div>";
echo "<div id='goodscat' style='display:none'>".$goodscat."</div>";
?>