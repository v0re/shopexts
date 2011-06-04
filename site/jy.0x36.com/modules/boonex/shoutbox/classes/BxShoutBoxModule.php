<?php

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

    bx_import('BxDolModuleDb');
    bx_import('BxDolModule');

    require_once( BX_DIRECTORY_PATH_PLUGINS . 'Services_JSON.php' );

    /**
     * Shoutbox module by BoonEx
     *
     * This module allow user to send messages that will show on site's home page. 
     * This is default module and Dolphin can not work properly without this module.
     *
     *
     *
     * Profile's Wall:
     * no wall events
     *
     *
     *
     * Spy:
     * no spy events
     *
     *
     *
     * Memberships/ACL:
     * use shoutbox - BX_USE_SHOUTBOX
     *
     *
     *
     * Service methods:
     *
     * Generate shoutbox window. 
     * @see BxShoutBoxModule::serviceGetShoutBox();
     * BxDolService::call('shoutbox', 'get_shoutbox');
     *
     * Alerts:
     *
     * no alerts here;
     *
     */
    class BxShoutBoxModule extends BxDolModule 
    {
        // contain some module information ;
        var $aModuleInfo;

        // contain path for current module;
        var $sPathToModule;

        // contain logged member's Id;
        var $iMemberId;

        // contain all used templates
        var $aUsedTemplates = array();

        /**
    	 * Class constructor ;
         *
         * @param   : $aModule (array) - contain some information about this module;
         *                  [ id ]           - (integer) module's  id ;
         *                  [ title ]        - (string)  module's  title ;
         *                  [ vendor ]       - (string)  module's  vendor ;
         *                  [ path ]         - (string)  path to this module ;
         *                  [ uri ]          - (string)  this module's URI ;
         *                  [ class_prefix ] - (string)  this module's php classes file prefix ;
         *                  [ db_prefix ]    - (string)  this module's Db tables prefix ;
         *                  [ date ]         - (string)  this module's date installation ;
    	 */
    	function BxShoutBoxModule(&$aModule) 
        {
            parent::BxDolModule($aModule);

            // prepare the location link ;
            $this -> sPathToModule  = BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri();
            $this -> aModuleInfo    = $aModule;
            $this -> iMemberId 		= getLoggedId();
        }

        /**
         * Write new message;
         * 
         * @return text (error message if have some troubles)
         */
        function actionWriteMessage()
        {
            if( $this -> isShoutBoxAllowed($this -> iMemberId, true) ) {

            	$sMessage = ( isset($_POST['message']) )
                    ? strip_tags( trim($_POST['message']) )
                    : '';

                if($sMessage) {
                	// process smiles;
                    if($this -> _oConfig -> bProcessSmiles) {
                        $sMessage = $this -> _processSmiles($sMessage);
                    }

					// create new message;
                    $this -> _oDb -> writeMessage($sMessage
                    	, $this -> iMemberId, ip2long( getVisitorIP() ) );

                    if($this -> _oConfig -> iAllowedMessagesCount) {
	                     // delete superfluous messages;
	                    $iMessagesCount = $this -> _oDb -> getMessagesCount();
	                    if($iMessagesCount > $this -> _oConfig -> iAllowedMessagesCount) {
	                        $this -> _oDb -> deleteMessages($iMessagesCount - $this -> _oConfig -> iAllowedMessagesCount);
	                    }
	 				}
                }
                else {
                    echo _t('_bx_shoutbox_message_empty');
                }
            }
            else {
            	echo _t('_bx_shoutbox_access_denied');
            }
        }

        /**
         * Block message
         * 
         * @param $iMessageId integer
         * @return void
         */
        function actionBlockMessage($iMessageId = 0)
        {
        	$sCallBackMessage = '';
        	$iMessageId = (int) $iMessageId;

        	//check membership level
        	if( $this -> isShoutBoxBlockIpAllowed($this -> iMemberId) && $iMessageId > 0 ) {
        		//get message info
        		$aMessageInfo = $this -> _oDb -> getMessageInfo($iMessageId);
        		if(!$aMessageInfo) {
        			$sCallBackMessage = _t('_Error Occured');
        		}
        		else {
        			//block user IP
        			bx_block_ip((int)$aMessageInfo['IP'], $this -> _oConfig -> iBlockExpirationSec, _t('_bx_shoutbox_ip_blocked'));

        			$this -> _oDb -> deleteMessagesByIp($aMessageInfo['IP']);	
        		}
        	}
        	else {
        		$sCallBackMessage = _t('_bx_shoutbox_access_denied');
        	}

			echo $sCallBackMessage;
        }

        /**
         * Delete message
         * 
         * @param $iMessageId integer
         * @return void
         */
        function actionDeleteMessage($iMessageId = 0)
        {
        	$sCallBackMessage = '';
        	$iMessageId = (int) $iMessageId;

        	//check membership level
        	if( $this -> isShoutBoxDeleteAllowed($this -> iMemberId) && $iMessageId > 0 ) {
				if( $this -> _oDb -> deleteMessage($iMessageId) ) {
					$this -> isShoutBoxDeleteAllowed($this -> iMemberId, true);
				}
				else {
					$sCallBackMessage = _t('_Error Occured');
				}
        	}
        	else {
        		$sCallBackMessage = _t('_bx_shoutbox_access_denied');
        	}

        	echo $sCallBackMessage;
        }

        /**
         * Get all latest messages;
         *
         * @param  : $iLastMessageId (integer) - last message's Id;
         * @return : (text) - in JSON format;
         */
        function actionGetMessages($iLastMessageId = 0)
        {
        	$iLastMessageId		= (int) $iLastMessageId;

            $oJsonParser        = new Services_JSON();
            $sMessages          = $this -> _getLastMessages($iLastMessageId);
            $iLastMessageId     = $this -> _oDb -> getLastMessageId();

            $aRetArray = array(
                'messages'          => $sMessages,
                'last_message_id'   => $iLastMessageId,
            );

            //return result
            echo $oJsonParser -> encode($aRetArray);
        }

        /**
         * Generate shoutbox's admin page ;
         *
         * @return : (text) - Html presentation data ;
         */
        function actionAdministration()
        {
        	$GLOBALS['iAdminPage'] = 1;

            if( !isAdmin() ) {
                header('location: ' . BX_DOL_URL_ROOT);
            }

            $aLanguageKeys = array(
                'settings' => _t('_bx_shoutbox_settings'),
            );

            // try to define globals category number;
            $iId = $this-> _oDb -> getSettingsCategory('shoutbox_update_time');
            if(!$iId) {
                $sContent = MsgBox( _t('_Empty') );
            }
            else {
                bx_import('BxDolAdminSettings');

                $mixedResult = '';
                if(isset($_POST['save']) && isset($_POST['cat'])) {
                    $oSettings = new BxDolAdminSettings($iId);
                    $mixedResult = $oSettings -> saveChanges($_POST);
                }

                $oSettings = new BxDolAdminSettings($iId);
                $sResult = $oSettings->getForm();

                if($mixedResult !== true && !empty($mixedResult))
                    $sResult = $mixedResult . $sResult;

                $sContent = $GLOBALS['oAdmTemplate'] 
                        -> parseHtmlByName( 'design_box_content.html', array('content' => $sResult) );
            }

            $this -> _oTemplate-> pageCodeAdminStart();
            echo $this -> _oTemplate -> adminBlock ($sContent, $aLanguageKeys['settings']);
            $this -> _oTemplate->pageCodeAdmin ( _t('_bx_shoutbox_module') );
        }

        /**
         * Generate the shoutbox window;
         */
        function serviceGetShoutBox()
        {
        	echo $this -> _oTemplate -> getShoutboxWindow($this -> sPathToModule
        			, $this -> _oDb -> getLastMessageId(), $this -> _getLastMessages());
        }

        /**
         * Get list of last messages;
         *
         * @param  :  $iLastId (integer) - last message's Id;
         * @return : (text) - html presentation data;
         */
        function _getLastMessages($iLastId = 0)
        {
        	return $this -> _oTemplate -> getProcessedMessages($this -> _oDb -> getMessages($iLastId)
	        		, $this -> isShoutBoxDeleteAllowed($this -> iMemberId)
	        		, $this -> isShoutBoxBlockIpAllowed($this -> iMemberId));
        }

		/**
		 * Define all membership actions
		 * 
		 * @return void
		 */
        function _defineActions() 
        {
            defineMembershipActions( 
            	array('shoutbox use', 'shoutbox delete messages', 'shoutbox block by ip') 
            );
        }

        /**
         * Process all  smiles codes;
         *
         * @param  : $sText (string) ;
         * @return : (string) - processed text; 
         */
        function _processSmiles($sText)
        {
            foreach($this -> _oConfig -> aSmiles as $sCode => $sImgPath)
            {
                $sImgPath = $this -> _oTemplate -> getIconUrl($sImgPath);
                $sText = str_replace($sCode, "<img border=\"0\" alt=\"{$sCode}\" src=\"{$sImgPath}\" />", $sText);
            }

            return $sText;
        }

        /**
         * Check membership level for current type if users (use shotbox);
         *
         * @param : $iMemberId (integer) - member's Id;
         * @param : $isPerformAction (boolean) - if isset this parameter that function will amplify the old action's value;
         * @return boolean
         */
        function isShoutBoxAllowed($iMemberId, $isPerformAction = false) 
        {
			if( isAdmin() ) {
        		return true;
        	}

			if( !defined('BX_SHOUTBOX_USE') ) {
				$this -> _defineActions();
			}

			$aCheck = checkAction($iMemberId, BX_SHOUTBOX_USE, $isPerformAction);
			return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
        }

        /**
         * Check membership level for current type if users (delete any of messages in shotbox);
         *
         * @param : $iMemberId (integer) - member's Id;
         * @param : $isPerformAction (boolean) - if isset this parameter that function will amplify the old action's value;
         * @return boolean
         */
        function isShoutBoxDeleteAllowed($iMemberId, $isPerformAction = false) 
        {
        	if( isAdmin() ) {
        		return true;
        	}

			if( !defined('BX_SHOUTBOX_DELETE_MESSAGES') ) {
				$this -> _defineActions();
			}

			$aCheck = checkAction($iMemberId, BX_SHOUTBOX_DELETE_MESSAGES, $isPerformAction);
			return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
        }

        /**
         * Check membership level for current type if users (block by ip);
         *
         * @param : $iMemberId (integer) - member's Id;
         * @param : $isPerformAction (boolean) - if isset this parameter that function will amplify the old action's value;
         * @return boolean
         */
        function isShoutBoxBlockIpAllowed($iMemberId, $isPerformAction = false) 
        {
        	if( isAdmin() ) {
        		return true;
        	}

			if( !defined('BX_SHOUTBOX_BLOCK_BY_IP') ) {
				$this -> _defineActions();
			}

			$aCheck = checkAction($iMemberId, BX_SHOUTBOX_BLOCK_BY_IP, $isPerformAction);
			return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
        }
    }
