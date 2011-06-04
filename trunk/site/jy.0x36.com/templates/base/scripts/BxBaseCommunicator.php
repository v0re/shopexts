<?

	/***************************************************************************
	*							Dolphin Smart Community Builder
	*							  -------------------
	*	 begin				: Mon Mar 23 2006
	*	 copyright			: (C) 2007 BoonEx Group
	*	 website			  : http://www.boonex.com
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

    require_once( BX_DIRECTORY_PATH_CLASSES . 'BxDolCommunicator.php');

    class BxBaseCommunicator extends BxDolCommunicator
	{
        // contain all needed templates for Html rendering ;
        var $aUsedTemplates;

        var $sMembersFlagExtension   = '.gif';

       /**
        * Class constructor ;
        *
        * @param   : $sPageName (string)  - page name (need for the page builder);
        * @param	: $aCommunicatorSettings (array)  - contain some necessary data ;
        * 					[ member_id	] (integer) - logged member's ID;
        * 					[ communicator_mode ] (string) - page mode ;
        * 					[ person_switcher ] (string) - switch the person mode - from me or to me ;
        * 					[ sorting ] (string) - type of message's sort ;
        * 					[ page ] (integer) - contain number of current page ;
        * 					[ per_page ] (integer) - contain per page number for current page ;
        * 					[ alert_page ] (integer) - contain number of current alert's page 
        */
        function BxBaseCommunicator($sPageName, &$aCommunicatorSettings)
        {
            // call the parent constructor ; 
    		parent::BxDolCommunicator($sPageName, $aCommunicatorSettings);
            
            //fill array with tamplates name;
            $this -> aUsedTemplates = array
            (
                'communicator_page'          => 'communicator_page.html',
                'communicator_settings'      => 'communicator_settings.html',
                'communicator_settings_page' => 'communicator_page_top_settings.html',
            );
        }

        /**
         * Function will draw the 'Communicator' block;
         *
         */
        function getBlockCode_CommunicatorPage()
        {
            global $oSysTemplate;

            // set default mode ;
           if ( !$this -> aCommunicatorSettings['communicator_mode'] )
                $this -> aCommunicatorSettings['communicator_mode'] = 'friends_requests';

            // generate the top page toggle ellements ;
            $aTopToggleItems = array
            (
                'friends_list'      =>  _t( '_Friends' ),
                'blocks_requests'   =>  _t( '_Block list' ),
                'greeting_requests' =>  _t( '_Greetings' ),
                'hotlist_requests'  =>  _t( '_Hot lists' ),
                'friends_requests'  =>  _t( '_MEMBERS_INVITE_YOU_FRIENDLIST' ),
            );

            $sRequest = BX_DOL_URL_ROOT . 'communicator.php?';
            foreach( $aTopToggleItems AS $sKey => $sValue )
            {
                $aTopToggleEllements[$sValue] = array
                (
                    'href' => $sRequest . '&communicator_mode=' . $sKey,
                    'dynamic' => true,
                    'active' => ($this -> aCommunicatorSettings['communicator_mode'] == $sKey ),
                );
            }

            // return processed html data;
            $sOutputHtml = $this -> getProcessingRows();

            // return generated template ;
            return array($sOutputHtml, $aTopToggleEllements);
        }

        /**
         * Function will generate received rows ;
         *
         * @return  : Html presentation data ;
         */
        function getProcessingRows()
        {
            global $oSysTemplate, $site, $oFunctions ;

            // ** init some needed variables ;

            $sOutputHtml        = '';
            $sPageContent       = '';
            $sActionsList       = '';
            $sSettings          = '';

            $sShowSettings      = true;
            $aRows              = array();

            // define the member's nickname;
            $sMemberNickName  = getNickName($this -> aCommunicatorSettings['member_id']);

            // all primary language's keys ;  
            $aLanguageKeys = array
            (
                'author'      => _t( '_Author' ),
                'type'        => _t( '_Type' ),
                'date'        => _t( '_Date' ),
                'click_sort'  => _t( '_Click to sort' ),
                'from_me'     => _t( '_From' )   . ' ' . $sMemberNickName,
                'to_me'       => _t( '_To' )     . ' ' . $sMemberNickName,
                'accept'      => _t( '_Add to Friend List' ),
                'reject'      => _t( '_Reject Invite' ),
                'delete'      => _t( '_Delete' ),
                'back_invite' => _t( '_Back Invite' ),
                'hotlist_add' => _t( '_Add to Hot List' ),
                'visitor'     => _t( '_Visitor' ),  
                'unblock'     => _t( '_Unblock' ),  
                'block'       => _t( '_Block' ),  
            );

            // get all requests from DB ;
            switch($this -> aCommunicatorSettings['communicator_mode']) 
            {
                case 'friends_requests' :
                   $aTypes = array
                   (
                        'from'  => _t( '_MEMBERS_INVITE_YOU_FRIENDLIST' ),
                        'to'    => _t( '_MEMBERS_YOU_INVITED_FRIENDLIST' )
                   );
                   $aRows = $this -> getRequests( 'sys_friend_list', $aTypes, ' AND `sys_friend_list`.`Check` = 0 ');
                break;

                case 'hotlist_requests' :
                    $aTypes = array
                    ( 
                        'from'  => _t( '_MEMBERS_YOU_HOTLISTED' ),
                        'to'    => _t( '_MEMBERS_YOU_HOTLISTED_BY' )
                    );
                    $aRows = $this -> getRequests( 'sys_fave_list', $aTypes);
                break;

                case 'greeting_requests' :
                    $aTypes = array
                    ( 
                        'from'          => _t( '_MEMBERS_YOU_KISSED' ),
                        'to'            => _t( '_MEMBERS_YOU_KISSED_BY' ),
                        'specific_key'  => '_N times',
                    );
                    $aRows = $this -> getRequests( 'sys_greetings', $aTypes, null, 'Number' );
                break;

                case 'blocks_requests' :
                    $aTypes = array
                    ( 
                        'from'          => _t( '_MEMBERS_YOU_BLOCKLISTED' ),
                        'to'            => _t( '_MEMBERS_YOU_BLOCKLISTED_BY' ),
                    );
                    $aRows = $this -> getRequests( 'sys_block_list', $aTypes );
                break;

               case 'friends_list'  :
                    $aTypes = array
                    (
                        'from'  => _t( '_Friend list' ),
                    	'to'	=> _t( '_Friend list' ),
                    );
                    $aRows = $this -> getRequests( 'sys_friend_list', $aTypes, 
                        ' AND `sys_friend_list`.`Check` = 1 OR ( `sys_friend_list`.`ID` = ' . $this -> aCommunicatorSettings['member_id'] 
                            . ' AND `sys_friend_list`.`Check` = 1 )' );

                    // set unvisible the settings block ;
                    $sShowSettings = false;        
                break;

                default :
                    $aTypes = array
                    (
                        'from'  => _t( '_MEMBERS_INVITE_YOU_FRIENDLIST' ),
                        'to'    => _t( '_MEMBERS_YOU_INVITED_FRIENDLIST' )
                    );
                    $aRows = $this -> getRequests( 'sys_friend_list', $aTypes, ' AND `sys_friend_list`.`Check` = 0 ' );
            }

            // ** Generate the page's pagination ;

            // fill array with all necessary `get` parameters ;
            $aNeededParameters = array( 'communicator_mode', 'person_switcher', 'sorting' );

            // collect the page's URL ;
            $sRequest = BX_DOL_URL_ROOT . 'communicator.php?action=get_page' ;

            // add additional parameters ;
            foreach( $aNeededParameters AS $sKey )
            {
                $sRequest .= ( array_key_exists($sKey, $this -> aCommunicatorSettings) and $this -> aCommunicatorSettings[$sKey] ) 
                    ? '&' . $sKey . '=' . $this -> aCommunicatorSettings[$sKey]
                    : null ;
            }

            $sCuttedUrl = $sRequest;
            $sRequest   .=  '&page={page}&per_page={per_page}';

            // create  the pagination object ;
            $oPaginate = new BxDolPaginate
            (
                array
                (
                    'page_url'   => $sRequest,
                    'count'      => $this -> iTotalRequestsCount,
                    'per_page'   => $this -> aCommunicatorSettings['per_page'],
                    'sorting'    => $this -> aCommunicatorSettings['sorting'],

                    'page'               => $this -> aCommunicatorSettings['page'],
                    'per_page_changer'   => false,
                    'page_reloader'      => true,

                    'on_change_page'     => "if ( typeof oCommunicatorPage != 'undefined' ) oCommunicatorPage.getPaginatePage('{$sRequest}')",
                    'on_change_per_page' => "if ( typeof oCommunicatorPage != 'undefined' ) oCommunicatorPage.getPage(this.value, '{$sCuttedUrl}')",
                )
            );

            $sPagination   = $oPaginate -> getPaginate();
            $sPerPageBlock = $oPaginate -> getPages();

            // process received requests;
            if ( $aRows ) 
            {
                $iIndex = 1;
                foreach($aRows AS $iKey => $aItems )
                {
                    // if member not a visitor ;
                    if ( $aItems['member_id'] )
                    {
                        // ** some member's information ;
                        $aProfileInfo    = getProfileInfo ($aItems['member_id']);

                        // member's Icon ;
                        $sMemberIcon     = get_member_icon($aProfileInfo['ID'], 'left');

                        // member's profile location ;
                        $sMemberLocation = getProfileLink ($aProfileInfo['ID']);

                        // member's nickname ;
                        $sMemberNickName  = $aProfileInfo['NickName'];

                        // define the member's age ;
                        $sMemberAge = ( $aProfileInfo['DateOfBirth'] != "0000-00-00" ) 
                            ? _t( "_y/o", age($aProfileInfo['DateOfBirth']) ) 
                            : null;

                        // define the member's country, sex, etc ... ;
                        $sMemberCountry =  $aProfileInfo['Country'];
                        $sMemberFlag    =  $site['flags'] . strtolower($sMemberCountry) . $this -> sMembersFlagExtension;
                        $sMemberSexImg  =  $oFunctions -> genSexIcon($aProfileInfo['Sex']);

                        if ( $sMemberCountry )
                            $sMemberCountryFlag = '<img src="' . $sMemberFlag . '" alt="' . $sMemberCountry . '" />';
                    }
                    else
                    {
                        // ** if it's a visitor

                        // member's Icon ;
                        $sMemberIcon        = $aLanguageKeys['visitor'];

                        // member's profile location ;
                        $sMemberLocation    = null;
                        $sMemberSexImg      = null;
                        $sMemberAge         = null;
                        $sMemberCountryFlag = null;
                        $sMemberCountry     = null;
                    }

                    // color devider ;  
                    $sFiledCss = !( $iIndex % 2 ) ? 'filled' : 'not_filled'; 

                    $aProcessedRows[] = array
                    (
                        'filled_class'  => $sFiledCss,

                        'row_value'     => $aItems['member_id'],
                        'member_icon'   => $sMemberIcon,

                        // define the profile page location ;
                        'member_location' => ( $sMemberLocation ) 
                            ? '<a href="' . $sMemberLocation . '">' . $sMemberNickName . '</a>' : null,

                        // define the member's sex ;
                        'member_sex_img'  => ( $sMemberSexImg ) 
                            ? ' <img src="' . $sMemberSexImg . '" alt="' . $aProfileInfo['Sex'] . '" />' : null ,

                        'member_age'      => $sMemberAge,
                        'member_flag'     => $sMemberCountryFlag,
                        'member_country'  => $sMemberCountry,

                        'type'            => $aItems['type'],
                        'message_date'    => $aItems['date'],
                    );

                    $iIndex++;
                }

                // init the sort toggle ellements ;
                switch ( $this -> aCommunicatorSettings['sorting'] )
                {
                    case 'date' :
                        $aSortToglleElements['date_sort_toggle'] = 'toggle_up';
                    break; 
                    case 'date_desc' :
                       $aSortToglleElements['date_sort_toggle'] = 'toggle_down';
                    break;
                    case 'author' :
                        $aSortToglleElements['author_sort_toggle'] = 'toggle_up';
                    break;
                    case 'author_desc' :
                        $aSortToglleElements['author_sort_toggle'] = 'toggle_down';
                    break;
                }

                // define the actions list for type of requests;
                switch( $this -> aCommunicatorSettings['communicator_mode'] )
                {
                    case 'friends_requests' :
                        // define the person mode ;
                        switch ($this -> aCommunicatorSettings['person_switcher'])
                        {
                            case 'to' :
                                $aForm = array (
                                    'form_attrs' => array (
                                        'action' =>  null,
                                        'method' => 'post',
                                    ),

                                    'params' => array (
                                        'remove_form' => true,
                                        'db' => array(
                                            'submit_name' => 'do_submit', // some filed name with non empty value to determine if the for was submitted, 
                                        ),
                                    ),

                                    'inputs' => array(
                                        'actions' => array(
                                            'type' => 'input_set',
                                            'colspan' => 'true',
                                            0 => array (
                                                'type'      => 'button',
                                                'value'     => $aLanguageKeys['accept'],
                                                'attrs'     => array('onclick' => "if ( typeof oCommunicatorPage != 'undefined' ) oCommunicatorPage.sendAction('communicator_container', 'accept_friends_request', 'getProcessingRows')"),
                                            ),
                                            1 => array (
                                                'type'      => 'button',
                                                'value'     => $aLanguageKeys['reject'],
                                                'attrs'     => array('onclick' => "if ( typeof oCommunicatorPage != 'undefined' ) oCommunicatorPage.sendAction('communicator_container', 'reject_friends_request', 'getProcessingRows')"),
                                            ),
                                        )
                                    )
                                );    

                                $oForm = new BxTemplFormView($aForm);
                                $sActionsList = $oForm -> getCode();
                                break;

                            case 'from' :
                                $aForm = array (
                                    'form_attrs' => array (
                                        'action' =>  null,
                                        'method' => 'post',
                                    ),

                                    'params' => array (
                                        'remove_form' => true,
                                        'db' => array(
                                            'submit_name' => 'do_submit', // some filed name with non empty value to determine if the for was submitted, 
                                        ),
                                    ),

                                    'inputs' => array(
                                        'actions' => array(
                                            'type' => 'input_set',
                                            'colspan' => 'true',
                                            0 => array (
                                                'type'      => 'button',
                                                'value'     => $aLanguageKeys['back_invite'],
                                                'attrs'     => array('onclick' => "if ( typeof oCommunicatorPage != 'undefined' ) oCommunicatorPage.sendAction('communicator_container', 'delete_friends_request', 'getProcessingRows')"),
                                            ),
                                        )
                                    )
                                );    

                                $oForm = new BxTemplFormView($aForm);
                                $sActionsList = $oForm -> getCode();
                            break;
                        }
                        break;

                    case 'hotlist_requests' :
                        // define the person mode ;
                        switch ($this -> aCommunicatorSettings['person_switcher'])
                        {
                            case 'to' :
                                $aForm = array (
                                    'form_attrs' => array (
                                        'action' =>  null,
                                        'method' => 'post',
                                    ),

                                    'params' => array (
                                        'remove_form' => true,
                                        'db' => array(
                                            'submit_name' => 'do_submit', // some filed name with non empty value to determine if the for was submitted, 
                                        ),
                                    ),

                                    'inputs' => array(
                                        'actions' => array(
                                            'type' => 'input_set',
                                            'colspan' => 'true',
                                            0 => array (
                                                'type'      => 'button',
                                                'value'     => $aLanguageKeys['hotlist_add'],
                                                'attrs'     => array('onclick' => "if ( typeof oCommunicatorPage != 'undefined' ) oCommunicatorPage.sendAction('communicator_container', 'add_hotlist', 'getProcessingRows')"),
                                            ),
                                        )
                                    )
                                );    

                                $oForm = new BxTemplFormView($aForm);
                                $sActionsList = $oForm -> getCode();
                                break;

                            case 'from' :
                                $aForm = array (
                                    'form_attrs' => array (
                                        'action' =>  null,
                                        'method' => 'post',
                                    ),

                                    'params' => array (
                                        'remove_form' => true,
                                        'db' => array(
                                            'submit_name' => 'do_submit', // some filed name with non empty value to determine if the for was submitted, 
                                        ),
                                    ),

                                    'inputs' => array(
                                        'actions' => array(
                                            'type' => 'input_set',
                                            'colspan' => 'true',
                                            0 => array (
                                                'type'      => 'button',
                                                'value'     => $aLanguageKeys['delete'],
                                                'attrs'     => array('onclick' => "if ( typeof oCommunicatorPage != 'undefined' ) oCommunicatorPage.sendAction('communicator_container', 'delete_hotlisted', 'getProcessingRows')"),
                                            ),
                                        )
                                    )
                                );    

                                $oForm = new BxTemplFormView($aForm);
                                $sActionsList = $oForm -> getCode();
                                break;
                        }
                    break;

                    case 'greeting_requests' :
                        $aForm = array (
                            'form_attrs' => array (
                                'action' =>  null,
                                'method' => 'post',
                            ),

                            'params' => array (
                                'remove_form' => true,
                                'db' => array(
                                    'submit_name' => 'do_submit', // some filed name with non empty value to determine if the for was submitted, 
                                ),
                            ),

                            'inputs' => array(
                                'actions' => array(
                                    'type' => 'input_set',
                                    'colspan' => 'true',
                                    0 => array (
                                        'type'      => 'button',
                                        'value'     => $aLanguageKeys['delete'],
                                        'attrs'     => array('onclick' => "if ( typeof oCommunicatorPage != 'undefined' ) oCommunicatorPage.sendAction('communicator_container', 'delete_greetings', 'getProcessingRows')"),
                                    ),
                                )
                            )
                        );

                        $oForm = new BxTemplFormView($aForm);
                        $sActionsList = $oForm -> getCode();
                        break;

                    case 'blocks_requests' :
                        // define the person mode ;
                        switch ($this -> aCommunicatorSettings['person_switcher'])
                        {
                            case 'to' :
                                $aForm = array (
                                    'form_attrs' => array (
                                        'action' =>  null,
                                        'method' => 'post',
                                    ),

                                    'params' => array (
                                        'remove_form' => true,
                                        'db' => array(
                                            'submit_name' => 'do_submit', // some filed name with non empty value to determine if the for was submitted, 
                                        ),
                                    ),

                                    'inputs' => array(
                                        'actions' => array(
                                            'type' => 'input_set',
                                            'colspan' => 'true',
                                            0 => array (
                                                'type'      => 'button',
                                                'value'     => $aLanguageKeys['block'],
                                                'attrs'     => array('onclick' => "if ( typeof oCommunicatorPage != 'undefined' ) oCommunicatorPage.sendAction('communicator_container', 'block_unblocked', 'getProcessingRows')"),
                                            ),
                                        )
                                    )
                                );

                                $oForm = new BxTemplFormView($aForm);
                                $sActionsList = $oForm -> getCode();
                                break;

                            case 'from' :
                                $aForm = array (
                                    'form_attrs' => array (
                                        'action' =>  null,
                                        'method' => 'post',
                                    ),

                                    'params' => array (
                                        'remove_form' => true,
                                        'db' => array(
                                            'submit_name' => 'do_submit', // some filed name with non empty value to determine if the for was submitted, 
                                        ),
                                    ),

                                    'inputs' => array(
                                        'actions' => array(
                                            'type' => 'input_set',
                                            'colspan' => 'true',
                                            0 => array (
                                                'type'      => 'button',
                                                'value'     => $aLanguageKeys['unblock'],
                                                'attrs'     => array('onclick' => "if ( typeof oCommunicatorPage != 'undefined' ) oCommunicatorPage.sendAction('communicator_container', 'unblock_blocked', 'getProcessingRows')"),
                                            ),
                                        )
                                    )
                                );

                                $oForm = new BxTemplFormView($aForm);
                                $sActionsList = $oForm -> getCode();
                                break;
                        }
                    break;

                    case 'friends_list'  :
                        $aForm = array (
                        'form_attrs' => array (
                            'action' =>  null,
                            'method' => 'post',
                        ),

                        'params' => array (
                            'remove_form' => true,
                            'db' => array(
                                'submit_name' => 'do_submit', // some filed name with non empty value to determine if the for was submitted, 
                            ),
                        ),

                        'inputs' => array(
                            'actions' => array(
                                'type' => 'input_set',
                                'colspan' => 'true',
                                0 => array (
                                    'type'      => 'button',
                                    'value'     => $aLanguageKeys['delete'],
                                    'attrs'     => array('onclick' => "if ( typeof oCommunicatorPage != 'undefined' ) oCommunicatorPage.sendAction('communicator_container', 'reject_friends_request', 'getProcessingRows')"),
                                ),
                            )
                        )
                    );

                    $oForm = new BxTemplFormView($aForm);
                    $sActionsList = $oForm -> getCode();
                    break;
                }

                // processing the sort link ;
                $sSortLink = getClearedParam('sorting', $sCuttedUrl) . '&page=' . $this -> aCommunicatorSettings['page'] 
                                . '&per_page=' . $this -> aCommunicatorSettings['per_page'] ;

                // fill array with template keys ;
                $aTemplateKeys = array
                (
                    'from_me'        => $aLanguageKeys['from_me'],
                    'to_me'          => $aLanguageKeys['to_me'],
                    'selected_from'  => ($this -> aCommunicatorSettings['person_switcher'] == 'from') ? 'checked="checked"' : null,
                    'selected_to'    => ($this -> aCommunicatorSettings['person_switcher'] == 'to') ? 'checked="checked"' : null,
                    'per_page_block' => $sPerPageBlock,

                    'page_sort_url'  => $sSortLink,
                    'sort_date'      => ( $this -> aCommunicatorSettings['sorting'] == 'date' )     ? 'date_desc'     : 'date',
                    'sort_author'    => ( $this -> aCommunicatorSettings['sorting'] == 'author' )   ? 'author_desc'   : 'author',

                    'date_sort_toggle_ellement'   => $aSortToglleElements['date_sort_toggle'],
                    'author_sort_toggle_ellement' => $aSortToglleElements['author_sort_toggle'],

                    'author'     => $aLanguageKeys['author'],
                    'type'       => $aLanguageKeys['type'],
                    'date'       => $aLanguageKeys['date'],
                    'click_sort' => $aLanguageKeys['click_sort'],

                    // contain received processed rows ;
                    'bx_repeat:rows'  => $aProcessedRows,

                    // contain current actions ;
                    'actions_list'    =>  $sActionsList,

                    'page_pagination' => $sPagination,
                );

                $sPageContent = $oSysTemplate -> parseHtmlByName( $this -> aUsedTemplates['communicator_page'], $aTemplateKeys );
            }
            else
            {
                $sPageContent = MsgBox( _t('_Empty') );
            }

            // ** Process the final template ;

            if ( $sShowSettings )
            {
                // generate the page settings ;
                 $aTemplateKeys = array
                (
                    'from_me'        => $aLanguageKeys['from_me'],
                    'to_me'          => $aLanguageKeys['to_me'],
                    'selected_from'  => ($this -> aCommunicatorSettings['person_switcher'] == 'from') ? 'checked="checked"' : null,
                    'selected_to'    => ($this -> aCommunicatorSettings['person_switcher'] == 'to') ? 'checked="checked"' : null,
                );

                $sSettings = $oSysTemplate -> parseHtmlByName( $this -> aUsedTemplates['communicator_settings'], $aTemplateKeys );
            }

            // fill array with template keys ;
            $aTemplateKeys = array
            (
                'current_page'             => 'communicator.php',
                'communicator_mode'        => $this -> aCommunicatorSettings['communicator_mode'],
                'communicator_person_mode' => $this -> aCommunicatorSettings['person_switcher'],
                'error_message'            => bx_js_string(_t( '_Please, select at least one message' )),
                'sure_message'             => bx_js_string(_t( '_Are you sure?' )),

                'settings'       => $sSettings,
                'per_page_block' => $sPerPageBlock,

                'page_content'   => $sPageContent,
            );

            
            // construct the final template ;
            $sOutputHtml = $oSysTemplate -> parseHtmlByName( $this -> aUsedTemplates['communicator_settings_page'], $aTemplateKeys );

            return $sOutputHtml;
        }
    }

?>
