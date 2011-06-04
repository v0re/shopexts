<?php

    class BxFaceBookConnectAlerts extends BxDolAlertsResponse 
    {
        var $oModule;

        /**
         * Class constructor;
         */
        function BxFaceBookConnectAlerts() {
            $this -> oModule = BxDolModule::getInstance('BxFaceBookConnectModule');            
        }

        function response(&$o) 
    	{
            if ( $o -> sUnit == 'profile' ) {
                switch ( $o -> sAction ) {
                    case 'logout' :
                        $aCookies = array('user', 'session_key', 'expires', 'ss');
                        foreach ($aCookies as $sName) {
                        	$sCookieName = $this -> oModule -> _oConfig -> mApiKey . '_' . $sName;
                            if( isset($_COOKIE[$sCookieName]) ) {
                            	setcookie($sCookieName, '', time() - 96 * 3600, '/' );
                                unset($_COOKIE[$sCookieName]);
                            }
                        }
                        setcookie($this -> oModule -> _oConfig -> mApiKey, '', time() - 96 * 3600, '/' );
                        unset($_COOKIE[$this -> oModule -> _oConfig -> mApiKey]);
                        break;

                    case 'join' :
                            bx_import('BxDolSession');
                			$oSession = BxDolSession::getInstance();

                			$iFacebookProfileUid = $oSession 
                                -> getValue($this -> oModule -> _oConfig -> sFacebookSessionUid);

                            if($iFacebookProfileUid) {
                                $oSession -> unsetValue($this -> oModule -> _oConfig -> sFacebookSessionUid);

                                //save Fb's uid
                                $this -> oModule -> _oDb -> saveFbUid($o -> iObject, $iFacebookProfileUid);

								//Auto-friend members if they are already friends on Facebook
            					$this -> oModule -> _makeFriends($o -> iObject);	
                            }
                        break;

                    case 'delete' :
                    	//remove Fb account
                    	$this -> oModule -> _oDb -> deleteFbUid($o -> iObject);
                    	break;

                    default :
                }
            }
        }
    }
