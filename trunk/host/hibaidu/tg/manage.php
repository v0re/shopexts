<?php

/*
	[kuaso!] (C)209-2010 Kuaso Inc.
	
	This is NOT a freeware, use is subject to license terms

	$Id: manage.php 3835 2010-01-24 16:17:18Z anjel $
*/

require_once("global.php");
is_login();
$pagetitle="�ƹ����";
$action=HtmlReplace($_REQUEST["action"]);
switch($action)
{
   case "saveaddlinks":
         $do_action=trim(HtmlReplace($_POST["do_action"]));
		 $keywords=trim(HtmlReplace($_POST["keywords"]));
	     $title=trim(HtmlReplace($_POST["title"]));
	     $url=trim(HtmlReplace($_POST["url"]));
	     $description=trim(HtmlReplace($_POST["description"]));
		 $price=trim(HtmlReplace($_POST["price"]));
		 if($do_action=="savemodify")
		 {
		     $link_id=intval($_POST["link_id"]);
		     $array=array('keywords'=>$keywords,'title'=>$title,'url'=>$url,'description'=>$description,'updatetime'=>time());
		     $db->update("kuaso_zz_links",$array,"link_id='".$link_id."'");
		 }
		 else
		 {
		     $array=array('keywords'=>$keywords,'title'=>$title,'url'=>$url,'description'=>$description,'price'=>$price,'user_id'=>$user["user_id"],'updatetime'=>time());
		     $db->insert("kuaso_zz_links",$array);
		 }
		 
		 
		 header("location:manage.php?");
   break;
   case "updateprice":
         $arrPrice=$_POST["arrPrice"];
		 foreach($arrPrice as $link_id=>$price)
		 {
		       $key=$db->get_one("select * from kuaso_zz_links where link_id='".$link_id."'");
		       if(get_qijia($key["keywords"])>$price)
			   {
			      header("location:manage.php?msg=".urlencode($key["keywords"]." ���۲��ܵ�����ۣ�"));
				  exit();
			   }
		       $array=array('price'=>$price,'updatetime'=>time());
			   $db->update("kuaso_zz_links",$array,"link_id='".$link_id."' and user_id='".$user["user_id"]."'");
			   
		 }
		 header("location:manage.php?msg=".urlencode("���³ɹ���"));
   break;
   	case "del":
	     $link_id=intval($_GET["link_id"]);
		 $db->query("delete from kuaso_zz_links where link_id='".$link_id."' and user_id='".$user["user_id"]."'");
		 header("location:manage.php?msg=".urlencode("ɾ���ɹ���"));
	break;
}
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
<div id="msg" style='color:#f00;text-align:center;'>
	        <?php
			$msg=HtmlReplace($_GET["msg"]);
			if(empty($msg))
			{
			   echo "";
			}
			else
			{
			    echo $msg;
			}
			?>
</div>
<table border=0 cellspacing=1 cellpadding=3 class="tablebg">
	<form name="form3" method="post" action="manage.php">  
	<input type="hidden" name="action" value="updateprice">
  <tr>
    <th>���</th>
    <th>�� �� ��</th>
    <th>&nbsp;</th>
    <th>�ؼ���״̬</th>
    <th>���</th>
    <th>����ģʽ</th>
    <th>&nbsp;</th>
    <th>&nbsp;</th>
    <th>�������</th>
    <th>���ƽ����</th>
    <th>���ѽ��</th>
    <th width="50">&nbsp;</th>
  </tr>
    <tr>
    <td align="center">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td><input type="submit" name="Submit4" value="���³���">
		</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    </tr>
  <?php
  $query=$db->query("select * from kuaso_zz_links where user_id='".$user["user_id"]."'");
  $j=0;
  while($link=$db->fetch_array($query))
  {
     $j++;
  ?>

  <tr>
    <td align="center"><?php echo $j;?></td>
    <td><?php echo "<a href=\"?action=modify&link_id=".$link["link_id"]."\">".$link["keywords"]."</a>";?></td>
    <td><a target="_blank" href="../s?wd=<?php echo urlencode($link["keywords"]);?>">����</a></td>
    <td>&nbsp;</td>
    <td><?php echo get_qijia($link["keywords"]);?>&nbsp;����</td>
    <td><input type="text" name="arrPrice[<?php echo $link["link_id"];?>]" value="<?php echo $link["price"];?>"></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td><?php echo $link["stat_click"];?></td>
    <td>&nbsp;</td>
    <td><?php echo $link["consumption"];?></td>
    <td><a href="?action=del&link_id=<?php echo $link["link_id"]?>" onclick="if(!confirm('ȷ��ɾ����?')) return false;">ɾ��</a></td>
  </tr>

  <?php
  }
  ?>
    <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td><input type="submit" name="Submit3" value="���³���"></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    </tr>
  </form>
