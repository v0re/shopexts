<?php

/*
	[kuaso!] (C)209-2010 Kuaso Inc.
	
	This is NOT a freeware, use is subject to license terms

	$Id: getpoints.php 1124 2010-01-24 16:17:18Z anjel $
*/

require_once("global.php");
is_login();
$pagetitle="��λ�û���";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
		<title>��ҳ--<?php echo $pagetitle;?></title>
        <link type="text/css" rel="stylesheet" media="all" href="images/global.css" />     
	</head>
	<body>
<?php
headhtml();
?>        	    
<div class="wrapper">
<table border=0 cellpadding=0 cellspacing=0 width=800 align="center">
<tr><td><br><h3 align="center">���ɷ���</h3></td></tr>
<tr><td align=center>�㵱ǰʹ�õ��˻��� <font color=red><?php echo $user["user_name"];?></font> ���˻���ʣ���� <font color=red><?php echo $user["points"];?></font> ��</td></tr>
</table><br>
	<table width="100%" border="1" cellspacing="0" cellpadding="0">
  <tr>
    <td><?php echo $zz_config['getpoints'];?></td>
  </tr>
</table>
