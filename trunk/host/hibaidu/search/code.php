<?php

/*
	[kuaso!] (C)209-2010 Kuaso Inc.
	
	This is NOT a freeware, use is subject to license terms

	$Id: code.php 2010-01-24 16:17:18Z anjel $
*/

require "../global.php";
?>
<html>
<head>
<meta content="text/html; charset=GB2312" http-equiv="content-type">
<title><?php echo $config["name"];?>������������-��Ѵ���</title>
<link href="help.css" rel="stylesheet" type="text/css">
<link href="iknow.css" rel="stylesheet" type="text/css">
<script language="javascript">
<!--
function h(obj,url){
obj.style.behavior='url(#default#homepage)';
obj.setHomePage(url);
}
-->
</script>
<style type="text/css">
<!--
.p1,td {FONT-SIZE: 14px; LINE-HEIGHT: 24px; FONT-FAMILY: "����"}
.p2 { FONT-SIZE: 14px; LINE-HEIGHT: 24px; color: #333333}
.p3 { FONT-SIZE: 14px; LINE-HEIGHT: 24px; color: #0033cc}
.p4 { font-size: 14px;  LINE-HEIGHT: 24px; color: #0033cc }
.padd10{padding-left:10px;}
.f12 {	FONT-SIZE: 13px; LINE-HEIGHT: 20px}
.Lbg{margin:13px 0 7px 0;height:1px;line-height:1px;border-top:1px solid #999999}
.f14B{font-size:14px;line-height:22px;font-weight:bold;}
.org,a.org:link,a.org:visited{color:#ff6600}
.box{padding:20px;font-size:14px}
-->
</style>
</head>
<body bgcolor=#ffffff text=#000000 vlink=#0033CC alink=#800080  link=#0033cc topmargin="0">
<a name="n"></a>
<table width="95%" border=0 align="center">
  <tr height=60>

    <td width=139 valign="top" height="69"><a href="<?php echo $config["url"];?>"><img src="<?php echo $config["url"];?>/images/logo.gif" border="0"></a></td>

    <td valign="bottom">
      <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr bgcolor="#e5ecf9">
          <td height="24">&nbsp;<b class="p1">��Ѵ���</b></td>
          <td height="24" class="p2">
            <div align="right"><a  href="jiqiao.html"><span style="font-size:14px;"><font color="#0033cc">��������</font></span></a> &nbsp;</div>
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
    <td height="19" colspan="3" class="padd10"><a href="#1" class="p3"><b><font color="#0033cc">�����������</font></b></a>��</td>
  </tr>
</table>
<b class="p1"><a name="1"></a></b><br>
<table width="95%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td>
            <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
              <tr>
                <td> <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td bgcolor="#e5ecf9" width="750">&nbsp;<b class="p1">�����������</b></td>
                    </tr>
                  </table>
                  <div align="right"></div></td>
              </tr>
            </table>

            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td width="750"><br> <TABLE cellSpacing=0 cellPadding=0 width="99%" border=0>
                          <TR>
                            <TD> <table cellspacing=0 cellpadding=0 width="90%" align=center border=0>
                                <tr>
                                  <td class="p2"> ��<?php echo $config["name"];?>�����ѿ����������<?php echo $config["name"];?>�������롣<br>
                                    ��ֻ�轫���´���֮һ���뵽������ҳ�У�������վ���ɻ��ͬ<?php echo $config["name"];?>��������һ��ǿ����������ܣ�<br></td>
                                </tr>
                              </table>
                              <BR> <TABLE cellSpacing=0 cellPadding=0 width=90% align=center border=0>
                                <TR>
                                  <TD> <FORM name=query action=<?php echo $config["url"];?>/s/ method=get target=_blank>
                                      <A href="<?php echo $config["url"];?>/"><IMG
                        src="<?php echo $config["url"];?>/images/logo-80px.gif" alt=KuaSo border=0
                        align=bottom></A>
                                      <INPUT size=30 name=wd>
                                      <INPUT name="submit" type=submit value=<?php echo $config["name"];?>һ��>
                                    </FORM>
									</TD>
                                </TR>
                                <TR>
                                  <TD vAlign=top><span class="style2">HTML���룺</span><BR>
                                    <TEXTAREA name=textarea rows=6 cols=60>&lt;form action="<?php echo $config['url'];?>/s/" target="_blank"&gt;
&lt;table bgcolor="#FFFFFF"&gt;&lt;tr&gt;&lt;td&gt;
&lt;a href="<?php echo $config["url"];?>/"&gt;&lt;img src="<?php echo $config["url"];?>/images/logo-80px.gif" alt="KuaSo" align="bottom" border="0"&gt;&lt;/a&gt;
&lt;input type=text name=wd size=30&gt;
&lt;input type="submit" value="<?php echo $config["name"];?>����"&gt;
&lt;/td&gt;&lt;/tr&gt;&lt;/table&gt;
&lt;/form&gt;</TEXTAREA>
                                  </TD>
                                </TR>
                              </TABLE>
                              <br><div class="Lbg" id="Lbg">&nbsp;</div>
                              <br>
                            ��                            </TD>
                          </TR>
                        </TABLE></td>
                    </tr>
      </table>            <br>      <br></td>
  </tr>
</table>

<hr size="1" color="#dddddd" width="95%">

<table width="95%" border=0 align="center">
  <tr>
    <td align=center class=p1> <font color="#666666">&copy; 2009 kuaso</font>
      <a href=<?php echo $config["url"];?>/duty/index.html ><font color="#666666">��������</font></a></td>
  </tr>
</table>
</body></html>