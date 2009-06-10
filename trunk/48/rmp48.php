<?php 
set_time_limit(0);
error_reporting(E_ALL^E_NOTICE);
?>
<html>
<head>
<title>ShopEx 会员价格重置工具</title>
<META HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8">
</head>
<body style="font-size:12px;">
<?php
if($_REQUEST['action'] == 'start'){
require_once("./config.php");
$link=mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
mysql_select_db(DB_NAME);
mysql_query("set names utf8");
$sqlfile = dirname(__FILE__)."/price.sql";
$sqlfile = str_replace("\\","/",$sqlfile);

if($_REQUEST['member'] == on){
  //清空价格表
  $sql = "truncate ".DB_PREFIX."goods_lv_price";
  mysql_query($sql) or die(mysql_error());

  $sql = "select name,member_lv_id,point,dis_count from ".DB_PREFIX."member_lv";
  $otRs = mysql_query($sql);
  while ($otRow = mysql_fetch_array($otRs)){
   //取出第一个元素
   $levelid = intval($otRow['member_lv_id']);
   $tmpDiscunt = $otRow['dis_count'];
   $sql = "select product_id,goods_id,price from ".DB_PREFIX."products";
   $rs = mysql_query($sql) or die(mysql_error()); 
   $instSql = "";
   while ($row = mysql_fetch_array($rs)){
    $instSql .= $row['product_id']."\t".$levelid."\t".$row['goods_id']."\t".($row['price'] * $tmpDiscunt)."\r\n";
   }
   error_log($instSql,3,$sqlfile);
   echo "等级 ".$otRow['name']." 数据生成完毕
";
   flush();
  }
  $sql = "LOAD DATA LOCAL INFILE '".$sqlfile."' INTO TABLE ".DB_PREFIX."goods_lv_price(product_id,level_id,goods_id,price);";
  mysql_query($sql) or die(mysql_error());
  @unlink($sqlfile);
  echo "<hr>会员价格数据已经全部导入数据库
";
  flush();
}else{
  echo $_REQUEST['member'];
}
  
}


?>
<form method="OST">
更新会员价<input type=checkbox name='member'>


<input type=hidden name='action' value='start'>
<input type=submit value='submint'>
</form>
</body>
</html>
