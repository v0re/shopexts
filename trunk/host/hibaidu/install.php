<?php
$action=$_GET["action"];
if ($action=="start")
{
$file_name="soso.sql"; //Ҫ�����SQL�ļ���
$dbhost=$_POST["dbhost"]; //���ݿ�������
$dbuser=$_POST["dbuser"]; //���ݿ��û���
$dbpass=$_POST["dbpass"]; //���ݿ�����
$dbname=$_POST["dbname"]; //���ݿ���
set_time_limit(0); //���ó�ʱʱ��Ϊ0����ʾһֱִ�С���php��safe modeģʽ����Ч����ʱ���ܻᵼ�µ��볬ʱ����ʱ��Ҫ�ֶε���
$fp = @fopen($file_name, "r") or die("���ܴ�SQL�ļ� $file_name");//���ļ�
$conn=mysql_connect($dbhost, $dbuser, $dbpass) or die("�����������ݿ� $dbhost");//�������ݿ�
mysql_query("set names utf8",$conn);  
$sql="DROP DATABASE $dbname";     //������ݿ����,��ɾ��.
mysql_query($sql);
$sql="CREATE DATABASE $dbname";  //������ϱ����,Ҳ��ɾ��...   ���԰�ȫ����Ҫ����һ��.
mysql_query($sql);

mysql_select_db($dbname) or die ("���ܴ����ݿ� $dbname");//�����ݿ� 
mysql_query("SET NAMES 'GBK'");
//echo "<div class=\"tips\">";
//echo "����ִ�е������<BR>";
while($SQL=GetNextSQL()){
if (mysql_query($SQL)){
//echo "ִ��SQL��".mysql_error()."";
//echo "SQL���Ϊ��".$SQL."<BR>";
}
}
//echo "�������"; 
//echo "</div>";
header("location:install.php?action=finish");
fclose($fp) or die("Can't close file $file_name");//�ر��ļ�
mysql_close(); 

//��ʼд��Data/db_config.php
$content.="<?php".chr(13).chr(10);
$content.="\$dbhost = '$dbhost';".chr(13).chr(10);
$content.="\$dbuser = '$dbuser';".chr(13).chr(10);
$content.="\$dbpw = '$dbpass';".chr(13).chr(10);
$content.="\$dbname = '$dbname';".chr(13).chr(10);
$content.="\$dbcharset = 'gbk';".chr(13).chr(10);
$content.="\$dbpconnect = '0';".chr(13).chr(10);
$content.="?>";
 $fp=@fopen("include/db_config.php","w") or die("д��ʽ���ļ�ʧ�ܣ��������Ŀ¼�Ƿ�Ϊ��д");//����conn.php�ļ�
 @fputs($fp,$content) or die("�ļ�д��ʧ��,�������Ŀ¼�Ƿ�Ϊ��д"); 
 @fclose($fp);
//end
}

function GetNextSQL() {
global $fp;
$sql="";
while ($line = @fgets($fp, 40960)) {
$line = trim($line);
//���������ڸ߰汾php�в���Ҫ
$line = str_replace("\\\\","\\",$line);
$line = str_replace("\'","'",$line);
$line = str_replace("\\r\\n",chr(13).chr(10),$line);
// $line = stripcslashes($line);
if (strlen($line)>1) {
if ($line[0]=="-" && $line[1]=="-") {
continue;
}
}
$sql.=$line.chr(13).chr(10);
if (strlen($line)>0){
if ($line[strlen($line)-1]==";"){
break;
}}
}
return $sql;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>������������ϵͳ��װ����www.kuaso.com</title>
<script language="javascript">
function checkform()
{
   if(installform.dbhost.value=="")
   {
   alert("����Ϊ��");
   installform.dbhost.focus();
   return false;
   }
   
         if(installform.dbuser.value=="")
   {
   alert("����Ϊ��");
   installform.dbuser.focus();
   return false;
   }
   
      if(installform.dbpass.value=="")
   {
   alert("����Ϊ��");
   installform.dbpass.focus();
   return false;
   }
   
      if(installform.dbname.value=="")
   {
   alert("����Ϊ��");
   installform.dbname.focus();
   return false;
   }
   
}
</script>
<style>
body{font-size:12px;}
#all{margin:0 auto;}
.table{background:#CCCCCC;}
.table td{background:#FFFFFF;}
.tips{width:800px;border:1px solid #CCCCCC;background:#EEEEEE; margin:0 auto;padding:8px;line-height:25px;}
#footer{width:100%;margin-top:10px;text-align:center;}
</style>
</head>

<body>
<div id="all">
<?php
if($action=="finish")
{
?>
    <div class="tips">����ɰ�װ,��ɾ��Ŀ¼��install.php�ļ�!<br>
	      <a href="index.php">��վ��ҳ</a>&nbsp;&nbsp;<a href="admin/">��̨����</a>
	</div>
<?php
}
else
{
?>
<form name="installform" method="post" action="?action=start" onsubmit="return checkform();">

<table width="600" border="0" align="center" cellpadding="3" cellspacing="1" class="table">
  <tr>
    <td width="120">���ݿ��ַ��</td>
    <td><input name="dbhost" type="text" id="dbhost" value="localhost"></td>
  </tr>
  <tr>
    <td>�û�����</td>
    <td><input name="dbuser" type="text" value="root"></td>
  </tr>
  <tr>
    <td>��  �룺</td>
    <td><input type="text" name="dbpass"></td>
  </tr>
  <tr>
    <td>���ݿ����ƣ�</td>
    <td><input type="text" name="dbname"></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><input type="submit" name="Submit" value="�ύ"></td>
  </tr>
</table>
</form>
<?php
}
?>
<div id="footer">
���ѷ°ٶ���������ϵͳ��װ����&nbsp;&nbsp;�������°汾:<a href="http://www.kuaso.com" target="_blank">http://www.kuaso.com</a>&nbsp;&nbsp;</div>
</div></body>
</html>