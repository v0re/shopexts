<?php

/*
	[kuaso!] (C)209-2010 Kuaso Inc.
	
	This is NOT a freeware, use is subject to license terms

	$Id: website.php 4170 2010-01-24 16:17:18Z anjel $
*/

require_once("global.php");
is_login();
$pagetitle="��վ����";
$action=$_REQUEST["action"];
switch($action)
{
   case "savewebsite":
         $do_action=trim(HtmlReplace($_POST["do_action"]));
         $site_name=trim(HtmlReplace($_POST["site_name"]));
	     $site_url=trim(HtmlReplace($_POST["site_url"]));
	     $description=trim(HtmlReplace($_POST["description"]));
		 
		 if($do_action=="modify")
		 {
		       $site_id=intval($_POST["site_id"]);
			   $array=array('site_name'=>$site_name,'site_url'=>$site_url,'description'=>$description,'updatetime'=>time());
			   $db->update("kuaso_zz_website",$array,"site_id='".$site_id."'");
		 }
		 else
		 {
		       $array=array('site_name'=>$site_name,'site_url'=>$site_url,'description'=>$description,'user_id'=>$user["user_id"],'addtime'=>time(),'updatetime'=>time());
			   $db->insert("kuaso_zz_website",$array);
		 }
		 
		 header("location:website.php?");
   break;

}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
		<title>��ҳ--<?php echo $pagetitle;?></title>
        <link type="text/css" rel="stylesheet" media="all" href="images/global.css" />  
		<script language="javascript">
		function checkform()
		{
		    var f=document.websiteform;
			var t=f.site_name;
			if(t.value==""||t.value==" "){alert("��վ���Ʋ���Ϊ��!");t.focus();return false;}
			var t=f.site_url;
			if(t.value==""||t.value==" "){alert("��ַ����Ϊ��!");t.focus();return false;}
			var t=f.description;
			if(t.value==""||t.value==" "){alert("��վ˵������Ϊ��!");t.focus();return false;}
		}
		</script>   
	</head>
	<body>
<?php
headhtml();
?>        	    
<div class="wrapper">
	<table width="100%" border="0" cellpadding="3" cellspacing="1" class="tablebg">
  <tr>
    <th>��վ����</th>
    <th>��վ��ַ</th>
    <th width="100">��¼</th>
    <th width="100">����</th>
  </tr>
  <?php
  $query=$db->query("select * from kuaso_zz_website where user_id='".$user["user_id"]."'");
  $j=0;
  while($site=$db->fetch_array($query))
  {
     $j++;
  ?>
  <tr>
    <td><?php echo $site["site_name"];?></td>
    <td><?php echo $site["site_url"];?></td>
    <td><a target="_blank" href="../s/?wd=site:<?php echo getdomain($site["site_url"]);?>">�鿴��¼���</a></td>
    <td><a href="?action=modify&site_id=<?php echo $site["site_id"];?>">�޸�</a></td>
  </tr>
  <?php
  }
  ?>
</table>
<?php
if($action=="modify")
{
   $site_id=intval($_GET["site_id"]);
   $sql="select * from kuaso_zz_website where user_id='".$user["user_id"]."' and site_id='".$site_id."'";//echo $sql;
   $row=$db->get_one($sql);
   $bt_txt="�޸���վ";
}
else
{
    $bt_txt="�����վ";
}
?>
<table width="100%" border="0" cellpadding="3" cellspacing="1" class="tablebg">
	<form name="websiteform" method="post" action="website.php" onsubmit="return checkform();">
	<input type="hidden" name="action" value="savewebsite">
	<input type="hidden" name="do_action" value="<?php echo $action;?>">
	<input type="hidden" name="site_id" value="<?php echo $site_id;?>">
      <tr>
        <th colspan="2"><?php echo $bt_txt;?></th>
      </tr>
      <tr>
        <td width="100">��վ����</td>
        <td><input name="site_name" type="text" size="80" value="<?php echo $row["site_name"];?>">
        ���32��</td>
      </tr>
      <tr>
        <td>��վ��ַ</td>
        <td><input name="site_url" type="text" size="80" value="<?php echo $row["site_url"];?>">
        ���20�� </td>
      </tr>
      <tr>
        <td>��վ˵��</td>
        <td><textarea name="description" cols="80" rows="8"><?php echo $row["description"];?></textarea>
        ���100��</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><input type="submit" name="Submit" value="<?php echo $bt_txt;?>"></td>
      </tr>
  </form>
    </table>
<?php
foothtml();
?>
