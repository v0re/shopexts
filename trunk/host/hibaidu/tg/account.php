<?php

/*
	[kuaso!] (C)209-2010 Kuaso Inc.
	
	This is NOT a freeware, use is subject to license terms

	$Id: account.php 3835 2010-01-24 16:17:18Z anjel $
*/

require_once("global.php");
is_login();
$pagetitle="�˻���Ϣ";
$action=$_REQUEST["action"];
switch($action)
{
     case "saveinfo":
	       $email=HtmlReplace($_POST["email"]);
		   $question=HtmlReplace($_POST["question"]);
		   $answer=HtmlReplace(trim($_POST["answer"]));
		   if(empty($answer))
		   {
		        $array=array('email'=>$email,'question'=>$question);
		   }
		   else
		   {
		        $array=array('email'=>$email,'question'=>$question,'answer'=>md5($answer));
		   }
		   $db->update("kuaso_zz_user",$array,"user_name='".$user["user_name"]."'");
		   header("location:account.php?msg=".urlencode("�޸���Ϣ�ɹ�"));
	 break;
	 case "saveepw":
	       $oldpassword=trim(HtmlReplace($_POST["oldpassword"]));
		   $newpassword=trim(HtmlReplace($_POST["newpassword"]));
		   $array=array('email'=>$email);
		   if($oldpassword!=$user["password"])
		   {
		        header("location:account.php?action=epw&msg=".urlencode("�����벻��ȷ����ȷ��������!"));
		   }
		   else
		   {
		      $array=array('password'=>$newpassword);
		      $db->update("kuaso_zz_user",$array,"user_name='".$user["user_name"]."'");
			   header("location:account.php?action=epw&msg=".urlencode("��������ɹ�!"));
		   }
	 break;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
<?php
if($action=="")
{
?>
	<table width="100%" border="1" cellspacing="0" cellpadding="0" class="">
	<form name="form1" method="post" action="account.php">
	<input type="hidden" name="action" value="saveinfo">
    <tr>
      <td>&nbsp;</td>
      <td style='color:#f00;'><?php
			$msg=HtmlReplace($_GET["msg"]);
			if(empty($msg))
			{
			   echo "�޸ĸ�����Ϣ";
			}
			else
			{
			    echo $msg;
			}
			?></td>
    </tr>
    <tr>
    <td width="100">�û�����</td>
    <td><?php echo $user["user_name"];?></td>
  </tr>
  <tr>
    <td>��ϵ��������</td>
    <td><?php echo $user["real_name"];?></td>
  </tr>
  <tr>
    <td>�����ʼ� �� </td>
    <td><input type="text" name="email" value="<?php echo $user["email"];?>"></td>
  </tr>
    <tr>
    <td>�һ��������⣺</td>
    <td><input name="question" type="text" size="60"  value="<?php echo $user["question"];?>"/></td>
  </tr>
  <tr>
    <td>�һ�����𰸣�</td>
    <td><input name="answer" type="text" size="60" />
      (�粻�޸��Ա�����)</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><input type="submit" name="Submit" value="�ύ"></td>
  </tr></form>
</table>
<?php
}
elseif($action=="epw")
{
?>
<table width="100%" border="1" cellspacing="0" cellpadding="0" class="">
<form id="form2" name="form2" method="post" action="account.php">
<input type="hidden" name="action" value="saveepw" />
  <tr>
    <td width="100">&nbsp;</td>
    <td style='color:#f00;'>
	<?php
			$msg=HtmlReplace($_GET["msg"]);
			if(empty($msg))
			{
			   echo "�޸��������";
			}
			else
			{
			    echo $msg;
			}
			?>	</td>
  </tr>
  <tr>
    <td>ԭ���룺</td>
    <td><input type="text" name="oldpassword" /></td>
  </tr>
  <tr>
    <td>�����룺</td>
    <td><input type="text" name="newpassword" /></td>
  </tr>
  <tr>
    <td>ȷ�������룺</td>
    <td><input type="text" name="newpassword2" /></td>
  </tr>

  <tr>
    <td>&nbsp;</td>
    <td><input type="submit" name="Submit2" value="�ύ" /></td>
  </tr>
  </form>
</table>

<?php
}
?>
