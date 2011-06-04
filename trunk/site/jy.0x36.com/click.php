<?php

/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -----------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2006 BoonEx Group
*     website              : http://www.boonex.com/
* This file is part of Dolphin - Smart Community Builder
*
* Dolphin is free software. This work is licensed under a Creative Commons Attribution 3.0 License. 
* http://creativecommons.org/licenses/by/3.0/
*
* Dolphin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
* See the Creative Commons Attribution 3.0 License for more details. 
* You should have received a copy of the Creative Commons Attribution 3.0 License along with Dolphin, 
* see license.txt file; if not, write to marketing@boonex.com
***************************************************************************/

require_once( 'inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'db.inc.php' );

$ID = urldecode($_SERVER['QUERY_STRING']);
$ID = (int)$ID;

$bann_arr = db_arr("SELECT `ID`, `Url` FROM `sys_banners` WHERE `ID` = $ID LIMIT 1");
$ID = (int)$bann_arr['ID'];
$Url = $bann_arr['Url'];

if ( $ID > 0 )
{
	db_res("INSERT INTO `sys_banners_clicks` SET `ID` = $ID, `Date` = ".time().", `IP` = '". $_SERVER['REMOTE_ADDR']. "'", 0);

	header ("HTTP/1.1 301 Moved Permanently");
	header ("Location: $Url");
	exit;
}
else
{
	echo "No such link";
}

?>