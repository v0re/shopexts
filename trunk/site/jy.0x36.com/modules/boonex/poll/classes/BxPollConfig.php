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

require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolConfig.php');

class BxPollConfig extends BxDolConfig 
{
	var $sUploadPath ;

    var $iAlowMembersPolls;

    // contain number of allowed member's pools;
    var $iAlowPollNumber;

    // allow or disallow the auto activation for polls;
    var $iAutoActivate;

    // contain number of visible profile polls on profile page ;
    var $iProfilePagePollsCount;

    // contain number of visible profile polls on index page ;
    var $iIndexPagePollsCount;

    // contain Db table's name ;
    var $sTableName;

    // contain Db table's name ;
    var $sTablePrefix;

    /**
	 * Constructor
	 */
	function BxPollConfig($aModule) {
	    parent::BxDolConfig($aModule);

        // get allowed members polls;
        $this -> iAlowMembersPolls =  getParam( 'enable_poll' );

        // get allowed number of polls;
        $this -> iAlowPollNumber =  getParam( 'profile_poll_num' );

        // chew poll's auto activation;
        $this -> iAutoActivate = getParam( 'profile_poll_act' ) == 'on' ? 1 : 0;
        
        $this -> iProfilePagePollsCount = getParam( 'profile_page_polls' );
        $this -> iIndexPagePollsCount   = getParam( 'index_page_polls' );
        
        // define the table name ;
        $this -> sTableName = $this -> getDbPrefix() . 'data';

        // define the prefix ;
        $this -> sTablePrefix = $this -> getDbPrefix();
	}
}
?>