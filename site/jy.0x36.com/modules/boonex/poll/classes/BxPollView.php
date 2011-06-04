<?php
 
    bx_import('BxDolPageView');
    bx_import('BxTemplVotingView');
    bx_import('BxTemplCmtsView');

    class BxPollView extends BxDolPageView
    {
        // contain link on current module's object;
        var $oModule;

        var $iPollId;

        // contain some information about current poll;
        var $aPollInfo = array();

        // need for vote objects;
        var $oVotingView;

        var $oCmtsView;

        // logged member's id;
        var $iMemberId;

        // contain some info about current module;
        var $aModule = array();

        // count of polls in poll's home page;
        var $iHomePage_countLatest   = 8;
        var $iHomePage_countFeatured = 4;

        /**
         * Class constructor;
         * 
         * @param : $sPageName   (string)  - builder's page name;
         * @param : $oPollModule (object)  - created poll's module object;
         * @param : $iPollId     (integer) - poll's Id;
         */
        function BxPollView($sPageName, &$aModule, &$oPollModule, $iPollId)
        {
            parent::BxDolPageView($sPageName);

            // define member's Id;
            $aProfileInfo = getProfileInfo();
            $this -> iMemberId   = ( isset($aProfileInfo['ID']) ) 
                ? $aProfileInfo['ID'] 
                : 0;

            $this -> oModule   = $oPollModule;
            $this -> iPollId   = $iPollId;

            if($this -> iPollId) {
                $this -> aPollInfo = $this -> oModule -> _oDb -> getPollInfo($this -> iPollId);
                if(!$this -> oModule -> oPrivacy -> check('view', $this -> aPollInfo[0]['id_poll'], $this -> iMemberId) ) {
                    echo $this -> oModule -> _oTemplate -> defaultPage( _t('_bx_poll'), MsgBox(_t('_bx_poll_access_denied')), 2 );
                    exit;
                }
            }

            if($this -> aPollInfo) {
                $this -> aPollInfo = array_shift($this -> aPollInfo);
            }

            $this -> oVotingView = new BxTemplVotingView ('bx_poll', $this -> iPollId);
            $this -> oCmtsView   = new BxTemplCmtsView   ('bx_poll', $this -> iPollId);

            $this -> aModule = $aModule;

	        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
	            _t('_bx_poll') => BX_DOL_URL_ROOT . $this-> oModule -> _oConfig -> getBaseUri() . '&action=poll_home',
	        	$this -> aPollInfo['poll_question'] => '',
	        ));
        }

        function genColumnsHeader()
        {
            $this -> sCode .= $this -> oModule -> getInitPollPage();
        }

        /**
         * Function will generate featured polls;
         */
        function getBlockCode_FeaturedHome()
        {
            // ** init some variables;
            $sPaginate = null;

            $iPage  = ( isset($_GET['page']) ) 
                ? (int) $_GET['page'] 
                : 1;

            $iPerPage = ( isset($_GET['per_page']) ) 
                ? (int) $_GET['per_page'] 
                : $this -> iHomePage_countFeatured;

            if ($iPerPage <= 0 ) {
                $iPerPage = $this -> iHomePage_countFeatured;
            }

            if ( !$iPage ) {
                $iPage = 1;
            }

            // get only the member's polls ;
            $iTotalNum = $this -> oModule -> _oDb -> getFeaturedCount(1, true);

            if ( !$iTotalNum ) {
                $sOutputCode  = MsgBox( _t( '_Empty' ) );
            }
            else {
                $sLimitFrom   = ($iPage - 1) * $iPerPage;
                $sqlLimit     = "LIMIT {$sLimitFrom}, {$iPerPage}";
                $aPolls       = $this -> oModule -> _oDb -> getAllFeaturedPolls($sqlLimit, 1, true);
                $sOutputCode  = $this -> oModule -> genPollsColumns($aPolls, 1);

                // define path to module;
                $sModulePath = $this -> oModule -> getModulePath() . '?action=featured';

                // build paginate block;
                $oPaginate = new BxDolPaginate(array(
                    'page_url' => $sModulePath,
                    'count' => $iTotalNum,
                    'per_page' => $iPerPage,
                    'page' => $iPage,
                    'per_page_changer' => true,
                    'page_reloader' => true,
                    'on_change_page' => 'return !loadDynamicBlock({id}, \'' . $sModulePath . '&action=poll_home&page={page}&per_page={per_page}\');',
                    'on_change_per_page' => ''
                ));

                $sPaginate = $oPaginate -> getSimplePaginate($sModulePath);
            }

            return array($sOutputCode, array(), $sPaginate);
        }

        /**
         * Function will generate latest polls;
         */
        function getBlockCode_LatestHome()
        {
            // ** init some variables;
            $sPaginate = null;

            $iPage  = ( isset($_GET['page']) ) 
                ? (int) $_GET['page'] 
                : 1;

            $iPerPage = ( isset($_GET['per_page']) ) 
                ? (int) $_GET['per_page'] 
                : $this -> iHomePage_countLatest;

            if ($iPerPage <= 0 ) {
                $iPerPage = $this -> iHomePage_countLatest;
            }

            if ( !$iPage ) {
                $iPage = 1;
            }

            // get only the member's polls ;
            $iTotalNum = $this -> oModule -> _oDb -> getFeaturedCount(0, true);

            if ( !$iTotalNum ) {
                $sOutputCode  = MsgBox( _t( '_Empty' ) );
            }
            else {
                $sLimitFrom   = ($iPage - 1) * $iPerPage;
                $sqlLimit     = "LIMIT {$sLimitFrom}, {$iPerPage}";
                $aPolls       = $this -> oModule -> _oDb -> getAllFeaturedPolls($sqlLimit, 0, true);
                $sOutputCode  = $this -> oModule -> genPollsColumns($aPolls, 2);

                // define path to module;
                $sModulePath = $this -> oModule -> getModulePath();

                // build paginate block;
                $oPaginate = new BxDolPaginate(array(
                    'page_url' => $sModulePath,
                    'count' => $iTotalNum,
                    'per_page' => $iPerPage,
                    'page' => $iPage,
                    'per_page_changer' => true,
                    'page_reloader' => true,
                    'on_change_page' => 'return !loadDynamicBlock({id}, \'' . $sModulePath . '&action=poll_home&page={page}&per_page={per_page}\');',
                    'on_change_per_page' => ''
                ));

                $sPaginate = $oPaginate -> getSimplePaginate($sModulePath);
            }

            return array($sOutputCode, array(), $sPaginate);
        }

        /**
         * Function will generate block with actions;
         */
        function getBlockCode_ActionsBlock()
        {
            if(!$this -> aPollInfo) {
                return MsgBox( _t('_Empty') );
            }

            $iOwnerId   = (int) $this -> aPollInfo['id_profile'];
            $aOwnerInfo = getProfileInfo($iOwnerId);

            // prepare all needed keys
            $aOwnerInfo['PollId']   =  (int) $this -> aPollInfo['id_poll'];    
            $aOwnerInfo['ID']       =  $this -> iMemberId;    
            $aOwnerInfo['BaseUri']  =  $this -> oModule -> _oConfig -> getBaseUri();
    
            $oSubscription = new BxDolSubscription();
            $aButton = $oSubscription -> getButton($this -> iMemberId, 'bx_poll', '', $this -> aPollInfo['id_poll']);
            $aOwnerInfo['sbs_poll_title']  =  $aButton['title'];
            $aOwnerInfo['sbs_poll_script'] =  $aButton['script'];
 
            $sActions = $GLOBALS['oFunctions'] -> genObjectsActions($aOwnerInfo, 'bx_poll');

            return  $oSubscription -> getData() . $sActions;
        }

        /**
         * The function will generate the block of the polls owner
         */
        function getBlockCode_OwnerBlock()
        {
            if(!$this -> aPollInfo) {
                return MsgBox( _t('_Empty') );
            }

           return  $this -> oModule -> getOwnerBlock($this -> aPollInfo['id_profile'], $this -> aPollInfo);
        }

        /**
         * Function will generate poll block;
         */
        function getBlockCode_PoolBlock()
        {
            if(!$this -> aPollInfo) {
                return MsgBox( _t('_Empty') );
            }

            $aPoll = array (
                'id_poll'       => $this -> aPollInfo['id_poll'],
                'id_profile'    => $this -> aPollInfo['id_profile'],
                'poll_date'     => $this -> aPollInfo['poll_date'],
                'poll_approval' => $this -> aPollInfo['poll_approval'],
            );

            return $this -> oModule -> getPollBlock($aPoll, false, true);
        }

        /**
         * Function will generate comments block;
         */
        function getBlockCode_CommentsBlock()
        {
            if(!$this -> aPollInfo) {
                return MsgBox( _t('_Empty') );
            }

            if(  $this -> oModule -> oPrivacy -> check('comment', $this -> aPollInfo['id_poll'], $oPoll -> aPollSettings['member_id']) ) {
                $sOutputCode = $this -> oCmtsView  -> getExtraCss();
                $sOutputCode .= $this -> oCmtsView  -> getExtraJs();

                $sOutputCode .= ( !$this -> oCmtsView -> isEnabled() ) 
                                    ? null
                                    : $this -> oCmtsView -> getCommentsFirst();
            }
            else {
                $sOutputCode = MsgBox( _t( '_bx_poll_privacy_comment_error' ) );
            }

            return $sOutputCode;
        }

        /**
         * Function will generate vote block;
         */
        function getBlockCode_VotingsBlock()
        {
            if(!$this -> aPollInfo) {
                return MsgBox( _t('_Empty') );
            }

            if(  $this -> oModule -> oPrivacy -> check('vote', $this -> aPollInfo['id_poll'], $oPoll -> aPollSettings['member_id']) ) {
                if ( $this -> oVotingView -> isEnabled()){
                    $sOutputCode = $this -> oVotingView -> getBigVoting();
                }
            }
            else {
                $sOutputCode = MsgBox( _t( '_bx_poll_privacy_vote_error' ) );
            }

            return $sOutputCode;
        }
    }