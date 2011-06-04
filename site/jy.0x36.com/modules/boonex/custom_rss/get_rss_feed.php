<?php

require_once( '../../../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'db.inc.php' );

$sMemberRSSSQL = "SELECT `RSSUrl` FROM `bx_crss_main` WHERE `ID`='". (int)bx_get('ID') ."' AND `Status`='active'";
$sCont = db_value( $sMemberRSSSQL );

if( !$sCont )
	exit;

$sUrl = $sCont;

header( 'Content-Type: text/xml' );
readfile( $sUrl );

?>