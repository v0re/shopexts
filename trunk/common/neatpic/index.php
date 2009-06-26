<?php
/*
+---------------------------------------------------+
| Name : NEATPIC (无数据版本)
+---------------------------------------------------+
| Created / Modify : 2003-12-27 / 2004-4-13
+---------------------------------------------------+
| Version : 1.2.3
+---------------------------------------------------+
| Author : walkerlee, gouki
+---------------------------------------------------+
| Powered by NEATSTUDIO 2002 - 2004
+---------------------------------------------------+
| QQ : 808075
| Email : walkerlee@163.net
| Homepge : http://www.neatstudio.com
| BBS : http://www.neatstudio.com/bbs/
+---------------------------------------------------+
| Note :
|
| 1.本软件对于非商业用户完全免费,如果要使用在商业用途
| 方面,必须取得作者的授权.
|
| 2.你可以任意传播以及修改本程序,但不能以任何形式删除
| 本程序的版权.请记住,保留作者版权是对作者工作的尊敬.
|
| 3.如果有问题,可以通过上面提供的方式进行解答,但作者
| 学业繁重,如果不能及时或者不解答,请谅解.
|
| 4.作者对使用该程序导致的问题,不予以负责.
|
| 5.本程序版权归 NeatStudio 所有.禁止任何侵权行为!
|
+---------------------------------------------------+
*/


//require_once ("config.php");

/*
+----------------------------------+
| Config
+----------------------------------+
| C / M : 2003-12-28 / 2004-4-13
+----------------------------------+
*/

define("M1", 1024000);  // 定义1M内容的大小，请不要改动

$configAdminPass		= "123456";										//管理员密码 注:安全起见,默认密码不能登陆管理
$configWantedPass		= false;										//查看相册是否需要密码 需要:true 不需要:false
$configOpenGzip			= true;					 						//是否压缩页面 压缩:true 不压缩:false
$configShowPicSize		= false;										//是否显示图片的大小 (单位:KB) 显示:true 不显示:false (注:不显示,程序运行速度将提高)
$configExt				= array('jpg', 'jpeg', 'gif', 'png', 'bmp');	//图片类型
$strLenMax				= 50;											//文件名字限制长度 (防止撑破表格)
$configEachPageMax		= 10;											//每页显示的图片数目
$configEachLineMax		= 4;											//每行显示的图片数目
$configTDWidth			= 185;											//表格宽度
$configTDHeight			= 138;											//表格高度
$configPageMax			= 5;											//分页前后预览数
$configDirPasswordFile	= "neatpicPassword.php";						//密码文件
$configTilte			= "Jacob的图片专栏";							//标题
$configVer				= "1.2.3";										//程序版本号

$configAllowSizes		= 10;											//允许上传的图片容量的总大小 （单位为M）
$user					= "jacob";										//定义权限

$homeUrl				= "http://jacobit.com";							//网站主页地址
$showHomeUrl			= true;											//是否显示主页链接

/*
+----------------------------------+
| Class
+----------------------------------+
| C / M : 2003-12-28 / 2003-12-29
+----------------------------------+
*/

