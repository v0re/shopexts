<?php

/*
	[kuaso!] (C)209-2010 Kuaso Inc.
	
	This is NOT a freeware, use is subject to license terms

	$Id: union.php 2726 2010-01-24 16:17:18Z anjel $
*/

require_once("global.php");
is_login();
$pagetitle="���˴���";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
		<title>��ҳ--<?php echo $pagetitle;?></title>
        <link type="text/css" rel="stylesheet" media="all" href="images/global.css" />
<script language="JavaScript">
<!--

function MM_goToURL() { //v3.0
  var i, args=MM_goToURL.arguments; document.MM_returnValue = false;
  for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
}
function JM_cc(ob){
var obj=MM_findObj(ob); if (obj) {
obj.select();
alert("���ƴ���ɹ�");
js=obj.createTextRange();js.execCommand("Copy");}
}

function MM_findObj(n, d) { //v4.0
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && document.getElementById) x=document.getElementById(n); return x;
}
//-->
</script>
	</head>
	<body>
<?php
headhtml();
?>
<div class="wrapper">
	<table width="100%" border="1" cellspacing="0" cellpadding="0">
  <tr>
    <td width="100">���ӷ�ʽ</td>
    <td>
	<textarea id="code_1" name="textfield" cols="60" rows="6">&lt;a href="<?php echo $config["url"];?>/?u=<?php echo $user["user_id"];?>" target="_blank"&gt;<?php echo $config["name"];?>&lt;/a&gt;</textarea>
	<input onclick="JM_cc('code_1')" type="submit" name="Submit" value="���ƴ���">
	(ÿ����IP�����1��)</td>
  </tr>
  <tr>
    <td>������ʽ</td>
    <td>
<TEXTAREA id="code_2" name=textarea rows=6 cols=60>&lt;form action="<?php echo $config['url'];?>/s" target="_blank"&gt;
&lt;table bgcolor="#FFFFFF"&gt;&lt;tr&gt;&lt;td&gt;
&lt;a href="<?php echo $config["url"];?>/?u=<?php echo $user["user_id"];?>" target="_blank"&gt;&lt;img src="<?php echo $config["url"];?>/images/logo-80px.gif" alt="KuaSo" align="bottom" border="0"&gt;&lt;/a&gt;
&lt;input type=text name=wd size=30&gt;
&lt;input type=hidden name=/?u size=30 value="<?php echo $user["user_id"];?>"&gt;
&lt;input type="submit" value="<?php echo $config["name"];?>����"&gt;
&lt;/td&gt;&lt;/tr&gt;&lt;/table&gt;
&lt;/form&gt;</TEXTAREA>
<input onclick="JM_cc('code_2')" type="submit" name="Submit" value="���ƴ���">(ÿ��IP��������1��)	</td>
  </tr>
</table>
