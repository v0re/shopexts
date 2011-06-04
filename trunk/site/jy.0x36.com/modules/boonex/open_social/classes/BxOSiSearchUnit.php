<?php

require_once(BX_DIRECTORY_PATH_INC . 'header.inc.php' );
bx_import('BxTemplSearchResultText');

class BxOSiSearchUnit extends BxTemplSearchResultText {
	var $_oConfig;
	var $sHomePath;
	var $sHomeUrl;

	var $bShowCheckboxes;

	var $oOsiTemplate;

	var $aCurrent = array(
		'name' => 'open_social',
		'title' => '_osi_Existed_applications',
		'table' => 'bx_osi_main',
		'ownFields' => array('ID', 'person_id', 'url', 'title', 'description', 'status', 'modified'),
		'searchFields' => array('title', 'description'),
		'restriction' => array(
			'status' => array('value'=>'active', 'field'=>'status', 'operator'=>'='),
		),
		'paginate' => array('perPage' => 10, 'page' => 1, 'totalNum' => 10, 'totalPages' => 1),
		'sorting' => 'last',
	);
	
	var $aPermalinks;

	function BxOSiSearchUnit(&$oConfig, $_oTemplate = null) {
		$this->_oConfig = $oConfig;
		$this->sHomePath = $this->_oConfig->getHomePath();
		$this->sHomeUrl = $this->_oConfig->getHomeUrl();

		$this->aPermalinks = array(
			'param' => 'permalinks_osi',
			'enabled' => array(
				'file' => '',
			),
			'disabled' => array(
				'file' => '',
			)
		);

		$this->bShowCheckboxes = false;
		if (isAdmin() || isModerator()) {
			$this->aCurrent['restriction']['status'] = '';
			$this->bShowCheckboxes = true;
		}

		parent::BxBaseSearchResultText();
		$this->oOsiTemplate = $_oTemplate;
	}

	function displaySearchUnit($aApplInfo) {
		global $oFunctions;

		$iAppID = (int)$aApplInfo['id'];
		$iOwnerID = (int)$aApplInfo['ownerId'];
		$sOwnerName = getNickname($iOwnerID);
		$sOwnerLink = getProfileLink($iOwnerID);
		$sAppUrl = process_text_output($aApplInfo['url']);
		$sAppDate = defineTimeInterval($aApplInfo['date']);
		$sAppTitle = process_text_output($aApplInfo['title']);
		$sStatusColor = ($aApplInfo['status']=='active') ? '#00CC00' : '#CC0000';

		$aUnitReplace = array();
		$aUnitReplace['status_color'] = $sStatusColor;
		$aUnitReplace['appl_id'] = $iAppID;
		$aUnitReplace['appl_url'] = $sAppUrl;
		$aUnitReplace['appl_title'] = $sAppTitle;
		$aUnitReplace['owner_url'] = $sOwnerLink;
		$aUnitReplace['owner_name'] = $sOwnerName;
		$aUnitReplace['appl_date'] = $sAppDate;

		return $this->oOsiTemplate->parseHtmlByTemplateName('unit_application', $aUnitReplace);
	}

	function showPagination($bAdmin = false) {
        require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolPaginate.php');
        $aLinkAddon = $this->getLinkAddByPrams();

		$oPaginate = new BxDolPaginate(array(
            'page_url' => $this->aCurrent['paginate']['page_url'],
            'count' => $this->aCurrent['paginate']['totalNum'],
            'per_page' => $this->aCurrent['paginate']['perPage'],
            'page' => $this->aCurrent['paginate']['page'],
            'per_page_changer' => true,
            'page_reloader' => true
        ));
        $sPaginate = '<div class="clear_both"></div>'.$oPaginate->getPaginate();
        
        return $sPaginate;
    }

	function _getPseud () {
	    return array(   
            'id' => 'ID',
            'ownerId' => 'person_id',
            'url' => 'url',
            'title' => 'title',
            'bodyText' => 'description',
            'status' => 'status',
            'date' => 'modified'
        );
	}
}

?>