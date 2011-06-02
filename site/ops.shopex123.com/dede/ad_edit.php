<?php
/**
 * 广告编辑
 *
 * @version        $Id: ad_edit.php 1 8:26 2010年7月12日Z tianya $
 * @package        DedeCMS.Administrator
 * @copyright      Copyright (c) 2007 - 2010, DesDev, Inc.
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require(dirname(__FILE__)."/config.php");
CheckPurview('plus_广告管理');
require_once(DEDEINC.'/typelink.class.php');
if(empty($dopost)) $dopost = '';
$aid = preg_replace("#[^0-9]#", '', $aid);
$ENV_GOBACK_URL = empty($_COOKIE['ENV_GOBACK_URL']) ? "ad_main.php" : $_COOKIE['ENV_GOBACK_URL'];

if($dopost=='delete')
{
    $dsql->ExecuteNoneQuery("DELETE FROM `#@__myad` WHERE aid='$aid' ");
    ShowMsg("成功删除一则广告代码！",$ENV_GOBACK_URL);
    exit();
}
else if($dopost=="getjs")
{
    require_once(DEDEINC.'/oxwindow.class.php');
    $jscode = "<script src='{$cfg_phpurl}/ad_js.php?aid=$aid' language='javascript'></script>";
    $showhtml = "<xmp style='color:#333333;background-color:#ffffff'>\r\n\r\n$jscode\r\n\r\n</xmp>";
    $showhtml .= "预览：<iframe name='testfrm' frameborder='0' src='ad_edit.php?aid={$aid}&dopost=testjs' id='testfrm' width='100%' height='200'></iframe>";
    $wintitle = "广告管理-获取JS";
    $wecome_info = "<a href='ad_main.php'><u>广告管理</u></a>::获取JS";
    $win = new OxWindow();
    $win->Init();
    $win->AddTitle("以下为选定广告的JS调用代码：");
    $winform = $win->GetWindow("hand",$showhtml);
    $win->Display();
    exit();
}
else if($dopost=='testjs')
{
    echo "<script src='{$cfg_phpurl}/ad_js.php?aid=$aid&nocache=1' language='javascript'></script>";
    exit();
}
else if($dopost=='saveedit')
{
    $starttime = GetMkTime($starttime);
    $endtime = GetMkTime($endtime);
    $query = "UPDATE `#@__myad`
     SET
     typeid='$typeid',
     adname='$adname',
     timeset='$timeset',
     starttime='$starttime',
     endtime='$endtime',
     normbody='$normbody',
     expbody='$expbody'
     WHERE aid='$aid'
     ";
    $dsql->ExecuteNoneQuery($query);
    ShowMsg("成功更改一则广告代码！",$ENV_GOBACK_URL);
    exit();
}
$row = $dsql->GetOne("SELECT * FROM `#@__myad` WHERE aid='$aid'");
include DedeInclude('templets/ad_edit.htm');