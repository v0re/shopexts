<?php

require("include/mall_config.php");
$link=mysql_connect($dbHost,$dbUser,$dbPass); 
mysql_select_db($dbName);

$sql = <<<EOF
	DROP TABLE IF EXISTS `sdb_hook`;
	CREATE TABLE IF NOT EXISTS `sdb_hook` (
		`hk_id` mediumint(6) unsigned NOT NULL auto_increment,
		`hk_event` varchar(50) NOT NULL default '',
		`hk_order` mediumint(6) unsigned NOT NULL default '0',
		`hk_enabled` enum('0','1') NOT NULL default '0',
		`hk_pkg` varchar(30) default NULL,
		`hk_method` varchar(30) NOT NULL default '',
		`hk_object` varchar(30) NOT NULL default '',
		`offerid` mediumint(6) unsigned NOT NULL default '0',
		PRIMARY KEY  (`hk_id`),
		KEY `hk_order` (`hk_order`)
	) ENGINE=MyISAM;


	INSERT INTO `sdb_hook` (`hk_id`, `hk_event`, `hk_order`, `hk_enabled`, `hk_pkg`, `hk_method`, `hk_object`, `offerid`) VALUES
	(1, 'OrderPreConsign', 20, '1', '', 'toConsign', 'orderFlow', 1),
	(2, 'OrderPreReship', 20, '1', '', 'toReship', 'orderFlow', 1),
	(3, 'OrderPrePayed', 100, '1', '', 'toPayed', 'orderFund', 1),
	(4, 'OrderPrePayed', 30, '1', '', 'toPayed', 'memberPoint', 0),
	(5, 'OrderPrePayed', 83, '1', '', 'toPayed', 'coupon', 1),
	(6, 'OrderPrePayed', 82, '1', '', 'toPayed', 'gift', 1),
	(7, 'OrderPrePayed', 81, '1', '', 'toPayed', 'getPoint', 1),
	(8, 'OrderPrePayed', 80, '1', '', 'toPayed', 'changePoint', 1),
	(9, 'OrderPreRefund', 200, '1', '', 'toRefund', 'getPoint', 1),
	(11, 'OrderPreRefund', 200, '1', '', 'toRefund', 'gift', 1),
	(12, 'OrderPreRefund', 200, '1', '', 'toRefund', 'changePoint', 1),
	(13, 'OrderPreRemove', 100, '1', '', 'toRemove', 'changePoint', 1),
	(14, 'OrderPreRemove', 80, '1', '', 'toRemove', 'gift', 1),
	(15, 'OrderPreCancel', 0, '1', '', 'toCancel', 'changePoint', 1),
	(16, 'OrderPreCancel', 0, '1', '', 'toCancel', 'gift', 1),
	(18, 'OrderPreRefund', 100, '1', '', 'toRefund', 'orderFund', 1),
	(19, 'OrderPreConsign', 0, '1', '', 'toConsign', 'gift', 1),
	(20, 'OrderPreReship', 0, '1', '', 'toReship', 'gift', 1),
	(21, 'OrderPreRemove', 20, '1', '', 'toRemove', 'memberPoint', 1),
	(22, 'OrderPreRefund', 150, '1', '', 'toRefund', 'memberPoint', 1);

EOF;

$aSql = explode(";",$sql);
array_pop($aSql);
if(is_array($aSql))
foreach($aSql as $sql){
	if(mysql_query($sql)){
		echo "done!<br>";
	}else{
		echo mysql_error();
	}
}

?>