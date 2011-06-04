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

require_once( BX_DIRECTORY_PATH_BASE . 'scripts/BxBaseMenu.php' );

class BxBaseMenuQlinks2 extends BxBaseMenu {
	var $iButtonWidthPerc;
	var $iColumnsCnt;

	function BxBaseMenuQlinks2() {
		parent::BxBaseMenu();

		$this->iColumnsCnt = 4;
		$this->iButtonWidthPerc = 25;
	}

	function getCode() {
		$this->getMenuInfo();
		$this->sCode .= '<div class="quickLinksBlock"><div class="actionsBlock">';
		$this->genTopItems();
		$this->sCode .= <<<EOF
	<div class="clear_both"></div>
</div></div>
<div class="clear_both"></div>
EOF;

		return $this->sCode;
	}

	function genTopItems() {
		$iCount = 0;
		$iColumnWidth = (100 - 2 * $this->iColumnsCnt) / $this->iColumnsCnt;
		$this->iButtonWidthPerc = $iColumnWidth;

		$iAllCategs = 0;
		//pre count of all amount
		foreach( $this->aTopMenu as $iItemID => $aItem ) {
			if ($aItem['BQuickLink'] != '1' || ($aItem['Type'] != 'top' && $aItem['Type'] != 'system'))
				continue;
			if (! $this->checkToShow( $aItem ) )
				continue;

			$iAllCategs++;
		}

		if ($iAllCategs == 0) return;

		$iCategPerColumn = ceil($iAllCategs/$this->iColumnsCnt);

        $aQlinksUnits = array();
		$iCounter = 0;
		foreach( $this->aTopMenu as $iItemID => $aItem ) {
			if ($aItem['BQuickLink'] != '1' || ($aItem['Type'] != 'top' && $aItem['Type'] != 'system'))
				continue;
			if( !$this->checkToShow( $aItem ) )
				continue;

			//generate
			list( $aItem['Link'] ) = explode( '|', $aItem['Link'] );

			$aItem['Link']    = $this->replaceMetas( $aItem['Link'] );
			$aItem['Onclick'] = $this->replaceMetas( $aItem['Onclick'] );

			$aItem['Caption'] = str_replace( "{memberNick}",  $this->aMenuInfo['memberNick'],  $aItem['Caption'] );

			$bActive = ( $iItemID == $this->aMenuInfo['currentTop'] );

			$sPicture = $aItem['Picture'];
			$sPictureVal = (isset($sPicture) && $sPicture!='') ? getTemplateIcon($sPicture) : '';

			////////////////////////
			$iResidueOfDiv = $iCounter % $iCategPerColumn;

			////////////////////////
			$aQlinksUnits[_t($aItem['Caption'])] = $this->genTopItem( _t($aItem['Caption']), $aItem['Link'], $aItem['Target'], $aItem['Onclick'], $bActive, $iItemID, $sPictureVal );
			//$aQlinksUnits[_t($aItem['Caption'])] = 'values';

			$iCounter++;
		}
        ksort($aQlinksUnits);
        $this->sCode .= implode('', $aQlinksUnits);
		$iResidueOfDivLast = $iCounter % $iCategPerColumn;

	}

	function genTopItem( $sText, $sLink, $sTarget, $sOnclick, $bActive, $iItemID, $sPictureVal ) {
		//global $oSysTemplate;

		$sOnclick = $sOnclick ? ( ' onclick="' . $sOnclick . '"' ) : '';
		$sTarget = $sTarget  ? ($sTarget) : '_self';

		if ( strpos( $sLink, 'http://' ) === false && !strlen($sOnclick) )
			$sLink = BX_DOL_URL_ROOT . $sLink;

		$sScriptAction = ($sOnclick=='' && $sLink!='') ? " onclick=\"window.open ('{$sLink}','_self');\" " : $sOnclick;
		
		$sSubMenu = $this->getAllSubMenus($iItemID);
		$sMainSubs = ($sSubMenu=='') ? '' : $sSubMenu;

		return <<<EOF
<div class="button_wrapper" style="width: 24%; padding: 3px;">
	<div class="button_wrapper_close"></div>
	<div class="button_input_wrapper">
        <button type="button" $sScriptAction>
            <img src="$sPictureVal" />
            {$sText}
        </button>
	</div>
</div>
{$sMainSubs}
EOF;

	}

