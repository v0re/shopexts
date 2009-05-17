<?php
HtmlHead("选择解压文件:") ;

if ( !IsSet($HTTP_POST_VARS['submit']) )
{
	$gzip_info = "" ;
	echo "check zlib support... " ;
	if ( !function_exists("gzopen") )
	{
		$gzip_info = "<font color=\"red\">注意! 您的空间没有zlib支持，因此用
		<a href=\"http://www.isphp.net/\" target=\"_blank\"><font color=\"blue\">phpZip</font></a> 
		压缩文件时，不要选择“压缩成Gzip格式”，否则将无法正确解压!</font>" ;
	}
	else
	{
		$gzip_info = "<font color=\"blue\">恭喜! 您的空间支持zlib压缩，强烈建议用 
		<a href=\"http://www.isphp.net/\"><font color=\"red\" target=\"_blank\">phpZip</font></a> 
		压缩文件时，选中“压缩成Gzip格式”，将会大大减少文件大小!</font>" ;
	}
	echo " ----------------- OK!<br>\n" . $gzip_info ;
	
	echo "<br><br><br><br>
<form action=\"{$_SERVER["PHP_SELF"]}\" method=\"post\" enctype=\"multipart/form-data\">
<table align=\"center\" width=\"450\">
<tr><td height=\"20\" colspan=\"2\">请先选择压缩文件的位置，然后点击“确定”按钮： <p></td></tr>


<tr><td colspan=2 height=10></td></tr>

<tr>
<td>指定服务器上文件：</td>
<td><input name=\"server_filename\" value=\"data.dat.gz\" style=\"color:#0000ff\">(可以用\".\"表示当前目录)</td>
<td>FTP用户名：</td>
<td><input name=\"user\" value=\"\" style=\"color:#0000ff\"></td>
<td>FTP密码：</td>
<td><input name=\"pass\" value=\"\" style=\"color:#0000ff\"></td>
</tr>

<tr><td colspan=\"2\" align=center><br><input type=\"submit\" name=\"submit\" value=\"确定\"></td></tr>
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
	exit("无效的文件或文件不存在,可能原因有文件大小太大，上传失败或没有指定服务器端文件等") ;	
}

$bgzExist = FALSE ;
if ( function_exists("gzopen") )
{
	$bgzExist = TRUE ;
}
$conn_id = ftp_connect('localhost','21') or die("Couldn't connect to ".$_POST['server']);

// try to login
if (@ftp_login($conn_id, $_POST['user'], $_POST['pass'])) {
    rptout("用户".$_POST['user']."@".$_POST['server']."已连接服务器\n");
} else {
    rptout("用户 ".$_POST['user']."无法连接服务器\n");
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
		$fp = @fopen('upfile_tmp.txt', "wb") or exit("不能新建文件 $filename ,因为没有写权限，请修改权限");
		@fwrite($fp, doread($gzp,$attr) );
		$pos += $attr ;
		fclose($fp);
		echo "Create file $filename<br>";
		if(@ftp_put($conn_id,$filename,'upfile_tmp.txt' , FTP_BINARY))
		{
					rptout($filename." 成功上传");
					$files_num++;
		}
		else
		{
					rptout("<font color=red>".$filename." 上传失败</font>");
					$filefail_num++;
		}

		$c_file++ ;
	}
}
$bgzExist ? @gzclose($gzp) : @fclose($gzp) ;
@ftp_close($conn_id);



echo "<h1>操作完毕! 共解出文件 $c_file 个， 文件夹 $c_dir 个，谢谢使用!</h1><p>" ;
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
	echo "<center><font size=\"5\" face=\"楷体_GB2312\" color=\"red\">使用完请立即删除本文件，以避免被其它人发现使用!</font></center>\n"
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