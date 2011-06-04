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

class BxFilesConfig extends BxDolFilesConfig {
	/**
	 * Constructor
	 */
	function BxFilesConfig($aModule) {
	    parent::BxDolFilesConfig($aModule);
	    
	    $this->aFilePostfix = array(
            'original' => '_{ext}'
        );
        
	    $this->aGlParams = array(
            'auto_activation' => 'bx_files_activation',
            'mode_top_index' => 'bx_files_mode_index',
            'category_auto_approve' => 'category_auto_activation_bx_files',
            'number_all' => 'bx_files_number_all',
            'number_index' => 'bx_files_number_index',
            'number_user' => 'bx_files_number_user',
            'number_related' => 'bx_files_number_related',
            'number_top' => 'bx_files_number_top',
            'number_browse' => 'bx_files_number_browse',
            'number_albums_browse' => 'bx_files_number_albums_browse',
            'number_albums_home' => 'bx_files_number_albums_home',
            'browse_width' => 'bx_files_thumb_width',
	        'allowed_exts' => 'bx_files_allowed_exts',
			'profile_album_name' => 'bx_files_profile_album_name',
        );
	}
	    
    function getAllUploaderArray ($sLink = '') {
    	return array(
            '_adm_admtools_Flash' => array('active' => !isset($_GET['mode']) ? true : false, 'href' => $sLink),
            '_' . $this->sPrefix . '_regular' => array('active' => $_GET['mode'] == 'single' ? true : false, 'href' => $sLink . "&mode=single"),
        );
    }
}

?>