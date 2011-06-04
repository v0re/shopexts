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


require_once( '../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'images.inc.php' );

ob_start();
$chars = array("a","b","c","d","e","f","h","i","k","m","n","o","r","s","t","u","v","w","x","z","2","3","4","5","6","7","8","9");
/*
$chars = array("a","A","b","B","c","C","d","D","e","E","f","F","g","G","h","H","i","I","j","J",
			   "k","K","L","m","M","n","N","o","p","P","q","Q","r","R","s","S","t","T",
			   "u","U","v","V","w","W","x","X","y","Y","z","Z","2","3","4","5","6","7","8","9");
*/
$textstr = '';
for ($i = 0, $length = 6; $i < $length; $i++)
   $textstr .= $chars[rand(0, count($chars) - 1)];

$hashtext = md5($textstr);

bx_import('BxDolSession');
$oSession = BxDolSession::getInstance();
$oSession->setValue('strSec', $hashtext);

if ( produceSecurityImage( $textstr, $hashtext ) != IMAGE_ERROR_SUCCESS )
{
	// output header
	header( "Content-Type: image/gif" );
	
    header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
    header( "Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT" );
    header( "Cache-Control: no-store, no-cache, must-revalidate" );
    header( "Cache-Control: post-check=0, pre-check=0", false );
    header( "Pragma: no-cache" );
	
	// output error image
	@readfile( $dir['profileImage'] . 'simg_error.gif' );
}

ob_end_flush();
?>
