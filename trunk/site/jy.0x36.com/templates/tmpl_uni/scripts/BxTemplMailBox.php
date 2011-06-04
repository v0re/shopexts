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

	require_once( BX_DIRECTORY_PATH_ROOT . 'templates/base/scripts/BxBaseMailBox.php');

	class BxTemplMailBox extends BxBaseMailBox 
	{
		/**
		 * Class constructor;
         *
		 * @param		: $sPageName (string)  - page name (need for page builder);
		 * @param		: $aMailBoxSettings (array)  - contain some necessary data ;
		 * 					[] member_id	(integer)- logged member's ID;
		 * 					[] recipient_id (integer) - message recipient's ID ;
		 * 					[] mailbox_mode (string) - inbox, outbox or trash switcher mode ;
		 * 					[] sort_mode (string) 	 - message sort mode;
		 * 					[] page (integer) 	 	 - number of current page ;
		 * 					[] per_page (integer) 	 - number of messages for per page ;
		 * 					[] messages_types (string) - all needed types of messages ;
		 * 					[] contacts_mode (string)  - type of contacts (friends, faves, contacted) ;
		 * 					[] contacts_page (integer) - number of current contact's page ;
		 * 					[] message_id	 (integer) - number of needed message ;
		 */

		function BxTemplMailBox($sPageName, &$aMailBoxSettings )
		{
			// call the parent constructor ;
			parent::BxBaseMailBox($sPageName, $aMailBoxSettings);
		}
	}

?>