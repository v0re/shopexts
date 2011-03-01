<?php

/*
	[kuaso!] (C)209-2010 Kuaso Inc.

	This is NOT a freeware, use is subject to license terms

	$Id: index.php 6672 2010-01-24 16:17:18Z anjel $
*/

require "global.php";
?>
<html>
<head>
<meta http-equiv=Content-Type content="text/html;charset=gb2312">
<title><?php echo $config["name"];?>一下，你就找到好网站</title>
<meta name="Keywords" content="<?php echo $config["Keywords"];?>">
<meta name="description" content="<?php echo $config["description"];?>">
<style>
body{margin:4px 0}p{margin:0;padding:0}img{border:0}td,p,
#u{font-size:12px}
#b,#u,#l td,a{font-family:arial}
#kw{font:16px Verdana;height:1.78em;padding-top:2px}
#b{height:30px;padding-top:4px}
#b,#b a{color:#77c}
#u{padding-right:10px;line-height:19px;text-align:right;margin:0 0 3px !important;margin:0 0 10px}
#sb{height:2em;width:5.6em}
#km{height:5px}
#l{margin:0 0 5px 15px}
#l td{padding-left:107px}p,table{width:650px;border:0}
#l td,#sb,#km{font-size:14px}
#l a,#l b{margin-right:1.14em}a{color:#00c}a:active{color:#f60}
#hp{position:absolute;margin-left:6px}
#lg{margin:-26px 0 -44px}
#lk{width:auto;line-height:18px;vertical-align:top}form{position:relative;z-index:9}
#hot_kw{width:500px;white-space: nowrap;line-height:20px;}
#hot_kw a{font-size:13px;}
#statcode{text-align:center;display:none;}.ad_tb{background:#cccccc;}.ad_tb td{background:#FFFFFF;width:25%;text-align:center;}
#tags {}
#tags a { }
#tags a:hover { text-decoration:none }
#tags a.focu {color:#000000;TEXT-DECORATION:none;font-weight:bold;}.popupMenu{width:100px;BORDER-RIGHT: #3366CC 1px solid; BORDER-TOP: #A2BAE7 1px solid; BORDER-LEFT: #A2BAE7 1px solid; BORDER-BOTTOM: #3366CC 1px solid; BACKGROUND-COLOR:#ffffff;line-height:180%;font-size:75%; position:absolute; z-index:100}
</style>
<script language="javascript" type="text/javascript">
function selectTag(showContent,wd,cate_id,selfObj){
	var tag = document.getElementById("tags").getElementsByTagName("a");
	var taglength = tag.length;
	for(i=0; i<taglength; i++){
		tag[i].className = "";
	}
	selfObj.className = "focu";
	
	document.f.wd.focus();
	document.f.wd.value=wd;
	document.f.s.value=cate_id;
	f.attributes[83].value=showContent;
	//document.f.action=showContent;

}
function selectTag_tieba(showContent,wd,cate_id,selfObj){
	var tag = document.getElementById("tags").getElementsByTagName("a");
	var taglength = tag.length;
	for(i=0; i<taglength; i++){
		tag[i].className = "";
	}
	selfObj.className = "focu";
	
	document.f.wd.focus();
	document.f.wd.value=wd;
	document.f.s.disabled="disabled";
	f.attributes[83].value=showContent;
	document.f.wd.name="kw";
	//document.f.action=showContent;

}
</script>
<script language="javascript" src="js/zeidu_menu.php"></script>
</head>
<body onLoad="onLoadHandler()" onMouseOut="window.status='<?php echo $config["status_content"];?>';return true">
<SCRIPT LANGUAGE="JavaScript">
<!--//
function killErr(){
	return true;
}
window.onerror=killErr;
//-->
</SCRIPT>
<div id=u>&nbsp;
<a href="search/url_submit.php" target="_blank">网站登录</a></div>
<center><img src="images/kuaso_logo.gif" width=270 height=129 usemap="#mp" id=lg><br><br><br><br><table cellpadding=0 cellspacing=0 id=l><tr><td>
<div id=m>
<div id="tags">
<a href="javascript:void(0)" class=focu onClick="selectTag('s','网页','',this)">网&nbsp;页</a><?php
$cate_query=$db->query("select * from kuaso_categories where parent_id='0' order by cate_id desc");
while($cate=$db->fetch_array($cate_query))
{
   echo "<div id=\"Memu_Desc_".$cate["cate_id"]."\" class=\"popupMenu\" style=\"display:none\"></div>";
   //echo "<a id=\"Desc_".$cate["cate_id"]."\" href=\"s/?wd=".urlencode($cate["cate_title"])."&s=".$cate["cate_id"]."\">".$cate["cate_title"]."</a>";  //没有下拉导航
   echo "<a id=\"Memu_Desc_".$cate["cate_id"]."_link\" href=\"javascript:void(0)\" onClick=\"selectTag('s','".$cate["cate_title"]."','".$cate["cate_id"]."',this)\">".$cate["cate_title"]."</a>"; //有下拉导航
}
?>
</div>
</div></td></tr></table>
<table cellpadding=0 cellspacing=0 style="margin-left:15px"><tr valign=top><td style="height:62px;padding-left:92px" nowrap><form name=f action=s><input name=wd type=text id=kw size=42 maxlength=100> 
<input type="hidden" name=s value="">
<input type=submit value=<?php echo $config["name"];?>一下 id=sb><span id=hp><a href="/tg">竞价<br>推广</a></span></form></td></tr></table>
<div id="hot_kw">
<?php
$query_ad=$db->query("select * from kuaso_ad where type='3' and is_show order by sortid asc");
while($row_ad=$db->fetch_array($query_ad))
{
  if(empty($row_ad["siteurl"]))
  {
     $urllink="s/?wd=".urlencode($row_ad["title"]);
  }
  else
  {
     $urllink=$row_ad["siteurl"];
  }
   echo "<a target=\"_blank\" href=\"".$urllink."\">".str_cut($row_ad["title"],300)."</a>&nbsp;&nbsp;";
}
?>
</div><br><br><br><br>
<p style=height:30px><a onClick="this.style.behavior='url(#default#homepage)';this.setHomePage('<?php echo $config["url"];?>')" href=#>把<?php echo $config["name"];?>设为主页</a>&nbsp;&nbsp;
<a href="g/#write">给<?php echo $config["name"];?>留言</a>
</p>
<?php
index_foothtml();
?>
<script>var w=document.f.wd;function s(o){if(w.value.length>0){var h=o.href;var q=encodeURIComponent(w.value);if(h.indexOf("q=")!=-1){o.href=h.replace(new RegExp("q=[^&$]*"),"q="+q)}else{o.href+="?q="+q}}};(function(){if(new RegExp("q=([^&]+)").test(location.search)){w.value=decodeURIComponent(RegExp.$1)}})();if(navigator.cookieEnabled&&!/sug?=0/.test(document.cookie)){document.write('<script src=js/bdsug.js?v=1.1.0.3><\/script>')};if(window.attachEvent){window.attachEvent("onload",function(){w.focus();})}else{window.addEventListener('load',function(){w.focus()},true)};window.onunload=function(){};</script>
<map name=mp><area shape=rect coords="43,22,227,91" href="javascript:window.external.AddFavorite(window.location.href,document.title);"  title="点此收藏 <?php echo $config["name"];?>"></map></center>
<script language="javascript">
document.writeln("<script src=\"js\/shoulu.php?ref="+escape(document.referrer)+"\"><\/script>");
var url = top.location;
var user_id;
url = url.toString();
user_id=url.substring(url.indexOf("?")+1,url.length);
if(!isNaN(user_id))
{
 // document.write(user_id);
  document.writeln("<script src=\"js\/zz.php?user_id="+user_id+"\"><\/script>");
}

</script>
</body>
</html>

