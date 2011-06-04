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

require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolFilesConfig.php');

class BxSoundsConfig extends BxDolFilesConfig {
	/**
	 * Constructor
	 */
	function BxSoundsConfig (&$aModule) {
	    parent::BxDolFilesConfig($aModule);
	    $this->aFilePostfix = array(
            '.mp3',
			'.jpg'
	    );
	    $this->aGlParams = array(
            'mode_top_index' => 'bx_sounds_mode_index',
            'category_auto_approve' => 'category_auto_activation_bx_sounds',
            'number_all' => 'bx_sounds_number_all',
	        'number_index' => 'bx_sounds_number_index',
            'number_user' => 'bx_sounds_number_user',
            'number_related' => 'bx_sounds_number_related',
            'number_top' => 'bx_sounds_number_top',
            'number_browse' => 'bx_sounds_number_browse',
            'number_previous_rated' => 'bx_sounds_number_previous_rated',
	        'number_albums_browse' => 'bx_sounds_number_albums_browse',
            'number_albums_home' => 'bx_sounds_number_albums_home',
            'file_width' => 'bx_sounds_file_width',
            'file_height' => 'bx_sounds_file_height',
            'browse_width' => 'bx_sounds_browse_width',
            'browse_height' => 'bx_sounds_browse_height',
			'allowed_exts' => 'bx_sounds_allowed_exts',
			'profile_album_name' => 'bx_sounds_profile_album_name',
	    );
	}
	
	function getFilesPath () {
	    return BX_DIRECTORY_PATH_ROOT . 'flash/modules/mp3/files/';
	}
	
    function getFilesUrl () {
        return BX_DOL_URL_ROOT . 'flash/modules/mp3/';
    }
    
    function getAllUploaderArray ($sLink = '') {
        return array(
            '_adm_admtools_Flash' => array('active' => !isset($_GET['mode']) ? true : false, 'href' => $sLink),
            '_' . $this->sPrefix . '_regular' => array('active' => $_GET['mode'] == 'single' ? true : false, 'href' => $sLink . "&mode=single"),
            '_' . $this->sPrefix . '_record' => array('active' => $_GET['mode'] == 'record' ? true : false, 'href' => $sLink . "&mode=record"),
        );
    } 
}

?>