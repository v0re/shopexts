<?php

	require('config.inc.php');
	$mem = explode('-',$_GET['query']);
	$user = $mem[0];
	$passwd = md5(trim($mem[1]));
	$link = mysql_connect(DB_HOST,DB_USER,DB_PASSWD);
	mysql_select_db(DB_NAME);
	mysql_query('set names utf8');
	$sql = 'select `passwd` from user where `user`="'.$user.'"';
	$result = mysql_query($sql) or die(mysql_error());
	$row = mysql_fetch_row($result);
	if ($row[0] == $passwd)
	{
		echo '登陆成功,欢迎'.$user.'的到来!';
	}
	else
	{
		echo '登陆失败,用户名或密码错误,请重新登录!';
	}
?>