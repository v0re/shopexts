<?php

/*
	[kuaso!] (C)209-2010 Kuaso Inc.
	
	This is NOT a freeware, use is subject to license terms

	$Id: zz.php 2010-01-24 16:17:18Z anjel $
*/

require_once("../global.php");
$user_id=intval($_GET["user_id"]);
if(!empty($user_id))
{
    $ip=ip();
	// $referer=$_SERVER['HTTP_REFERER'];
    $v=$db->get_one("select * from kuaso_stat_visitor where v_ip='".$ip."' and v_time>='".(time()-(86400*1))."'");
	if(empty($v))
	{
        $user=$db->get_one("select * from kuaso_zz_user where user_id='".$user_id."'");
	    //if($user["xc_ip"]<945)
	    //{
		   $array=array('v_time'=>time(),'v_ip'=>$ip);
		   $db->insert("kuaso_stat_visitor",$array);
		   $db->query("update kuaso_zz_user set points=points+1 where user_id='".$user_id."'");
	    // }
	}
}
?>
