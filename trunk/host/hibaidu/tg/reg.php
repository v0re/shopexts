<?php

/*
	[kuaso!] (C)209-2010 Kuaso Inc.
	
	This is NOT a freeware, use is subject to license terms

	$Id: reg.php 7289 2010-01-24 16:17:18Z anjel $
*/

require( "global.php" );
$action = $_POST['action'];
if ( $action == "savereg" )
{
                $user_name = htmlreplace( $_POST['user_name'] );
                $password = htmlreplace( $_POST['passwd1'] );
                $email = htmlreplace( $_POST['email'] );
                $question = htmlreplace( $_POST['question'] );
                $answer = htmlreplace( trim( $_POST['answer'] ) );
                $real_name = htmlreplace( $_POST['real_name'] );
                $check = $db->get_one( "select * from kuaso_zz_user where user_name='".$user_name."'" );
                if ( empty( $check ) )
                {
                                $array = array(
                                                "user_name" => $user_name,
                                                "password" => md5( $password ),
                                                "real_name" => $real_name,
                                                "email" => $email,

"question" => $question,
                                                "answer" => md5( $answer ),
                                                "reg_ip" => ip( ),
                                                "reg_time" => time( ),
                                                "points" => $tg_config['zs_points']);
		 $db->insert("kuaso_zz_user",$array);
		 $_SESSION["user_name"]=$user_name;
		 header("location:./");
	 }
	 else
	 {
	    header("location:reg.php?msg=".urlencode("�û����Ѵ���,�뻻������!"));
	 }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title><?php echo $config["name"];?>�ƹ�ƽ̨</title>
<link href="images/reg.css" rel="stylesheet" type="text/css">
</head>
<script language="javascript">
function checkform()
{
    var f1 = document.forms[0];
	var wm = "�Բ��𣬱����";
	var noerror = 1;

	// --- entered_login ---
	var t1 = f1.user_name;
	if (t1.value == "" || t1.value == " ") {
		wm += "�û�����";
		noerror = 0;
	}
	var t1 = f1.passwd1;
	var t2 = f1.passwd2;
	if (t1.value == "" || t1.value == " "||t2.value == "" || t2.value == " ") {
		wm += "���룬";
		noerror = 0;
	}
	var t1 = f1.email;
	if (t1.value == "" || t1.value == " "||(t1.value!=""&&!/^\w(\w*\.*)*@(\w+\.)+\w{2,4}$/.test(t1.value))) {
		wm += "���䣬";
		noerror = 0;
	}
	var t1 = f1.real_name;
	if (t1.value == "" || t1.value == " ") {
		wm += "��ϵ��������";
		noerror = 0;
	}
	// --- check if errors occurred ---
	if (noerror == 0) {
		alert(wm.substr(0, wm.length-1));
		return false;
	}
	else return true;
}
</script>
<body>
<center>
  <div  id="main" align="left">
    <table width="100%"  border="0" cellspacing="0" cellpadding="0" id="hd">
      <tr>
        <td width="150" valign="top"><a href="./"><img src="images/logo.gif" border="0" align="absmiddle" class="lg"></a></td>
        <td valign="top"><div class="tt">
            <div class="r">&nbsp;</div>
          �û�ע��</div></td>
      </tr>
    </table>
    <br>
		<div class="pad_1">
		  <p><strong>ע�Ჽ��:</strong> <img src="images/ico6_1.gif" align="absmiddle"> <strong>��д��Ϣ</strong>&nbsp;&nbsp;&nbsp;&nbsp;2 ȷ����Ϣ<br>
		    <br>
		    <img src="images/ico6_7.gif" align="absmiddle">&nbsp;&nbsp;<span class="col_1">�������д��ʵ��Ϣ���Ա�Ϊ���ṩ���񡣴�*Ϊ�����<br>
		    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><br>
	      </p>
		  <div class="regTit">ע����Ϣ</div>
		<form name="register" id="register" action="reg.php" method="post" onSubmit="return checkform();">
		<input type=hidden name=action value=savereg>

			<table border="0" cellspacing="0" cellpadding="0" width="100%"><tr ></tr>
              
              <tr >
              <td valign="top" class="pad_3">
				<strong>�û���&nbsp;*</strong><br />
				<p><input type="text" name="user_name" class="input bd" maxlength="32" id="user_name" value=""/></p><p class="error"></p>
				<div class="note">��Ӣ�ľ���</div></td>
            </tr>
            <tr>
              <td valign="top" class="pad_3">
				<strong>��������&nbsp;*</strong><br>
				<p><input name="passwd1" class="input bd" type="password" id="passwd1" maxlength="32" value=""/></p><p class="error"></p>
				<div class="note">������λ</div></td>
            </tr>
            <tr>
              <td valign="top" class="pad_3">
				  <strong>���ظ�����&nbsp;*</strong><br>
				  <p><input name="passwd2" class="input bd" type="password" id="passwd2" maxlength="32" value="" /></p><p class="error"></p></td>
            </tr>
			<tr>
              <td valign="top" class="pad_3">
				  <strong>�һ���������&nbsp;*</strong><br>
			  <p><input name="question" class="input bd" type="text" id="question" maxlength="32" value="" />
			  </p><p class="error"></p></td>
            </tr>
			<tr>
              <td valign="top" class="pad_3">
				  <strong>�һ������&nbsp;*</strong><br>
			  <p><input name="answer" class="input bd" type="text" id="answer" maxlength="32" value="" />
			  </p><p class="error"></p>
			  <div class="note">��������ʧʱƾ����ȡ</div>
			  </td>
            </tr>
		  </table>
			<div class="regTit">��ϵ����</div>
			<table border="0" cellspacing="0" cellpadding="0" width="100%">
            <tr>
              <td valign="top" class="pad_3">
				<strong>�����ʼ�&nbsp;*</strong><br>
				<p><input name="email" class="input bd" maxlength="100" value="" id="email"   /></p><p class="error"></p>
				<div class="note">��õ��ʼ���ַ������kuaso@sina.com</div></td>
            </tr>
            <tr>
              <td valign="top" class="pad_3">
				<strong>��ϵ������&nbsp;*</strong><br>
				<p><input name="real_name" size="12" class="bd" maxlength="50" value="" id="real_name"/></p><p class="error"></p>
                			<div class="note">�ƹ㸺������ʵ����</div></td>
            </tr>

			</table>
			<div class="cutline"></div>
            <table border="0" cellspacing="0" cellpadding="0" width="100%">
<!-- BLOCK: ����Э��� -->

<tr style="display:none">
<td colspan="2" valign="top" class="pad_3"><p><input name="agree" type="checkbox" id="checkbox" /><label for="checkbox"> �����Ķ�����������</label> <span  title="֣����ʾ������<?php echo $config["name"];?>�ƹ�����ͬ�����û����С�<?php echo $config["name"];?>�����ƹ㡱���������ȷ���汾���û����ܡ�<?php echo $config["name"];?>�����ƹ㡱��������ζ��ͬ�Ⲣǩ���˱���ͬ���û�ǩ��������ļ����йء�<?php echo $config["name"];?>�����ƹ㡱���������뱾��ͬ�����ģ��Ա���ͬΪ׼"><?php echo $config["name"];?>�ƹ�����ͬ</span></p><p class="error"></p>
<input type="hidden" name="protocolshow" value="1">
</td>
</tr>
            <tr>
              <td height="30" colspan="2"><br />
			  <input type="hidden" name="stage" value="1">
			  <input type="submit" class="but3" name="stage" value="ע��" />
			  <br/><br/></td>
            </tr>
          </table></form>
    </div>
</div>
</center>
</body>
</html>
