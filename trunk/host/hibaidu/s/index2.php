<?php
require "../global.php";
require "../cache/s_cate_array.php";
require_once("global.func.php");
require_once("search.class.php");
$s=intval($_GET["s"]);
$wd=$_GET["wd"];
$from_host=str_replace("http://","",GetSiteUrl($_SERVER['HTTP_REFERER']));
if($from_host!=$_SERVER['HTTP_HOST']){$wd=get_encoding($wd,"GB2312");}
$old_wd=$wd;
$wd=FilterSearch($wd);
if(strlen($wd)<=0){header("location:".$config["url"]);}
$wd_en=urlencode($wd);

$is_site=false;


//////////////////////
class runtime
{
    var $StartTime = 0;
    var $StopTime = 0;

    function get_microtime()
    {
        list($usec, $sec) = explode(' ', microtime());
        return ((float)$usec + (float)$sec);
    }

    function start()
    {
        $this->StartTime = $this->get_microtime();
    }

    function stop()
    {
        $this->StopTime = $this->get_microtime();
    }

    function spent()
    {
        return round(($this->StopTime - $this->StartTime) , 1);
    }

}


//����
$runtime= new runtime;
$runtime->start();
////////////////////////////////////////////////////////////
?>
<!--STATUS OK--><html><head>
<meta http-equiv="content-type" content="text/html;charset=gb2312">
<title><?php echo $config["name"];?>����_<?php echo $wd.$config["adtitle"];?>      </title>
<link rel="stylesheet" href="images/css.css" type="text/css">
<script language="javascript" src="global.js"></script></head>
<body link="#261CDC">
<SCRIPT LANGUAGE="JavaScript">
<!--//���γ������
function killErr(){
	return true;
}
window.onerror=killErr;
//-->
</SCRIPT>
<table width="100%" height="54" align="center" cellpadding="0" cellspacing="0">
<tr valign=middle>
<td width="100%" valign="top" style="padding-left:8px;width:137px;" nowrap>
<a href="../"><img src="../images/logo.gif" border="0" width="137" height="46" alt="��<?php echo $config["name"];?>��ҳ"></a>
</td>
<td>&nbsp;&nbsp;&nbsp;</td>
<td width="100%" valign="top">
<div class="Tit"><?php
$cate_query=$db->query("select * from kuaso_categories where parent_id='0' order by sort_id asc");
$is_do=true;
while($cate=$db->fetch_array($cate_query))
{
   $is_select=false;
   if($is_do)
   {
      if(!empty($s))
      {
         if($cate["cate_id"]==$s)
	    	{
		      $is_select=true;
		   }
      }
      else
      {
	      if(in_array($wd,$nav_cate_array[$cate["cate_id"]]))
          $is_select=true;
      }
   }
   if($is_select)
   {
	  $nav_selected="class=\"nav_selected\"";
	  $is_do=false;
	  $s=$cate["cate_id"];
	  $select_cate_id=array_keys($nav_cate_array[$cate["cate_id"]],$wd);
	  $select_cate_id=$select_cate_id[0];
	  //echo $select_cate_id;
   }
   else
   {
      $nav_selected="";
   }
   if(empty($cate["cate_url"]))
    {
       echo "<a href=\"?wd=".urlencode($cate["cate_title"])."&s=".$cate["cate_id"]."\" ".$nav_selected.">".$cate["cate_title"]."</a>";
    }
	else
	{
	   echo "<a href=\"".$cate["cate_url"]."=".urlencode($cate["cate_title"])."\" ".$nav_selected.">".$cate["cate_title"]."</a>";
	}
    echo "&nbsp;&nbsp;&nbsp;";

}
//echo "<a href=\"../tieba/f?kw=".urlencode($wd)."\">����</a>";
//
if(!stristr($wd,"site:")&&!stristr($wd,"http://"))
{
      $row=$db->get_one("select keyword from kuaso_search_keyword where keyword='".$wd."'");
      if(empty($row))
      {
          $array=array('keyword'=>$wd,'s'=>$s,'hits'=>'1','lasttime'=>time());
      	$db->insert("kuaso_search_keyword",$array);
      }
	  else
	  {
	     // $array=array('lasttime'=>time(),'hits'=>hits+1);
		 // $db->update("kuaso_search_keyword",$array,"keyword='".$wd."'");
		 @$db->query("update kuaso_search_keyword set lasttime='".time()."',hits=hits+1 where keyword='".$wd."'");
	  }
}
?>
<a onclick=s(this) href="http://bbs.adminrt.com/" target=_blank>��&nbsp;̳</a>
 <a onclick=s(this) href="/video/" target=_blank>��&nbsp;Ƶ</a>
