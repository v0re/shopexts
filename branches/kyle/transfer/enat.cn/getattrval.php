<?php

//加载ShopEx的配置文件
include_once("../include/mall_config.php");

mysql_connect($dbHost, $dbUser, $dbPass);
mysql_select_db($dbName);
mysql_query("set names gb2312");

//清空属性配件表
$sql = "TRUNCATE sdb_mall_prop_category";
mysql_query($sql);
$sql = "TRUNCATE sdb_mall_prop";
mysql_query($sql);
$sql = "TRUNCATE sdb_mall_prop_value";
mysql_query($sql);

//清空商品关联表
$sql = "TRUNCATE sdb_mall_goods_prop_grp";
mysql_query($sql);
$sql = "TRUNCATE sdb_mall_goods_prop_grp_value";
mysql_query($sql);
$sql = "TRUNCATE sdb_mall_goods_prop";
mysql_query($sql);

$sql = "select catid,cat from sdb_mall_offer_pcat where pid>0 group by cat order by catid";
$rs = mysql_query($sql);
while($row = mysql_fetch_array($rs)){
	$aCat[$row['cat']] = $row['catid'];
	$sql = "insert into sdb_mall_prop_category(offerid,prop_cat_id,cat_name) values('1','{$row[catid]}','{$row[cat]}')";
	mysql_query($sql);
	$sql = "update sdb_mall_offer_pcat set prop_cat_id='{$row['catid']}' where cat='{$row[cat]}'";
	mysql_query($sql);
	//生成规格属性
	$sql = "insert into sdb_mall_prop(offerid,prop_name,prop_type,datatype,prop_cat_id) values('1','商品尺码','propvalue','char','{$row[catid]}')";
	mysql_query($sql);
	//插入规格属性值
	$model_prop_id = mysql_insert_id();
	$aModel = getValByColName('model',$row['cat']);
	$aModel_prop_value_id = makePropValue($model_prop_id,$aModel);

	//生成颜色
	$sql = "insert into sdb_mall_prop(offerid,prop_name,prop_type,datatype,prop_cat_id) values('1','商品颜色','propvalue','char','{$row[catid]}')";
	mysql_query($sql);
	//插入颜色属性值
	$color_prop_id = mysql_insert_id();
	$aColor = getValByColName('prod1',$row['cat']);
	$aColor_prop_value_id = makePropValue($color_prop_id,$aColor);
	
	$sql = "select ProdNum,model,prod1,PriceList,PriceOrigin from buyok_produc where MidCode='{$row[cat]}'";
	$inrs = mysql_query($sql);
	while($inrow = mysql_fetch_array($inrs)){
		$gid = $inrow['ProdNum'];
		$price = $inrow['PriceList'];
		$mktprice = $inrow['PriceOrigin']; 
		//插入商品两个属性
		$sql = "insert into sdb_mall_goods_prop (prop_id,gid,offerid,show_type) values ('{$model_prop_id}','{$gid}','1','select')";
		mysql_query($sql);
		$sql = "insert into sdb_mall_goods_prop (prop_id,gid,offerid,show_type) values ('{$color_prop_id}','{$gid}','1','select')";
		mysql_query($sql);
		//获取商品绑定的属性值
		$sql = "select model,prod1 from buyok_produc where ProdNum='{$gid}'";
		$ininrs = mysql_query($sql);
		$ininrow = mysql_fetch_array($ininrs);
		//获取商品的两个属性值
		$aModel = explode('、',$ininrow['model']);
		array_walk($aModel,'trim_value');
		$aColor = explode('、',$ininrow['prod1']);
		array_walk($aColor,'trim_value');
		//是否已经设置默认组标识，必须要设置一个默认组，否则前台计算价格出现NaN的情况
		$hasDF = false;
		//生成属性值关联表
		foreach($aModel as $model){
			foreach($aColor as $color){
				//插入组号
				if($hasDF){
					$sql = "insert into  sdb_mall_goods_prop_grp (grp_id,offerid,gid,storage,price,mktprice) values (null,'1',{$gid},'99','{$price}','{$mktprice}')";
				}else{
					$sql = "insert into  sdb_mall_goods_prop_grp (grp_id,offerid,gid,storage,price,mktprice,default_tag) values (null,'1',{$gid},'99','{$price}','{$mktprice}','1')";
					$hasDF = true;
				}
				mysql_query($sql);
				$grp_id = mysql_insert_id();
				//插入规格值
				$model_prop_value_id = $aModel_prop_value_id[$model];
				$sql = "insert into sdb_mall_goods_prop_grp_value (grp_id,gid,prop_value_id,prop_id,offerid) values ('{$grp_id}','{$gid}','{$model_prop_value_id}','{$model_prop_id}','1')";
				mysql_query($sql);
				//插入颜色值
				$color_prop_value_id = $aColor_prop_value_id[$color];
				$sql = "insert into sdb_mall_goods_prop_grp_value (grp_id,gid,prop_value_id,prop_id,offerid) values ('{$grp_id}','{$gid}','{$color_prop_value_id}','{$color_prop_id}','1')";
				mysql_query($sql);
			}
		}		
	}
}

echo "all done";


function getValByColName($col,$cat){
	$sql = "select $col from buyok_produc where MidCode='{$cat}' group by $col";
	$rs = mysql_query($sql);
	$aRnt = array();
	while($row = mysql_fetch_array($rs)){
		$aTmp = explode('、',$row[$col]);
		$aRnt = array_merge($aRnt,$aTmp);
	}

	array_walk($aRnt,'trim_value');
	$aRnt = array_unique($aRnt);
	sort($aRnt);
	
	return $aRnt;
}


function trim_value(&$value) { 
	$value = trim($value); 
}

function makePropValue($prop_id,$aPropValue){
	if(!is_array($aPropValue)) return false;
	$aTmp = array();
	foreach($aPropValue as $value){
		$sql = "insert into sdb_mall_prop_value (prop_id,offerid,prop_value) values('{$prop_id}','1','{$value}')";
		mysql_query($sql);
		$aTmp[$value] = mysql_insert_id();
	}
	return $aTmp;
}

?>