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

    bx_import('BxDolCron');

    require_once('BxSpyModule.php');

    class BxSpyCron extends BxDolCron 
    {
        var $oSpyObject;
        var $iAllowedRows;

        /** 
         * Class constructor;
         */
        function BxSpyCron()
        {
            $this -> oSpyObject = BxDolModule::getInstance('BxSpyModule');   
            $this -> iAllowedRows   = $this -> oSpyObject -> _oConfig -> iAllowedRows;
        }

        /**
         * Function will delete all unnecessary events;
         */
        function processing() 
        {
            $iEventsCount = $this -> oSpyObject -> _oDb -> getActivityCount();
            $iCount = $iEventsCount - $this -> iAllowedRows;

            if($iCount > 0) {
                $this -> oSpyObject -> _oDb -> deleteUselessData($iCount);
            }
        }
    }