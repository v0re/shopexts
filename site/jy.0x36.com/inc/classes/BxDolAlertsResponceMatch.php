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

require_once( BX_DIRECTORY_PATH_INC . "match.inc.php");
bx_import('BxDolAlerts');
bx_import('BxDolDb');
bx_import('BxDolEmailTemplates');

class BxDolAlertsResponceMatch extends BxDolAlertsResponse
{
    function response($oAlert) 
    {
        $iRecipientId = $oAlert->iObject;

        if ($oAlert->sUnit == 'profile') 
        {
            switch ($oAlert->sAction) 
            {
                case 'join':
                case 'edit':
                    $this->_checkProfileMatch($iRecipientId, $oAlert->sAction);
                    break;
                    
                case 'change_status':
                    $this->_profileChangeStatus();
                    break;
                    
                case 'delete':
                    $this->_profileDelete($iRecipientId);
                    break;
            }
        }
    }
    
    function _checkProfileMatch($iProfileId, $sAction)
    {
        $aProfile = getProfileInfo($iProfileId);

        if ($aProfile['Status'] == 'Active' && ($aProfile['UpdateMatch'] || $sAction == 'join'))
        {
            $oDb = new BxDolDb();
            
            // clear field "UpdateMatch"
            $oDb->query("UPDATE `Profiles` SET `UpdateMatch` = 0 WHERE `ID`= $iProfileId");
            
            // clear cache
            $oDb->query("DELETE FROM `sys_profiles_match`");
            
            // get send mails
            $aSendMails = $oDb->getRow("SELECT `profiles_match` FROM `sys_profiles_match_mails` WHERE `profile_id` = $iProfileId");
            $aSend = !empty($aSendMails) ? unserialize($aSendMails['profiles_match']) : array();
                
            $aProfiles = getMatchProfiles($iProfileId);
            foreach ($aProfiles as $iProfId)
            {
                if (!isset($aSend[(int)$iProfId]))
                {
                    $oEmailTemplate = new BxDolEmailTemplates();
                    $aMessage = $oEmailTemplate->parseTemplate('t_CupidMail', array(
                        'StrID' => $iProfId,
                        'MatchProfileLink' => getProfileLink($iProfileId)
                    ), $iProfId);
                    $aProfile = getProfileInfo($iProfId);
                
                    if (!empty($aProfile) && $aProfile['Status'] == 'Active')                         
                        $oDb->query("INSERT INTO `sys_sbs_queue`(`email`, `subject`, `body`) VALUES('" . $aProfile['Email'] . "', '" . process_db_input($aMessage['subject'], BX_TAGS_NO_ACTION, BX_SLASHES_NO_ACTION) . "', '" . process_db_input($aMessage['body'], BX_TAGS_NO_ACTION, BX_SLASHES_NO_ACTION) . "')");
                        
                    $aSend[(int)$iProfId] = 0;
                }
            }
            
            if (empty($aSendMails))
                $oDb->query("INSERT INTO `sys_profiles_match_mails`(`profile_id`, `profiles_match`) VALUES($iProfileId, '" . serialize($aSend) . "')");
            else
                $oDb->query("UPDATE `sys_profiles_match_mails` SET `profiles_match` = '" . serialize($aSend) . "' WHERE `profile_id` = $iProfileId");
        }
    }
    
    function _profileDelete($iProfileId)
    {
        $oDb = new BxDolDb();
        
        $oDb->query("DELETE FROM `sys_profiles_match`");
        $oDb->query("DELETE FROM `sys_profiles_match_mails` WHERE `profile_id` = $iProfileId");
    }
    
    function _profileChangeStatus()
    {
        $oDb = new BxDolDb();
        $oDb->query("DELETE FROM `sys_profiles_match`");
    }
}

?>
