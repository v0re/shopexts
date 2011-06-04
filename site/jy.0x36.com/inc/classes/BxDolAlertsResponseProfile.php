<?
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

bx_import('BxDolTags');
bx_import('BxDolAlerts');
bx_import('BxDolProfilesController');

class BxDolAlertsResponseProfile extends BxDolAlertsResponse {
	function BxDolAlertsResponseProfile() {
	    parent::BxDolAlertsResponse();
	}

    function response($oAlert) {
    	$sMethodName = '_process' . ucfirst($oAlert->sUnit) . str_replace(' ', '', ucwords(str_replace('_', ' ', $oAlert->sAction)));
    	if(method_exists($this, $sMethodName))
            $this->$sMethodName($oAlert);
    }

    function _processProfileBeforeJoin($oAlert) {}

    function _processProfileJoin($oAlert) {
        $oPC = new BxDolProfilesController();

        //--- reparse profile tags
        $oTags = new BxDolTags();
        $oTags->reparseObjTags('profile', $oAlert->iObject);

        //--- send new user notification
        if(getParam('newusernotify') == 'on' )
            $oPC->sendNewUserNotify($oAlert->iObject);        

        //--- Promotional membership
        if(getParam('enable_promotion_membership') == 'on') {
            $iMemershipDays = getParam('promotion_membership_days');
            setMembership($oAlert->iObject, MEMBERSHIP_ID_PROMOTION, $iMemershipDays, true);
        }
    }

    function _processProfileBeforeLogin($oAlert) {}

	function _processProfileLogin($oAlert) {}

	function _processProfileLogout($oAlert) {}
	
	function _processProfileEdit ($oAlert) {
		//--- reparse profile tags
        $oTags = new BxDolTags();
        $oTags->reparseObjTags('profile', $oAlert->iObject);
	}
}
?>