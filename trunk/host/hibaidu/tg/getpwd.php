<?php

/*
	[kuaso!] (C)209-2010 Kuaso Inc.
	
	This is NOT a freeware, use is subject to license terms

	$Id: getpwd.php 3235 2010-01-24 16:17:18Z anjel $
*/

require("../global.php");
$user_name=HtmlReplace($_POST["user_name"]);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title><?php echo $config["name"];?>�ƹ�ƽ̨--�һ�����</title>
<link href="images/reg.css" rel="stylesheet" type="text/css"></head>
<body>
<center>
  <div  id="main" align="left">
    <table width="100%"  border="0" cellspacing="0" cellpadding="0" id="hd">
      <tr>
        <td width="150" valign="top"><a href="./"><img src="images/logo.gif" border="0" align="absmiddle" class="lg"></a></td>
        <td valign="top"><div class="tt">
            <div class="r">&nbsp;</div>
            <strong>�һ�����</strong></div></td>
      </tr>
    </table>
    <br>
	<div class="pad_1">
		  <p><strong>�һ�����:</strong> <img src="images/ico6_1.gif" align="absmiddle">&nbsp;&nbsp;�����û���&nbsp;&nbsp;&nbsp;&nbsp;2 �ش������&nbsp;&nbsp;&nbsp;&nbsp;3 �����µ�����<br>
		    <br>
	      <img src="images/ico6_7.gif" align="absmiddle">&nbsp;&nbsp;<span class="col_1">�������������Ϣ���԰����������һ����롣</span></p>
          <form name="form1" method="post" action="">
		  <?php
		  if(empty($user_name))
		  {
		  ?>
            1���û����� 
            <input type="text" name="user_name">
            <input type="submit" name="Submit" value="��һ��">
		<?php
		}
		else
		{
		   $user=$db->get_one("select * from kuaso_zz_user where user_name='".$user_name."'");
		   if(empty($user))
		   {
		       echo "<font class=\"error\">".$user_name."&nbsp;&nbsp;�û�������.</font><br><br><a href=\"?\">���ص�һ��</a>";
		   }
		   else
		   {
		       $answer=HtmlReplace(trim($_POST["answer"]));
		       ?>
			   <input type="hidden" name="user_name" value="<?php echo $user_name;?>">
			   �һ��������⣺<?php echo $user["question"];?><br>
			   2���һ�����𰸣�
			   <input type="text" name="answer" value="<?php echo $answer;?>">
			   <input type="submit" name="Submit" value="��һ��">
			   <?php
			   
			   if(!empty($answer))
			   {
			      $sql="select * from kuaso_zz_user where user_name='".$user_name."' and answer='".md5($answer)."'";//echo $sql;
			      $row=$db->get_one($sql);
				  if(empty($row))
				  {
				      echo "<br><br><font class=\"error\">�һ�����𰸲���ȷ!</font>";
				  }
				  else
				  {
				      ?>
					  <br>
					  <br>
					  3���ظ���ȷ<br>
					  �������µ����룺<input type="text" name="password">
					  <input type="submit" name="Submit" value="ȷ��">
					  <?php
					  $password=HtmlReplace(trim($_POST["password"]));
					  if(!empty($password))
					  {
					     $array=array('password'=>md5($password));
					     $db->update("kuaso_zz_user",$array,"user_name='".$user_name."'and answer='".md5($answer)."'");
					     echo "<br>��ϲ,�޸ĳɹ�!&nbsp;&nbsp;<a href=\"login.php\">�����¼</a>";
					  }
				  }
			   }
		   }
		
		}
		?>
          </form>
          <p><span class="col_1"><br>
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><br>
          </p>
	</div>
		  
</div>
</center>
</body>
</html>
