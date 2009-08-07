<?php

	set_time_limit(0);
	error_reporting(E_ALL^E_NOTICE);
	require_once("config.php");
	$link=mysql_connect(DB_HOST,DB_USER,DB_PASSWORD); 
	mysql_select_db(DB_NAME);
	mysql_query("set names utf8");
	$sql = 'select `id`,`categoryid` from lebi_product';
	$result = mysql_query($sql);	
	$list =array();
	while($row = mysql_fetch_array($result,MYSQL_NUM))
	{
		//,分割分类字段 分类字段是这样的 ,83,116,(83是子分类 116是父分类)
		$arr = explode(',',$row[1]);
		//得到子分类
		$next = next($arr);
		//将id和得到的子分类分别作为数组的键名和值
		$list[$row[0]] = $next;
	}
	//print_r($list);
	foreach($list as $key =>$var)
	{
		$updateSql = "update sdb_goods set `cat_id`='$var' where `goods_id`='$key'";
		$res = mysql_query($updateSql) or die(mysql_error());
		if($res)
		{
			echo '`goods_id`='.$key.'的分类被更新为=>'.$var.'<br />';
		}
	}
?>