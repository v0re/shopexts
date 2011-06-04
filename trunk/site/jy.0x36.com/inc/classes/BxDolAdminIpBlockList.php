<?php

/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Group
*     website              : http://www.boonex.com
* This file is part of Dolphin - Smart Community Builder
*
* Dolphin is free software; you can redistribute it and/or modify it under
* the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the
* License, or  any later version.
*
* Dolphin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
* See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with Dolphin,
* see license.txt file; if not, write to marketing@boonex.com
***************************************************************************/

bx_import('BxDolMistake');
bx_import('BxTemplFormView');

class BxDolAdminIpBlockList extends BxDolMistake {
    var $_oDb;
    var $_sActionUrl;

	/**
	 * constructor
	 */
	function BxDolAdminIpBlockList($sActionUrl = '') {
	    parent::BxDolMistake();

	    $this->_oDb = $GLOBALS['MySQL'];
 	    $this->_sActionUrl = !empty($sActionUrl) ? $sActionUrl : bx_html_attribute($_SERVER['PHP_SELF']) . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '');
	}

	function GenStoredMemIPs() {
		$sFromC = _t('_From');
        $sMemberC = _t('_Member');
		$sDatatimeC = _t('_Date');
		$sCaptionC = _t('_adm_ipbl_Stored_members_caption');

		$sRes = '<br /><h2>'.$sCaptionC.'</h2>';

		$sTableRes .= <<<EOF
<table style="width:99%;" border="1" cellpadding="2" cellspacing="1" style="border-collapse: collapse">
	<tr>
		<td>{$sFromC}</td>
        <td>{$sMemberC}</td>
		<td>{$sDatatimeC}</td>
	</tr>
EOF;

		$sCntSQL = "SELECT COUNT(*) FROM `sys_ip_members_visits`";
		////////////////////////////
		$iTotalNum = db_value( $sCntSQL );
		if( !$iTotalNum ) {
			return $sRes . MsgBox(_t('_Empty'));
		}
		$iPerPage = (int)$_GET['per_page'];
		if( !$iPerPage )
			$iPerPage = 10;
		$iCurPage = (int)$_GET['page'];
		if( $iCurPage < 1 )
			$iCurPage = 1;
		$sLimitFrom = ( $iCurPage - 1 ) * $iPerPage;
		$sqlLimit = "LIMIT {$sLimitFrom}, {$iPerPage}";
		////////////////////////////

		$sSQL = "SELECT * FROM `sys_ip_members_visits` ORDER BY `DateTime` ASC {$sqlLimit}";
		$rIPList = db_res( $sSQL );

		while( $aIPList = mysql_fetch_assoc( $rIPList ) ) {
			$iID = (int)$aIPList['ID'];
			$sFrom = long2ip($aIPList['From']);
			$sLastDT = $aIPList['DateTime'];
            $sMember = $aIPList['MemberID'] ? '<a href="' . getProfileLink($aIPList['MemberID']) . '">' . getNickname($aIPList['MemberID']) . '</a>' : '';

			$sTableRes .= "<tr><td>{$sFrom}</td><td>{$sMember}</td><td>{$sLastDT}</td></tr>";
		}

		$sTableRes .= <<<EOF
</table>
<div class="clear_both"></div>
<br />
EOF;

		$sRequest = $GLOBALS['site']['url_admin'] . 'ip_blacklist.php?page={page}&per_page={per_page}';
		$oPaginate = new BxDolPaginate (
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

		return $sRes . $sTableRes . $oPaginate -> getPaginate();
	}

	function GenIPBlackListTable() {
		$sSitePluginsUrl = BX_DOL_URL_PLUGINS;

		$sFromC = _t('_From');
		$sToC = _t('_To');
		$sTypeC = _t('_adm_ipbl_IP_Role');
		$sDescriptionC = _t('_Description');
		$sDatatimeC = _t('_adm_ipbl_Date_of_finish');
		$sActionC = _t('_Action');
		$sEditC = _t('_Edit');
		$sDeleteC = _t('_Delete');
		$sType0C = _t('_adm_ipbl_Type0_desc');
		$sType1C = _t('_adm_ipbl_Type1_desc');
		$sType2C = _t('_adm_ipbl_Type2_desc');

		$sSQL = "SELECT *, FROM_UNIXTIME(`LastDT`) AS `LastDT_U` FROM `sys_ip_list` ORDER BY `From` ASC";
		$rIPList = db_res( $sSQL );

		$sTitle = '';

		$iIpListType = (int)getParam('ipListGlobalType');
		switch ($iIpListType) {
			case 1:
				$sTitle = '<h2>'.$sType1C.'</h2>';
				break;
			case 2:
				$sTitle = '<h2>'.$sType2C.'</h2>';
				break;
			case 0:
			default:
				$sTitle = '<h2>'.$sType0C.'</h2>';
				break;
		}

		$sRows = '';
		while( $aIPList = mysql_fetch_assoc( $rIPList ) ) {
			$iID = (int)$aIPList['ID'];
			$sFrom = long2ip($aIPList['From']);

			$sTo = ($aIPList['To'] == 0) ? '' : long2ip($aIPList['To']);
			$sType = process_html_output($aIPList['Type']);
			//$sDates = explode(' ', $aIPList['LastDT_U']);
			$sLastDT = $aIPList['LastDT_U'];
			//list($iYear, $iMonth, $iDay) = explode( '-', $sDates[0]);
			$sDesc = process_html_output($aIPList['Desc']);

			$sRows .= <<<EOF
<tr>
	<td>{$sFrom}</td>
	<td>{$sTo}</td>
	<td>{$sType}</td>
	<td>{$sLastDT}</td>
	<td>{$sDesc}</td>
	<td>
		<a href="javascript:void(0)" onclick="ip_accept_values_to_form('{$iID}', '{$sFrom}', '{$sTo}', '{$sType}', '{$sLastDT}', '{$sDesc}'); return false;">{$sEditC}</a> | 
		<a href="{$this->_sActionUrl}?action=apply_delete&id={$iID}">{$sDeleteC}</a>
	</td>
</tr>
EOF;
		}

		return <<<EOF
{$sTitle}
<table style="width:99%;" border="1" cellpadding="2" cellspacing="1" style="border-collapse: collapse">
	<tr>
		<td>{$sFromC}</td>
		<td>{$sToC}</td>
		<td>{$sTypeC}</td>
		<td>{$sDatatimeC}</td>
		<td>{$sDescriptionC}</td>
		<td>{$sActionC}</td>
	</tr>
	{$sRows}
</table>
<div class="clear_both"></div>

<script type="text/javascript">
	function ip_accept_values_to_form(id_val, from_val, to_val, type_val, lastdt_val, desc_val) {
		$('.form_input_hidden[name="id"]').val(id_val);
		$('.form_input_text[name="from"]').val(from_val);
		$('.form_input_text[name="to"]').val(to_val);
		$('.form_input_select[name="type"]').val(type_val);
		$('.form_input_datetime[name="LastDT"]').val(lastdt_val);
		$('.form_input_text[name="desc"]').val(desc_val);
	}
</script>
EOF;
	}

	function getManagingForm() {
		$sApplyChangesC = _t('_sys_admin_apply');
		$sFromC = _t('_From');
		$sToC = _t('_To');
		$sSampleC = _t('_adm_ipbl_sample');
		$sTypeC = _t('_adm_ipbl_IP_Role');
		$sDescriptionC = _t('_Description');
		$sDatatimeC = _t('_adm_ipbl_Date_of_finish');
		$sErrorC = _t('_Error Occured');

		$aForm = array(
			'form_attrs' => array(
				'name' => 'apply_ip_list_form',
				'action' => $this->_sActionUrl,
				'method' => 'post',
			),
			'params' => array (
				'db' => array(
					'table' => 'sys_ip_list',
					'key' => 'ID',
					'submit_name' => 'add_button',
				),
			),
			'inputs' => array(
				'FromIP' => array(
					'type' => 'text',
					'name' => 'from',
					'caption' => $sFromC,
					'label' => $sSampleC . ': 10.0.0.0',
					'required' => true,
					'checker' => array (
						'func' => 'length',
						'params' => array(7,15),
						'error' => $sErrorC,
					),
				),
				'ToIP' => array(
					'type' => 'text',
					'name' => 'to',
					'caption' => $sToC,
					'label' => $sSampleC . ': 10.0.0.100',
					'required' => true,
					'checker' => array (
						'func' => 'length',
						'params' => array(7,15),
						'error' => $sErrorC,
					),
				),
				'IPRole' => array(
					'type' => 'select',
					'name' => 'type',
					'caption' => $sTypeC,
					'values' => array('allow', 'deny'),
					'required' => true,
				),
                'DateTime' => array(
                    'type' => 'datetime',
                    'name' => 'LastDT',
                    'caption' => $sDatatimeC,
                    'required' => true,
                    'checker' => array (
                        'func' => 'DateTime',
                        'error' => $sErrorC,
                    ),
                    'db' => array (
                        'pass' => 'DateTime', 
                    ),    
                    'display' => 'filterDate',
                ),
				'Desc' => array(
					'type' => 'text',
					'name' => 'desc',
					'caption' => $sDescriptionC,
					'required' => true,
					'checker' => array (
						'func' => 'length',
						'params' => array(2,128),
						'error' => $sErrorC,
					),
					'db' => array (
						'pass' => 'Xss', 
					),
				),
                'ID' => array(
                    'type' => 'hidden',
                    'value' => '0',
                    'name' => 'id',
                ),
				'add_button' => array(
					'type' => 'submit',
					'name' => 'add_button',
					'value' => $sApplyChangesC,
				),
			),
		);

		$sResult = '';
		$oForm = new BxTemplFormView($aForm);
		$oForm->initChecker();
		if ($oForm->isSubmittedAndValid()) {
            /*list($iDay, $iMonth, $iYear) = explode( '/', $_REQUEST['datatime']);
            $iDay = (int)$iDay;
            $iMonth = (int)$iMonth;
            $iYear = (int)$iYear;
			//$sCurTime = date("Y:m:d H:i:s");// 2012-06-20 15:46:21
			$sCurTime = "{$iYear}:{$iMonth}:{$iDay} 12:00:00";*/

			$sFrom = ip2long($_REQUEST['from']);
			$sTo = ip2long($_REQUEST['to']);

			$sType = ((int)$_REQUEST['type']==1) ? 'deny' : 'allow';

			$aValsAdd = array (
				'From' => $sFrom,
				'To' => $sTo,
				/*'LastDT' => $sCurTime,*/
				'Type' => $sType
			);

			$iLastId = ((int)$_REQUEST['id']>0) ? (int)$_REQUEST['id'] : -1;

			if ($iLastId>0) {
				$oForm->update($iLastId, $aValsAdd);
			} else {
				$iLastId = $oForm->insert($aValsAdd);
			}

			$sResult = ($iLastId > 0) ? MsgBox(_t('_Success')) : MsgBox($sErrorC);
		}
		return $sResult . $oForm->getCode();
	}

	function ActionApplyDelete() {
		$iID = (int)$_REQUEST['id'];

		if ($iID>0) {
			$sDeleteSQL = "DELETE FROM `sys_ip_list` WHERE `ID`='{$iID}' LIMIT 1";
			db_res($sDeleteSQL);
		}
	}

    function deleteExpired () {
        $iTime = time();
        $r = db_res("DELETE FROM `sys_ip_list` WHERE `LastDT` <= $iTime");    
        if ($r && ($iAffectedRows = db_affected_rows())) {
            db_res("OPTIMIZE TABLE `sys_ip_list`");
            return $iAffectedRows;
        } else {
            return 0;
        }
    }
}

?>
