<?php

/*
	[kuaso!] (C)209-2010 Kuaso Inc.
	
	This is NOT a freeware, use is subject to license terms

	$Id: shoulu.php 2010-01-24 16:17:18Z anjel $
*/

require "../global.php";
$url=HtmlReplace($_GET["ref"]);
if($url<>$config["url"]&&stristr($url,"http://"))
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
			   $ip=ip();
	           //$referer=$_SERVER['HTTP_REFERER'];
               $v=$db->get_one("select * from kuaso_stat_visitor where v_ip='".$ip."' and v_time>='".(time()-(86400*1))."'");
	           if(empty($v))
	           {
		           $array=array('v_time'=>time(),'v_ip'=>$ip);
		           $db->insert("kuaso_stat_visitor",$array);
				   $db->query("update kuaso_sites set com_time='".time()."',com_count_ip=com_count_ip+1 where url='".$url."'");
	              
	           }
		 }
		 $site=$db->get_one("select * from kuaso_sites where url='$url'");
	     if(!empty($site))
	     {
		     $row=$db->get_one("select * from kuaso_links where url='".$url."'");
		     if(empty($row))
		      {
					 $spider=new spider;
   				     $spider->url($url);
  				     $title=$spider->title;
  				     $fulltxt=$spider->fulltxt(800);
					 $keywords=$spider->keywords;
                     $description=$spider->description;
   				     $pagesize=$spider->pagesize;
  				     $array=array('url'=>$url,'title'=>$title,'fulltxt'=>$fulltxt,'pagesize'=>$pagesize,'keywords'=>$keywords,'description'=>$description,'updatetime'=>time());
  				     $db->insert("kuaso_links",$array);
		      }
		 }
}
?>
