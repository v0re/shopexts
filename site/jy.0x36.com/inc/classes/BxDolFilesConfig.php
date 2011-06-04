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

require_once('BxDolConfig.php');

class BxDolFilesConfig extends BxDolConfig {
	var $sPrefix;
    //sys_options parameters
    var $aGlParams;
    // array of possible file's endings
    var $aFilePostfix;
    // array of shared file's memberships
    var $aMemActions;
    
    var $isPermalinkEnabled;
    /**
	 * Constructor
	 */
	function BxDolFilesConfig ($aModule) {
	    parent::BxDolConfig($aModule);
		$this->sPrefix = 'bx_' . $this->getUri();
		$this->isPermalinkEnabled = 'on' == getParam($this->sPrefix . '_permalinks') 
		    ? true 
		    : false;
	}
	
	function getFilesPath () {
	    return $this->getHomePath() . 'data/files/';
	}
	
    function getFilesUrl () {
        return $this->getHomeUrl(). 'data/files/';
    }
    
    function getGlParam ($sPseud) {
        return getParam($this->aGlParams[$sPseud]);
    }
	
	function getMainPrefix () {
        return $this->sPrefix;
    }
    
    function getActionArray () {
        $sPref = '_' . $this->sPrefix . '_admin_';
        return array(
            'action_activate' => array(
                'caption' => $sPref . 'activate',
                'method' => 'adminApproveFile'
            ),
            'action_deactivate' => array(
                'caption' => $sPref . 'deactivate',
                'method' => 'adminDisapproveFile'
            ),
            'action_featured' => array(
                'caption' => $sPref . 'feature',
                'method' => 'adminMakeFeatured'
            ),
            'action_unfeatured' => array(
                'caption' => $sPref . 'unfeature',
                'method' => 'adminMakeUnfeatured'
            ),
            'action_delete' => array(
                'caption' => $sPref . 'delete',
                'method' => '_deleteFile'
            ),
        );
    }
    
    function getAlbumMainActionsArray () {
        $sPref = 'album_';
        return array(
            $sPref . 'edit' => array('type' => 'submit', 'value' => _t('_Edit')),
            $sPref . 'delete' => array('type' => 'submit', 'value' => _t('_Delete')),
            $sPref . 'organize' => array('type' => 'submit', 'value' => _t('_' . $this->sPrefix . '_organize_objects')),
            $sPref . 'add_objects' => array('type' => 'submit', 'value' => _t('_' . $this->sPrefix . '_add_objects')),
        );
    }

    function getUploaderSwitcher ($sLink = '') {
    	$aAllUploaders = $this->getAllUploaderArray($sLink);
    	$aList = array_values($this->getUploaderList());
    	$aChoosen = array();
    	foreach ($aAllUploaders as $sKey => $aValue) {
    		if (in_array($sKey, $aList))
    			$aChoosen[_t($sKey)] = $aValue;
    	}
        return $aChoosen;
    }
    
    function checkAllowedExts ($sExt) {
        $sAllowed = $this->getGlParam('allowed_exts');
        if (strlen($sAllowed) > 0) {
            $aExts = preg_split('/[\s,;]/', $sAllowed);
            if (!in_array($sExt, $aExts))
                 return false;
        }
        return true;
    }
    
    function getAvailableFlashExts () {
        $sAllowed = $this->getGlParam('allowed_exts');
        if (strlen($sAllowed) > 0) {
            $aAllowed = preg_split('/[\s,;]/', $sAllowed);
            foreach ($aAllowed as $sValue) {
                $sFinalExts .= "*." . $sValue . ";"; 
            }
            $sFinalExts = trim($sFinalExts, ';');
        }
        else
            $sFinalExts = "*.*";
        return $sFinalExts;
    }
    
    function getUploaderList () {
    	$aAllTypes = array('flash', 'regular', 'record', 'embed');
    	$sData = getParam($this->sPrefix . '_uploader_switcher');
    	if (strlen($sData) > 0)
			$aAllTypes = explode(',', $sData);
    	
    	foreach ($aAllTypes as $sValue) {
    		if ($sValue == 'flash')
    			$aItems[$sValue] = '_adm_admtools_Flash';
			else
    			$aItems[$sValue] = '_' . $this->sPrefix . '_' . $sValue;
    	}    	
		return $aItems;
    }
    
    function getAllUploaderArray ($sLink = '') {
    	return array(
            '_adm_admtools_Flash' => array('active' => !isset($_GET['mode']) ? true : false, 'href' => $sLink),
            '_' . $this->sPrefix . '_regular' => array('active' => $_GET['mode'] == 'single' ? true : false, 'href' => $sLink . "&mode=single"),
            '_' . $this->sPrefix . '_record' => array('active' => $_GET['mode'] == 'record' ? true : false, 'href' => $sLink . "&mode=record"),
            '_' . $this->sPrefix . '_embed' => array('active' => $_GET['mode'] == 'embed' ? true : false, 'href' => $sLink . "&mode=embed"),
        );
    }
}
?>