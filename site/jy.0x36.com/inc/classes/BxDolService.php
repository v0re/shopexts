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

require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolRequest.php');
require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolModuleDb.php');

/**
 * Service calls to modules' methods.
 *
 * The class has one static method is needed to make service calls 
 * to module's methods from the Dolphin's core or the other modules.
 *
 *
 * Example of usage:
 * BxDolService::call('payment', 'get_add_to_cart_link', array($iVendorId, $mixedModuleId, $iItemId, $iItemCount));
 *
 *
 * Memberships/ACL:
 * Doesn't depend on user's membership.
 *
 *
 * Alerts:
 * no alerts available
 *
 */
class BxDolService {
    function call($mixed, $sMethod, $aParams = array(), $sClass = 'Module') {
        $oDb = new BxDolModuleDb();

        $aModule = array();
        if(is_string($mixed))
            $aModule = $oDb->getModuleByUri($mixed);
        else
            $aModule = $oDb->getModuleById($mixed);

        return empty($aModule) ? '' : BxDolRequest::processAsService($aModule, $sMethod, $aParams, $sClass);
    }
}
?>
