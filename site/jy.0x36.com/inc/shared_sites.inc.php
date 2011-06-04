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

require_once(BX_DIRECTORY_PATH_INC . 'db.inc.php');

define('SHARED_SITES_TABLE', 'sys_shared_sites');

function getSitesArray ($sLink) {

    $aSites = $GLOBALS['MySQL']->fromCache ('sys_shared_sites', 'getAllWithKey', "SELECT `ID` as `id`, `URL` as `url`, `Icon` as `icon`, `Name` FROM `" . SHARED_SITES_TABLE . "`", 'Name');

    $sLink = htmlentities(($sLink));

    foreach ($aSites as $sKey => $aValue)
    	$aSites[$sKey]['url'] .= $sLink;
    
	return $aSites;
}

?>
