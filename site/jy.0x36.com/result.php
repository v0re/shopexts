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

require_once( 'inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );

// --------------- page variables / login

$_page['name_index'] = 41;
$_page['css_name'] = 'result.css';

$logged['member'] = member_auth( 0, false );

switch ( $_REQUEST['result'] )
{
    case '1000':
        $header	= _t("_RESULT0_H");
        $result_text = _t("_RESULT0_H");
        $desc = _t("_RESULT1000");
	break;
    case '0':
        $header	= _t("_RESULT0_H");
        $result_text = _t("_RESULT0_H");
        $desc = _t("_RESULT0","<a href=\"cart.php?" . time() . "\">");
    break;
    case '-1':
		$header = _t("_RESULT-1_H");
    	$result_text = _t("_RESULT-1_A");
    	$desc = _t("_RESULT-1_D");
    break;
    case '1':
        $header	= _t("_RESULT1_H");
        $result_text = _t("_RESULT1_THANK", $site['title']);
        $desc = _t("_RESULT1_DESC");
    break;
    case '2':
        $header	= _t("_RESULT1_H");;
        $result_text = _t("_RESULT1_THANK", $site['title']);
        $desc = _t("_RESULT2DESC", $site['title']);
    break;
    default:
        exit;
    break;
}


if ( $_POST['result'] == 2 || $_POST['result'] == 3 )
{
    $i = 0;
    while ( $_COOKIE["cartentries$_COOKIE[memberID]"][$i] )
        setcookie( "cartentries$_COOKIE[memberID]" . "[$i]", $_COOKIE[cartentries][$i++], time() - 24*3600, "/" );
}

$_page['header'] = $header;
$_page['header_text'] = $header;

// --------------- page components

$_ni = $_page['name_index'];
$_page_cont[$_ni]['page_main_code'] = PageCompPageMainCode();

// --------------- [END] page components

PageCode();

// --------------- page components functions

/**
 * page code function
 */
function PageCompPageMainCode()
{
	global $result_text;
	global $desc;

	ob_start();

?>
<div class="result_text"><?= $result_text ?></div>
<div class="result_desc"><?= $desc ?></div>
<?

	$ret = ob_get_contents();
	ob_end_clean();

	return $ret;
}

?>