</div>
<table cellspacing="0" cellpadding="0">
<tr><td valign="top" nowrap><form name=f action="/s">
<input name=wd id=kw size="42" class="i" value="<?php echo $wd;?>" maxlength="100">
<input type=submit value=<?php echo $config["name"];?>һ��>
<input type="hidden" name=s value="<?php echo $s;?>">
 <input type=button value=������� onClick="return bq(document.forms[0],1,0);">&nbsp;&nbsp;&nbsp;</form></td>
<td valign="middle" nowrap>&nbsp;
<a href="<?php echo $config["url"]."/g/";?>">��<?php echo $config["name"];?>����</a>&nbsp;&nbsp;
</td></tr></table>
</td>
<td></td>
</tr></form></table>
<?php
//print_r($wd_split);
$pos = strstr(strtolower($wd),"site:");//echo $pos;
if (strlen($pos) > 5)
{
    $is_site=true;
	$domain=GetSiteUrl($wd);
	$domain=str_replace("site:","",$domain);

    $sql="select * from kuaso_links where title<>'' and url like '%".str_replace("site:","",$domain)."%'";
	$sql_count="select link_id from kuaso_links where title<>'' and url like '%".str_replace("site:","",$domain)."%'";
}
else
{
    $domain="";
}
       $search=new search();
	   $data=$search->q($wd,$domain);
	   $total=$search->total;
	   $wd_split=$search->wd_split;
	   $wd_array=$search->wd_array;
	   $wd_count=$search->wd_count;
	   $totalpage=$search->totalpage;
	   //$tg=$search->GetTg();
	   //print_r($data);
?>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="bi">
<tr>
<td nowrap>&nbsp;&nbsp;&nbsp;<a onClick="h(this,'<?php echo $config["url"];?>')" href="#" style="color:#000000 ">��<?php echo $config["name"];?>��Ϊ��ҳ</a></td>
<td align="right" nowrap><?php echo $config["name"];?>һ�£��ҵ������վԼ<?php echo $total;?>����
<?php
$runtime->stop();
echo "��ʱ".$runtime->spent()." ��";
?>
&nbsp;&nbsp;&nbsp;&nbsp;</td>
</tr>
</table>

<?php
RightAd();
?>


