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

    require_once( BX_DIRECTORY_PATH_ROOT . 'templates/base/scripts/BxBaseBrowse.php');

	class BxTemplBrowse extends BxBaseBrowse 
	{
		/**
		 * Class constructor ;
         *
		 * @param 		: $aFilteredSettings (array) ;
		 * 					: 	sex (string) - set filter by sex,
		 *					: 	age (string) - set filter by age,
		 *					: 	country (string) - set filter by country,
		 *					: 	photos_only (string) - set filter 'with photo only',
		 *					: 	online_only (string) - set filter 'online only',
		 * @param		: $aDisplaySettings (array) ;
		 * 					: page (integer) - current page,
		 * 					: per_page (integer) - number ellements for per page,
		 * 					: sort (string) - sort parameters for SQL instructions,
		 * 					: mode (mode) - switch mode to extended and simple,
		 * @param		: $sPageName (string) - need for page builder ;
		 */
		function BxTemplBrowse( &$aFilteredSettings, &$aDisplaySettings, $sPageName ) 
		{
		    // call the parent constructor ;
            parent::BxBaseBrowse( $aFilteredSettings, $aDisplaySettings, $sPageName );
		}
	}

?>