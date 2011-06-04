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

class BxPhotosConfig extends BxDolFilesConfig {
	/**
	 * Constructor
	 */
	function BxPhotosConfig (&$aModule) {
	    parent::BxDolFilesConfig($aModule);
	    $this->aFilePostfix = array(
            'thumb' => '_rt.jpg',
            'browse' => '_t.jpg',
            'icon' => '_ri.jpg',
            'file' => '_m.jpg',
            'original' => '.{ext}'
	    );
	    $this->aGlParams = array(
            'auto_activation' => 'bx_photos_activation',
            'mode_top_index' => 'bx_photos_mode_index',
            'category_auto_approve' => 'category_auto_activation_bx_photos',
            'number_all' => 'bx_photos_number_all',
	        'number_index' => 'bx_photos_number_index',
            'number_user' => 'bx_photos_number_user',
            'number_related' => 'bx_photos_number_related',
            'number_top' => 'bx_photos_number_top',
            'number_browse' => 'bx_photos_number_browse',
            'number_previous_rated' => 'bx_photos_number_previous_rated',
	        'number_albums_browse' => 'bx_photos_number_albums_browse',
	        'number_albums_home' => 'bx_photos_number_albums_home',
            'icon_width' => 'bx_photos_icon_width',
            'icon_height' => 'bx_photos_icon_height',
            'thumb_width' => 'bx_photos_thumb_width',
            'thumb_height' => 'bx_photos_thumb_height',
            'file_width' => 'bx_photos_file_width',
            'file_height' => 'bx_photos_file_height',
            'browse_width' => 'bx_photos_browse_width',
            'browse_height' => 'bx_photos_browse_height',
			'allowed_exts' => 'bx_photos_allowed_exts',
			'flickr_photo_api' => 'bx_photos_flickr_photo_api',
	        'profile_album_name' => 'bx_photos_profile_album_name',
	        'album_slideshow_on' => 'bx_photos_album_slideshow_on',
	        'album_slideshow_height' => 'bx_photos_album_slideshow_height',
	        'rss_feed_on' => 'bx_photos_rss_feed_on'
	    );

        if(!defined("FLICKR_PHOTO_RSS"))
            define("FLICKR_PHOTO_RSS", "http://api.flickr.com/services/rest/?method=flickr.photos.getInfo&api_key=#api_key#&photo_id=#photo#");
        if(!defined("FLICKR_PHOTO_URL"))
            define("FLICKR_PHOTO_URL", "http://farm#farm#.static.flickr.com/#server#/#id#_#secret##mode#.#ext#");
	}
}

?>