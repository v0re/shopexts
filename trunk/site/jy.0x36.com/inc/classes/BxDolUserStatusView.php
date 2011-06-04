<?php

class BxDolUserStatusView 
{
    // contain path to the site's home URL;
	var $sHomeUrl;

    // contain all possible statuses for member;
    var $aStatuses;

    /**
     * Class constructor ;
     */
    function BxDolUserStatusView() 
    {
        $this -> sHomeUrl = BX_DOL_URL_ROOT;
        $this -> aStatuses = array(

            'online'  => array(
                'icon8'  => 'sys_status_online.png',
                'icon'  => 'status_online.png',
                'title' => _t('_Online'),
            ),

            'offline' => array(
                'icon8'  => 'sys_status_offline.png',
                'icon'  => 'status_offline.png',
                'title' => _t('_Offline'),
            ),

            'away' => array(
                'icon8'  => 'sys_status_away.png',
                'icon'  => 'status_away.png',
                'title' => _t('_Away'),
            ),

            'busy'    => array(
                'icon8'  => 'sys_status_busy.png',
                'icon'  => 'status_busy.png',
                'title' => _t('_Busy'),
            ),
        );
    }

    /**
     * Function will cheack recived status into registered statuses array;
     *
     * @param  : $sStatus (string) - member's status;
     * @return : (integer) - 1 if status was registered or 0 if not;
     */
    function getRegisteredStatus($sStatus)
    {
        if( array_key_exists($sStatus, $this -> aStatuses) ) {
            return 1;
        }
        else {
            return 0;
        }
    }

    /**
     * Function will return member's status icon ;
     *
     * @param  : $iMemberId (integer) - logged member's Id ;
     * @return : (string) - icon's name;
     */
	function getStatusIcon($iMemberId, $sSize = 'icon') 
    {
        // default value;
        $sMemberIcon = $this -> aStatuses['offline'][$sSize];

        if ( get_user_online_status($iMemberId) ) {
            $sMemberStatus = $GLOBALS['MySQL']->fromMemory ("user_status.$iMemberId", 'getOne', "SELECT `UserStatus` FROM `Profiles` WHERE `ID` = {$iMemberId}");
            if( array_key_exists($sMemberStatus, $this -> aStatuses) ) {
                $sMemberIcon = $this -> aStatuses[$sMemberStatus][$sSize];
            }
        }

		return $sMemberIcon;
	}

    /**
     * Function will return member's status icon ;
     *
     * @param  : $iMemberId (integer) - logged member's Id ;
     * @return : (string) - icon's name;
     */
	function getStatus($iMemberId) 
    {
        // default value;
        $sMemberStatus = $this -> aStatuses['offline']['title'];

        if ( get_user_online_status($iMemberId) ) {
            // get profile status;
            $sMemberStatus = $GLOBALS['MySQL']->fromMemory ("user_status.$iMemberId", 'getOne', "SELECT `UserStatus` FROM `Profiles` WHERE `ID` = {$iMemberId}");
        }

		return $sMemberStatus;
	}

    /**
     * Function will generate field for input memeber's status text;
     *
     * @param  : $iMemberID (integer) - logged member's Id;
     * @return : Html presentation data;
     */
	function getStatusField($iMemberId) 
    {
   		global $oSysTemplate;

        $aMemberInfo    = getProfileInfo($iMemberId);
        $sStatusMessage = process_line_output($aMemberInfo['UserStatusMessage']);

        $aTemplateKeys = array (
            'status_message' =>  $sStatusMessage,
        );

        return $oSysTemplate -> parseHtmlByName('member_menu_status_text_field.html', $aTemplateKeys);
   	}

	/**
     * Function will generate text box for changes member's status text; 
     *
     * @param  : $iMemberId (integer) - logged member's id;
     * @return : Html presentation data;
     */
    function getStatusPageLight($iMemberId) 
    {
		global $oSysTemplate;

        $aProfileInfo = getProfileInfo($iMemberId);
        $aTemplateKeys = array (
            'status_message' => $aProfileInfo['UserStatusMessage'],
        );

        return $oSysTemplate -> parseHtmlByName( 'user_status_light.html', $aTemplateKeys );
    }
}

?>
