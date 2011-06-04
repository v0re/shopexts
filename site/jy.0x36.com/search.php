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

require_once( './inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC     . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC     . 'admin.inc.php' );
require_once( BX_DIRECTORY_PATH_INC     . 'db.inc.php' );
require_once( BX_DIRECTORY_PATH_INC     . 'match.inc.php');
require_once( BX_DIRECTORY_PATH_CLASSES . 'BxDolProfileFields.php' );
require_once( BX_DIRECTORY_PATH_CLASSES . 'BxDolProfilesController.php' );

bx_import('BxDolDb');
bx_import('BxBaseSearchProfile');
bx_import('BxTemplProfileView');

class BxDolSearchPageView extends BxDolPageView {
    var $oPF;

	var $aFilterSortSettings;

    function BxDolSearchPageView() {
        parent::BxDolPageView('search');

        // get search mode
        switch( $_REQUEST['search_mode'] ) {
        	case 'quick': $iPFArea = 10; break;
        	case 'adv':   $iPFArea = 11; break;
        	default:      $iPFArea = 9; // simple search (default)
        }

        $this->oPF = new BxDolProfileFields($iPFArea);

		$this->aFilterSortSettings = array();
    }

	function collectFilteredSettings() {
		//$this->aFilterSortSettings
		$sSort		= (isset($_GET['sort'])) ? process_db_input($_GET['sort'], BX_TAGS_STRIP) : null;
		// $sPhotos	= (isset($_GET['photos_only'])) ? $_GET['photos_only'] : null;
		// $sOnline	= (isset($_GET['online_only'])) ? $_GET['online_only'] : null;	
		// $sInfoMode	= (isset($_GET['search_result_mode']) && $_GET['search_result_mode'] == 'ext') ? 'ext' : 'sim';

		$this->aFilterSortSettings = array (
		    //'f_photos'	=> $sPhotos,
		    //'f_online'	=> $sOnline,
		    'sort'	=> $sSort,
		    //'s_mode'	=> $sInfoMode
		);
	}

    function getBlockCode_SearchForm() {
        global $logged;

        $aProfile = $logged['member'] ? getProfileInfo(getLoggedId()) : array();

        // default params for search form
        $aDefaultParams = array(
            'LookingFor' => isset($_GET['LookingFor']) ? $_GET['LookingFor'] : ($aProfile['Sex'] ? $aProfile['Sex'] : 'male'),
            'Sex' => isset($_GET['Sex']) ? $_GET['Sex'] : ($aProfile['LookingFor'] ? $aProfile['LookingFor'] : 'female'),
            'Country' => isset($_GET['Country'][0]) ? $_GET['Country'][0] : ($aProfile['Country'] ? $aProfile['Country'] : getParam('default_country')),
            'DateOfBirth' => isset($_GET['DateOfBirth']) ? $_GET['DateOfBirth'] : getParam('search_start_age') . '-' . getParam('search_end_age'),
        	'Tags' => isset($_GET['Tags']) ? $_GET['Tags'] : '',
        	'online_only' => isset($_GET['online_only']) ? $_GET['online_only'] : '',
        	'photos_only' => isset($_GET['photos_only']) ? $_GET['photos_only'] : ''
        );

    	$sForms = $this->oPF->getFormCode(array('default_params' => $aDefaultParams));

        $bSimAct = ($this->oPF->iAreaID == 9) ? true : false;
        $bAdvAct = ($this->oPF->iAreaID == 11) ? true : false;
        $bQuiAct = ($this->oPF->iAreaID == 10) ? true : false;
		$sUrl = BX_DOL_URL_ROOT . 'search.php';
        $sLinks = BxDolPageView::getBlockCaptionMenu(mktime(), array(
            'simple' => array('href' => $sUrl . '?search_mode=sim', 'title' => _t('_search_tab_simple'), 'onclick' => '', 'active' => $bSimAct),
            'advanced' => array('href' => $sUrl . '?search_mode=adv', 'title' => _t('_search_tab_Adv'), 'onclick' => '', 'active' => $bAdvAct),
            'quick' => array('href' => $sUrl . '?search_mode=quick', 'title' => _t('_search_tab_quick'), 'onclick' => '', 'active' => $bQuiAct),
        ));

        return DesignBoxContent(_t('_Search profiles'), $sForms, 1, $sLinks);
    }
    
    function showMatchProfiles($iBlockID)
    {
        $iProfileId = getLoggedId();
        
        if (!$iProfileId)
            return array('', MsgBox(_t('_Empty')));
        
        $sSort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'none';
        $aProfiles = getMatchProfiles($iProfileId, false, $sSort);
        
        if (empty($aProfiles))
            return array('', MsgBox(_t('_Empty')));
            
        $sBaseUri = 'search.php?show=match';
        $sTopLinksUri = '';
        $sPaginateUri = '';
        
        foreach ($_REQUEST as $sKey => $sVal)
        {
            switch ($sKey)
            {
                case 'page':
                    $sPaginateUri .= '&page=' . $sVal;
                    break;
                case 'per_page':
                    $sPaginateUri .= '&per_page=' . $sVal;
                    break;
                case 'sort':
                    $sPaginateUri .= '&sort=' . $sVal;
                    break;
                case 'mode':
                    $sTopLinksUri .= '&mode=' . $sVal;
                    break;
            }
        }

        $aPaginate = array(
            'page_url' => $sBaseUri . $sTopLinksUri . '&page={page}&per_page={per_page}&sort={sorting}',
            'info' => true,
            'page_links' => true,
            'per_page' => isset($_REQUEST['per_page']) ? (int)$_REQUEST['per_page'] : 25,
            'sorting' => $sSort,
            'count' => count($aProfiles),
            'page' => isset($_REQUEST['page']) ? (int)$_REQUEST['page'] : 1,
            'page_reloader' => true,
            'per_page_changer' => true
        );
        $sMode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : 'simple';
            
        $oPaginate = new BxDolPaginate($aPaginate);
        
        $oSearchProfile = new BxBaseSearchProfile();
        $aExtendedCss = array( 'ext_css_class' => 'search_filled_block');
        $sTemplateName = $sMode == 'extended' ? 'search_profiles_ext.html' : 'search_profiles_sim.html';
        $iIndex = 0;
        $sOutputHtml = '';
        
        for ($i = ($aPaginate['page'] - 1) * $aPaginate['per_page']; 
            $i < $aPaginate['page'] * $aPaginate['per_page'] && $i < $aPaginate['count']; $i++)
        {
            $aProfile = getProfileInfo($aProfiles[$i]);
            
            if ($aProfile['Couple']) 
            {        
                $aCoupleInfo = getProfileInfo($aProfile['Couple']);
                if (!($iIndex % 2)) 
                    $sOutputHtml .= $oSearchProfile->PrintSearhResult($aProfile, $aCoupleInfo, null, $sTemplateName);
                else 
                    $sOutputHtml .= $oSearchProfile->PrintSearhResult($aProfile, $aCoupleInfo, $aExtendedCss, $sTemplateName);
            } 
            else 
            { 
                if (!($iIndex % 2)) 
                    $sOutputHtml .= $oSearchProfile->PrintSearhResult($aProfile, '', null, $sTemplateName);
                else 
                    $sOutputHtml .= $oSearchProfile->PrintSearhResult($aProfile, '', $aExtendedCss, $sTemplateName);
            }
            
            $iIndex++;
        }
        
        $aSortingParam = array (
            'none' => _t('_None'),
            'activity' => _t('_Latest activity'),
            'date_reg' => _t('_FieldCaption_DateReg_View'),
        );

        // gen sorting block ( type of : drop down ) ;
        $sSortBlock = $oPaginate->getSorting($aSortingParam);
        $sSortElement = <<<EOF
<div class="top_settings_block">
    <div class="ordered_block">
        {$sSortBlock}
    </div>
    <div class="clear_both"></div>
</div>
EOF;
        
        $sOutputHtml .= '<div class="clear_both"></div>';
        $sContent = $sSortElement . $GLOBALS['oSysTemplate']->parseHtmlByName('view_profiles.html', array('content' => $sOutputHtml)) . $oPaginate->getPaginate();
        $aItems = array(
             _t('_Simple') => array(
                 'href' => $sBaseUri . $sPaginateUri . '&mode=simple',
                 'dynamic' => true,
                 'active' => $sMode == 'simple'
             ),
             _t('_Extended') => array(
                 'href' => $sBaseUri . $sPaginateUri . '&mode=extended',
                 'dynamic' => true,
                 'active' => $sMode == 'extended'
             )
        );
        $sLinks = BxDolPageView::getBlockCaptionItemCode($iBlockID, $aItems);
        
        return array(
            $sLinks,
            $sContent
        );
    }
    
    function getBlockCode_Results($iBlockID) {
        //collect inputs
		$aRequestParams = $this->oPF->collectSearchRequestParams();

		if( isset( $_REQUEST['Tags'] ) and trim( $_REQUEST['Tags'] ) )
			$aRequestParams['Tags'] = trim( process_pass_data( $_REQUEST['Tags'] ) );

		if( isset( $_REQUEST['distance'] ) and (int)$_REQUEST['distance'] )
        	$aRequestParams['distance'] = (int)$_REQUEST['distance'];

        // start page generation
        $oProfile = new BxBaseProfileGenerator(getLoggedId());

		switch($_REQUEST['show']) {
			case 'match':
			    list($sLinks, $sResults) = $this->showMatchProfiles($iBlockID);
				break;
			case 'calendar':
				list($sResults, $aDBTopMenu, $sPagination, $sTopFilter) = $oProfile->GenProfilesCalendarBlock();
				$sLinks = (is_array($aDBTopMenu) && count($aDBTopMenu)>0) ? $this->getBlockCaptionItemCode($iBlockID, $aDBTopMenu) : '';
				break;
			default:
				$this->collectFilteredSettings();
				list($sResults, $aDBTopMenu, $sPagination, $sTopFilter) = $oProfile->GenSearchResultBlock($this->oPF->aBlocks, $aRequestParams, $this->aFilterSortSettings, 'search.php');
				$sLinks = (is_array($aDBTopMenu) && count($aDBTopMenu)>0) ? $this->getBlockCaptionItemCode($iBlockID, $aDBTopMenu) : '';
				break;
		}
		
		return DesignBoxContent($this->getTitle(), $sTopFilter . $sResults . $sPagination, 1, $sLinks);
    }

	function getTitle() {
		$sHeaderTitle =  _t('_Search profiles');
		switch($_REQUEST['show']) {
			case 'match':
				$sHeaderTitle = _t('_Match');
				break;
			/*case 'online':
				$sHeaderTitle = _t('_Online');
				break;*/
			case 'featured':
				$sHeaderTitle = _t('_Featured');
				break;
			case 'top_rated':
				$sHeaderTitle = _t('_Top Rated');
				break;
			case 'popular':
				$sHeaderTitle = _t('_Popular');
				break;
			case 'birthdays':
				$sHeaderTitle = _t('_Birthdays');
				break;
			case 'world_map':
				$sHeaderTitle = _t('_World_Map');
				break;
			case 'calendar':
				$sHeaderTitle = _t('_People_Calendar');
				break;
		}
		return $sHeaderTitle;
	}
}

check_logged();

$_page['name_index'] = 81;
$_page['css_name']   = 'search.css';

$oSearchView = new BxDolSearchPageView();
$sHeaderTitle = $oSearchView->getTitle();
$_page['header'] = $sHeaderTitle;
$_ni = $_page['name_index'];
$_page_cont[$_ni]['page_main_code'] = $oSearchView->getCode();

PageCode();
exit;

?>
