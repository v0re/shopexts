<?php

/*
	[kuaso!] (C)209-2010 Kuaso Inc.
	
	This is NOT a freeware, use is subject to license terms

	$Id: addurl.php 2010-01-24 16:17:18Z anjel $
*/

error_reporting(0);
require "../global.php";
require "../include/spider/spider_class.php";
$action=HtmlReplace(trim($_GET["action"]));
if($action=="add")
{
     $oldurl=$url= HtmlReplace(trim($_POST["url"]));
	 $query=$db->query("select * from kuaso_url_submit where url='$url'");
	 $num=$db->num_rows($query);
	  if($num==0)
	  {
          $array=array('url'=>$url,'ip'=>ip(),'addtime'=>time());
		  $db->insert("kuaso_url_submit",$array);
	  }
     //
	 if($config['is_tijiao_shoulu'])
	 {
	   $url= GetSiteUrl($url);
	   $site=$db->get_one("select * from kuaso_sites where url='$url'");
	     if(empty($site))
	     {
	       $array=array('url'=>$url,'spider_depth'=>$config["spider_depth"],'indexdate'=>time(),'addtime'=>time());
		   $db->insert("kuaso_sites",$array);
	     }
		 $site=$db->get_one("select * from kuaso_sites where url='$url'");
	     if(!empty($site))
	     {
		     $row=$db->get_one("select * from kuaso_links where url='".$url."'");
		     if(empty($row))
		      {
					 AddAndUpdateUrl($url,"add");
  				     
		      }
			  else
			  {
					 AddAndUpdateUrl($url,"update");
			  }
			  //
			  if($oldurl!=$url)
			  {
			        $row=$db->get_one("select * from kuaso_links where url='".rtrim($oldurl,"/")."'");
		            if(empty($row))
		            {
					      AddAndUpdateUrl($oldurl,"add");
  				     
		            }
			        else
			         {
					             AddAndUpdateUrl($oldurl,"update");
			         }
			  }
		 }
	  //
	  }
	  header("location:success.php");
}
function AddAndUpdateUrl($url,$action)
{
    global $db;
					 $spider=new spider;
   				     $spider->url($url);
  				     $title=$spider->title;
  				     $fulltxt=$spider->fulltxt(800);
					 $keywords=$spider->keywords;
                     $description=$spider->description;
   				     $pagesize=$spider->pagesize;
  				     $array=array('url'=>$url,'title'=>$title,'fulltxt'=>$fulltxt,'pagesize'=>$pagesize,'keywords'=>$keywords,'description'=>$description,'updatetime'=>time());
					 if($action=="add")
					 {
					     $db->insert("kuaso_links",$array);
					 }
					 elseif($action=="update")
					 {
					     $db->update("kuaso_links",$array,"url='".$url."'");  
					 }
  				     
}
?><html>
<head>
<title><?php echo $config["name"];?>����ָ��_��վ��¼</title>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<style type="text/css"><!--
.f13 {font-size: 13px;
	line-height: 18px;}
.f14 {font-size: 14px;}
-->
</style>

<SCRIPT language=javascript>
<!--
function CheckForm(thisForm) {
	if (isEmpty(thisForm.url.value) || (thisForm.url.value == "http://")) {
         	alert("��û��������վ��ַ!");
      		thisForm.url.focus();
     		return;
    	}
	else if (thisForm.url.value.indexOf("http://") != 0) {
		alert("���ټ���http://,�����ټ��һ��!");
		thisForm.url.focus();
		return;
	}
	else if (thisForm.url.value.indexOf("http://") != thisForm.url.value.lastIndexOf("http://")) {
		alert("�������http://,�����ټ��һ��!");
		thisForm.url.focus();
		return;
	}
	else if (isEmpty(thisForm.code.value)) {
         	alert("��û��������֤��!");
      		thisForm.code.focus();
     		return;
    	}
	else
    		thisForm.submit();
}
function isEmpty(value) {
  return ((value == null) || (value.length == 0))
}
//-->
</SCRIPT>

</head>
		<FORM name=regform action=?action=add method=post target="_top">
          <table cellspacing=0 cellpadding=0 width=700 align=center border=0>
            <tr>
              <td valign=top class="f13">������������<?php echo $config["url"];?>��<br>
      ������
        <input id=url2 size=50 value=http:// 
                  name=url>
                <br>
                ������ ��������֤�롡
<img src="../include/vdimgck.php" border="0" hspace="3" align="absmiddle">
                
<input type=hidden name=ivc value="Vw9SXVYPBg8=">
                <input size=10 
                  name=code>
                <input onClick="CheckForm(document.all['regform']);" type=button value="�ύ��վ" name=Submit2>
              </td>
            </tr>
          </table>
		</FORM>
		</body>
</html>
