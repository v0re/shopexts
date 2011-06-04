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

    /**
     * Member menu
     *
     * Related classes: 
     *  @see BxBaseMemberMenu        - member menu base representation 
     *  @see BxTemplMemberMenu       - member menu template representation 
     *  
     * To add a new menu item, you need to navigate to "admin/member_menu_compose.php" through web interface where you can do it.
     * If you need to add a specific menu item, you will find the whole list of menu properties below.
     * Table structure - `sys_menu_member`;
     *
     * `Caption`     - menu caption;
     * `Name`        - menu item name (to be used in the Admin Panel);
     * `Icon`        - menu icon (to be displayed in the menu);
     * `Link`        - URL assigned to the menu item;
     * `Script`      - javascript code (an onclick event will be created);
     * `Eval`        - system field (you can find its explanation below);
     * `PopupMenu`   - use it when you need to create a drop-down list for the menu item (below you will find an example);
     * `Order`       - sorting;
     * `Active`      - this field can have "0" or "1" which stands for "invisible" and "visible" respectively;
     * `Editable`    - this field can have "0" or "1" thus being ineditable or editable respectively in the Admin Panel;
     * `Deletable`   - this field can have "0" or "1" thus being undeletable or deletable (respectively) through the Admin Panel web interface;
     * `Target`      - the "target" attribute for the link (for example, "_blank" will open the link in a new window);
     * `Position`    - menu item position (it can have one of the two values: 'top' to place the menu item in the left position on the menu, 'top_extra' to place the item in the right position on the menu);
     * `Type`        - menu type (it can be one of these three types: 'link','system','linked_item'). 'link' - is an ordinary link, 'system' - this menu item will be created by the function specified in the `Eval` field` ????, 'linked_item' - a child element of the menu item (see an example below);
     * `Parent`      - this field specifies the parent of the menu item (it is usually used together with the `linked_item` menu type),
     * `Bubble`      - a special field which enables drawing different events notifiers next to the menu item (see an example below),
     * `Description` - menu item description (when hovering over the menu item with a mouse, a block with this description will be displayed below the menu item; this field is language keys based)
     *
     *
     * Example of usage:
     * 
     * 1. Example of using the `Eval` field:
     *      - for example, you need to append a member's ID to the URL specified in the `Link` field;
     *          so, you need to change the URL in the `Link` field in the following way:
     *          http://my.com?ID={evalResult} (here the marker {evalResult} will be replaced with the function output
     *          Then you create a function in the `Eval` field:
     *          return (isset($_COOKIE['memberID']) && isMember() ) ? (int) $_COOKIE['memberID'] : 0;        
     *
     * 2. Example of using the `PopupMenu` field
     *        - assume you need to create a `News` menu item and create a drop-down block in it which would display some information.
     *              To do so, you first need to run an SQL query which will insert data into the appropriate fields:
     *            ...`Caption` = '_News', `Link` = 'news.php'... and then specify some code in the `PopupMenu` field which will create the contents of the current menu:
     *              
     *              //example of PHP code into `PopupMenu` field;  
     *               require_once('News.php');
     *               return getNewsSubMenu();
     *                   
     * 3. Example of using the `Bubble` field (system notifiers)
     *      - assume you need your menu item to display the number of mails in real time (i.e. if someone has written you a mail, you would be notified immediately),
     *      to do so, you need to create a menu item as in the previous example and create a function in the `Bubble` field which will request the mail script for any changes: 
     *
     *       bx_import('BxTemplMailBox');
     *       // return list of new messages ;
     *       $aRetEval= BxTemplMailBox::get_member_menu_bubble_new_messages({ID}, {iOldCount});
     * 
     *       where $aRetEval is an array which will be processed  (it shouldn't be renamed!!!)
     *       the first parameter {ID} is Profile's ID, the second parameter is the number of mails retrieved during the previous iteration.
     *       
     *       the function will return an array looking like this:
     *
     *           $aRetEval = array(
     *               'count'     => $iNewMessages,
     *               'messages'  => $aNotifyMessages,
     *           );
     *           
     *       where 'count' is the whole number of mails, 'messages' is an array of messages;
     *       @see BxTemplMailBox::get_member_menu_bubble_new_messages;
     *
     * 4. Example of using the 'linked_item' type field
     *       - For example, you need to append your menu item to already existing one, for example to the `profile` menu item
     *       To do so, specify the name of your module in the `Name` field (for example 'maps'). Then put a PHP code in the `Eval` field;
     *       it will form the content of your menu item ( @see BxDolService::serviceGetMemberMenuLink )
     *       in the `Parent` field specify the ID of the item you need to append your item to and specify 'linked_item' in the `Type` field
     *
     * Memberships/ACL:
     * no levels
     *
     *
     * 
     * Alerts:
     * no alerts
     */
    class BxDolMemberMenu 
    {
        // contain all registered menu's bubbles;
        var $sBubbles = null;

        // contain current menu possition (allowed values : top, bottom, fixed)
        var $sMemberMenuPosition = null;

        var $iBubblesUpdateTime  = 30000; // in milliseconds;

        var $iNotifyDestroyTime  = 3000; // in milliseconds;

        // page that will procces all ajax queries from member menu ;
        var $sQueryPageReciver = 'member_menu_queries.php';

        var $sBubblePrefix = 'bubble_';
        var $oCacheObject;

        var $iKeysFileTTL = 600; //cache life time
		var $sMenuCacheFile = 'sys_menu_member';
		var $sMenuMemberKeysCache = 'mm_sys_menu_member_keys_';

        /**
         * Class constructor;
         *
         */
        function BxDolMemberMenu()
        {
            $this -> sMemberMenuPosition = ( isset($_COOKIE['menu_position']) ) 
                ? $_COOKIE['menu_position']
                : getParam( 'ext_nav_menu_top_position' );
        }

        /**
         * Function will generate extra sub menu item;
         *
         * @param : $aLinkInfo (array);
                ['item_link']       - (string) module's URL;
                ['item_onclick']    - (string) if isset this value that script will generate onclick param into link;
                ['item_title']      - (string) module's title;
                ['extra_info']      - (string) module's extra info (fore exmaple: number of polls);
                ['item_img_src']    - (string) module's icon's URL;
                ['item_img_alt']    - (string) module's icon's alt text;
                ['item_img_width']  - (integer) module's icon's width;
                ['item_img_height'] - (integer) module's icon's height;
         */
        function getGetExtraMenuLink(&$aLinkInfo)
        {
            global $oSysTemplate;

            $aTemplateKeys = array(
                'bx_if:item_img' => array (
                    'condition' =>  ($aLinkInfo['item_img_src']),
                    'content'   => array (
                        'item_img_src'      => $aLinkInfo['item_img_src'],
                        'item_img_alt'      => $aLinkInfo['item_title'],
                        'item_img_width'    => ($aLinkInfo['item_img_width'])  ? $aLinkInfo['item_img_width']  : 16,
                        'item_img_height'   => ($aLinkInfo['item_img_height']) ? $aLinkInfo['item_img_height'] : 16,
                    ),
                ),

                'item_link'     => $aLinkInfo['item_link'],
                'item_onclick'  => ($aLinkInfo['item_onclick']) ? ' onclick="' . $aLinkInfo['item_onclick'] . ';return false"' : null,
                'item_title'    => $aLinkInfo['item_title'],

                'extra_info'    => ($aLinkInfo['extra_info']) ? ' (' . $aLinkInfo['extra_info'] . ')' : null,
            );

            $sOutputCode = $oSysTemplate -> parseHtmlByName( 'member_menu_sub_item.html', $aTemplateKeys );
            return $sOutputCode;
        }

        /**
         * Function will generate description window for menu's items;
         *
         * @param  : $sDescription (string) - item's description;
         * @return : Html presentation data;
         */
        function getDescriptionWindow($sDescription)
        {
            global $oSysTemplate;

            $aTemplateKeys = array(
                'description' => $sDescription,
            );

            $sOutputCode = $oSysTemplate -> parseHtmlByName( 'member_menu_descr_window.html', $aTemplateKeys );
            return $sOutputCode;
        }

        /**
         * Get instance of cache object
         * 
         * @return object
         */
	    function getCacheObject()
	    {
	        if ($this -> oCacheObject != null) {
	            return $this->oCacheObject;
	        } else {
	            $sEngine = getParam('sys_mm_cache_engine');  
	            $this->oCacheObject = bx_instance ('BxDolCache'.$sEngine);
	            if (!$this->oCacheObject->isAvailable())
	                $this->oCacheObject = bx_instance ('BxDolCacheFile');
	            return $this->oCacheObject;
	        }
	    }

        /**
         * Parse member menu structure 
         * 
         * @param $aMemberInfo array
         * @param $aMenuStructure array
         * @return text
         */
        function _parseStructure($aMemberInfo, $aMenuStructure)
        {
			global $oSysTemplate, $oFunctions;

			if(!$aMenuStructure) {
				return;
			}

           	$oCache = $this -> getCacheObject();

			$oPermalinks   = new BxDolPermalinks();
    		$oUserStatus   = new BxDolUserStatusView();
            $sMemberStatus = $oUserStatus -> getStatusIcon($aMemberInfo['ID']) ;

			$aReplaced = $aDefinedMenuItems = array();
			$memberID = $aMemberInfo['ID'];
			$iIndex = 0;
			$sStartPosition = '';

			//-- process menu structure --//
			$aDefinedMenuItems = $oCache->getData(
				$this -> getCacheKey($aMemberInfo['ID']), $this -> iKeysFileTTL);

			if(!$aDefinedMenuItems) {
				foreach($aMenuStructure AS $sPosition => $aMenuItems )
	    		{
	    			foreach($aMenuItems as $iKey => $aItems)
	    			{
	    			    foreach($aItems AS $sMenuKey => $sValue )
	    				{
	    					if ( $sMenuKey != 'PopupMenu' && $sMenuKey != 'linked_items' ) {
	                            if ( $sMenuKey == 'Caption' ) {
	        						$aReplaced[$sPosition][$iKey][$sMenuKey] = $oFunctions -> markerReplace($aMemberInfo, $sValue, $aItems['Eval'], true);
	        					}
	                            else {
	        						$aReplaced[$sPosition][$iKey][$sMenuKey] = $oFunctions -> markerReplace($aMemberInfo, $sValue, $aItems['Eval']);
	                            }
	                        }
	                        else {
	                            $aReplaced[$sPosition][$iKey][$sMenuKey] = $sValue;
	                        }
	    				}
	
	    				if($sStartPosition != $sPosition) {
	    					$iIndex = 0;
	    					$sStartPosition = $sPosition;
	    				}
	
	    				//-- process next --//
						// collect the link;
	    				if ( $aReplaced[$sPosition][$iKey]['Type'] == 'link' 
	    						&& $aReplaced[$sPosition][$iKey]['Caption'] 
	    						&& ($aReplaced[$sPosition][$iKey]['Link'] || $aReplaced[$sPosition][$iKey]['Script']) ) {
		
								$sPartCaption = $aReplaced[$sPosition][$iKey]['Caption'];
	
								//define some settings for member's block ;
		                        $sMenuImage = ( $aReplaced[$sPosition][$iKey]['Name'] == 'MemberBlock' ) 
		                        	? getTemplateIcon($sMemberStatus) 
		                        	: getTemplateIcon($aReplaced[$sPosition][$iKey]['Icon']);
	
		    					$sItemImgId = ( $aReplaced[$sPosition][$iKey]['Name'] == 'MemberBlock' ) 
		    						? 'current_members_status_img'
		    						 : null;
	
		                        $sReduceImg = ( $aReplaced[$sPosition][$iKey]['Name'] != 'MemberBlock' ) 
		                        	? $sMenuImage 
		                        	: null;
	    	
		                    	if($aReplaced[$sPosition][$iKey]['Caption'] == '{system}') {
									$sMenuImage =  $aReplaced[$sPosition][$iKey]['Icon'] 
		                            	? getTemplateIcon( $aReplaced[$sPosition][$iKey]['Icon']) 
		                            	: getTemplateIcon('spacer.gif');
		
									$sPartCaption = eval($aReplaced[$sPosition][$iKey]['Eval']);
	                        	}
	
	    						if ($aReplaced[$sPosition][$iKey]['Bubble']) {
		                            $sCode  = str_replace('{iOldCount}', 0, $aReplaced[$sPosition][$iKey]['Bubble']);
		                            $sCode  = str_replace('{ID}', $memberID, $sCode);
		
		                            eval($sCode);
		                            $this -> sBubbles .= "\"{$aReplaced[$sPosition][$iKey]['Name']}\" : {count:'{$aRetEval['count']}'}, \n";
	                        	}
	
		                        $aDefinedMenuItems[$sPosition][$iIndex] = array
		    					(
		    						// primary link's info ;
		                            'menu_caption'		 => $aReplaced[$sPosition][$iKey]['Name'] == 'MemberBlock' 
		    														|| $aReplaced[$sPosition][$iKey]['Caption'] == '{system}'
		    															 ? $sPartCaption 
		    															 : null,
	
		    						'menu_link'			 => $aReplaced[$sPosition][$iKey]['Script'] 
		    													? 'javascript:void(0)' 
		    													: $oPermalinks->permalink($aReplaced[$sPosition][$iKey]['Link']),
	
		    						'extended_action'	 => $aReplaced[$sPosition][$iKey]['Script'] 
		    													? 'onclick="' . $aReplaced[$sPosition][$iKey]['Script'] . '"' 
		    													: null,
	
		    						'target'			 => $aReplaced[$sPosition][$iKey]['Target'] == '_blank'
		    														? 'target="_blank"' 
		    														: null,
	
		    						'menu_image'		 => $sMenuImage,
		    						'bubble_box'		 => $aReplaced[$sPosition][$iKey]['Bubble'] 
		                                                        ? $oSysTemplate -> parseHtmlByName( 'member_menu_bubble.html', 
		                                                            array(
		                                                                'extra_styles' => ( $aRetEval['count'] ) ? null : 'style="display:none"', 
		                                                                'count' => $aRetEval['count'], 
		                                                                'bubble_id' => $this -> sBubblePrefix . $aReplaced[$sPosition][$iKey]['Name'])) 
		                                                        : null,
		
		                            'indent'             => ( $this -> sMemberMenuPosition  == 'bottom' )  ? 'menu_item_bottom' : 'menu_item_top',
		                            'item_link_indent'   => ( $this -> sMemberMenuPosition  == 'bottom' )  ? 'bottom_indent' : 'top_indent',
		
		                            // init block id if nedded;
		                            'bx_if:item_id' => array (
		                                'condition' =>  ( $sItemImgId ),
		                                'content'   => array (
		                                    'item_img_id'        => $sItemImgId,
		                                ),
		                            ),
		
		                            'menu_id' => $aReplaced[$sPosition][$iKey]['ID'],
		
		                            // menu description ;
		                            'bx_if:menu_desc' => array(
		                                'condition' => $aReplaced[$sPosition][$iKey]['Description'],
		                                'content'   => array (
		                                   'menu_id'     => $aReplaced[$sPosition][$iKey]['ID'],
		                                   'desc_window' => $this -> getDescriptionWindow( _t($aReplaced[$sPosition][$iKey]['Description']) ),
		                                   'desc_indent' => ( $this -> sMemberMenuPosition  == 'bottom' ) ? 'description_bottom' : 'description_top',
		                                ),
		                            ),
		                        );
	
		                        if($aReplaced[$sPosition][$iKey]['Caption'] == '{system}') {
	                           		 $sPartCaption = _t($aReplaced[$sPosition][$iKey]['Description']);
		                        }

		                        // define top menu's popup section ;
		                        $aContentKeys = array (
		                            'menu_id'     => $aReplaced[$sPosition][$iKey]['ID'],
		
		                            // draw reduce element by top side ;
		                            'bx_if:reduce_element_top' => array (
		                                'condition' =>  ( $this -> sMemberMenuPosition  == 'bottom' ),
		                                'content'   => array (
		                                    'menu_id'               => $aReplaced[$sPosition][$iKey]['ID'],
		                                    'item_link'             => $aReplaced[$sPosition][$iKey]['Link'],
		                                    'extended_action' 		=> empty($aReplaced[$sPosition][$iKey]['extended_action']) ? '' : $aReplaced[$sPosition][$iKey]['extended_action'],
		                                    'cover'                 => 'top_cover',
		                                    'item_name'             => $sPartCaption,
		
		                                    'bx_if:part_image' => array(
		                                        'condition' => ($sReduceImg),
		                                        'content'   => array(
		                                            'item_img_src'          => $sReduceImg,
		                                            'cleared_item_name' => strip_tags($aReplaced[$sPosition][$iKey]['Caption']),
		                                        ),
		                                    ),
		                                ),
		                            ),
		
		                            // draw reduce element by top side ;
		                            'bx_if:reduce_element_bottom' => array (
		                                'condition' =>  ( $this -> sMemberMenuPosition  == 'top' || $this -> sMemberMenuPosition  == 'static' ),
		                                'content'   => array (
		                                    'menu_id'         => $aReplaced[$sPosition][$iKey]['ID'],
		                                    'item_link'       => $aReplaced[$sPosition][$iKey]['Link'],
		                                    'extended_action' => empty($aReplaced[$sPosition][$iKey]['extended_action']) ? '' : $aReplaced[$sPosition][$iKey]['extended_action'],
		                                    'cover'           => 'bottom_cover',
		                                    'item_name'       => $sPartCaption,
		
		                                    'bx_if:part_image' => array(
		                                        'condition' => ($sReduceImg),
		                                        'content'   => array(
		                                            'item_img_src'          => $sReduceImg,
		                                            'cleared_item_name' => strip_tags($aReplaced[$sPosition][$iKey]['Caption']),
		                                        ),
		                                    ),
		                                ),
		                            ),
		                        );
	
								if ( $aReplaced[$sPosition][$iKey]['PopupMenu'] ) {
		                            $aDefinedMenuItems[$sPosition][$iIndex]['menu_link']       = 'javascript:void(0)';
		                            $aDefinedMenuItems[$sPosition][$iIndex]['extended_action'] = null;
		                        }
		
		                        $aDefinedMenuItems[$sPosition][$iIndex]['bx_if:sub_menu'] = array (
		                            'condition'  => $aReplaced[$sPosition][$iKey]['PopupMenu'],
		                        	'content'    =>  $aContentKeys,
								);
		
		                        $iIndex++;
	                    }
	    				//--
					}
	    		}

			    //generate cache file
			    $aBubbles = array('bubbles' => $this -> sBubbles);
			    $aDefinedMenuItems = array_merge($aDefinedMenuItems, $aBubbles);
				$oCache->setData( $this -> getCacheKey($aMemberInfo['ID'])
					, $aDefinedMenuItems, $this -> iKeysFileTTL); 
			}	
			//--

			//define bubble list
			$sBubbleList = isset($aDefinedMenuItems['bubbles'])
				? trim($aDefinedMenuItems['bubbles']) //get it from cache
				: trim($this -> sBubbles);

			//generate data
            $aTemplateKeys = array (
                'bx_repeat:items' => $aDefinedMenuItems['top'],
            	'bx_repeat:extra_items' => $aDefinedMenuItems['top_extra'],
                'site_url' =>  BX_DOL_URL_ROOT,
                'menu_position' => $this -> sMemberMenuPosition, 
                'is_profile_page' => defined('BX_PROFILE_PAGE') ? 'true' : 'false',
                'bubbles_list' => preg_replace('/,$/', '',  $sBubbleList),
                'bubbles_update_time' => $this -> iBubblesUpdateTime,
                'notify_destroy_time' => $this -> iNotifyDestroyTime,
                'page_reciver' => $this -> sQueryPageReciver,
                'bubble_prefix' => $this -> sBubblePrefix,
            );

            $oSysTemplate -> addJs('user_status.js');
            return $oSysTemplate -> parseHtmlByName('extra_top_menu.html', $aTemplateKeys);
        }

        /**
         * Function will generate extra navigation menu for logged member ;
         *
         * @param : $memberID ( integer )   - member's ID ;
         * @return : Html presentation data or array with menu's structure;
        */
        function genMemberMenu($memberID = 0) 
    	{
            if (isset($GLOBALS['bx_profiler'])) $GLOBALS['bx_profiler']->beginMenu('Member Menu');

			// ** init some needed variables ;

            $aMemberInfo = array();
    		$sOutputCode   = null;
            //--

			// if member's id was defined, that will receive all member's info ;
            if ($memberID) {
                $aMemberInfo  =  getProfileInfo($memberID);
                $aMemberInfo['ProfileLink'] = getProfileLink($aMemberInfo['ID']);
            }

            // if member not logged ;
            if (!$aMemberInfo) {
                if (isset($GLOBALS['bx_profiler'])) $GLOBALS['bx_profiler']->endMenu('Member Menu');
                return ;
            }

			// read data from cache file ;
           	$oCache = $this -> getCacheObject();
           	$aMenuStructure = $oCache->getData($GLOBALS['MySQL']->genDbCacheKey($this -> sMenuCacheFile));
           	if(!$aMenuStructure) {
           		$aMenuStructure = $this -> createMemberMenuCache();
           	}

			$sOutputCode = $this -> _parseStructure($aMemberInfo, $aMenuStructure);

            if (isset($GLOBALS['bx_profiler'])) $GLOBALS['bx_profiler']->endMenu('Member Menu');

    		return $sOutputCode;
        }

        /**
         * Function will return menu's sub content;
         *
         * @param  : $iMemberId (integer) - logged member's Id;
         * @param  : $sSubMenuCode (string)  - sub menu's php code;
         * @param  : $aLinkedItems (array)   - linked links items;
         * @return : Html presentation data; 
         */
        function getSubMenuContent( $iMemberId, $sSubMenuCode, $aLinkedItems = array() )
        {
            global $oFunctions;

            $iMemberId = (int) $iMemberId;

            $aMemberInfo  = getProfileInfo($iMemberId);
            $sSubMenuCode = eval( $oFunctions -> markerReplace( $aMemberInfo, $sSubMenuCode) ); 

            if($aLinkedItems) {
                foreach($aLinkedItems as $iKey => $aItems)
                {
                    $sSubMenuCode .= eval( $oFunctions -> markerReplace( $aMemberInfo, $aItems['code']) );
                }
            }

            return $sSubMenuCode;
        }

        /**
         * Generate name for cache
         * 
         * @param $iProfileId integer
         * @return string
         */
		function getCacheKey($iProfileId)
		{
	        global $site;

	        return $this -> sMenuMemberKeysCache . $iProfileId . '_' . md5($site['ver'] 
	        	. $site['build'] . $site['url'] . getCurrentLangName(false) 
	        	. $GLOBALS['oSysTemplate']->getCode()) . '.php';
		}

        /**
         * Delete member menu key file
         * 
         * @param $iProfile integer
         * @return unknown_type
         */
        function deleteMemberMenuKeyFile($iProfile)
        {
        	$oCache = $this -> getCacheObject();
        	$oCache -> setData($this -> getCacheKey($iProfile), array(), $this -> iKeysFileTTL);
        }

        /**
         * Delete all member menu cache files
         * 
         * @return boolean
         */
        function deleteMemberMenuCaches()
        {
			//remove all member_menu_keys files
	        $oCache = $this -> getCacheObject();
	        $oCache->removeAllByPrefix($this -> sMenuMemberKeysCache);

        	return $GLOBALS['MySQL']->cleanCache($this -> sMenuCacheFile);
        }

        /**
         * @description : function will create menu's cache file ;
         * 
         * @return array
        */
        function createMemberMenuCache() 
        {
        	$this -> deleteMemberMenuCaches();

        	$oPermalink = new BxDolPermalinks();
        	$aCacheData = array();

        	$sQuery = "SELECT * FROM `sys_menu_member` WHERE `Active` = '1' ORDER BY `Order`";
        	$rResult = db_res($sQuery);

        	while( true == ($aRow = mysql_fetch_assoc($rResult)) ) 
            {
        	    $aRow['Link'] = $oPermalink -> permalink($aRow['Link']);
                $aRow['linked_items'] = $this -> getLinkedItem($aRow['ID']);

        		$aCacheData[$aRow['Position']][$aRow['ID']] = $aRow;
        	}

            // if items not found ;
            if ( !mysql_num_rows($rResult) ) {
                $aCacheData[$sMenuSection] = array();
            }

            $oCache = $this -> getCacheObject();
            if (!empty($aCacheData) && is_array($aCacheData)) {
                $oCache->setData ($GLOBALS['MySQL']->genDbCacheKey($this -> sMenuCacheFile), $aCacheData); 
            }

        	return $aCacheData;
        }

        /**
         * Function will get all linked item for recived menu's item;
         * 
         * @param  : $iMenuId (integer) - menu's Id;
         * @return : (array);
                [code] - (string) evaluate code;
         */
        function getLinkedItem($iMenuId)
        {
        	$iMenuId = (int) $iMenuId;
            $sQuery  = "SELECT `Eval` FROM `sys_menu_member` WHERE `Parent` = {$iMenuId} AND `Type` = 'linked_item'";
            $rResult = db_res($sQuery);

            $aLinkedItems = array();
            while ( true == ($aRow = mysql_fetch_assoc($rResult)) ) 
            {
                $aLinkedItems[] = array(
                    'code' => $aRow['Eval']
                );
            }

            return $aLinkedItems;
        }
    }
