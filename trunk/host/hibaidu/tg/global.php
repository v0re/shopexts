<?php

/*
	[kuaso!] (C)209-2010 Kuaso Inc.
	
	This is NOT a freeware, use is subject to license terms

	$Id: global.php 350 2010-01-24 16:17:18Z anjel $
*/

session_start();
require_once("../global.php");
require_once("../cache/zz_config.php");
require_once("global.func.php");

$user_name=$_SESSION["user_name"];
if(!empty($user_name))
{
   $user=$db->get_one("select * from kuaso_zz_user where user_name='".$user_name."'");
}
$zz_user_group_array=array('1'=>'普通会员','2'=>'代理');
?>
