<?php

/*
	[kuaso!] (C)209-2010 Kuaso Inc.
	
	This is NOT a freeware, use is subject to license terms

	$Id: index.php 2173 2010-01-24 16:17:18Z anjel $
*/

require_once("global.php");
is_login();
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
		<title><?php echo $config["name"];?>�ƹ�--��ҳ</title>
        <link type="text/css" rel="stylesheet" media="all" href="images/global.css" />     
	</head>
	<body>
<?php
headhtml();
?>        	    
<div class="wrapper">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="300"><div class="container my-acct">
                        <div id="AccTopCont" class="inner acc-inner">
                            <h4>�ҵ��˻�</h4>
                            <div class="acc-cont">
                                <ul>
                                    <li id="SingleSatus">
                                        <b>�˻���<?php echo $user["user_name"];?></b>
                                        <p class="status" id="BreadNavigation"><span id="AccName" rel=""></span></p>
                                    </li>
								<!--	<li id="">
                                        <b>����<?php echo $zz_user_group_array[$user["user_group"]];?></b>
                                        <p></p>
                                    </li>-->
                                    <li id="SingleCount">
                                        <b>���֣�<?php echo $user["points"];?></b>
                                        <p><em></em>��<a target="_parent" id="PayBillLink" href="getpoints.php" class="pay-bill">��λ�û���</a></p>
                                    </li>
                                </ul>
                                <div id="SelectUser" class="select-users">                                </div>
                            </div>
                            <div id="AccMask" class="mask-module"></div>
                        </div>
                        <div id="AccBack" class="acc-back"></div>
                        
                    </div></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>

