<?php

/*
	[kuaso!] (C)209-2010 Kuaso Inc.
	
	This is NOT a freeware, use is subject to license terms

	$Id: click.php 2010-01-24 16:17:18Z anjel $
*/

require ("../global.php");
$query_string=base64_decode($_SERVER['QUERY_STRING']);
parse_str($query_string);
$ip=ip();
$c=$db->get_one("select * from kuaso_stat_click where c_ip='".$ip."' and c_time>='".(time()-(60))."'");
if(empty($c))
{
     $link=$db->get_one("select * from kuaso_zz_links where link_id='".$link_id."'");
     $db->query("update kuaso_zz_user set points=points-".$link["price"]." where user_id='".$link["user_id"]."'");
	 $db->query("update kuaso_zz_links set stat_click=stat_click+1,consumption=consumption+".$link["price"]." where link_id='".$link_id."'");
	 $array=array('c_time'=>time(),'c_ip'=>$ip);
	 $db->insert("kuaso_stat_click",$array);
}

header("location:".$url);
?>
