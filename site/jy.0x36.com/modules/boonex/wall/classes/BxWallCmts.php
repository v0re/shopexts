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

bx_import('BxTemplCmtsView');

class BxWallCmts extends BxTemplCmtsView {
    var $_oModule;
    
	/**
	 * Constructor
	 */
	function BxWallCmts($sSystem, $iId, $iInit = 1) {
	    parent::BxTemplCmtsView($sSystem, $iId, $iInit);

	    $this->_oModule = BxDolModule::getInstance('BxWallModule');
	}
	function actionCmtPost () {
		$mixedResult = parent::actionCmtPost();
		if(empty($mixedResult)) 
			return $mixedResult;

		$aEvents = $this->_oModule->_oDb->getEvents(array('type' => 'id', 'object_id' => (int)$this->getId()));
		if(isset($aEvents[0]['owner_id']) && (int)$aEvents[0]['owner_id'] > 0) {
			//--- Wall -> Update for Alerts Engine ---//
			bx_import('BxDolAlerts');
			$oAlert = new BxDolAlerts('bx_' . $this->_oModule->_oConfig->getUri(), 'update', $aEvents[0]['owner_id']);
			$oAlert->alert();
			//--- Wall -> Update for Alerts Engine ---//
		}

		return $mixedResult;
    }
	/**
	 * get full comments block with initializations
	 */
    function getCommentsFirst ($sType) {
        return $this->_oModule->_oTemplate->parseHtmlByTemplateName('comments', array(
            'cmt_actions' => $this->getActions(0, $sType), 
            'cmt_object' => $this->getId(),
            'cmt_addon' => $this->getCmtsInit()
        ));
    }
}
?>