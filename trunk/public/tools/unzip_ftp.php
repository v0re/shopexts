<?php
HtmlHead("ѡ���ѹ�ļ�:") ;

if ( !IsSet($HTTP_POST_VARS['submit']) )
{
	$gzip_info = "" ;
	echo "check zlib support... " ;
	if ( !function_exists("gzopen") )
	{
		$gzip_info = "<font color=\"red\">ע��! ���Ŀռ�û��zlib֧�֣������
		<a href=\"http://www.isphp.net/\" target=\"_blank\"><font color=\"blue\">phpZip</font></a> 
		ѹ���ļ�ʱ����Ҫѡ��ѹ����Gzip��ʽ���������޷���ȷ��ѹ!</font>" ;
	}
	else
	{
		$gzip_info = "<font color=\"blue\">��ϲ! ���Ŀռ�֧��zlibѹ����ǿ�ҽ����� 
		<a href=\"http://www.isphp.net/\"><font color=\"red\" target=\"_blank\">phpZip</font></a> 
		ѹ���ļ�ʱ��ѡ�С�ѹ����Gzip��ʽ��������������ļ���С!</font>" ;
	}
	echo " ----------------- OK!<br>\n" . $gzip_info ;
	
	echo "<br><br><br><br>
<form action=\"{$_SERVER["PHP_SELF"]}\" method=\"post\" enctype=\"multipart/form-data\">
<table align=\"center\" width=\"450\">
<tr><td height=\"20\" colspan=\"2\">����ѡ��ѹ���ļ���λ�ã�Ȼ������ȷ������ť�� <p></td></tr>


<tr><td colspan=2 height=10></td></tr>

<tr>
<td>ָ�����������ļ���</td>
<td><input name=\"server_filename\" value=\"data.dat.gz\" style=\"color:#0000ff\">(������\".\"��ʾ��ǰĿ¼)</td>
<td>FTP�û�����</td>
<td><input name=\"user\" value=\"\" style=\"color:#0000ff\"></td>
<td>FTP���룺</td>
<td><input name=\"pass\" value=\"\" style=\"color:#0000ff\"></td>
</tr>

<tr><td colspan=\"2\" align=center><br><input type=\"submit\" name=\"submit\" value=\"ȷ��\"></td></tr>
</table>
</form>
" ;
	HtmlFoot() ;
	exit ;
}
?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function s()
{
	if(document.body.scrollHeight>document.body.clientHeight-30)
	{
		scroll(0,document.body.scrollHeight-document.body.clientHeight+30);
	}
}
//-->
</SCRIPT>
<?php
function rptout($str)
{
	echo $str."<br><script>s();</script>";
	flush();
}

function doread($handle,$length)
{
	if ( function_exists("gzopen") )
		$ret = @gzread($handle, $length);
	else
		$ret = @fread($handle, $length);
	return $ret;

}

$tmpfile = basename($_POST['server_filename']) ;




if ( !$tmpfile )
{
	exit("��Ч���ļ����ļ�������,����ԭ�����ļ���С̫���ϴ�ʧ�ܻ�û��ָ�����������ļ���") ;	
}

$bgzExist = FALSE ;
if ( function_exists("gzopen") )
{
	$bgzExist = TRUE ;
}
$conn_id = ftp_connect('localhost','21') or die("Couldn't connect to ".$_POST['server']);

// try to login
if (@ftp_login($conn_id, $_POST['user'], $_POST['pass'])) {
    rptout("�û�".$_POST['user']."@".$_POST['server']."�����ӷ�����\n");
} else {
    rptout("�û� ".$_POST['user']."�޷����ӷ�����\n");
	exit;
}

ftp_chdir($conn_id,"/");

$alldata = "" ;
$pos = 0 ;

$gzp = $bgzExist ? @gzopen($tmpfile, "rb") : @fopen($tmpfile, "rb") ;


$nFileCount = doread($gzp, 16) ;
$pos += 16 ;

$size = doread($gzp, 16) ;
$pos += 16 ;

$info = doread($gzp, $size-1) ;		// strip the last '\n'
$pos += $size ;

$info_array = explode("\n", $info) ;

$c_file = 0 ;
$c_dir = 0 ;
 doread($gzp, 1) ;
foreach ($info_array as $str_row)
{
	list($filename, $attr) = explode("|", $str_row);
	if ( substr($attr,0,6) == "[/dir]" )
	{
		echo "End of dir $filename<br>";
		continue;
	}
	
	if ( substr($attr,0,5)=="[dir]" )
	{
		if (@ftp_mkdir ($conn_id, $filename))
			 rptout("Make dir $filename");
		$c_dir++ ;
	}
	else
	{
		$fp = @fopen('upfile_tmp.txt', "wb") or exit("�����½��ļ� $filename ,��Ϊû��дȨ�ޣ����޸�Ȩ��");
		@fwrite($fp, doread($gzp,$attr) );
		$pos += $attr ;
		fclose($fp);
		echo "Create file $filename<br>";
		if(@ftp_put($conn_id,$filename,'upfile_tmp.txt' , FTP_BINARY))
		{
					rptout($filename." �ɹ��ϴ�");
					$files_num++;
		}
		else
		{
					rptout("<font color=red>".$filename." �ϴ�ʧ��</font>");
					$filefail_num++;
		}

		$c_file++ ;
	}
}
$bgzExist ? @gzclose($gzp) : @fclose($gzp) ;
@ftp_close($conn_id);



echo "<h1>�������! ������ļ� $c_file ���� �ļ��� $c_dir ����ллʹ��!</h1><p>" ;
HtmlFoot() ;




function HtmlHead($title="", $css_file="")
{
	echo "<html>\n"
		. "\n"
		. "<head>\n"
		. "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=gb2312\">\n"
		. "<title>$title</title>\n"
		. "<style type=\"text/css\">\n"
		. "body,pre,td {font-size:12px; background-color:#fcfcfc; font-family:Tahoma,verdana,Arial}\n"
		. "input,textarea{font-size:12px; background-color:#f0f0f0; font-family:Tahoma,verdana,Arial}\n"
		. "</style>\n"
		. "</head>\n"
		. "\n"
		. "<body>\n" ;
}

function HtmlFoot()
{
	echo "<center><font size=\"5\" face=\"����_GB2312\" color=\"red\">ʹ����������ɾ�����ļ����Ա��ⱻ�����˷���ʹ��!</font></center>\n"
		. "<br><hr color=\"#003388\">\n"
		. "<center>\n"
		. "<p style=\"font-family:verdana; font-size:12px\">Contact us: \n"
		. "<a href=\"http://www.isphp.net/\" target=\"_blank\">http://www.isphp.net/</a></p>\n"
		. "</center>\n"
		. "</body>\n"
		. "\n"
		. "</html>" ;
}

?>