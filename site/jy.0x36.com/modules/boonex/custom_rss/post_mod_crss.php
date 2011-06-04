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

require_once('../../../inc/header.inc.php');
require_once( BX_DIRECTORY_PATH_INC . 'admin_design.inc.php' );
bx_import('BxDolPaginate');

$logged['admin'] = member_auth( 1, true, true );

// $_page['css_name']	= 'browse.css';
// $_page['header'] = "Browse custom RSS";

if (isset($_POST['crsss'])  && is_array($_POST['crsss'])) {
	foreach($_POST['crsss'] as $iCrss) {
 		switch (true) {
			case isset($_POST['action_delete']):
 				$iOldID = (int)$iCrss;
				$sRSSSQL = "DELETE FROM `bx_crss_main` WHERE `ID`='{$iOldID}'";
				db_res($sRSSSQL);
				break;
			case isset($_POST['action_approve']):
				$iOldID = (int)$iCrss;
				$sRSSSQL = "UPDATE `bx_crss_main` SET `status`='active' WHERE `ID`='{$iOldID}'";
				db_res($sRSSSQL);
				break;
			case isset($_POST['action_disapprove']):
				$iOldID = (int)$iCrss;
				$sRSSSQL = "UPDATE `bx_crss_main` SET `status`='passive' WHERE `ID`='{$iOldID}'";
				db_res($sRSSSQL);
				break;
		}
 	}
}

///////////////pagination/////////////////////
$iTotalNum = db_value( "SELECT COUNT(*) FROM `bx_crss_main` WHERE `ProfileID`>0" );
if( !$iTotalNum )
	$sRSSs .= MsgBox(_t('_Empty'));
$iPerPage = (int)bx_get('per_page');
if (!$iPerPage)
	$iPerPage = 10;
$iCurPage = (int)bx_get('page');
if( $iCurPage < 1 )
	$iCurPage = 1;
$sLimitFrom = ( $iCurPage - 1 ) * $iPerPage;
$aSqlQuery = "LIMIT {$sLimitFrom}, {$iPerPage}";
///////////////eof pagination/////////////////////

$aManage = array('medID', 'medProfId', 'medTitle', 'medUri', 'medDate', 'medViews', 'medExt', 'Approved');

if ($iTotalNum > 0) {
	$sMemberRSSSQL = "SELECT * FROM `bx_crss_main` {$aSqlQuery}";
	$vMemberRSS = db_res($sMemberRSSSQL);

	while( $aRSSInfo = mysql_fetch_assoc($vMemberRSS) ) {
		$iRssID = (int)$aRSSInfo['ID'];
		$sRssUrl = process_line_output($aRSSInfo['RSSUrl']);
		$sRssDesc = process_line_output($aRSSInfo['Description']);
		$sRssUrlJS = addslashes(htmlspecialchars($sRssUrl));
		$sStatusColor = ($aRSSInfo['Status']=='active') ? '#00CC00' : '#CC0000';

		$sRSSs .= <<<EOF
<div class="browseUnit" style="border: 2px solid {$sStatusColor}; height:75px; width:46%;float:left;margin:5px;">
	<div class="browseCheckbox">
		<input id="ch{$iRssID}" type="checkbox" value="{$iRssID}" name="crsss[]" />
	</div>
	<div class="clear_both"></div>
	<div class="addInfo" style="width:290px;">
		&nbsp;URL: <input type="text" name="rss_url" id="rss_url" size="35" maxlength="255" value="{$sRssUrl}" />
	</div>
	<div class="addInfo" style="width:290px;">
		&nbsp;Description: {$sRssDesc}
	</div>
</div>
EOF;
	}

	$sRequest = bx_html_attribute($_SERVER['PHP_SELF']) . '?page={page}&per_page={per_page}';

	///////////////pagination/////////////////////
	// gen pagination block ;
	$oPaginate = new BxDolPaginate
	(
		array
		(
			'page_url'	=> $sRequest,
			'count'		=> $iTotalNum,
			'per_page'	=> $iPerPage,
			'page'		=> $iCurPage,
			'per_page_changer'	 => true,
			'page_reloader'		 => true,
			'on_change_page'	 => null,
			'on_change_per_page' => null,
		)
	);
	$sPagination = $oPaginate -> getPaginate();
	///////////////eof pagination/////////////////////

	bx_import('BxTemplSearchResult');
	$oSearchResult = new BxTemplSearchResult();
	$sAdmPanel = $oSearchResult->showAdminActionsPanel('crss_box', array('action_approve' => '_Approve', 'action_disapprove' => '_Disapprove', 'action_delete' => '_Delete'), 'crsss');
	$sUrl = bx_html_attribute($_SERVER['PHP_SELF']);
	$sCode .= <<<EOF
<form action="{$sUrl}" method="post" name="ads_moderation">
	<div id="crss_box">
		{$sRSSs}
		<div class="clear_both"></div>
		{$sPagination}
	</div>
	{$sAdmPanel}
</form>
EOF;
}

$sHeaderValue = _t('_crss_Manager');
$sCode = ($sCode == '') ? MsgBox(_t('_Empty')) : $sCode;
$sResult = DesignBoxAdmin($sHeaderValue, $sCode);

$iNameIndex = 9;
$_page = array(
    'name_index' => $iNameIndex,
    'css_name' => array('common.css', 'forms_adv.css', 'browse.css'),
    'header' => $sHeaderValue,
    'header_text' => $sHeaderValue
);
$_page_cont[$iNameIndex]['page_main_code'] = $sResult;
PageCodeAdmin();

?>