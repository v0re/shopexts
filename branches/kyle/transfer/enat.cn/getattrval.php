<?php

//����ShopEx�������ļ�
include_once("../include/mall_config.php");

mysql_connect($dbHost, $dbUser, $dbPass);
mysql_select_db($dbName);
mysql_query("set names gb2312");

//������������
$sql = "TRUNCATE sdb_mall_prop_category";
mysql_query($sql);
$sql = "TRUNCATE sdb_mall_prop";
mysql_query($sql);
$sql = "TRUNCATE sdb_mall_prop_value";
mysql_query($sql);

//�����Ʒ������
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
	//���ɹ������
	$sql = "insert into sdb_mall_prop(offerid,prop_name,prop_type,datatype,prop_cat_id) values('1','��Ʒ����','propvalue','char','{$row[catid]}')";
	mysql_query($sql);
	//����������ֵ
	$model_prop_id = mysql_insert_id();
	$aModel = getValByColName('model',$row['cat']);
	$aModel_prop_value_id = makePropValue($model_prop_id,$aModel);

	//������ɫ
	$sql = "insert into sdb_mall_prop(offerid,prop_name,prop_type,datatype,prop_cat_id) values('1','��Ʒ��ɫ','propvalue','char','{$row[catid]}')";
	mysql_query($sql);
	//������ɫ����ֵ
	$color_prop_id = mysql_insert_id();
	$aColor = getValByColName('prod1',$row['cat']);
	$aColor_prop_value_id = makePropValue($color_prop_id,$aColor);
	
	$sql = "select ProdNum,model,prod1,PriceList,PriceOrigin from buyok_produc where MidCode='{$row[cat]}'";
	$inrs = mysql_query($sql);
	while($inrow = mysql_fetch_array($inrs)){
		$gid = $inrow['ProdNum'];
		$price = $inrow['PriceList'];
		$mktprice = $inrow['PriceOrigin']; 
		//������Ʒ��������
		$sql = "insert into sdb_mall_goods_prop (prop_id,gid,offerid,show_type) values ('{$model_prop_id}','{$gid}','1','select')";
		mysql_query($sql);
		$sql = "insert into sdb_mall_goods_prop (prop_id,gid,offerid,show_type) values ('{$color_prop_id}','{$gid}','1','select')";
		mysql_query($sql);
		//��ȡ��Ʒ�󶨵�����ֵ
		$sql = "select model,prod1 from buyok_produc where ProdNum='{$gid}'";
		$ininrs = mysql_query($sql);
		$ininrow = mysql_fetch_array($ininrs);
		//��ȡ��Ʒ����������ֵ
		$aModel = explode('��',$ininrow['model']);
		array_walk($aModel,'trim_value');
		$aColor = explode('��',$ininrow['prod1']);
		array_walk($aColor,'trim_value');
		//�Ƿ��Ѿ�����Ĭ�����ʶ������Ҫ����һ��Ĭ���飬����ǰ̨����۸����NaN�����
		$hasDF = false;
		//��������ֵ������
		foreach($aModel as $model){
			foreach($aColor as $color){
				//�������
				if($hasDF){
					$sql = "insert into  sdb_mall_goods_prop_grp (grp_id,offerid,gid,storage,price,mktprice) values (null,'1',{$gid},'99','{$price}','{$mktprice}')";
				}else{
					$sql = "insert into  sdb_mall_goods_prop_grp (grp_id,offerid,gid,storage,price,mktprice,default_tag) values (null,'1',{$gid},'99','{$price}','{$mktprice}','1')";
					$hasDF = true;
				}
				mysql_query($sql);
				$grp_id = mysql_insert_id();
				//������ֵ
				$model_prop_value_id = $aModel_prop_value_id[$model];
				$sql = "insert into sdb_mall_goods_prop_grp_value (grp_id,gid,prop_value_id,prop_id,offerid) values ('{$grp_id}','{$gid}','{$model_prop_value_id}','{$model_prop_id}','1')";
				mysql_query($sql);
				//������ɫֵ
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
		$aTmp = explode('��',$row[$col]);
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