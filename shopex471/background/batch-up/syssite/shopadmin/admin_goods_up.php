<?php
/**
* 
* 后台文件
*
* @package  ShopEx网上商店系统
* @author   $Revision: 5 $ $Author: Xuhc $
* @url		http://www.shopex.cn/
* @since    PHP 4.3
* @copyright ShopEx.cn
* 
**/

include ("top.php");

$t = new Template("./tpl");
$t->set_file("admin_goods_up","admin_goods_up.htm");

$shopCat = newclass("shopCategory");
$shopCat->shopId=$_SESSION["SHOP_ID"];
$shopCat->makecatchooser("catid",0,0,"",$PROG_TAGS["ptag_375"],1,0);

 //设置FILE编码语言
$shopinfo = new shop();
$shopinfo->getbyid($SHOP_ID);
$sel_file_lang = "<select name='sel_file_lang'>";
$sel_file_lang .="<option value='utf8'>".$PROG_TAGS["ptag_1549"]."</option>";
reset($ARR_LANGNAME);
while(list($k,$v) = each($ARR_LANGNAME)){
	if($shopinfo->lang == $k){
		$sel_file_lang .="<option value='$k' selected>$v</option>";
	}
	else{
		$sel_file_lang .="<option value='$k'>$v</option>";
	}
}
$sel_file_lang .="</select>";

$t->set_var("catid", $shopCat->catChooser);
$t->set_var("sel_file_lang", $sel_file_lang);
$t->parse("out","admin_goods_up");
include ("shortcut.php");
$t->sp("out");
include ("bottom.php");
?>