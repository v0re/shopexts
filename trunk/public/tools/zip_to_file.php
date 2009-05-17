<?php

if ( !IsSet($_GET['dirname']) )
{
	show_input_form() ;
}
else
{
	// check if empty
	if ( empty($_GET['dirname']) )
	{
		hg_exit("请输入文件夹名!") ;
	}

	// check valid dirname
	if ( FALSE !== strpos($_GET['dirname'], "/") )
	{
		hg_exit("\"/\" 是非法的文件夹名!") ;
	}
	if ( FALSE !== strstr($_GET['dirname'], "..") )
	{
		hg_exit("\"..\" 是非法的文件夹名!") ;
	}

	// check valid dir
	if ( !is_dir($_GET['dirname']) )
	{
		hg_exit("\"{$_GET['dirname']}\" 不是一个有效的文件夹!") ;
	}

	$szData = "" ;
	$szInfo = "" ;
	set_time_limit(0);
	$file_count = @ZipDir($_GET['dirname'], &$szData, &$szInfo) ;
	$info_size_16byte = @sprintf("%016d", @strlen($szInfo)) ;
	$szData = @sprintf("%016d",$file_count) . $info_size_16byte . $szInfo . $szData ;
	$filename = $_GET['dirname'] . ".dat" ;
	if ( function_exists(gzencode) )
	{
		$szData = gzencode($szData) ;
		$filename .= ".gz" ;
	}
	
	//Header("Content-type: application/octet-stream");
	//Header("Accept-Ranges: bytes");
	//Header("Accept-Length: " . strlen($szData));
	//Header("Content-Disposition: attachment; filename=$filename");

	//file_put_contents($filename,$szData);
	$fp = fopen($filename,"wb+");
	fwrite($fp,$szData);

	echo "<br><hr><br><b>done!!</b>";
}


function show_input_form()
{
	echo HtmlHead("文件打包") ;
	echo "<form name=\"input\">\n"
		. "请输入要打包的文件夹,注意,仅当前目录下的文件夹才可以下载!<p>\n"
		. "<input name=\"dirname\">\n"
		. "<input type=\"button\" value=\"确定\" onClick=\"show_download_link(dirname.value);\">\n"
		. "</form>\n" ;
	echo "<script>\n"
		. "input.dirname.focus();\n"
		. "function show_download_link(dir)\n"
		. "{"
		. "   var top = (screen.height-200)/2 ;\n"
		. "   var left = (screen.width-300)/2 ;\n"
		. "   newwin=window.open('', '', 'width=300,height=200,top=' + top + ',left=' + left + ', resizable=0,scrollbars=auto');\n"
		. "   url = \"{$_SERVER['PHP_SELF']}\" + \"?dirname=\" + dir ;\n"
		. "	  newwin.document.write('<a href=' + url + '>点击此链接下载，<br>或者右键点击此处选择\"另存为\"</a>');\n"
		. "}"
		. "</script>\n" ;
	echo HtmlFoot() ;
}


function ZipDir($szDirName, &$szData, &$szInfo)
{
	// write dir header
	$szInfo .= "$szDirName|[dir]\n" ;
	$file_count = 0 ;
	$hDir = OpenDir($szDirName) ;
	while ( $file = ReadDir($hDir) )
	{
		if ( $file=="." || $file==".." )	continue ;

		$szCurFile = "$szDirName/$file" ;

		if ( Is_Dir($szCurFile) )
		{
			$file_count += ZipDir($szCurFile, &$szData, &$szInfo) ;
		}
		else if ( Is_File($szCurFile) )
		{
			$hCurFile = fopen($szCurFile, "rb") ;
			$size = filesize($szCurFile) ;
			$szStream = fread( $hCurFile, $size ) ;
			fclose($hCurFile) ;
			$file_count++ ;

			// write info
			$szInfo .= "$szCurFile|$size\n" ;

			// write data
			$szData .= $szStream ;
		}
	}

	// write dir footer
	$szInfo .= "$szDirName|[/dir]\n" ;
	return $file_count ;
}


function hg_exit($str)
{
	echo HtmlHead("Error, exit!") ;
	echo "<h5>" . $str . "</h5>" ;
	echo HtmlFoot() ;
	exit ;
}


function HtmlHead($title)
{
	return "<html>\n\n<head>\n"
		. "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=gb2312\">\n"
		. "<style type=\"text/css\">\n"
		. "body,input,td{font:12px verdana}\n"
		. "</style>\n"
		. "</head>\n\n<body>\n\n" ;
}


function HtmlFoot()
{
	return Copyright() . "\n</body>\n\n</html>" ;
}


function Copyright()
{
	return "<center><font size=\"5\" face=\"楷体_GB2312\" color=\"red\">使用完请立即删除本文件，以避免被其它人发现使用!</font></center>\n"
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