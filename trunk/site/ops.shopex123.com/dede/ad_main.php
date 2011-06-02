<?php
/**
 * 广告管理
 *
 * @version        $Id: ad_main.php 1 8:26 2010年7月12日Z tianya $
 * @package        DedeCMS.Administrator
 * @copyright      Copyright (c) 2007 - 2010, DesDev, Inc.
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require_once(dirname(__FILE__).'/config.php');
require_once(DEDEINC.'/datalistcp.class.php');
require_once(DEDEINC.'/common.func.php');
setcookie('ENV_GOBACK_URL',$dedeNowurl,time()+3600,'/');

$sql = "SELECT ad.aid,ad.tagname,tp.typename,ad.adname,ad.timeset,ad.endtime
FROM `#@__myad` ad LEFT JOIN `#@__arctype` tp on tp.id=ad.typeid
ORDER BY ad.aid desc";

$dlist = new DataListCP();
$dlist->SetTemplet(DEDEADMIN."/templets/ad_main.htm");
$dlist->SetSource($sql);
$dlist->display();

function TestType($tname)
{
    if($tname=="")
    {
        return "所有栏目";
    }
    else
    {
        return $tname;
    }
}

function TimeSetValue($ts)
{
    if($ts==0)
    {
        return "不限时间";
    }
    else
    {
        return "限时标记";
    }
}