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
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );

check_logged();

$sSys = isset($_REQUEST['sys']) ? $_REQUEST['sys'] : '';
$sAction = isset($_REQUEST['action']) && preg_match ('/^[A-Za-z_-]+$/', $_REQUEST['action']) ? $_REQUEST['action'] : '';
$iId = (int)$_REQUEST['id'];

bx_import ('BxTemplCmtsView');
$aSystems =& BxDolCmts::getSystems ();

if ($sSys && $sAction && $iId && isset($aSystems[$sSys])) {

    $oCmts = null;
    if ($aSystems[$sSys]['class_name']) {
        require_once (BX_DIRECTORY_PATH_ROOT . $aSystems[$sSys]['class_file']);
        $sClassName = $aSystems[$sSys]['class_name'];
        $oCmts = new $sClassName($sSys, $iId, true);
    } else {        
        $oCmts = new BxTemplCmtsView($sSys, $iId, true);
    }
    
    $sMethod = 'action' . $sAction;
    echo $oCmts->$sMethod();

}

?>
