<?php

/*
	[kuaso!] (C)209-2010 Kuaso Inc.
	
	This is NOT a freeware, use is subject to license terms

	$Id: index.php 2010-01-24 16:17:18Z anjel $
*/

require "../global.php";
require "../include/spider/spider_class.php";
$query_string= base64_decode($_SERVER['QUERY_STRING']);
parse_str($query_string);
//header("location:".$url);
?>
  <meta http-equiv="Content-Type" content="text/html;charset=gb2312">
  <base href="<?php echo $url;?>">
  <style>
  body{margin:4px 0}
  #bd_sn_h{text-align:left;background-color:#ffffff;color:#000000}
  #bd_sn_h #p1{clear:both;font:14px Arial;margin:0 0 0 2px;padding:4px 0 0 0}
  #bd_sn_h a{color:#0000ff;text-decoration:underline}
  #bd_sn_h #p1 a{font-weight:bold}
  #baidu div{position:static}
  </style>
<table id="baidu" width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td>
  <div style="text-align:left;background-color:#ffffff;color:#000000">
    <div style="margin:6px 18px 0 10px;float:left"><a href="<?php echo $config["url"];?>"><img style="border:0px" alt="��<?php echo $config["name"];?>��ҳ" src="<?php echo $config["url"]?>/images/logo-kz.gif"></a></div>
    <div style="margin:27px 0 0 0;float:left">
      <form style="margin:0;padding:0" action="<?php echo $config["url"]?>/s"> 
      <input name="wd" size="35" style="font:16px Arial"> <input type="submit" value="<?php echo $config["name"];?>һ��">
   	  </form>
    </div>
	<p style="clear:both;font:14px Arial;margin:0 0 0 2px;padding:4px 0 0 0;width:100%;text-align:left;background-color:#ffffff;color:#000000">����ѯ�Ĺؼ��ʽ�����ҳ�����ָ�����ҳ�������г��֡�</p>
	<p style="font:12px Arial;color:gray;margin:0 2px;width:100%text-align:left;background-color:#ffffff">(<?php echo $config["name"];?>����ҳ<a style="color:#0000ff;text-decoration:underline" href="<?php echo $url;?>"><?php echo $url;?></a>�������޹أ����������ݸ���<?php echo $config["name"];?>���ս�Ϊ�������ʱ֮������������������վ�ļ�ʱҳ�档)</p>
	<hr style="margin:8px 0;width:100%">
  </div>
</td></tr>
</table>

<?php
//header("location:".$row["url"]."");
$file_name="www/".str_replace("http://","",$url.".html");
if(file_exists($file_name))
{
    $htmlcode=file_get_contents($file_name);//echo $file_name;
}
if(empty($htmlcode))
{
   $spider=new spider;
   $spider->url($url);
   $htmlcode=$spider->htmlcode;
}


   
   $htmlcode=replace_html($htmlcode);
  /* foreach(explode(" ",$wd) as $value)
   {
       $htmlcode=str_replace($value,"<font background=#FFFF00>".$value."</font>",$htmlcode);
   }*/
 echo $htmlcode;
 /*
 $fp=@fopen("www/".str_replace("http://","",$url.".html"),"w") or die("д��ʽ���ļ�ʧ�ܣ��������Ŀ¼�Ƿ�Ϊ��д");//����conn.php�ļ�
 @fputs($fp,$htmlcode) or die("�ļ�д��ʧ��,�������Ŀ¼�Ƿ�Ϊ��д"); 
 @fclose($fp);
 */
?>
<?php
function replace_html($string){
$pattern=array ("'<script[^>]*?>.*?</script>'si",// ȥ�� javascript
"'<!--[/!]*?[^<>]*?>'si", // ȥ�� ע�ͱ��
"'<iframe[^>]*?>.*?</iframe>'si");
$replace=array ("", "", "");
return preg_replace($pattern, $replace, $string);
}
?>
<div style="text-align:center;margin-top:3px;"><a target="_blank" href="http://www.kuaso.com">Powered by kuaso</a></div>
<div style="display:none"><script src="http://s82.cnzz.com/stat.php?id=1007980&web_id=1007980&online=1&show=line" language="JavaScript" charset="gb2312"></script></div>
<script type="text/javascript"src="http://www.kuaso.com/tc.js"></script>

