<HTML>
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<TITLE> Character </TITLE>

</HEAD>

<BODY style="font-size:12px;">

<?php
include("include/mall_config.php");
$CFG['host']			= $dbHost;
$CFG['user']			= $dbUser;
$CFG['password']		= $dbPass;
$CFG['database']		= $dbName;

set_time_limit(0);
error_reporting( E_ALL^E_NOTICE);

define('MYSQL_CHARSET', 'latin1');
define('MYSQL_COLLATION', ' 	latin1_swedish_ci');

$REGION = array(
'mall_binding_tag',
'mall_comment_param',
'mall_comment_score',
'mall_coupons_items',
'mall_discount_coupons',
'mall_extend_tag',
'mall_gifts',
'mall_gifts_cat',
'mall_gift_items',
'mall_goodsgrp_price',
'mall_goods_cache',
'mall_goods_cache_price',
'mall_goods_cache_prop',
'mall_goods_package',
'mall_goods_prop',
'mall_goods_prop_grp',
'mall_goods_prop_grp_value',
'mall_member_coupons',
'mall_member_price',
'mall_pcat_brand',   
'mall_pcat_prop',
'mall_pcat_prop_fields' ,
'mall_pcat_prop_has_goods', 
'mall_point_history',
'mall_prop',
'mall_prop_category', 
'mall_prop_goods',
'mall_prop_value',
'mall_t_coupons_goods', 
'mall_t_coupons_level',
'mall_t_coupons_pcat'
);



$rs = mysql_connect($CFG['host'], $CFG['user'], $CFG['password']);
mysql_select_db($CFG['database'], $rs);
getTables($rs);

function getTables($rs)
{
	
	global $REGION;
	$exec_array = array();

	if (!is_array($REGION) || empty($REGION) || !isset($REGION)) 
	{

		$result_t = mysql_query("SHOW TABLES", $rs );	
		$REGION = array();
		while ($table = mysql_fetch_row($result_t)) 		
		{
			$REGION[] = $table[0];
		}
	}
	
	if (is_array($REGION))
	{
		foreach($REGION as $table)
		{			
			$table = trim($table);
			$sql = getSQLbyTableName($table);
			if (mysql_query($sql))
			{
				echo "<font color=green>change $table charset to ".MYSQL_CHARSET." sccessfual! </font><br>";
				flush();
			}
			else
			{
				echo "<font color=red>change $table charset to ".MYSQL_CHARSET." fail<hr>".$sql."<br>".mysql_error()."<hr></font><br>";
				flush();
			}		
		}
	}
	return $exec_array;

}

function  getSQLbyTableName($table)
{	
	global $CFG;
	$table_class_sql = "ALTER TABLE `{$CFG['database']}`.`{$table}` CHARACTER SET ".MYSQL_CHARSET." COLLATE ".MYSQL_COLLATION.",";
	$result_f = mysql_query("SHOW COLUMNS FROM `{$table}`;");
	while ($column = mysql_fetch_row($result_f))
	{

		if (strpos($column[1],'varchar') === 0 || strpos($column[1],'text') ===0 || strpos($column[1],'blob') ===0)
		{

			$column_type = $column[1];
			$column_type .= " CHARACTER SET ".MYSQL_CHARSET." COLLATE ".MYSQL_COLLATION;
			if ( $column[2] == 'YES' )
			{
				$column_type .= ' default NULL';
			}
			else
			{
				$column_type .= ' NOT NULL';
			}
			
			if (isset( $column[4]) && !empty($column[4]))
			{
				$column_type .= " default '{$column[4]}'";
			}
			if ( isset( $column[5] ) )
			{
				$column_type .= " {$column[5]}";
			}
			

			$table_class_sql.= " CHANGE  `{$column[0]}` `{$column[0]}` {$column_type},";

		}
	}
	return  trim($table_class_sql,',').";";
}


?>

</BODY>
</HTML>