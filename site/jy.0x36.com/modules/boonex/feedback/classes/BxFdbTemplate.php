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

bx_import('BxDolTextTemplate');

class BxFdbTemplate extends BxDolTextTemplate {
	function BxFdbTemplate(&$oConfig, &$oDb) {
	    parent::BxDolTextTemplate($oConfig, $oDb);

	    $this->sCssPrefix = 'feedback';
	}
	function displayAdminBlock($aParams) {
	    $oSearchResult = $aParams['search_result_object'];
	    unset($aParams['search_result_object']);

	    $sModuleUri = $this->_oConfig->getUri();
	    $aButtons = array(
            $sModuleUri . '-approve' => _t('_' . $sModuleUri . '_lcaption_approve'),
            $sModuleUri . '-reject' => _t('_' . $sModuleUri . '_lcaption_reject'),
            $sModuleUri . '-delete' => _t('_' . $sModuleUri . '_lcaption_delete')
        );

	    $aResult = array(
            'include_css' => $this->addCss(array('view.css', 'cmts.css'), true),
	    	'include_js_content' => $this->getViewJs(),
            'filter' => $oSearchResult->showAdminFilterPanel($this->_oDb->unescape($aParams['filter_value']), $sModuleUri . '-filter-txt', $sModuleUri . '-filter-chb', $sModuleUri . '-filter'),
            'content' => $this->displayList($aParams),
            'control' => $oSearchResult->showAdminActionsPanel($this->sCssPrefix . '-view-admin', $aButtons, $sModuleUri . '-ids')
        );
                
	    return $this->addJs(array('main.js'), true) . $this->parseHtmlByName('admin.html', $aResult);
	}
	function displayList($aParams) {
	    $sSampleType = $aParams['sample_type'];
	    $iViewerId = isset($aParams['viewer_id']) ? (int)$aParams['viewer_id'] : 0;
	    $iViewerType = $aParams['viewer_type'];
	    $iStart = isset($aParams['start']) ? (int)$aParams['start'] : -1;
	    $iPerPage = isset($aParams['count']) ? (int)$aParams['count'] : -1;
	    $bShowEmpty = isset($aParams['show_empty']) ? $aParams['show_empty'] : true;
        $bAdminPanel = $iViewerType == BX_TD_VIEWER_TYPE_ADMIN && ((isset($aParams['admin_panel']) && $aParams['admin_panel']) || $sSampleType == 'admin');

        $sModuleUri = $this->_oConfig->getUri();
	    $aEntries = $this->_oDb->getEntries($aParams);
	    if(empty($aEntries)) 
	    	return $bShowEmpty ? MsgBox(_t('_' . $sModuleUri . '_msg_no_results')) : "";
	    	    
	    $oTags = new BxDolTags();
	    $oCategories = new BxDolCategories();
	    
	    //--- Language translations ---//
        $sLKLinkApprove = _t('_' . $sModuleUri . '_lcaption_approve');
        $sLKLinkReject = _t('_' . $sModuleUri . '_lcaption_reject');        
        $sLKLinkEdit = _t('_' . $sModuleUri . '_lcaption_edit');
        $sLKLinkDelete = _t('_' . $sModuleUri . '_lcaption_delete');
        
        $sBaseUri = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri();
        $sJsMainObject = $this->_oConfig->getJsObject();

        $aResult['sample'] = $sSampleType;
        $aResult['bx_repeat:entries'] = array();        
	    foreach($aEntries as $aEntry) {
	        $sVotes = "";

	        if($this->_oConfig->isVotesEnabled() && $aEntry['is_vote'] == 1) {
                $oVotes = $this->_oModule->_createObjectVoting($aEntry['id']);
                $sVotes = $oVotes->getJustVotingElement(0, $aEntry['id']);
	        }

	        $aResult['bx_repeat:entries'][] = array(
                'id' => $this->_oConfig->getSystemPrefix() . $aEntry['id'],
                'author_icon' => get_member_icon($aEntry['author_id'], 'left'),
                'caption' => str_replace("$", "&#36;", $aEntry['caption']),
                'class' => !in_array($sSampleType, array('view')) ? ' ' . $this->sCssPrefix . '-text-snippet' : '',
                'date' => getLocaleDate($aEntry['date']),
                'content' => str_replace("$", "&#36;", $aEntry['content']),
                'link' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aEntry['uri'],
                'voting' => $sVotes,
				'bx_if:checkbox' => array(
                    'condition' => $bAdminPanel,
                    'content' => array(
                        'id' => $aEntry['id']
                    ),
                ),
                'bx_if:status' => array(
                    'condition' => ($iViewerType == BX_TD_VIEWER_TYPE_MEMBER && $iViewerId == $aEntry['author_id']) || $iViewerType == BX_TD_VIEWER_TYPE_ADMIN,
                    'content' => array(
                        'status' => _t('_' . $sModuleUri . '_status_' . $aEntry['status'])
                    ),
                ),
                'bx_if:edit_link' => array (
                    'condition' => ($iViewerType == BX_TD_VIEWER_TYPE_MEMBER  && $iViewerId == $aEntry['author_id']) || $iViewerType == BX_TD_VIEWER_TYPE_ADMIN,
                    'content' => array(
                        'edit_link_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'post/' . $aEntry['uri'],
                        'edit_link_caption' => $sLKLinkEdit,
                    )
                )                
            );	        
	    };

	    $aResult['paginate'] = '';
	    if(!in_array($sSampleType, array('id', 'uri', 'view', 'search_unit'))) {
    	    if(!empty($sSampleType))
    	    	$this->_updatePaginate($aParams);

    	    $aResult['paginate'] = $this->oPaginate->getPaginate($iStart, $iPerPage);
	    }

	    $aResult['loading'] = LoadingBox($sModuleUri . '-' . $sSampleType . '-loading');

	    return $this->parseHtmlByName('list.html', $aResult);
	}
	protected function _updatePaginate($aParams) {
		switch($aParams['sample_type']) {
			case 'owner':
				$this->oPaginate->setCount($this->_oDb->getCount($aParams));
                $this->oPaginate->setOnChangePage($this->_oConfig->getJsObject() . '.changePage({start}, {per_page}, \'' . $aParams['sample_type'] . '\', \'' . urlencode(serialize($aParams['sample_params'])) . '\')');
				break;

			default:
				parent::_updatePaginate($aParams);
		}
	}
}
?>