</table>

    <br>
<?php
if($action!="step_2")
{
        if($action=="modify")
		{
		     $link_id=intval($_GET["link_id"]);
		     $action_value="saveaddlinks";
			 $do_action="savemodify";
			 $title=$bt_txt="�޸Ĺؼ���";
			 $link=$db->get_one("select * from kuaso_zz_links where link_id='".$link_id."' and user_id='".$user["user_id"]."'");
		}
		else
		{
		     $action_value="step_2";
		     $title="��ӹؼ���";
			 $bt_txt="��һ��";
		}
?>
    <table width="100%" border="0" cellpadding="3" cellspacing="1" class="tablebg">
	<form name="form1" method="post" action="manage.php">
	<input type="hidden" name="action" value="<?php echo $action_value;?>">
	<input type="hidden" name="do_action" value="<?php echo $do_action;?>">
	<input type="hidden" name="link_id" value="<?php echo $link_id;?>">
      <tr>
        <th colspan="2"><?php echo $title;?></th>
      </tr>
      <tr>
        <td width="100">�� �� ��</td>
        <td><input name="keywords" type="text" size="80" value="<?php echo $link["keywords"];?>">
        ���32��</td>
      </tr>
      <tr>
        <td>��ҳ����</td>
        <td><input name="title" type="text" size="80" value="<?php echo $link["title"];?>">
        ���20�� </td>
      </tr>
      <tr>
        <td>URL ��ַ</td>
        <td><input name="url" type="text" size="80" value="<?php echo $link["url"];?>">
        ���248�ֽ�</td>
      </tr>
      <tr>
        <td>��ҳ����</td>
        <td><textarea name="description" cols="80" rows="8"><?php echo $link["description"];?></textarea>
        ���100��</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><input type="submit" name="Submit" value="<?php echo $bt_txt;?>"></td>
      </tr>
	  </form>
    </table>
<?php
}
elseif($action=="step_2")
{
    $keywords=trim(HtmlReplace($_POST["keywords"]));
	$title=trim(HtmlReplace($_POST["title"]));
	$url=trim(HtmlReplace($_POST["url"]));
	$description=trim(HtmlReplace($_POST["description"]));
?>
   <table width="100%" border="0" cellpadding="3" cellspacing="1" class="tablebg">
   <form name="form2" method="post" action="manage.php">
   <input type="hidden" name="action" value="saveaddlinks">
   <input type="hidden" name="keywords" value="<?php echo $keywords;?>">
   <input type="hidden" name="title" value="<?php echo $title;?>">
   <input type="hidden" name="url" value="<?php echo $url;?>">
   <input type="hidden" name="description" value="<?php echo $description;?>">
      <tr>
        <th>�ؼ���</th>
        <th>���</th>
        <th>����</th>
      </tr>
      <tr>
        <td><?php echo $keywords;?></td>
        <td><?php echo get_qijia($keywords);?></td>
        <td><input type="text" name="price"></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td colspan="2"><input type="submit" name="Submit2" value="�ύ"></td>
      </tr>
     </form>
    </table>
<?php
}
?>
