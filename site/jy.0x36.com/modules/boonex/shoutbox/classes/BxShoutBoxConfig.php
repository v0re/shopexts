<?php

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

    class BxShoutBoxConfig extends BxDolConfig 
    {
        // contain Db table's name ;
        var $sTablePrefix;
        var $iLifeTime;

		var $iUpdateTime;
		var $iAllowedMessagesCount;
		var $bProcessSmiles;
		var $aSmiles;

        /**
    	 * Class constructor;
    	 */
    	function BxShoutBoxConfig($aModule) 
        {
    	    parent::BxDolConfig($aModule);

            // define the tables prefix ;
            $this -> sTablePrefix 			= $this -> getDbPrefix();
            $this -> iLifeTime 				= (int) getParam('shoutbox_clean_oldest'); //in seconds

            $this -> iUpdateTime            = (int) getParam('shoutbox_update_time'); //(in milliseconds)
            $this -> iAllowedMessagesCount  = (int) getParam('shoutbox_allowed_messages');

            $this -> bProcessSmiles         = 'on' == getParam('shoutbox_process_smiles')
            	? true
            	: false;    

            $this -> iBlockExpirationSec   = (int) getParam('shoutbox_block_sec'); //in seconds

            //list of processed smiles
            $this -> aSmiles = array(
                ':arrow:'  => 'icon_arrow.gif',
                ':D'       => 'icon_biggrin.gif',
                ':-D'      => 'icon_biggrin.gif',
                ':grin:'   => 'icon_biggrin.gif',
                ':?'       => 'icon_confused.gif',
                ':-?'      => 'icon_confused.gif',
                '???:'     => 'icon_confused.gif',
                '8)'       => 'icon_cool.gif',
                '8-)'      => 'icon_cool.gif',
                ':cool:'   => 'icon_cool.gif',
                ':cry:'    => 'icon_cry.gif',
                ':shock:'  => 'icon_eek.gif',
                ':evil:'   => 'icon_evil.gif',
                ':!:'      => 'icon_exclaim.gif',
                ':idea:'   => 'icon_idea.gif',
                ':lol:'    => 'icon_lol.gif',
                ':x'       => 'icon_mad.gif',
                ':-x'      => 'icon_mad.gif',
                ':mad:'    => 'icon_mad.gif',
                ':mrgreen' => 'icon_mrgreen.gif',
                ':|'       => 'icon_neutral.gif',
                ':-|'      => 'icon_neutral.gif',
                ':neutral' => 'icon_neutral.gif',
                ':?:'      => 'icon_question.gif',
                ':P'       => 'icon_razz.gif',
                ':-P'      => 'icon_razz.gif',
                ':razz:'   => 'icon_razz.gif',
                ':oops:'   => 'icon_redface.gif',
                ':roll:'   => 'icon_rolleyes.gif',
                ':('       => 'icon_sad.gif',
                ':-('      => 'icon_sad.gif',
                ':sad:'    => 'icon_sad.gif',
                ':)'       => 'icon_smile.gif',
                ':-)'      => 'icon_smile.gif',
                ':smile:'  => 'icon_smile.gif',
                ':o'       => 'icon_surprised.gif',
                ':-o'      => 'icon_surprised.gif',
                ':eek:'    => 'icon_surprised.gif',
                ':twisted' => 'icon_twisted.gif',
                ':wink:'   => 'icon_wink.gif',
                ';)'       => 'icon_wink.gif',
                ';-)'      => 'icon_wink.gif',
            );
    	}
    }