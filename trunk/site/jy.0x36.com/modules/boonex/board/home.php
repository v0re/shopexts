<?
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

require_once( BX_DIRECTORY_PATH_MODULES . $aModule['path'] . '/classes/' . $aModule['class_prefix'] . 'Module.php');

global $_page;
global $_page_cont;

$iId = ( isset($_COOKIE['memberID']) && ($GLOBALS['logged']['member'] || $GLOBALS['logged']['admin']) ) ? (int) $_COOKIE['memberID'] : 0;
$iIndex = 57;

$_page['name_index']	= $iIndex;
$_page['css_name']		= 'main.css';

$_page['header'] = _t('_board_page_caption');
$_page['header_text'] = _t('_board_box_caption', $site['title']);

$oBoard = new BxBoardModule($aModule);
$_page_cont[$iIndex]['page_main_code'] = $oBoard->getContent($iId, $aRequest[0]);

PageCode($oBoard->_oTemplate);
?>