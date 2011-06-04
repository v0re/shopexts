<?php

	/***************************************************************************
	*							Dolphin Smart Community Builder
	*							  -----------------
	*	 begin				: Mon Mar 23 2006
	*	 copyright			: (C) 2006 BoonEx Group
	*	 website			  : http://www.boonex.com/
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

	require_once( BX_DIRECTORY_PATH_ROOT . 'templates/base/scripts/BxBaseCommunicator.php');

	class BxTemplCommunicator extends BxBaseCommunicator 
	{
        /**
         * Class constructor ;
         *
         * @param   : $sPageName (string)  - page name (need for the page builder);
         * @param	: $aCommunicatorSettings (array)  - contain some necessary data ;
         * 					[ member_id	] (integer) - logged member's ID;
         * 					[ communicator_mode ] (string) - page mode ;
         * 					[ person_switcher ] (string) - switch the person mode - from me or to me ;
         * 					[ sort_mode ] (string) - type of message's sort ;
         * 					[ page ] (integer) - contain number of current page ;
         * 					[ per_page ] (integer) - contain per page number for current page ;
         * 					[ alert_page ] (integer) - contain number of current alert's page ;
         */
         function BxTemplCommunicator($sPageName, &$aCommunicatorSettings)
         {
            // call the parent constructor ;
			parent::BxBaseCommunicator($sPageName, $aCommunicatorSettings);
         }
	}

?>