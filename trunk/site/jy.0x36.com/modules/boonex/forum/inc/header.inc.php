<?php
/***************************************************************************
*                            Orca Interactive Forum Script
*                              ---------------
*     Started             : Fr Nov 10 2006
*     Copyright        : (C) 2007 BoonEx Group
*     Website             : http://www.boonex.com
* This file is part of Orca - Interactive Forum Script
*
* Orca is free software; you can redistribute it and/or modify it under 
* the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the 
* License, or any later version.      
*
* Orca is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
* See the GNU General Public License for more details. 
* You should have received a copy of the GNU General Public License along with Orca, 
* see license.txt file; if not, write to marketing@boonex.com
*
***************************************************************************/

global $gConf;

$gConf['ver'] = 'Orca-v.2.0.2';
$gConf['def_title'] = 'Forum';
$path = ''; // path to orca files


/**
 * forum tweaks
 */ 
$gConf['date_format'] = '%b %d, %Y %H:%i'; // time/date format
$gConf['topics_per_page'] = 10; // topics per page
$gConf['topics_desc_len'] = 128;
$gConf['live_tracker_desc_len'] = 128;
$gConf['edit_timeout'] = 3600; // edit timeout in sec

$gConf['email']['sender'] = ''; // email sender

$gConf['user']['admin'] = 'admin'; // admin user

$gConf['min_point'] = -4; // min points to hide post automatically

$gConf['online'] = 72000; // online user timeout (seconds) default: 20 min 

$xsl_mode = isset($_GET['xsl_mode']) && $_GET['xsl_mode'] ? $_GET['xsl_mode'] : (isset($_COOKIE['xsl_mode']) ? $_COOKIE['xsl_mode'] : '');
if (preg_match("/^\w+$/",$xsl_mode))
{
        $gConf['xsl_mode'] = $xsl_mode;
        setcookie ('xsl_mode', $xsl_mode);
}
else
{
        $gConf['xsl_mode'] = 'auto'; // client, server
}

// mod rewrite configuration, also make changes in layout/base/xsl/rewrite.xsl, js/BxHistory.js and .htaccess
$gConf['rewrite']['cat'] = 'group/%s.htm';
$gConf['rewrite']['forum'] = 'forum/%s-%d.htm';
$gConf['rewrite']['topic'] = 'topic/%s.htm';
$gConf['rewrite']['user'] = 'user/%s.htm';
$gConf['rewrite']['rss_forum'] = 'rss/forum/%s.htm';
$gConf['rewrite']['rss_topic'] = 'rss/topic/%s.htm';
$gConf['rewrite']['rss_user'] = 'rss/user/%s.htm';
$gConf['rewrite']['rss_all'] = 'rss/all.htm';

$aPathInfo = pathinfo(__FILE__);
require_once ($aPathInfo['dirname'] . '/../integrations/' . BX_ORCA_INTEGRATION . '/config.php');

?>
