<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
 <HEAD>
  <TITLE> New Document </TITLE>
  <META NAME="Generator" CONTENT="EditPlus">
  <META NAME="Author" CONTENT="">
  <META NAME="Keywords" CONTENT="">
  <META NAME="Description" CONTENT="">
 </HEAD>
 <BODY>
<?php
require "config.php";
$email = htmlspecialchars( $_POST['email'] );
$name = htmlspecialchars( $_POST['name'] );
$sb = htmlspecialchars( $_POST['sb'] );
$msg = htmlspecialchars( $_POST['msg'] );

require("phpmailer/class.phpmailer.php");

if ( $email && $name &&  $msg ){
	
	$mail = new PHPMailer();

		//$mail->IsSMTP();                                      // set mailer to use SMTP
		//$mail->Host = "smtp.126.com";  // specify main and backup server
		//$mail->SMTPAuth = true;     // turn on SMTP authentication
		//$mail->Username = "cn.cos";  // SMTP username
		//$mail->Password = "202619"; // SMTP password

		$mail->CharSet = 'utf-8';
		$mail->Encoding = 'base64';

		$mail->From = $email;
		$mail->FromName = $name;
	//loop for add address
		
		$mail->AddAddress($E_MAIL);
		$mail->WordWrap = 50;                                 // set word wrap to 50 characters
		//$mail->AddAttachment("/var/tmp/file.tar.gz");         // add attachments
		//$mail->AddAttachment("/tmp/image.jpg", "new.jpg");    // optional name
		$mail->IsHTML(true);                                  // set email format to HTML

		$mail->Subject = $sb;
		$mail->Body    = $msg;

		if(!$mail->Send())
		{
		   echo "Message could not be sent. <p>";
		   echo "Mailer Error: " . $mail->ErrorInfo;
		}

		echo "<SCRIPT LANGUAGE=\"JavaScript\">\n
		<!--\n
			alert(\"Message has been sent, thank you\");\n
		//-->\n
		</SCRIPT>\n";
	}


?>


  
 </BODY>
</HTML>