Class neatpic
{
	var $configWantedPass;
	var $configAdminPass;
	var $configOpenGzip;
	var $configShowPicSize;
	var $configExt = array();
	var $strLenMax;
	var $configEachPageMax;
	var $configEachLineMax;
	var $configTDHeight;
	var $configTDWidth;
	var $configPageMax;
	var $configTilte;
	var $configVer;
	var $configAllowSizes;
	//var $user;
	//var $homeUrl;
	//var $showHomeUrl;

	var $dirOptionList;
	var $timer;
	var $usedTime;
	var $pathLevelNum;
	var $nowDirNmae;
	var $dirNum;
	var $picNum;
	var $pageTotal;
	var $start;
	var $offSet;
	var $pageStart;
	var $pageMiddle;
	var $pageEnd;
	var $temp;
	var $picID;
	var $picRealSizeWidth;
	var $picRealSizeHeight;

	var $picArray = array();
	var $picFileArray = array();
	var $dirArray = array();
	var $dirNameArray = array();
	var $pathArray = array();
	var $pathError = false;

	var $page;
	var $path;
	var $style;
	var $c;

	/*
	+----------------------------------+
	| Constructor
	+----------------------------------+
	| C / M : 2003-12-28 / 2003-12-29
	+----------------------------------+
	*/
	
	function neatpic($configWantedPass, $configAdminPass, $configDirPasswordFile, $configOpenGzip, $configShowPicSize, $configExt, $strLenMax, $configEachPageMax, $configEachLineMax, $configTDHeight, $configTDWidth, $configPageMax, $configTilte, $configVer, $configAllowSizes, $user, $homeUrl, $showHomeUrl)
	{
		$this->configWantedPass				= & $configWantedPass;
		$this->configAdminPass				= & $configAdminPass;
		$this->configDirPasswordFile		= & $configDirPasswordFile;
		$this->configOpenGzip				= & $configOpenGzip;
		$this->configShowPicSize			= & $configShowPicSize;
		$this->configExt					= & $configExt;
		$this->strLenMax					= & $strLenMax;
		$this->configEachPageMax			= & $configEachPageMax;
		$this->configEachLineMax			= & $configEachLineMax;
		$this->configTDHeight				= & $configTDHeight ;
		$this->configTDWidth				= & $configTDWidth;
		$this->configPageMax				= & $configPageMax;
		$this->configTilte					= & $configTilte;
		$this->configVer					= & $configVer;
		$this->configAllowSizes				= & $configAllowSizes;
		$this->user							= & $user;
		$this->homeUrl						= & $homeUrl;
		$this->showHomeUrl					= & $showHomeUrl;
	}

	/*
	+----------------------------------+
	| Open gzip
	+----------------------------------+
	| C / M : 2003-12-29 / --
	+----------------------------------+
	*/
	
	function gzip()
	{
		if ($this->configOpenGzip == true) 
			ob_start("ob_gzhandler");
	}

	/*
	+----------------------------------+
	| Get the querystring
	+----------------------------------+
	| C / M : 2003-12-28 / 2003-12-29
	+----------------------------------+
	*/

	function getVars()
	{
		$this->page = rawurldecode($_GET['page']);
		$this->path = rawurldecode($_GET['path']);
		$this->style = $_GET['style'];

		if (!$this->style) $this->style = "small";
		if (!$this->path) $this->path = ".";
	}

	/*
	+----------------------------------+
	| Check error
	+----------------------------------+
	| C / M : 2003-12-28 / 2004-1-1
	+----------------------------------+
	*/

	function checkError()
	{
		if (preg_match("/\.\./", $this->path)) $pathError = true;
		if (!is_dir($this->path)) $pathError = true;

		if ($pathError == true)
		{
			header("location:".$_SERVER['PHP_SELF']);
			exit;
		}
	}

	/*
	+----------------------------------+
	| Path array initialize
	+----------------------------------+
	| C / M : 2003-12-28 / 2003-12-29
	+----------------------------------+
	*/

	function pathArrayInitialize()
	{
		if (!$this->path) $this->path = ".";

		$this->pathArray = explode("/", $this->path);
		$this->pathLevelNum = count($this->pathArray);
		$this->nowDirName = $this->pathArray[$this->pathLevelNum - 1];
		if ($this->nowDirName == ".") $this->nowDirName = "根目录";
	}

	/*
	+----------------------------------+
	| Timer
	+----------------------------------+
	| C / M : 2003-12-29 / --
	+----------------------------------+
	*/

	function timer()
	{
		$time = explode( " ", microtime());
		$usec = (double)$time[0];
		$sec = (double)$time[1];
		$this->timer = $usec + $sec;
	}

	/*
	+----------------------------------+
	| Show used time
	+----------------------------------+
	| C / M : 2003-12-29 / --
	+----------------------------------+
	*/

	function usedTime()
	{
		$startTime = $this->timer;
		$this->timer();
		$endTime = $this->timer;
		$usedTime = $endTime - $startTime;
		$this->usedTime = sprintf("%0.4f", $usedTime);
	}

	/*
	+----------------------------------+
	| Make over direct
	+----------------------------------+
	| C / M : 2003-12-28 / 2003-12-29
	+----------------------------------+
	*/

	function makeOverdirect()
	{
		$overPath = ".";

		for($i = 1; $i < $this->pathLevelNum - 1; $i++)
		{
			$overPath = $overPath."/".$this->pathArray[$i];
		}

		$this->dirArray[] = $overPath;
		$this->dirNameArray[] = "上级目录";

		for($i = 1; $i < $this->pathLevelNum; $i++)
		{
			$this->encodePath .= rawurlencode($this->pathArray[$i])."/";
		}
	}

	/*
	+----------------------------------+
	| GetFileExt
	+----------------------------------+
	| C / M : 2003-12-28 / --
	+----------------------------------+
	*/

	function getFileExt($fileName)
	{
		$pos = strrpos($fileName, '.');
		return strtolower(substr($fileName, $pos+1, (strlen($fileName)-$pos-1)));
	}

	/*
	+----------------------------------+
	| Make direct list
	+----------------------------------+
	| C / M : 2003-12-28 / 2003-12-29
	+----------------------------------+
	*/

	function makeDirList()
	{
		$dir = dir($this->path);

		while($file = $dir->read())
		{
			if ($file <> "." and $file <> "..")
			{
				$fileName = $file;
				$file = $this->path."/".$file;

				if (is_dir($file))
				{
					$this->dirArray[] = $file;
					$this->dirNameArray[] = $fileName;
				}
				
				if (in_array($this->getFileExt($file), $this->configExt))
				{
					$this->picEncodeArray[] = "./" . $this->encodePath . rawurlencode($fileName);
					$this->picArray[] = $file;
					$this->picFileArray[] = $fileName;
				}
			}
		}

	}


	/*
	+----------------------------------+
	| 获取文件夹总大小 
	+----------------------------------+
	| create by Jacob 
	+----------------------------------+
	| C / M : 2004-4-18 / --
	+----------------------------------+
	*/
	function getDirSize($path)
	{
		$dir = dir($path);
		$size = 0;

		while($file = $dir->read())
		{

			$fileName = $file;
			$file = $path."/".$file;

			// 连.&..的值也计上
			if (is_dir($file) && $fileName <> ".." && $fileName <> ".")
			{
				$size += $this->getDirSize($file);
			}else 
				$size += filesize($file);

		}
		$dir->close();

		return $size;

	}

	/*
	+----------------------------------+
	| Get each array number
	+----------------------------------+
	| C / M : 2003-12-28 / --
	+----------------------------------+
	*/

	function getEachArrayNum()
	{
		$this->dirNum = count($this->dirArray);
		$this->picNum = count($this->picArray);
	}

	/*
	+----------------------------------+
	| Make page bar
	+----------------------------------+
	| C / M : 2003-12-28 / 2003-12-29
	+----------------------------------+
	*/

	function makePageBar()
	{

		$this->pageTotal = ceil($this->picNum / $this->configEachPageMax);

		if (!$this->page or $this->page < 0) $this->page = 1;
		if ($this->page > $this->pageTotal) $this->page = $this->pageTotal;

		$this->offSet = $this->configEachPageMax * $this->page;
		$this->start = $this->offSet - $this->configEachPageMax;

		if ($this->start < 0) $this->start = 0;
		if ($this->offSet > $this->picNum) $this->offSet = $this->picNum;

		$this->pageStart = $this->page - $this->configPageMax;
		if ($this->pageStart <= 0) $this->pageStart = 1;

		$this->pageMiddle = $this->page + 1;
		$this->pageEnd = $this->pageMiddle + $this->configPageMax;
		
		if ($this->page <= $this->configPageMax) $this->pageEnd = $this->configPageMax * 2 + 1;
		if ($this->pageEnd > $this->pageTotal) $this->pageEnd = $this->pageTotal + 1;
	}

	/*
	+----------------------------------+
	| Show page bar
	+----------------------------------+
	| C / M : 2003-12-28 / 2003-12-29
	+----------------------------------+
	*/

	function showPageBar()
	{
		print("<center>\n");
		print("<BR>");
		print("[ <A HREF=\"".$_SERVER['PHP_SELF']."?path=".rawurlencode($this->path)."&style=".$this->style."&page=".($this->page - 1)."\" title=\"上一页\">上一页</A> ]&nbsp;");
		
		print("<A HREF=\"".$_SERVER['PHP_SELF']."?path=".rawurlencode($this->path)."&style=".$this->style."&page=1\"  title=\"首页\"><< </A>\n");

		for ($i = $this->pageStart; $i < $this->page; $i++)
			print("<A HREF=\"".$_SERVER['PHP_SELF']."?path=".rawurlencode($this->path)."&style=".$this->style."&page=".$i."\" title=\"第 ".$i." 页\">[".$i."]</A>&nbsp;");

		printf("[<FONT COLOR=\"red\"><B>%s</B></FONT>]", $this->page);

		for ($i = $this->pageMiddle; $i < $this->pageEnd; $i++)
			print("<A HREF=\"".$_SERVER['PHP_SELF']."?path=".rawurlencode($this->path)."&style=".$this->style."&page=".$i."\" title=\"第 ".$i." 页\">[".$i."]</A>&nbsp;");

		print("...<A HREF=\"".$_SERVER['PHP_SELF']."?path=".rawurlencode($this->path)."&style=".$this->style."&page=".$this->pageTotal."\" title=\"第 " . $this->pageTotal . " 页\">[" . $this->pageTotal . "]</A>\n");
		
		print(" <A HREF=\"".$_SERVER['PHP_SELF']."?path=".rawurlencode($this->path)."&style=".$this->style."&page=".$this->pageTotal."\" title=\"尾页\">>></A>\n");

		print("[ <A HREF=\"".$_SERVER['PHP_SELF']."?path=".rawurlencode($this->path)."&style=".$this->style."&page=".($this->page + 1)."\" title=\"下一页\">下一页</A> ]&nbsp;共 <B><FONT COLOR=\"red\">".$this->pageTotal."</FONT></B> 页&nbsp;&nbsp;当前所在第 <B><FONT COLOR=\"red\">".$this->page."</FONT></B> 页");
		print("<BR><BR>");
		print("</center>\n");
	}

	/*
	+----------------------------------+
	| Set picture ID
	+----------------------------------+
	| C / M : 2003-12-28 / --
	+----------------------------------+
	*/

	function setPicID($id)
	{
		$this->picID = $id;
	}

	/*
	+----------------------------------+
	| Get picture dimension
	+----------------------------------+
	| C / M : 2003-12-28 / --
	+----------------------------------+
	*/

	function getPicDim()
	{				

		$picSize = GetImageSize($this->picArray[$this->picID]);
		preg_match("!width=\"(.*)\" height=\"(.*)\"!", $picSize['3'], $tempSize);

		$this->picRealSizeWidth		= $tempSize['1'];
		$this->picRealSizeHeight	= $tempSize['2'];

		/*
		$tempSize['1'] < $this->configTDWidth ? $this->temp['Width'] = $tempSize['1'] : $this->temp['Width'] = $this->configTDWidth;
		$tempSize['2'] < $this->configTDHeight ? $this->temp['Height'] = $tempSize['2'] : $this->temp['Height'] = $this->configTDHeight;
		*/

		$tWidth = $this->picRealSizeWidth / $this->configTDWidth;
		$tHeight = $this->picRealSizeHeight / $this->configTDHeight;

		if ($this->picRealSizeWidth > $this->configTDWidth OR $this->picRealSizeHeight > $this->configTDHeight)
		{
			if ($tWidth > $tHeight)
			{
				$this->temp['Width'] = $this->configTDWidth;
				$this->temp['Height'] = number_format($this->picRealSizeHeight / $tWidth);
			}
			elseif ($tWidth < $tHeight)
			{
				$this->temp['Height'] = $this->configTDHeight;
				$this->temp['Width'] = number_format($this->picRealSizeWidth / $tHeight);
			}
			else
			{
				$this->temp['Width'] = $this->configTDWidth;
				$this->temp['Height'] = $this->configTDHeight;
			}
		}
		else
		{
			$this->temp['Width']	= $this->picRealSizeWidth;
			$this->temp['Height']	= $this->picRealSizeHeight;
		}
	}

	/*
	+----------------------------------+
	| Javascript method
	+----------------------------------+
	| Create by Jacob
	+----------------------------------+
	| C / M : 2004-4-20 / --
	+----------------------------------+
	*/

	function ShowJSm()
	{
		print('
		<script>
		function rmThis(what,url){
			var info;
			if (what == "")
				info = "是否确定删除？";
			else
				info = "是否确定删除" + what;
			if(window.confirm(info)){
				location.href = url;
			}
		}
		</script>
		');
	}


	/*
	+----------------------------------+
	| Show the title javascript
	+----------------------------------+
	| C / M : 2003-12-29 / 2003-12-30
	+----------------------------------+
	*/

	function ShowJS()
	{
		print('
		<script>
		/******************************************************************************
		  NEATPIC Show Title
		  Modified by: walkerlee
		  Date: 2003-12-30
		  Based upon:  Crossday Studio and http://www.cnzzz.com
		*******************************************************************************/

		tPopWait=20;
		showPopStep=10;
		popOpacity=85;

		sPop=null;
		curShow=null;
		tFadeOut=null;
		tFadeIn=null;
		tFadeWaiting=null;

		document.write("<style type=\'text/css\'id=\'defaultPopStyle\'>");
		document.write(".cPopText { font-family: Verdana, Tahoma; background-color: #F7F7F7; border: 1px #000000 solid; font-size: 11px; padding-right: 4px; padding-left: 4px; height: 20px; padding-top: 2px; padding-bottom: 2px; filter: Alpha(Opacity=0)}");

		document.write("</style>");
		document.write("<div id=\'popLayer\' style=\'position:absolute;z-index:1000;\' class=\'cPopText\'></div>");


		function showPopupText(){
			var o=event.srcElement;
			MouseX=event.x;
			MouseY=event.y;
			if(o.alt!=null && o.alt!="") { o.pop=o.alt;o.alt="" }
				if(o.title!=null && o.title!=""){ o.pop=o.title;o.title="" }
				if(o.pop) { o.pop=o.pop.replace("\n","<br>"); o.pop=o.pop.replace("\n","<br>"); }
			if(o.pop!=sPop) {
				sPop=o.pop;
				clearTimeout(curShow);
				clearTimeout(tFadeOut);
				clearTimeout(tFadeIn);
				clearTimeout(tFadeWaiting);	
				if(sPop==null || sPop=="") {
					popLayer.innerHTML="";
					popLayer.style.filter="Alpha()";
					popLayer.filters.Alpha.opacity=0;	
				} else {
					if(o.dyclass!=null) popStyle=o.dyclass 
					else popStyle="cPopText";
					curShow=setTimeout("showIt()",tPopWait);
				}
			}
		}

		function showIt() {
			popLayer.className=popStyle;
			popLayer.innerHTML=\'<BR>&nbsp;&nbsp;\'+sPop+\'&nbsp;&nbsp;<BR><BR>\';
			popWidth=popLayer.clientWidth;
			popHeight=popLayer.clientHeight;
			if(MouseX+12+popWidth>document.body.clientWidth) popLeftAdjust=-popWidth-24
				else popLeftAdjust=0;
			if(MouseY+12+popHeight>document.body.clientHeight) popTopAdjust=-popHeight-24
				else popTopAdjust=0;
			popLayer.style.left=MouseX+12+document.body.scrollLeft+popLeftAdjust;
			popLayer.style.top=MouseY+12+document.body.scrollTop+popTopAdjust;
			popLayer.style.filter="Alpha(Opacity=0)";
			fadeOut();
		}

		function fadeOut(){
			if(popLayer.filters.Alpha.opacity<popOpacity) {
				popLayer.filters.Alpha.opacity+=showPopStep;
				tFadeOut=setTimeout("fadeOut()",1);
			}
		}

		document.onmouseover=showPopupText;
				</script>
		');
	}

	/*
	+----------------------------------+
	| Show css
	+----------------------------------+
	| C / M : 2003-12-28 / --
	+----------------------------------+
	*/

	function showCSS()
	{
		print("
		<style type='text/css'>
		a:link, a:visited, a:active { text-decoration: none; color: #000 }
		a:hover { color: orangered; text-decoration:none }
		BODY { scrollbar-face-color: #DEE3E7; scrollbar-highlight-color: #FFFFFF; scrollbar-shadow-color: #DEE3E7; scrollbar-3dlight-color: #D1D7DC; scrollbar-arrow-color:  #006699; scrollbar-track-color: #EFEFEF; scrollbar-darkshadow-color: #98AAB1; font: 12px Verdana; color:#333333; font-family: Tahoma,Verdana, Tahoma, Arial,Helvetica, sans-serif; font-size: 12px; color: #000; margin:0px 12px 0px 12px;background-color:#FFF }
		TD {font: 12px Verdana; color:#333333; font-family: Tahoma,Verdana, Tahoma, Arial,Helvetica, sans-serif; font-size: 12px; color: #000; };
		input, textarea {
		font-family: Verdana;
		font-size: 8pt;
		border: 1px solid #C0C0C0;
		color:#333333; background-color:#FFFFFF
		}
		</style>
		");
	}

	/*
	+----------------------------------+
	| Show title
	+----------------------------------+
	| C / M : 2003-12-28 / --
	+----------------------------------+
	*/

	function showTitle()
	{
		print("<meta HTTP-EQUIV=Content-Type content=\"text/html; charset=gb2312\">\n");
		print("<title>".$this->configTilte."</title>\n");
		print("<BODY>\n");
		print("<A NAME=\"TOP\">\n");
		print("<BR>\n");
		print("<center>\n");
		print($this->configTilte);
		print("</center>\n");
		print("<BR><BR>\n");
	}

	/*
	+----------------------------------+
	| Show state
	+----------------------------------+
	| C / M : 2003-12-28 / 2004-4-9
	+----------------------------------+
	*/

	function showState()
	{
		print("<center>\n");
		print("<table width=\"750\">\n");
		print("<tbody>\n");
		print("<tr>\n");
		print("<td bgcolor=\"F7F7F7\" height=\"30\" style=\"border: 1px solid #CCCCCC\">\n");
		print("<CENTER>当前目录 : <B><FONT COLOR=\"red\">".$this->nowDirName."</FONT></B>&nbsp;&nbsp;[ 子目录数 : <B><FONT COLOR=\"red\">". ($this->dirNum - 1) ."</FONT></B>&nbsp;&nbsp;图片数目 : <B><FONT COLOR=\"red\">".$this->picNum."</FONT></B>  每页显示 : <B><FONT COLOR=\"red\">".$this->configEachPageMax."</FONT></B> 个 ]&nbsp;&nbsp;查看模式: [ <A HREF=\"".$_SERVER['PHP_SELF']."?path=".rawurlencode($this->path)."&style=real&page=".$this->page."\"><FONT COLOR=\"blue\" title=\"按照真实比例查看图片\">真实</FONT></A> ]&nbsp;[ <A HREF=\"".$_SERVER['PHP_SELF']."?path=".rawurlencode($this->path)."&style=small&page=".$this->page."\"><FONT COLOR=\"blue\" title=\"以缩小比例查看图片\">缩略</FONT></A> ]&nbsp;&nbsp;</CENTER>");
		print("</td>\n");

		
		if ($this->showHomeUrl == true) {
			print("<td bgcolor=\"F7F7F7\" width=\"80\" style=\"border: 1px solid #CCCCCC\" align=\"center\">\n");
			print("[ <A HREF=\"" . $this->homeUrl . "\"><FONT COLOR=red>Home</FONT></A> ]");
			print("</td>\n");
		}

		print("</tr>\n");
		print("</tbody>\n");
		print("</table>\n");
		print("</center>\n");
	}

	/*
	+----------------------------------+
	| Make option direct list
	+----------------------------------+
	| Modify by Jacob
	+----------------------------------+
	| C / M : 2004-3-24 / 2004-4-20
	+----------------------------------+
	*/

/*
<table width="93%" border="0" cellpadding="0" cellspacing="1" bgcolor="#F7F7F7">
  <tr><td bgcolor=FFFFFF><A HREF=\"\"></A></td></tr>
</table>
*/

	//$_SERVER['PHP_SELF'] . "?rmPath=" . rawurlencode($this->dirArray[$i]) . "&thisPath=" . rawurlencode($_GET['path']) . "&action=rmdirquite

	function makeOptionList()
	{
		$session = & $_SESSION;
		if ($session['loginUser'] != null && $session['loginUser'] == $this->user)
		{
			$this->ShowJSm();
			for($i = 1; $i < $this->dirNum; $i++) {
				$this->dirOptionList .= "[ <A HREF=\"" . $_SERVER['PHP_SELF'] . "?rmPath=" . rawurlencode($this->dirArray[$i]) . "&thisPath=" . rawurlencode($_GET['path']) . "&action=rmdirquite\"><img src=\"img/del.png\" border=0 alt=\"删除[" . $this->dirNameArray[$i] . "]目录以及子文件？\"></A><A HREF=\"" . $_SERVER['PHP_SELF'] . "?path=" . rawurlencode($this->dirArray[$i]) . "\" alt=\"进入[" . $this->dirNameArray[$i] . "]目录\"><img src=\"img/bao.gif\" border=\"0\"> " . $this->dirNameArray[$i] . "</A> ] ";
			}
		} else {
			for($i = 1; $i < $this->dirNum; $i++) {
				$this->dirOptionList .= "[ </A><A HREF=\"" . $_SERVER['PHP_SELF'] . "?path=" . rawurlencode($this->dirArray[$i]) . "\" alt=\"进入[" . $this->dirNameArray[$i] . "]目录\"><img src=\"img/bao.gif\" border=\"0\"> " . $this->dirNameArray[$i] . "</A> ] ";
			}
		}

	}


	/*
	+----------------------------------+
	| Show direct list
	+----------------------------------+
	| C / M : 2003-12-28 / 2004-3-24
	+----------------------------------+
	*/

	function showDirList()
	{
		$session = & $_SESSION;
		$_width;
		$_str;
		if ($session['loginUser'] != null && $session['loginUser'] == $this->user)
		{
			$_width = 250;
			$_str = " <input type=button value=\"新建分类目录\" OnClick=\"self.location='" . $_SERVER['PHP_SELF'] . "?path=" . rawurlencode($this->path) . "&action=makedir'\" alt=\"新建子目录\">&nbsp;&nbsp;";
		}else {
			$_width= 140;
			$_str = "";
		}
		print("<center>\n");
		print("<table width=\"750\">\n");
		print("<tbody>\n");
		print("<tr>\n");
		print("<td bgcolor=\"F7F7F7\" height=\"30\" style=\"border: 1px solid #CCCCCC\" width=\"70\">\n");
		print("<CENTER>目录列表</CENTER>");
		print("</td>\n");

		print("<td bgcolor=\"F7F7F7\" align=\"center\" height=\"30\" width=\"" . $_width . "\" style=\"border: 1px solid #CCCCCC\">\n");
		print("<input type=button value=\" 返回上级目录 \" OnClick=\"self.location='" . $_SERVER['PHP_SELF'] . "?path=" . rawurlencode($this->dirArray[0]) . "'\" alt=\"返回 上级目录\"> " . $_str);
		print("<td bgcolor=\"F7F7F7\" height=\"30\" style=\"border: 1px solid #CCCCCC\">\n");
		print("&nbsp;&nbsp;" . $this->dirOptionList);
		print("</td>\n");
		print("</td>\n");
		print("</tr>\n");
		print("</tbody>\n");
		print("</table>\n");
		print("</center>\n");
	}


	/*
	+----------------------------------+
	| Cute the long file name
	+----------------------------------+
	| C / M : 2003-12-29 / --
	+----------------------------------+
	*/

	function sortName($filename)
	{
		$filename = substr($filename, 0, strrpos($filename, '.'));
		$strlen = strlen($filename);
		if ($strlen > $this->strLenMax) $filename = substr($filename, 0, ($this->strLenMax)) . chr(0) . "...";
		
		return $filename;
	}
	
	/*
	+----------------------------------+
	| Show picture list
	+----------------------------------+
	| C / M : 2003-12-28 / 2003-12-29
	+----------------------------------+
	*/

	function showPicList()
	{
		
		print("<FORM name=\"dfile\" action=\"". $_SERVER['PHP_SELF'] ."?action=del&style=" . $_GET['style'] . "&page=" . $_GET['page'] . "\" METHOD=\"POST\">\n");
		print("<INPUT TYPE=hidden NAME=\"path\" VALUE=\"" . rawurlencode($this->path) . "\">");

		/*
		+----------------------------------+
		| Real size style
		+----------------------------------+
		*/
		
		$session = & $_SESSION;
		
		if ($this->style == "real")
		{		
			
			print("<center>\n");

			for($i = $this->start; $i < $this->offSet; $i++)
			{
				$this->setPicID($i);
				$this->getPicDim();

				/*
				+----------------------------------+
				| Read and format this picture's size
				+----------------------------------+
				*/

				$this->configShowPicSize == true ? $picFileSize = sprintf("%0.2f", filesize($this->picArray[$i]) / 1024) : $picFileSize = " -- ";

				if ($session['loginUser'] != null && $session['loginUser'] == $this->user)
					print("<BR><INPUT TYPE=\"checkbox\" NAME=\"delfile[]\" VALUE=\"" . $this->picFileArray[$i] . "\" title=\"删除图片 <FONT COLOR=blue>" . $this->picFileArray[$i] . "</FONT>\">&nbsp;&nbsp;");

				printf("<A href=\"#TOP\">返回顶部</A>&nbsp;&nbsp;#%s&nbsp;&nbsp;%s&nbsp;&nbsp;%s × %s&nbsp;&nbsp;%s KB<BR><BR>\n",($i + 1), $this->picFileArray[$i], $this->picRealSizeWidth, $this->picRealSizeHeight, $picFileSize);
				printf("<A href=\"%s\" target=\"_blank\"><IMG SRC=\"%s\" BORDER=\"0\"></A><BR><BR>\n", $this->picEncodeArray[$i], $this->picEncodeArray[$i]);
			}

			print("</center>\n");
			
		}
		/*
		+----------------------------------+
		| Small size style
		+----------------------------------+
		*/
		else
		{
			print("<center>\n");
			printf("<TABLE border=0><TBODY><TR>\n");
			for($i = $this->start; $i < $this->offSet; $i++)
			{
				$I++;

				$this->setPicID($i);
				$this->getPicDim();

				/*
				+----------------------------------+
				| Read and format this picture's size
				+----------------------------------+
				*/

				$this->configShowPicSize == false ? $picFileSize = " -- " : $picFileSize = sprintf("%0.2f", filesize($this->picArray[$i]) / 1024);

				print("<TD style=\"border: 1px solid #CCCCCC\">\n");
				print("<TABLE BORDER=\"0\" CELLPADDING=\"0\" CELLSPACING=\"0\" STYLE=\"BORDER-COLLAPSE: COLLAPSE\">\n");
				print("<TBODY>\n");
				print("<TR>\n");
				print("<TD bgcolor=\"#F7F7F7\" height=\"20\" colspan=\"3\"><CENTER>" . $this->sortName($this->picFileArray[$i]) . "</CENTER></TD>\n");
				print("</TR>\n");
				print("<TR>\n");
				print("<TD width=\"" . $this->configTDWidth . "\" height=\"" . $this->configTDHeight . "\" style=\"border: 0px solid #CCCCCC\" colspan=\"3\"><CENTER><A href=\"" . $this->picEncodeArray[$i] . "\" target=\"_blank\"><IMG SRC=\"" . $this->picEncodeArray[$i] . "\" BORDER=\"0\" width=\"" . $this->temp['Width'] . "\" height=\"" . $this->temp['Height'] . "\" ALT=\"文件 : <FONT COLOR='red'>" . $this->picFileArray[$i] . "</FONT>&nbsp;&nbsp;<BR>&nbsp;&nbsp;尺寸 : <FONT COLOR='blue'>" . $this->picRealSizeWidth . " × " . $this->picRealSizeHeight . "</FONT> 像素&nbsp;&nbsp;<BR>&nbsp;&nbsp;格式 : <FONT COLOR='green'>" . $this->getFileExt($this->picFileArray[$i]) . "</FONT>&nbsp;&nbsp;<BR>&nbsp;&nbsp;大小 : <FONT COLOR='green'>" . $picFileSize . "</FONT> KB&nbsp;&nbsp;\"></A></CENTER></TD>\n");
				print("<TR>\n");
				print("<TD bgcolor=\"#F7F7F7\" width=30><CENTER>");

				if ($session['loginUser'] != null && $session['loginUser'] == $this->user)
					print("<INPUT TYPE=\"checkbox\" NAME=\"delfile[]\" VALUE=\"" . $this->picFileArray[$i] . "\" title=\"删除图片 <FONT COLOR=blue>" . $this->picFileArray[$i] . "</FONT>\">");

				print("</CENTER></TD><TD bgcolor=\"#F7F7F7\" height=\"30\"><CENTER> " . $this->picRealSizeWidth . " × " . $this->picRealSizeHeight . " </CENTER></TD><TD bgcolor=\"#F7F7F7\" height=\"20\"><CENTER>" . $picFileSize . " KB</CENTER></TD></TR></TBODY></TABLE></TD>\n");
				
				if ($this->configEachLineMax == $I)
				{
					$I = 0;
					print("</TR><TR>\n");
				}
			}
			print("</TR>\n</TBODY></TABLE>\n");
			print("<BR><A href=\"#TOP\">返回顶部</A><BR>\n");
			print("</center>\n");
		}

		print("</FORM>\n");
	}

	/*
	+----------------------------------+
	| Show config state
	+----------------------------------+
	| C / M : 2003-12-29 / --
	+----------------------------------+
	*/

	function showConfigState()
	{
		$this->configOpenGzip == true ? $openGzip = "开启" : $openGzip = "关闭";
		$this->configShowPicSize == true ? $showPicSize = "开启" : $showPicSize = "关闭";
		$this->configWantedPass == true ? $showWantedPass = "开启" : $showWantedPass = "关闭";

		print("<center>\n");
		print("<table width=\"750\">\n");
		print("<tbody>\n");
		print("<tr>\n");
		print("<td bgcolor=\"F7F7F7\" height=\"30\" style=\"border: 1px solid #CCCCCC\">\n");

		/*
		printf("<CENTER>当前设置:&nbsp;&nbsp;压缩页面 : <FONT COLOR=\"red\"><B>%s</B></FONT>&nbsp;&nbsp;显示图片大小 : <FONT COLOR=\"red\"><B>%s</B></FONT>&nbsp;&nbsp;登录认证 : <FONT COLOR=\"red\"><B>%s</B></FONT>&nbsp;&nbsp;&nbsp;&nbsp;[ <FONT COLOR=\"blue\">帮助手册</FONT> ]\n", $openGzip, $showPicSize, $showWantedPass);
		*/
		
		printf("<CENTER>当前设置:&nbsp;&nbsp;压缩页面 : <FONT COLOR=\"red\"><B>%s</B></FONT>&nbsp;&nbsp;显示图片大小 : <FONT COLOR=\"red\"><B>%s</B></FONT>&nbsp;&nbsp;登录认证 : <FONT COLOR=\"red\"><B>%s</B></FONT>&nbsp;&nbsp;&nbsp;&nbsp;[ <A HREF=\"".$_SERVER['PHP_SELF']."?action=showhelp\" ><FONT COLOR=\"blue\" >NEATPIC 帮助</FONT></A> ]\n", $openGzip, $showPicSize, $showWantedPass);

		print("</td>\n");
		print("<td bgcolor=\"F7F7F7\" height=\"30\" style=\"border: 1px solid #CCCCCC\">");
		printf("<CENTER><A HREF=\"%s?action=login&path=%s\"><FONT COLOR=\"red\">管理员登录</FONT></A></CENTER>", $_SERVER['PHP_SELF'], rawurlencode($this->path));
		print("</td>\n");
		print("</tr>\n");
		print("</tbody>\n");
		print("</table>\n");
		print("<BR>\n");
		printf($this->decode("QXV0aG9yc2hpcDogPEEgSFJFRj0iaHR0cDovL3d3dy5uZWF0c3R1ZGlvLmNvbSI+TkVBVFBJQyAoUEhQIMS/wrzWsbbBsOYpPC9BPiAgQXNzaXN0YW50OiA8QSBIUkVGPSJodHRwOi8vZ2FyaGVlLmNvbS9iYnMiPkphY29iPC9BPiA8YnI+IFZlcnNpb24mbmJzcDs6Jm5ic3A7JXMgJm5ic3A7UHJvY2Vzc2VkIGluICVzIHNlYzxCUj5Db3B5cmlnaHQgTmVhdFN0dWRpbyAyMDAyLTIwMDQgPEJSPg=="), $this->configVer, $this->usedTime);
		print("<BR><BR>\n");
		print("</center>\n");
		
	}

	/*
	+----------------------------------+
	| Show login window
	+----------------------------------+
	| C / M : 2003-12-29 / 2004-3-26
	+----------------------------------+
	*/

	function showLogin()
	{
		print("<center>\n");

		print("<table width=\"750\">\n");
		print("<tbody>\n");
		print("<tr>\n");
		print("<td bgcolor=\"F7F7F7\" height=\"30\" style=\"border: 1px solid #CCCCCC\">\n");
		print("<CENTER>登陆验证</CENTER>");
		print("</td>\n");
		print("</tr>\n");
		print("</tbody>\n");
		print("</table>\n");

		print("<table width=\"750\">\n");
		print("<tbody>\n");
		print("<tr>\n");
		print("<td bgcolor=\"F7F7F7\" height=\"30\" style=\"border: 1px solid #CCCCCC\">\n");
		print("
		<CENTER><FORM METHOD=POST ACTION=\"".$_SERVER['PHP_SELF']."?action=loginout\"><BR>\n
		登录密码 : <INPUT TYPE=\"password\" NAME=\"password\"> <INPUT TYPE=\"submit\" VALUE=\"登录\">\n
		<INPUT TYPE=\"hidden\" NAME=\"login\" VALUE=\"" . $_GET['action'] . "\">
		<INPUT TYPE=\"hidden\" NAME=\"path\" VALUE=\"" . $_GET['path'] . "\">
		</FORM></CENTER>\n
		");		
		print("</td>\n");
		print("</tr>\n");
		print("</tbody>\n");
		print("</table>\n");
		print("</center>\n");
	}


	/*
	+----------------------------------+
	| Show Admincp
	+----------------------------------+
	| C / M : 2003-12-29 / 2004-4-17
	+----------------------------------+
	*/

	function showAdmincp()
	{
		$session = & $_SESSION;
		if ($session['loginUser'] != null && $session['loginUser'] == $this->user)
		{
			$allFileSize = $this->getDirSize(".");
			$used_per = $allFileSize/ ($this->configAllowSizes * M1);

			print("<center>\n");
			print("<table width=\"750\">\n");
			print("<tbody>\n");
			print("<tr>\n");
			print("<td bgcolor=\"F7F7F7\" height=\"30\" style=\"border: 1px solid #CCCCCC\" width=\"70\">\n");
			print("<CENTER>管理选项</CENTER>");
			print("</td>\n");
			print("<FORM action=\"" . $_SERVER['PHP_SELF'] . "?action=upload\" method=\"POST\" enctype=\"multipart/form-data\">\n");
			print("<td bgcolor=\"F7F7F7\" height=\"30\" style=\"border: 1px solid #CCCCCC\" width=\"380\">&nbsp;&nbsp;\n");
			if (is_writeable($this->path) && $allFileSize < ($this->configAllowSizes * M1) && $this->path != ".")
				print("<INPUT TYPE=hidden NAME=\"path\" VALUE=\"" . rawurlencode($this->path) . "\"><INPUT style=\"height:20\" TYPE=FILE NAME=\"image\" title=\"上传文件到 <font color=blue>" . $this->nowDirName . "</font> 目录\"> <INPUT TYPE=submit VALUE=\"上传图片\"> <input type=button value=\"批量上传\" OnClick=\"self.location='" . $_SERVER['PHP_SELF'] . "?path=" . rawurlencode($this->path) . "&action=uploadmore'\" alt=\"批量上传图片\">");
			else {
				printf("<FONT COLOR=\"red\"><B>无法上传图片 </B></FONT><FONT COLOR=\"red\">原因: [ ");
				if ( $this->path == "./img" ) {
					printf("img为系统目录，不允许上传");
				} else if (!is_writeable($this->path) ) {
					printf("目录 <FONT COLOR=\"blue\">%s</FONT> 不可写", $this->nowDirName);
				} else if ( $allFileSize > ($this->configAllowSizes * M1) ) {
					printf("文件总大小超出标准");
				} else if ( $this->path == "." ) {
					printf("<FONT COLOR=\"blue\">根目录不允许上传</FONT>，请进入各分类再上传");
				}
				printf(" ]</FONT>");
				
			}
			print("</td>\n");
			print("</FORM>\n");

			$alt = "占用百分比，最大" . $this->configAllowSizes . "M，已使用" . sprintf("%01.2f",$used_per*100) ."%";
			print("<td bgcolor=\"F7F7F7\" height=\"30\" style=\"border: 1px solid #CCCCCC\" align=\"center\" alt=\"" . $alt ."\">\n");
			print("<table width=\"60\" height=\"10\" border=0 cellpadding=0 cellspacing=1 bgcolor=\"red\">\n");
			print("  <tr>\n");
			print("    <td bgcolor=\"red\" width=\"" . $used_per*60 . "\" alt=\"" . $alt ."\"></td>\n");
			print("    <td bgcolor=\"white\" width=\"" . (60-$used_per*60) . "\" alt=\"" . $alt ."\"></td>\n");
			print("  </tr>\n");
			print("</table>\n");
			print("</td>\n");
			print("<td bgcolor=\"F7F7F7\" height=\"30\" style=\"border: 1px solid #CCCCCC\">\n");
			print("<CENTER>\n");
			print("<A HREF=\"javascript:document.dfile.submit()\"><FONT COLOR=\"blue\" title=\"删除已经选定了的图片\">删除图片</FONT></A> | <A HREF=\"".$_SERVER['PHP_SELF']."?action=cfgdirpass&path=" . rawurlencode($this->path) . "\"><FONT COLOR=\"blue\" title=\"添加/编辑 当前目录的访问密码\">设置目录密码</FONT></A> | <A HREF=\"".$_SERVER['PHP_SELF']."?action=loginout&path=" . rawurlencode($this->path) . "\"><B><FONT COLOR=\"red\" title=\"退出管理员登录\">退出</FONT></B></A>\n");
			print("</CENTER>");
			print("</td>\n");
			print("</tr>\n");
			print("</tbody>\n");
			print("</table>\n");
			print("</center>\n");
		}
	}


	/*
	+----------------------------------+
	| del selected file
	+----------------------------------+
	| C / M : 2004-4-2 / --
	+----------------------------------+
	*/

	function delFile()
	{
		if ($_GET['action'] == 'del')
		{
			$session = & $_SESSION;

			if ($session['loginUser'] != null && $session['loginUser'] == $this->user)
			{
				$path = rawurldecode($_POST['path']);
				$delFile = & $_POST['delfile'];

				foreach($delFile as $file)
				{
					unlink($path . "/" . $file);
				}

				header("location:" . $_SERVER['PHP_SELF'] . "?path=" . $_POST['path'] . "&style=" . $_GET['style'] . "&page=" . $_GET['page']);
			}
		}
	}

	
	/*
	+----------------------------------+
	| del dir
	+----------------------------------+
	| create by Jacob
	+----------------------------------+
	| C / M : 2004-4-20 / --
	+----------------------------------+
	*/

	function rmDirRf($path)
	{
		if (file_exists($path))
		{
			$dir = dir($path); 
			while($file = $dir->read())
			{

				$fileName = $file;  
				$file = $path."/".$file;  

				if ($fileName <> ".." && $fileName <> ".")
				{
					if (is_dir($file) ) {
						$this->rmDirRf($file);
					}else {
						unlink($file); 
					}
				}

			}
			$dir->close();
			rmdir($path);
		}
	}


	/*
	+----------------------------------+
	| accept del dir request
	+----------------------------------+
	| create by Jacob
	+----------------------------------+
	| C / M : 2004-4-20 / --
	+----------------------------------+
	*/

	function rmDirQuite() 
	{
		if ($_GET['action'] == 'rmdirquite') {
		
			$session = & $_SESSION;
			if ($session['loginUser'] != null && $session['loginUser'] == $this->user) 
			{
				if ($_GET['do'])
				{
					$dirPath = rawurldecode($_GET['rmPath']);

					// 防止越权和删除img文件夹
					if (substr($dirPath,0,2) == ".." || substr($dirPath,0,1) == "/" 
						|| $dirPath == "." || $dirPath == "./img") 
					{
						$this->error("你没有此操作权限");
					} 
					else 
					{
						$this->rmDirRf($dirPath);
					}
					
					header("location:".$_SERVER['PHP_SELF']."?path=" . rawurlencode($_GET['thisPath']));
				}else {

					$this->timer();
					$this->showCSS();
					$this->showTitle();
					$this->ShowJS();

					print("<table width=\"750\" align=center>\n");
					print("<tbody>\n");
					print("<tr>\n");
					print("<td bgcolor=\"#F7F7F7\" height=\"30\" style=\"border: 1px solid #CCCCCC\" align=center>\n");
					print("确定删除此文件夹？");
					print("<INPUT TYPE=\"button\" value=\"  确 定  \"  onClick=\"self.location='".$_SERVER['PHP_SELF']."?action=rmdirquite&do=yes&thisPath=".rawurlencode($_GET['thisPath'])."&rmPath=".rawurlencode($_GET['rmPath'])."'\">\n");
					print("<INPUT TYPE=\"button\" value=\"  取 消  \"  onclick=\"javascript:history.go(-1)\">\n");
					print("</td>\n");
					print("</tr>\n");
					print("</tbody>\n");
					print("</table>\n");
					print("<br><br>");

					$this->usedTime();
					$this->showConfigState();

					exit;
				}
			}
		}
	}

	/*
	+----------------------------------+
	| show upload
	+----------------------------------+
	| C / M : 2004-3-26 / --
	+----------------------------------+
	*/

	function showUpload()
	{
		if ($_GET['action'] == 'upload')
		{
			$this->timer();
			$this->showCSS();
			$this->showTitle();
			$this->upload();
			$this->usedTime();
			$this->showConfigState();

			exit;
		}
	}

	
	/*
	+----------------------------------+
	| make dir
	+----------------------------------+
	| create by Jacob
	+----------------------------------+
	| C / M : 2004-4-19 / --
	+----------------------------------+
	*/

	function makeDir() {


		if ($_GET['action'] == 'makedir') {

			$session = & $_SESSION;

			if ($_GET['do'] AND $session['loginUser'] != null && $session['loginUser'] == $this->user)
			{

				$path = rawurldecode($_POST['path']);
				$dir = rawurldecode($_POST['dirName']);
				if (file_exists($path . "/" . $dir)) $this->error("文件夹已存在");
				else {
					mkdir($path . "/" . $dir);
				}
				
				header("location:".$_SERVER['PHP_SELF']."?path=" . $_POST['path']);
			}else {

				$this->timer();
				$this->showCSS();
				$this->showTitle();
				$this->ShowJS();

				print("<table width=\"750\" align=center>\n");
				print("<tbody>\n");
				print("<tr>\n");
				print("<td bgcolor=\"#F7F7F7\" height=\"30\" style=\"border: 1px solid #CCCCCC\" align=center>\n");
				print("添加子目录");
				print("</td>\n");
				print("</tr>\n");
				print("<tr>\n");
				print("<td bgcolor=\"F7F7F7\" height=\"30\" style=\"border: 1px solid #CCCCCC\" align=center>\n");
				print("	<FORM METHOD=POST ACTION=\"".$_SERVER['PHP_SELF']."?action=makedir&do=yes\"><BR>\n	目录名称 : <INPUT TYPE=\"text\" NAME=\"dirName\" title=\" 请输入名称 \"><BR><BR>		<INPUT TYPE=\"submit\" VALUE=\"      确     定       \">\n	<INPUT TYPE=\"hidden\" NAME=\"path\" VALUE=\"" . $_GET['path'] . "\">	</FORM>\n	");		
				print("</td>\n");
				print("</tr>\n");
				print("</tbody>\n");
				print("</table>\n");

				$this->usedTime();
				$this->showConfigState();

				exit;
			}
		}
	}


	
	/*
	+----------------------------------+
	| upload image
	+----------------------------------+
	| C / M : 2004-3-26 / --
	+----------------------------------+
	*/

	function upload()
	{		
			
		$session = & $_SESSION;

		if ($session['loginUser'] != null && $session['loginUser'] == $this->user)
		{
			$path = rawurldecode($_POST['path']);
			$tmpPath = explode('/', $path);
			$tmpPathLevel = count($tmpPath);
			
			for ($i = 1; $i < $tmpPathLevel; $i++)
				$decodePath .= rawurlencode($tmpPath[$i]) . "/";

			$uploadFile = $_FILES['image']['name'];

			if (file_exists($path . "/" . $uploadFile))
				$uploadFile = date('is') . $_FILES['image']['name'];

			$imgType = $this->getFileExt($_FILES['image']['name']);

			if (!in_array($imgType, $this->configExt)) $this->error('文件类型非法!');

			if (!copy($_FILES['image']['tmp_name'], $path . "/" . $uploadFile)) $this->error('文件上传发生错误!');

			print("<center>\n");
			print("<table width=\"750\">\n");
			print("<tbody>\n");
			print("<tr>\n");
			print("<td bgcolor=\"#F7F7F7\" height=\"50\" style=\"border: 1px solid #CCCCCC\">\n");
			print("<CENTER><FONT COLOR=\"red\"><B>文件上传成功</B></FONT></CENTER>");
			print("</td>\n");
			print("</tr>\n");
			print("<tr>\n");
			print("<td bgcolor=\"#FFFFFF\" height=\"50\" style=\"border: 1px solid #CCCCCC\">\n");
			printf("<CENTER><BR><FONT COLOR=\"blue\">文件名</FONT> ： <FONT COLOR=\"green\">%s</FONT>&nbsp;&nbsp;<FONT COLOR=\"blue\">文件大小</FONT> ： <FONT COLOR=\"green\">%s KB</FONT>&nbsp;&nbsp;<FONT COLOR=\"blue\">文件类型</FONT> ： <FONT COLOR=\"green\">%s</FONT><BR><BR><IMG SRC=\"%s%s\" border=1><BR><BR></CENTER>", $uploadFile, sprintf("%0.2f", $_FILES['image']['size'] / 1024), $imgType, $decodePath, rawurlencode($uploadFile));
			print("</td>\n");
			print("</tr>\n");
			print("<tr>\n");
			print("<td bgcolor=\"#F7F7F7\" height=\"50\" style=\"border: 1px solid #CCCCCC\">\n");
			printf("<CENTER>[ <A HREF=\"%s%s\" target=\"_blank\">查看上传图片</A> | <A HREF=\"%s?path=%s\">返回当前目录</A> ]</CENTER>", $decodePath, rawurlencode($uploadFile), $_SERVER['PHP_SELF'], $_POST['path']);
			print("</td>\n");
			print("</tr>\n");
			print("</tbody>\n");
			print("</table>\n");
			print("</center>\n");
		}
	}

	/*
	+----------------------------------+
	| upload more image
	+----------------------------------+
	| C / M : 2004-4-5 / --
	+----------------------------------+
	*/

	function uploadMore()
	{
		$session = & $_SESSION;

		if ($session['loginUser'] != null && $session['loginUser'] == $this->user)
		{
		
			if ($_GET['action'] == 'uploadmore')
			{
				$this->timer();
				$this->showCSS();
				$this->showTitle();
				$this->ShowJS();
				
				if($_GET['do'] == 'yes')
				{
					set_time_limit(0);

					$path = rawurldecode($_GET['path']);
					$tmpPath = explode('/', $path);
					$tmpPathLevel = count($tmpPath);
					
					for ($i = 1; $i < $tmpPathLevel; $i++)
						$decodePath .= rawurlencode($tmpPath[$i]) . "/";

					$picNum = count($_FILES['images']['tmp_name']);

					for($i = 0; $i < $picNum; $i++)
					{							
						if($_FILES['images']['tmp_name'][$i])
						{
							$uploadFile = $_FILES['images']['name'][$i];
							if (file_exists($path . "/" . $uploadFile))
								$uploadFile = date('is') . $_FILES['images']['name'][$i];

							$imgType = $this->getFileExt($_FILES['images']['name'][$i]);

							if (!in_array($imgType, $this->configExt)) $this->error("文件类型非法! 图片编号：[" . ($i + 1) . "]");

							if (!copy($_FILES['images']['tmp_name'][$i], $path . "/" . $uploadFile)) $this->error("文件上传发生错误! 图片编号：[" . ($i + 1) . "]");

							$uploadFileArray[]	= $uploadFile;
							$imgTypeArray[]		= $imgType;
							$imgSizeArray[]		= sprintf("%0.2f", $_FILES['images']['size'][$i] / 1024);

						}
					}
					print("<center>\n");
					print("<table width=\"750\">\n");
					print("<tbody>\n");
					print("<tr>\n");
					print("<td bgcolor=\"#F7F7F7\" height=\"50\" style=\"border: 1px solid #CCCCCC\">\n");
					print("<CENTER><FONT COLOR=\"red\"><B>文件批量上传成功</B></FONT></CENTER>");
					print("</td>\n");
					print("</tr>\n");

					for($i = 0; $i < count($uploadFileArray); $i++)
					{
						print("<tr>\n");
						print("<td bgcolor=\"#FFFFFF\" height=\"50\" style=\"border: 1px solid #CCCCCC\">\n");
						printf("<CENTER><BR><FONT COLOR=\"blue\">#" . ($i + 1) . " 文件名</FONT> ： <FONT COLOR=\"green\">%s</FONT>&nbsp;&nbsp;<FONT COLOR=\"blue\">文件大小</FONT> ： <FONT COLOR=\"green\">%s KB</FONT>&nbsp;&nbsp;<FONT COLOR=\"blue\">文件类型</FONT> ： <FONT COLOR=\"green\">%s</FONT><BR><BR><IMG SRC=\"%s%s\" border=1><BR><BR></CENTER>", $uploadFileArray[$i], $imgSizeArray[$i], $imgTypeArray[$i], $decodePath, rawurlencode($uploadFileArray[$i]));
						print("</td>\n");
						print("</tr>\n");
						print("<tr>\n");
						print("<td bgcolor=\"#F7F7F7\" height=\"50\" style=\"border: 1px solid #CCCCCC\">\n");
						printf("<CENTER>[ <A HREF=\"%s%s\" target=\"_blank\">查看上传图片</A> | <A HREF=\"%s?path=%s\">返回当前目录</A> ]</CENTER>", $decodePath, rawurlencode($uploadFileArray[$i]), $_SERVER['PHP_SELF'], rawurlencode($_GET['path']));
						print("</td>\n");
						print("</tr>\n");
					}

					print("</tbody>\n");
					print("</table>\n");
					print("</center>\n");
				}
				else
				{
					($_POST['uploadnum']) ? $num = & $_POST['uploadnum'] : $num = 5;
					
					print("<center>\n");
					print("<table width=\"750\">\n");
					print("<tbody>\n");
					print("<tr>\n");
					print("<td bgcolor=\"#F7F7F7\" height=\"30\" style=\"border: 1px solid #CCCCCC\">\n");
					print("<CENTER><FONT COLOR=\"red\">批量上传图片</FONT></CENTER>");
					print("</td>\n");
					print("</tr>\n");
					print("<tr>\n");
					print("<FORM action=\"" . $_SERVER['PHP_SELF'] . "?path=" . rawurlencode($_GET['path']). "&action=uploadmore&do=yes\" METHOD=\"POST\" enctype=\"multipart/form-data\">\n");
					print("<td bgcolor=\"#FFFFFF\" height=\"50\" style=\"border: 1px solid #CCCCCC\" align=center><BR>\n");
					
					for ($i = 1; $i <= $num; $i++)
						print("#" . $i . " <INPUT TYPE=\"file\" NAME=\"images[]\" SIZE=\"40\"><BR>\n");

					print("<BR></td>\n");
					print("</tr>\n");
					print("<tr>\n");
					print("<td bgcolor=\"#F7F7F7\" height=\"30\" style=\"border: 1px solid #CCCCCC\">\n");
					print("<CENTER><INPUT TYPE=\"submit\" VALUE=\"上传图片\">&nbsp;&nbsp;&nbsp;&nbsp;<INPUT TYPE=\"button\" onclick=\"javascript:history.go(-1)\" VALUE=\"返回上页\"></CENTER>");
					print("</td>\n");
					print("</FORM>\n");
					print("</tr>\n");
					print("<tr>\n");
					print("<FORM action=\"" . $_SERVER['PHP_SELF'] . "?path=" . rawurlencode($_GET['path']). "&action=uploadmore\" METHOD=\"POST\">\n");
					print("<td bgcolor=\"#FFFFFF\" height=\"50\" style=\"border: 1px solid #CCCCCC\" align=center>\n");
					print("重新设定要批量上传的图片数量：&nbsp;&nbsp;我要一次性上传 <INPUT TYPE=\"text\" NAME=\"uploadnum\" size=\"3\"> 张图片&nbsp;&nbsp;<INPUT TYPE=\"submit\" VALUE=\"  设置  \">\n");
					print("</td>\n");
					print("</FORM>\n");
					print("</tr>\n");
					print("</tbody>\n");
					print("</table>\n");
					print("</center>\n");
				}

				$this->usedTime();
				$this->showConfigState();

				exit;
			}
		}
	}

	/*
	+----------------------------------+
	| Decode
	+----------------------------------+
	| C / M : 2003-12-30 / --
	+----------------------------------+
	*/

	function decode($str)
	{
		$str = rawurldecode($str);
		$str = base64_decode($str);

		$this->c = true;

		return $str;
	}

	function c()
	{
		if(!$this->c)
			header($this->decode("bG9jYXRpb246aHR0cDovL3d3dy5uZWF0c3R1ZGlvLmNvbQ%3D%3D"));
	}

	/*
	+----------------------------------+
	| Show if config wanted password
	+----------------------------------+
	| C / M : 2003-12-29 / --
	+----------------------------------+
	*/

	function showWantPass()
	{
		if ($this->configWantedPass == true OR $_GET['action'] == 'login' OR $_GET['action'] == 'loginout' OR $_POST['login'] == 'login')
		{	
			$session = & $_SESSION;

			if ($_GET['action'] == 'loginout')
			{
				if (!( $session['loginUser'] != null && $session['loginUser'] == $this->user) )
				{	
					if ($_POST['password'] == $this->configAdminPass AND $this->configAdminPass != "neatpic") $session['loginUser'] = $this->user;
				}
				else
				{
					session_destroy();
				}

				
				($_POST['path']) ? $path = $_POST['path'] : $path = $_GET['path'];
				header("location:".$_SERVER['PHP_SELF']."?path=" . rawurlencode($path));
				exit;
			}

			if (!($session['loginUser'] != null && $session['loginUser'] == $this->user))
			{				
				
				$this->timer();
				$this->showCSS();
				$this->showTitle();
				$this->showLogin();
				$this->usedTime();
				$this->showConfigState();

				exit;
			}
		}
	}

	/*
	+----------------------------------+
	| config dir password
	+----------------------------------+
	| C / M : 2004-3-27 / -- --
	+----------------------------------+
	*/

	function configDirPass()
	{
		if ($_GET['action'] == 'cfgdirpass')
		{
			$session = & $_SESSION;
			
			if ($_GET['do'] AND $session['loginUser'] != null && $session['loginUser'] == $this->user)
			{
				if (file_exists(rawurldecode($_POST['path']) . "/" . $this->configDirPasswordFile))
				{
					$password = file(rawurldecode($_POST['path']) . "/" . $this->configDirPasswordFile);
					list(, $password) = explode('|', chop($password[0]));
					
					if (md5($_POST['oldpassword']) != $password)
						$this->error("旧密码不匹配");
				}

				if ($_POST['newpassword'] != $_POST['checkpassword'])
					$this->error("两次密码输入不匹配");

				if (!$_POST['newpassword'])
					unlink(rawurldecode($_POST['path']) . "/" . $this->configDirPasswordFile);
				else
				{
					if (!is_writeable(rawurldecode($_POST['path']) . "/"))
						$this->error("要设置访问的目录不可写!请先设置其属性为777.");

					$fp = fopen(rawurldecode($_POST['path']) . "/" . $this->configDirPasswordFile, "w+");
					fwrite($fp, "<?php die()?>|" . md5($_POST['newpassword']));
					fclose($fp);
				}

				header("location:".$_SERVER['PHP_SELF']."?path=" . $_POST['path']);
			}
			else
			{
				$this->timer();
				$this->showCSS();
				$this->showTitle();
				$this->ShowJS();
				
				print("<center>\n");
				print("<table width=\"750\">\n");
				print("<tbody>\n");
				print("<tr>\n");
				print("<td bgcolor=\"#F7F7F7\" height=\"30\" style=\"border: 1px solid #CCCCCC\">\n");
				print("<CENTER>目录访问密码设置</CENTER>");
				print("</td>\n");
				print("</tr>\n");
				print("<tr>\n");
				print("<td bgcolor=\"F7F7F7\" height=\"30\" style=\"border: 1px solid #CCCCCC\">\n");
				print("
				<CENTER><FORM METHOD=POST ACTION=\"".$_SERVER['PHP_SELF']."?action=cfgdirpass&do=yes\"><BR>\n
				旧的密码 : <INPUT TYPE=\"password\" NAME=\"oldpassword\" title=\" 如果目录原来有密码,请输入旧的密码 \"><BR><BR>
				新的密码 : <INPUT TYPE=\"password\" NAME=\"newpassword\" title=\" 输入新的目录密码 \"><BR><BR>
				确认密码 : <INPUT TYPE=\"password\" NAME=\"checkpassword\" title=\" 确认新的目录密码 \"><BR><BR>
				<INPUT TYPE=\"submit\" VALUE=\"    添加/更新 密码    \">\n
				<INPUT TYPE=\"hidden\" NAME=\"path\" VALUE=\"" . $_GET['path'] . "\">
				</FORM></CENTER>\n
				");		
				print("</td>\n");
				print("</tr>\n");
				print("</tbody>\n");
				print("</table>\n");
				print("</center>\n");

				$this->usedTime();
				$this->showConfigState();

				exit;
			}
		}
	}

	/*
	+----------------------------------+
	| Dir password checking
	+----------------------------------+
	| C / M : 2004-3-27 / -- --
	+----------------------------------+
	*/

	function checkingDirPass()
	{
		if ($_GET['action'] == 'checkdirpass')
		{
			$session = & $_SESSION;

			$password = file(rawurldecode($_POST['path']) . "/" . $this->configDirPasswordFile);
			list(, $password) = explode('|', chop($password[0]));

			if ($password == md5($_POST['password']))
				$session[$_POST['path']] = md5($password);

			header("location:".$_SERVER['PHP_SELF']."?path=" . $_POST['path']);

		}
	}

	/*
	+----------------------------------+
	| Check dir password
	+----------------------------------+
	| C / M : 2004-3-27 / -- --
	+----------------------------------+
	*/

	function checkDirPass()
	{
		$this->checkingDirPass();
		
		$session = & $_SESSION;
		
		if (file_exists($this->path . "/" . $this->configDirPasswordFile))
		{
			if (!$session[rawurlencode($this->path)] AND !($session['loginUser'] != null && $session['loginUser'] == $this->user))
				$this->showDirPassLogin();
		}
	}

	/*
	+----------------------------------+
	| Show dir Pass login window
	+----------------------------------+
	| C / M : 2004-3-27 / -- --
	+----------------------------------+
	*/

	function showDirPassLogin()
	{
		$this->timer();
		$this->showCSS();
		$this->showTitle();
		$this->ShowJS();
		
		print("<center>\n");
		print("<table width=\"750\">\n");
		print("<tbody>\n");
		print("<tr>\n");
		print("<td bgcolor=\"#F7F7F7\" height=\"50\" style=\"border: 1px solid #CCCCCC\">\n");
		print("<CENTER>该目录设置了密码,请输入相应的访问密码</CENTER>");
		print("</td>\n");
		print("</tr>\n");
		print("<tr>\n");
		print("<td bgcolor=\"F7F7F7\" height=\"30\" style=\"border: 1px solid #CCCCCC\">\n");
		print("
		<CENTER><FORM METHOD=POST ACTION=\"".$_SERVER['PHP_SELF']."?action=checkdirpass\"><BR>\n
		访问密码 : <INPUT TYPE=\"password\" NAME=\"password\"> <INPUT TYPE=\"submit\" VALUE=\"提交\">\n
		<INPUT TYPE=\"hidden\" NAME=\"path\" VALUE=\"" . rawurlencode($this->path) . "\">
		</FORM></CENTER>\n
		");		
		print("</td>\n");
		print("</tr>\n");
		print("</tbody>\n");
		print("</table>\n");
		print("</center>\n");

		$this->usedTime();
		$this->showConfigState();

		exit;
	}

	/*
	+----------------------------------+
	| Show error
	+----------------------------------+
	| C / M : 2004-3-27 / -- --
	+----------------------------------+
	*/
	function error($msg)
	{
		echo "<script language=javascript>";
		echo "window.alert('$msg');";
		echo "history.go(-1);";
		echo "</script>";
		exit;
	}

	/*
	+----------------------------------+
	| Show Help file
	+----------------------------------+
	| C / M : 2004-4-9 / 2004-4-12
	+----------------------------------+
	*/
	function showHelp()
	{
		if ($_GET['action'] == 'showhelp')
		{
			$this->timer();
			$this->showCSS();
			$this->showTitle();
			$this->ShowJS();

			//这里的内容是一个数组,从1开始...为的是方便表格里的序号按顺序排下去
			//之所以采用数据,是为了方便添加其它帮助,这样的话,更方便以后添加
			//想法,对帮助的$helpContent[2][3]内容进行MD5加密,然后再程序里验证一下就行了,
			//如果有问题则显示错误或其它方式如你的那段DECODE到你的网页的代码,
			//但是我个人不主张,不过为了防止别人更改我们的感谢名单,可以考虑试一下下的~

			$helpContent[1][0] = "NEATPIC (目录直读版) 程序究竟是什么？";	//Link content
			$helpContent[1][1] = "点击查看什么是 NEATPIC (目录直读版) 程序";	//Link title
			$helpContent[1][2] = "whats the neatpic";	//Link's name <a name=''></a>
			$helpContent[1][3] = "TkVBVFBJQyjEv8K81rG2wbDmKcrHIE5FQVQgU1RVRElPILzMIE5FQVRQSUMguvPNxrP2tcTEv8K81rG2wbDmsb6jrLG%2B18W88r3g1sHJz7XE1K3U8qOssb6zzNDy1rvT0NK7uPbOxLz%2Bo6y%2FycrHyLTKtc%2FWwcu087bgyv3NvMasudzA7bPM0PLTtdPQtcS5psTco6zI587EvP7Jz7Sro6zX08S%2FwrzP1Mq%2Bo6zL9cLUzby1yLXIuabE3KGj";	//help content

			$helpContent[2][0] = "NEATPIC (目录直读版) 感谢名单。";	//Link content
			$helpContent[2][1] = "点击查看帮助过 NEATPIC (目录直读版) 朋友和用户";	//Link title
			$helpContent[2][2] = "thanks";	//Link's name <a name=''></a>
			$helpContent[2][3] = "CQkJs8zQ8s%2Frt6ggOiA8Zm9udCBjb2xvcj1yZWQ%2Bb2xkd29sZjwvZm9udD48YnI%2BDQoJCQm5psTcvajS6SA6IG9sZHdvbGYsIEt2b3JhbiwgV2luZG5ldHMsIEVhc3ksIExhbmQsIMDPsfi%2BxrDJLCDAz87a0bssIMDPue0sIM3BsqbK8ywgZ291a2ksIHjS%2Fmuh73o8YnI%2BDQoJCQmzzNDysuLK1CA6IHjS%2Fmuh73osIG9sZHdvbGYsIHN0YXJkdXN0LCDAz87a0bssIExhbmQsIEt2b3JhbiwgZ291a2kgtci1yLrctuC1xMXz09EuLi4gLi4uPGJyPg0KCQkJ1sbX97PJ1LEgOiB3YWxrZXIsIGdvdWtp";	//help content

			$helpContent[3][0] = "我需要使用NEATPIC (目录直读版) 吗？";	//Link content
			$helpContent[3][1] = "点击查看究竟哪些用户需要使用 NEATPIC (目录直读版) ";	//Link title
			$helpContent[3][2] = "doit";	//Link's name <a name=''></a>
			$helpContent[3][3] = "yOe5%2B8Tj09DSu7TzttG1xM28xqzQ6NKqus3F89PRw8fSu8bwt9bP7aOstvi21NfUvLrX9s340rO1xMuuxr3T1rK7yse63NPQ0MXQxKO7yOe5%2B7K7z7C538q508NBQ0RTRUXV4sDgv7TNvMjtvP7X1LavyfqzybXEV0VC0rPD5qOsxMfDtMTjvs2%2FydLUyrnTw87Sw8fV4rj2s8zQ8qOsy%2Fy9q7e9sePE472rzbzGrNW5yr64%2BMTjtcTF89PRw8ejrLb4x9K7ub%2FJ0tTJ6Laot8POysPcwuvS1LfA1rnE47XE0rvQqdL%2By73NvMassbvG5Mv708O7p7%2B0tb2how%3D%3D";	//help content

			$helpContent[4][0] = "NEATPIC (目录直读版)所需要的系统基本配置";	//Link content
			$helpContent[4][1] = "点击查看 NEATPIC (目录直读版) 所需的最低基本配置";	//Link title
			$helpContent[4][2] = "neatconfig";	//Link's name <a name=''></a>
			$helpContent[4][3] = "CQkJMS63%2Fs7xxvfWp7PWUEhQILDmsb4gNC4wLjYg0tTJzyAozt7Q6Mr9vt2%2F4tans9YpPGJyPg0KCQkJMi6wssirxKPKvbnYsdU8YnI%2BDQoJCQkzLsjnufvU2mFwYWNoZSAyLlguWM%2FCyrnTwyzW0M7ExL%2FCvCzOxLz%2Bvauyu8Tc1f2zo8%2FUyr6how%3D%3D";	//help content

			$helpContent[5][0] = "NEATPIC (目录直读版) 有哪些功能？";	//Link content
			$helpContent[5][1] = "点击查看 NEATPIC (目录直读版) 所拥有的功能";	//Link title
			$helpContent[5][2] = "how i can";	//Link's name <a name=''></a>
			$helpContent[5][3] = "CQkJCQkxLtfUtq%2FS1Mv1wtTNvLXEt73Kvc%2FUyr7L%2BdPQzbzGrKOosrvQ6NKqR0S%2F4rXE1qez1qOpOzxCUj4NCgkJCQkJMi6%2FydLUuPm%2B3dDo0qrJ6LaoxL%2FCvLfDzsrD3MLrOzxCUj4NCgkJCQkJMy7Wp7PW1tDOxNfTxL%2FCvMP7OzxCUj4NCgkJCQkJNC7OxLz%2Byc%2B0q6Oo0tHWp7PWtuDOxLz%2Byc%2B0q7mmxNyjqTs8QlI%2BDQoJCQkJCTUuxfrBv8m%2Bs%2F3NvMasOzxCUj4NCgkJCQkJNi7Ev8K8wdCx7c%2FUyr6jrNans9bO3s%2Fe19PEv8K8OzxCUj4NCgkJCQkJNy7Lq8Sjyr2y6b%2B0zbzGrKO61ebKtbe9yr2y6b%2B0us3L9cLUt73KvbLpv7Q7PEJSPg0KCQkJCQk4LsrzserSxravtb3NvMasyc%2B%2FydLUzerV%2B8%2FUyr7NvMasw%2FuzxqOstPPQoaOsuPHKvdLUvLCz37TnoaM8QlI%2BDQoJCQkJCTkuxuTL%2FLv5sb65psTcsrvSu9K7venJ3KOsyOfSs8Pm0bnL9aOst9bSs8%2FUyr6jrMe%2F1sa1x8K8yM%2FWpA%3D%3D";	//help content

			$helpContent[6][0] = "NEATPIC (目录直读版) 申明";	//Link content
			$helpContent[6][1] = "点击查看 NEATPIC (目录直读版) 的申明";	//Link title
			$helpContent[6][2] = "copyright";	//Link's name <a name=''></a>
			$helpContent[6][3] = "TkVBVFBJQyDKx9PJIE5FQVQgU1RVRElPILbAwaK%2Fqreio6zTtdPQuMOzzNDyy%2FnT0LXEsObIqKOsx%2BvKudPD1d%2FX8NbYztLDx7XEsObIqKOs1NrKudPDyrGxo8H0ztLDx7XEsObIqKGj";	//help content

			$helpContent[7][0] = "Jacob 扩展版说明";	//Link content
			$helpContent[7][1] = "点击查看 Jacob 扩展版的说明";	//Link title
			$helpContent[7][2] = "jacob";	//Link's name <a name=''></a>
			$helpContent[7][3] = "SmFjb2Ig1NogTkVBVFBJQyAxLjIuM7Dmyc+9+NDQwcu5psTcwKmz5KOsxNrI3bD8wKjI58/CPGJyPjEuILncwO3AuLzTyc+/1bzk1bzTw7fWzvY8YnI+DQoyLiDU9rzT08O7p8ioz96jrM/e1sbWuLaoxL/CvLe2zqc8YnI+DQozLiDU9rzTv8m9qMGi19PEv8K8o6zJvrP919PEv8K8tcS5psTco6zWp7PW1tDOxDxicj4NCjQuILjEseTBy9K7z8LEv8K8z9TKvr3nw+ajrLe9sePTw7unstnX9zxicj4NCjUuIM/e1sa4+cS/wrzWu8TczqrNvMasudzA7bXEytfSs6OssrvE3MnPtKs8YnI+DQo2LiC01sLUyrXP1ta4tqjTw7unv8nTw7/VvOS089Cho6zU3br2wtTF+sG/PGJyPg0KNy4g1Pa807/Jyei2qMrHt/HP1Mq+zfjVvtb30rO1xMG0vdM=";	//help content
			
			print ("
				<center>
				<table width=750><tbody>
					<tr><td bgcolor='#F7F7F7' height='30' style='border: 1px solid #CCCCCC' align='center'>
						<font color=red>NEATPIC (目录直读版) 帮助文件</font>
					</td></tr>
				</tbody></table>
				<table width=750><tbody>
					<tr><td bgcolor='#FFFFFF' style='border: 1px solid #CCCCCC' align='Left'>
					<br>
			");
			for($i = 1 ; $i <= count($helpContent); $i++)
			{
				print " &nbsp;&nbsp;&nbsp;&nbsp; ".$i."."." <a href=\"#".$helpContent[$i][2]." \"><font title=\" ".$helpContent[$i][1]." \"> ".$helpContent[$i][0]."</font></a>";
				print "<br>";
			}
			print ("
				<br>
				</td></tr>
			");
			for($i = 1 ; $i <= count($helpContent); $i++)
			{
				print "	<tr><td bgcolor='#F7F7F7' height='25' style='border: 1px solid #CCCCCC' >";
				print "&nbsp;&nbsp;" . $i . ".<font color='blue'>".$helpContent[$i][0]."</font><a name=".$helpContent[$i][2]."></a>";
				print "</td></tr><tr><td bgcolor='#FFFFFF' height='25' style='border: 1px solid #CCCCCC' > ";
				print "<center><table border=0 width=95%><tr><td>";
				print "<br>" . $this->decode($helpContent[$i][3]) . "<br><br>";
				print "</td></tr></table></center>";
				print "</td></tr>";
			}			
			print ("
					<tr><td bgcolor='#F7F7F7' height='50' style='border: 1px solid #CCCCCC' align='center'>
						<INPUT TYPE='button' value='返回上页' onclick='javascript:history.go(-1)'>
					</td></tr>
				</tbody></table>
				</center>
			");

			$this->usedTime();
			$this->showConfigState();

			exit;

		}
	}

	/*
	+----------------------------------+
	| Execute Class
	+----------------------------------+
	| C / M : 2003-12-28 / 2003-12-29
	+----------------------------------+
	*/

	function execute()
	{
		$this->showWantPass();
		$this->configDirPass();
		$this->showHelp();
		$this->uploadMore();
		$this->makeDir();
		$this->delFile();
		$this->rmDirQuite();
		$this->showUpload();
		$this->gzip();
		$this->timer();
		$this->getVars();
		$this->checkError();
		$this->checkDirPass();
		$this->showCSS();
		$this->showTitle();
		$this->ShowJS();
		$this->pathArrayInitialize();
		$this->makeOverdirect();
		$this->makeDirList();
		$this->getEachArrayNum();
		$this->makeOptionList();
		$this->makePageBar();
		$this->showState();
		$this->showDirList();
		$this->showAdmincp();
		$this->showPageBar();
		$this->showPicList();
		$this->showPageBar();
		//$this->showDirList();
		$this->usedTime();
		$this->showConfigState();
		$this->c();
	}
}

/*
+----------------------------------+
| Main
+----------------------------------+
| C / M : 2003-12-28 / 2003-12-29
+----------------------------------+
*/

error_reporting(0);
session_start();
header("content-Type: text/html; charset=GB2312");

	/*
	+----------------------------------+
	| Create object
	+----------------------------------+
	| C / M : 2003-12-29 / --
	+----------------------------------+
	*/

	$neatpic = new neatpic($configWantedPass, $configAdminPass, $configDirPasswordFile, $configOpenGzip, $configShowPicSize, $configExt, $strLenMax, $configEachPageMax, $configEachLineMax, $configTDHeight, $configTDWidth, $configPageMax, $configTilte, $configVer, $configAllowSizes, $user, $homeUrl, $showHomeUrl);

	/*
	+----------------------------------+
	| Execute class
	+----------------------------------+
	| C / M : 2003-12-30 / --
	+----------------------------------+
	*/

	$neatpic->execute();

?>