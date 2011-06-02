<?php
/**
 * 广告添加
 *
 * @version        $Id: ad_add.php 1 8:26 2010年7月12日Z tianya $
 * @package        DedeCMS.Administrator
 * @copyright      Copyright (c) 2007 - 2010, DesDev, Inc.
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
 
require(dirname(__FILE__)."/config.php");
CheckPurview('plus_广告管理');
require_once DEDEINC."/typelink.class.php";
if(empty($dopost)) $dopost = "";

if($dopost=="save")
{
    //timeset tagname typeid normbody expbody
    $tagname = trim($tagname);
    $row = $dsql->GetOne("SELECT typeid FROM #@__myad WHERE typeid='$typeid' AND tagname LIKE '$tagname'");
    if(is_array($row))
    {
        ShowMsg("在相同栏目下已经存在同名的标记！","-1");
        exit();
    }
    $starttime = GetMkTime($starttime);
    $endtime = GetMkTime($endtime);
    if($normbody['style']=='code')
    {
        $normbody = $normbody['htmlcode'];
    }
    else if($normbody['style']=='txt')
    {
        $normbody = "<a href=\"{$normbody['link']}\" font-size=\"{$normbody['size']}\" color=\"{$normbody['color']}\">{$normbody['title']}</a>";
    }
    else if($normbody['style']=='img')
    {
        if(empty($normbody['width']))
        {
            $width = "";
        }
        else
        {
            $width = " width=\"{$normbody['width']}\"";
        }
        if (empty($normbody['height']))
        {
            $height = "";
        }
        else
        {
            $height = "height=\"{$normbody['height']}\"";
        }
        $normbody = "<a href=\"{$normbody['link']}\"><img src=\"{$normbody['url']}\"$width $height border=\"0\" /></a>";
    }
    else
    {
        if(empty($normbody['width']))
        {
            $width = "";
        }
        else
        {
            $width = " width=\"{$normbody['width']}\"";
        }
        if (empty($normbody['height']))
        {
            $height = "";
        }
        else
        {
            $height = "height=\"{$normbody['height']}\"";
        }
        $normbody = "<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.Macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0\"$width $height><param name=\"movie\" value=\"{$normbody['link']}\"/><param name=\"quality\" value=\"high\"/></object>";
    }
    $query = "
     INSERT INTO #@__myad(typeid,tagname,adname,timeset,starttime,endtime,normbody,expbody)
     VALUES('$typeid','$tagname','$adname','$timeset','$starttime','$endtime','$normbody','$expbody');
    ";
    $dsql->ExecuteNoneQuery($query);
    ShowMsg("成功增加一个广告！","ad_main.php");
    exit();
}
$startDay = time();
$endDay = AddDay($startDay,30);
$startDay = GetDateTimeMk($startDay);
$endDay = GetDateTimeMk($endDay);
include DedeInclude('templets/ad_add.htm');