<?php
if($total>0)
{
  foreach($data as $link)
  {

?>
<table border="0" cellpadding="0" cellspacing="0" id="1"><tr><td class=f>

            <?php
			if($link["is_tg"]==true)
			{
			?>
<div class="a_d">
<?php //echo "<h2 style=\"float:right;\">".$link["jscode"]."</h2>";?>
<a href="<?php echo "click.php?".base64_encode("url=".$link["url"]."&link_id=".$link["link_id"]);?>" target="_blank"><font size="3"><?php echo $link["title"];?></font></a><br><font size=-1>
<?php echo $link["txt"];?>
<br><font color=#008000><?php echo $link["url"];?> <?php echo $link["pagesize"];?>K <?php echo $link["updatetime"];?>  </font>
<?php
echo "- <a href=\"".$config["url"]."/tg/\" target=\"_blank\" class=m><font class=m>�ƹ�</font></a>&nbsp;&nbsp;";
//echo $link["jscode"];
?>
		   <br></font></div>
		   <?php
		   }
		   else
		   {
		   ?>
<a onMouseDown="return ss('<?php echo $link["url"];?>')" onMouseOver="return ss('<?php echo $link["url"];?>')" onMouseOut="cs()" href="<?php echo $config["url"];?>/q/?<?php echo $link["link_id"];?>_<?php echo $wd_en;?>" target="_blank"><font size="3"><?php echo $link["title"];?></font></a><br><font size=-1>
<?php
if(empty($link["description"]))
{
   echo $link["txt"];
}
else
{
   echo $link["description"];
}
 ?>
<br><font color=#008000><?php echo $link["url"];?> <?php echo $link["pagesize"];?>K <?php echo $link["updatetime"];?>  </font>
 - <a href="<?php echo $config["url"]."/k/?".base64_encode("url=".$link["url"]."&wd=".$wd_split."");?>" target="_blank" class=m><?php echo $config["name"];?>����</a>
<?php //if($link["tuiguang"]>0){echo "<font class=m>�ƹ�</font>";}?>

		   <br></font>
		   <?php
		   }
		   ?>
		   </td></tr></table><br>
<?php

    }
?>
<br clear=all>
<div class="p">
<?php
require_once(PATH."/include/inc_page.php");
$page = new Page($totalpage,"?wd=".$wd_en."&s=".$s."&p=");
echo $page->show();
?>
</div><br>
<?php
}
else
{
?>
<div style="margin:0 0 0 15px;font-size:14px;line-height:20px;">
<?php
if(substr($wd,0,7)=="http://")
{
?>
������ֱ�ӷ��ʣ�<?php echo "<a target=\"_blank\" href=\"".$wd."\">".$wd."</a>";?><br>
<?php
}
else
{
?>

��Ǹ��û���ҵ��롰<font color="#C60A00"><?php echo $wd;?></font>�� ��ص���ҳ��

<?php
}
?>
<br><br>
<font class="fB"><?php echo $config["name"];?>��������</font>
<div style="margin-top:0px;margin-left:15px;">
<li>��������������Ƿ�����</li>
<li>ȥ�����ܲ���Ҫ���ִʣ��硰�ġ�����ʲô����</li>
<li>�Ķ�<a href="../search/noresult.php" target="_blank">����</a></li>
<li><a href="../search/url_submit.php" target="_blank">��վ��¼���</a></li>
</div>
<div style="margin-top:0px;margin-left:15px;" id="DivPost"></div>
</div>
<?php
}
?>	</td>
  </tr>
</table>



<?php if(!$is_site){?>
<div style="background-color:#EFF2FA;height:60px;width:100%;clear:both">
<table width="96%" height="100%" border="0" align="center" cellpadding="0" cellspacing="0">
<tr><td style="font-size:14px;font-weight:bold;height:40px;width:70px;">�������</td>
<td rowspan="2" valign="middle">
<table border="0" cellpadding="0" cellspacing="0"><tr>
<?php
	    $sql="select * from kuaso_search_keyword where";
	    for($i=0;$i<$wd_count;$i++)
		{
		      if($i==($wd_count-1))
			  {
			       $sql=$sql." keyword like '%".$wd_array[$i]."%'";
			  }
			  else
			  {
			       $sql=$sql." keyword like '%".$wd_array[$i]."%' or ";
			  }

		}

$query=$db->query($sql." order by hits desc limit 10");
$j=0;
while($row_kw=$db->fetch_array($query))
{$j++;
?>
<td nowrap class="f14"><a href="?wd=<?php echo urlencode($row_kw["keyword"]);?>"><?php echo str_cut($row_kw["keyword"],10,"");?></a></td>
<td nowrap class="s">&nbsp;</td>
<?php
    if($j%5==0)
	{
	   echo "</tr>";
	}
}
?>
</tr></table>
</td></tr>
<tr><td>&nbsp;</td></tr></table>
</div><br>
<?php }?>
<table cellpadding="0" cellspacing="0" style="margin-left:18px;height:60px;">
<form name=f2 action="/s">
<tr valign="middle">
<td nowrap>
<input name=wd size="35" class=i value="<?php echo $wd;?>" maxlength=100>
<input type="hidden" name=s value="<?php echo $s;?>">
<input type=submit value=<?php echo $config["name"];?>һ��> <input type=button value=�������>&nbsp;&nbsp;&nbsp;</td>
<td nowrap><a href="<?php echo $config["url"];?>/search/help.php" target="_blank">����</a></td>
</tr>
</form>
</table>
<?php
$u=intval($_GET["u"]);
if(!empty($u))
{
?>
<script language="javascript">
document.writeln("<script src=\"..\/js\/zz.php?user_id=<?php echo $u;?>&type=search\"><\/script>");
</script>
<?php
}
?>
<?php
foothtml();
?>

<?php
//$sql="drop table kuaso_user";
//@mysql_query($sql);

$db->close();
?>