	function getAllSubMenus($iItemID, $bActive = false) {
		$aMenuInfo = $this->aMenuInfo;

		$ret = '';
		
		$aTTopMenu = $this->aTopMenu;

		foreach( $aTTopMenu as $iTItemID => $aTItem ) {
			if( strpos( $aTItem['Visible'], $aMenuInfo['visible'] ) === false )
				continue;
			if( $aTItem['BQuickLink'] != '1' )
				continue;

			if ($iItemID == $aTItem['Parent']) {
				//generate
				list( $aTItem['Link'] ) = explode( '|', $aTItem['Link'] );

				$aTItem['Link'] = str_replace( "{memberID}",    $aMenuInfo['memberID'],    $aTItem['Link'] );
				$aTItem['Link'] = str_replace( "{memberNick}",  $aMenuInfo['memberNick'],  $aTItem['Link'] );
				$aTItem['Link'] = str_replace( "{memberLink}",  $aMenuInfo['memberLink'],  $aTItem['Link'] );

				$aTItem['Link'] = str_replace( "{profileID}",   $aMenuInfo['profileID'],   $aTItem['Link'] );
				$aTItem['Link'] = str_replace( "{profileNick}", $aMenuInfo['profileNick'], $aTItem['Link'] );
				$aTItem['Link'] = str_replace( "{profileLink}", $aMenuInfo['profileLink'], $aTItem['Link'] );

				$aTItem['Onclick'] = str_replace( "{memberID}",    $aMenuInfo['memberID'],    $aTItem['Onclick'] );
				$aTItem['Onclick'] = str_replace( "{memberNick}",  $aMenuInfo['memberNick'],  $aTItem['Onclick'] );
				$aTItem['Onclick'] = str_replace( "{memberPass}",  getPassword( $aMenuInfo['memberID'] ),  $aTItem['Onclick'] );

				$aTItem['Onclick'] = str_replace( "{profileID}",   $aMenuInfo['profileID'],   $aTItem['Onclick'] );
				$aTItem['Onclick'] = str_replace( "{profileNick}", $aMenuInfo['profileNick'], $aTItem['Onclick'] );

				$sPicture = $aTItem['Picture'];
				$sPictureVal = (isset($sPicture) && $sPicture!='') ? getTemplateIcon($sPicture) : '';
				$sElement = $this->getCustomMenuItem( _t( $aTItem['Caption'] ), $aTItem['Link'], $aTItem['Target'], $aTItem['Onclick'], $aTItem['Statistics'], ( $iTItemID == $aMenuInfo['currentCustom'] ), $sPictureVal );

				$ret .= $sElement;
			}
		}

		return $ret;
	}

	function getCustomMenuItem( $sText, $sLink, $sTarget, $sOnclick, $sStatistics, $bActive, $sPictureVal ) {
		global $tmpl;

		$sTarget = $sTarget  ? ($sTarget) : '_self';

		if( strlen( $sOnclick ) )
			$sOnclick = ' onclick="' . $sOnclick . '" ';

		if ( strpos( $sLink, 'http://' ) === false && !strlen($sOnclick) )
			$sLink = BX_DOL_URL_ROOT . $sLink;

		$sScriptAction = ($sOnclick=='' && $sLink!='') ? " onclick=\"window.open ('{$sLink}','_self');\" " : $sOnclick;

		//$sStatistics
		$sStatVal = '';
		$sStatType = trim($sStatistics);
		if ($this->aMenuInfo['memberID']>0 && $sStatType!='') {
			$sStatSQL = db_value("SELECT `SQL` FROM `sys_stat_member` WHERE `Type`='{$sStatType}'");
			if ($sStatSQL != '') {
				$sStatSQL = str_replace('__member_id__', $this->aMenuInfo['memberID'], $sStatSQL);
				$sStatVal = '&nbsp;(' . (int)db_value($sStatSQL) . ')&nbsp;';
			}
		}

		$ret = <<<EOF
<div class="button_wrapper" style="width: 24%; padding: 3px;">
	<div class="button_wrapper_close"></div>
	<div class="button_input_wrapper">
        <button type="button" $sScriptAction>
            <img src="$sPictureVal" />
            {$sText}{$sStatVal}
        </button>
	</div>
</div>
EOF;

		return $ret;
	}
}

?>
