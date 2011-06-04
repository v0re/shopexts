<?
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Group
*     website              : http://www.boonex.com
* This file is part of Dolphin - Smart Community Builder
*
* Dolphin is free software; you can redistribute it and/or modify it under
* the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the
* License, or  any later version.
*
* Dolphin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
* See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with Dolphin,
* see license.txt file; if not, write to marketing@boonex.com
***************************************************************************/

    require_once("../inc/header.inc.php");

    $GLOBALS['aRequest'] = explode('/', $_GET['r']);

    if ($GLOBALS['aRequest'][1] == 'admin' || $GLOBALS['aRequest'][1] == 'administration')
        $GLOBALS['iAdminPage'] = 1;

    require_once(BX_DIRECTORY_PATH_INC . "design.inc.php");
    require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolModuleDb.php');

	$sName = process_db_input(array_shift($GLOBALS['aRequest']), BX_TAGS_STRIP);

    $oDb = new BxDolModuleDb();
    $GLOBALS['aModule'] = $oDb->getModuleByUri($sName);
    
    if(empty($GLOBALS['aModule']))    
        BxDolRequest::moduleNotFound($sName);
	include(BX_DIRECTORY_PATH_MODULES . $GLOBALS['aModule']['path'] . 'request.php');
?>
