<?php

    class BxSimpleMessengerResponse extends BxDolAlertsResponse 
    {
        function response(&$o) 
    	{
            if ( $o -> sUnit == 'profile' ) {
                switch ( $o -> sAction ) {
    				case 'delete' :
                       $oModule = BxDolModule::getInstance('BxSimpleMessengerModule');
                       $oModule -> _oDb -> deleteAllMessagesHistory($o -> iObject); 
    				break;
    			}
            }
        }
    }