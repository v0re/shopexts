<?php

define('BX_SECURITY_EXCEPTIONS', true);
$aBxSecurityExceptions = array(
    'POST.request',
    'GET.request',
    'REQUEST.request',
);

require_once('../../../inc/header.inc.php');
require_once(BX_DIRECTORY_PATH_INC . 'db.inc.php');

$sMemberApplSQL = "SELECT `url` FROM `bx_osi_main` WHERE `ID`='". (int)bx_get('ID') ."' AND `Status`='active'";
$sCont = db_value($sMemberApplSQL);

if (! $sCont)
	exit;

$sUrl = $sCont;


?>
