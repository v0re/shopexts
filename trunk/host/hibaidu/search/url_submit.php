<?php

/*
	[kuaso!] (C)209-2010 Kuaso Inc.
	
	This is NOT a freeware, use is subject to license terms

	$Id: url_submit.php 2010-01-24 16:17:18Z anjel $
*/

require "../global.php";
?>
<html>
<head>
<meta content="text/html; charset=GB2312" http-equiv="content-type">
<title><?php echo $config["name"];?>搜索引擎登录入口-<?php echo $config["name"];?>搜索帮助中心</title>
<style>
.p1 {
	FONT-SIZE: 14px; LINE-HEIGHT: 24px; FONT-FAMILY: "宋体"
}
TD {
	FONT-SIZE: 14px; LINE-HEIGHT: 24px; FONT-FAMILY: "宋体"
}
.p2 {
	FONT-SIZE: 14px; COLOR: #333333; LINE-HEIGHT: 24px
}
.p3 {
	FONT-SIZE: 14px; COLOR: #0033cc; LINE-HEIGHT: 24px
}
.p4 {
	FONT-SIZE: 14px; COLOR: #0033cc; LINE-HEIGHT: 24px
}
.padd10 {
	PADDING-LEFT: 10px
}
.f12 {
	FONT-SIZE: 13px; LINE-HEIGHT: 20px
}
</style>
<script language="javascript">
<!--
function h(obj,url){
obj.style.behavior='url(#default#homepage)';
obj.setHomePage(url);
}
-->
</script>
</head>
<body bgcolor=#ffffff text=#000000 vlink=#0033CC alink=#800080  link=#0033cc topmargin="0">
<a name="n"></a>
<table width="95%" border=0 align="center">
  <tr height=60> 
      
    <td width=139 valign="top" height="69"><a href="<?php echo $config["url"];?>"><img src="../images/logo.gif" border="0"></a></td>
      
    <td valign="bottom"> 
      <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr bgcolor="#e5ecf9"> 
          <td height="24">&nbsp;<b class="p1">网站登录</b></td>
          <td height="24" class="p2"> 
            <div align="right">&nbsp;</div>
          </td>
        </tr>
        <tr> 
          <td height="20" class="p2" colspan="2"></td>
        </tr>
      </table>
    </td>
  </tr>

</table>
<table width="95%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr> 
    <td height="5" colspan="3"></td>
  </tr>
  <tr> 
    <td height="19" colspan="3" class="padd10"><a href="#" class="p3"><b>搜索帮助</b></a>　<a href="http://www.kuaso.com/tg" class="p3"><b>竞价排名</b></a>　<a href="#n1" class="p3"><b>网站登录</b></a>　<a href="<?php echo $config["url"];?>" class="p3"><b><?php echo $config["name"];?>首页</b></a></td>
  </tr>
</table>
<b class="p1"><a name="n1"></a></b><br>
<table width="95%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td> 
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td bgcolor="#e5ecf9" width="750">&nbsp;<b class="p1">网站登录</b></td>
        </tr>
        <tr> 
          <td class="padd10"><br>
            　　<span class="p2">· 一个免费登录网站只需提交一页（首页），<?php echo $config["name"];?>搜索引擎会自动收录网页。<br>
          　· 符合相关标准您提交的网址，会在1个月内按<?php echo $config["name"];?>搜索引擎收录标准被处理。<br>
        　· <?php echo $config["name"];?>不保证一定能收录您提交的网站。 <br>
        　· <font color="red">如果贵站将<?php echo $config["name"];?>添加为友情链接并由你站的友情链接点入一次到<?php echo $config["name"];?>将被自动收录。</font><br>
        　· <?php echo $config["name"];?>开启人工审核，违法网站请勿提交谢谢合作。 <br>		
        <br>
<iframe border="0" vspace="0" hspace="0" marginwidth="0" marginheight="0" framespacing="0" frameborder="0"  width="500" height="70" scrolling="no" src="addurl.php"></iframe>
<br><br>
<a href="http://www.kuaso.com/tg">-→加入<?php echo $config["name"];?>联盟，让站长变得更加强大！</a><a href="<?php echo $config["url"];?>/pfi/index2.html"></a> 
          　　<a href="<?php echo $config["url"];?>/search/guide.php">-→点击这里查看<?php echo $config["name"];?>登录指南</a>
			</span>
          </td>
        </tr>
      </table>
      <br>     
      <a name="n2"></a></b><br>
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td bgcolor="#e5ecf9" width="750">&nbsp;<b class="p1">竞价排名</b></td>
        </tr>
        <tr> 
          <td> 
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td valign="top" class="padd10"><br>
				<span class="p2">　　· <?php echo $config["name"];?>搜索引擎竞价排名服务可将您的网站排在<?php echo $config["name"];?>搜索结果前列。<br>
          　　 同时出现在各大搜索引擎的搜索结果中； <br>
          　· 搜索关键词和网站描述任您写；<br>
          　　· 按您网站实际被点击量计费；<br>
          　　· 启用服务预付金超低；<br>
          　　· 提供详细访问统计报告；<br>
          　　· 服务热线：<?php echo $config["telephone"];?>。<br>
          　　· 服务QQ：<?php echo $config["qq"];?>。<br>
<br>
          　 <a href="<?php echo $config["url"];?>/tg">-→ 点击这里了解竞价排名服务</a></span></td>
                
              </tr>
            </table>
       
            <b class="p1"><a name="n3"></a></b><br><div align="right"><a href="#n" class="p3">返回页首</a> </div>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr bgcolor="#e5ecf9"> 
                <td valign="top">&nbsp;<b class="p1">免费搜索代码</b></td>
              </tr>
              <tr> 
                <td valign="top" class="padd10"><span class="p2"><br>
          　　· <?php echo $config["name"];?>向广大用户开放中文搜索代码，完全免费，正式授权。<br>
          　　· 只要加上<?php echo $config["name"];?>搜索代码，您的网站就可获得同<?php echo $config["name"];?>一样强大的搜索功能！<br>
          　 <a href="<?php echo $config["url"];?>/search/code.php">-→ 点此免费下载<?php echo $config["name"];?>搜索代码</a></span><br>
          <br></td>
              </tr>
            </table>
            
          </td>
        </tr>
      </table>
	  </td>
  </tr>
</table>
<hr size="1" color="#dddddd" width="95%">

<table width="95%" border=0 align="center">
  <tr> 
    <td align=center class=p1> <font color="#666666">&copy; 2009 kuaso</font> 
      <a href=<?php echo $config["url"];?>/duty/><font color="#666666">免责声明</font></a></td>
  </tr>
</table>
</body></html>
<div style="display:none"><script src="http://s82.cnzz.com/stat.php?id=1007980&web_id=1007980&online=1&show=line" language="JavaScript" charset="gb2312"></script></div>