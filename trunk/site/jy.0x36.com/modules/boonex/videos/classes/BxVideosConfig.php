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

class BxVideosConfig extends BxDolFilesConfig {
	/**
	 * Constructor
	 */
	function BxVideosConfig (&$aModule) {
	    parent::BxDolFilesConfig($aModule);
	    $this->aFilePostfix = array(
            '.flv',
            '_small.jpg',
            '.jpg',
            '.mpg',
            '.mp4',
			'.m4v'
	    );
	    $this->aGlParams = array(
            'mode_top_index' => 'bx_videos_mode_index',
            'category_auto_approve' => 'category_auto_activation_bx_videos',
            'number_all' => 'bx_videos_number_all',
	        'number_index' => 'bx_videos_number_index',
            'number_user' => 'bx_videos_number_user',
            'number_related' => 'bx_videos_number_related',
            'number_top' => 'bx_videos_number_top',
            'number_browse' => 'bx_videos_number_browse',
            'number_previous_rated' => 'bx_videos_number_previous_rated',
	        'number_albums_browse' => 'bx_videos_number_albums_browse',
            'number_albums_home' => 'bx_videos_number_albums_home',
            'file_width' => 'bx_videos_file_width',
            'file_height' => 'bx_videos_file_height',
            'browse_width' => 'bx_videos_browse_width',
            'browse_height' => 'bx_videos_browse_height',
			'allowed_exts' => 'bx_videos_allowed_exts',
			'profile_album_name' => 'bx_videos_profile_album_name',
			'album_slideshow_on' => 'bx_videos_album_slideshow_on',
	        'album_slideshow_height' => 'bx_videos_album_slideshow_height'
	    );

        if(!defined("YOUTUBE_VIDEO_RSS"))
            define("YOUTUBE_VIDEO_RSS", 'http://gdata.youtube.com/feeds/api/videos?q="#video#"');
		if(!defined("YOUTUBE_VIDEO_PLAYER"))
            define("YOUTUBE_VIDEO_PLAYER", '<object width="100%" height="344" style="display:block;"><param name="movie" value="http://www.youtube.com/v/#video#&rel=0&color1=0xb1b1b1&color2=0xcfcfcf&feature=player_embedded&fs=1&autoplay=#autoplay#&iv_load_policy=3&showinfo=0"></param><param name="allowFullScreen" value="true"></param><param name="wmode" value="#wmode#"></param><embed src="http://www.youtube.com/v/#video#&rel=0&color1=0xb1b1b1&color2=0xcfcfcf&feature=player_embedded&fs=1&autoplay=#autoplay#&iv_load_policy=3&showinfo=0" type="application/x-shockwave-flash" allowfullscreen="true" width="100%" height="344" wmode="#wmode#"></embed></object>');
		if(!defined("YOUTUBE_VIDEO_EMBED"))
            define("YOUTUBE_VIDEO_EMBED", '<object width="425" height="344" style="display:block;"><param name="movie" value="http://www.youtube.com/v/#video#&rel=0&color1=0xb1b1b1&color2=0xcfcfcf&feature=player_embedded&fs=1&autoplay=#autoplay#"></param><param name="allowFullScreen" value="true"></param><param name="wmode" value="#wmode#"></param><embed src="http://www.youtube.com/v/#video#&rel=0&color1=0xb1b1b1&color2=0xcfcfcf&feature=player_embedded&fs=1&autoplay=#autoplay#" type="application/x-shockwave-flash" allowfullscreen="true" width="425" height="344" wmode="#wmode#"></embed></object>');
	}
	
	function getFilesPath () {
	    return BX_DIRECTORY_PATH_ROOT . 'flash/modules/video/files/';
	}
	
    function getFilesUrl () {
        return BX_DOL_URL_ROOT . 'flash/modules/video/files/';
    }
}

?>