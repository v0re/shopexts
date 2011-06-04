<?php

    class BxPollResponse extends BxDolAlertsResponse 
    {
        function response(&$o) 
    	{
            if ( $o -> sUnit == 'profile' ) {
                switch ( $o -> sAction ) {
    				case 'delete' :
                       $oPoll = BxDolModule::getInstance('BxPollModule');
                       $oPoll -> _oDb -> deleteProfilePolls($o -> iObject); 
    				break;
    			}
            }
        }
    }