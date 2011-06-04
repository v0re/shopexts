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
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'admin_design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'prof.inc.php' );

$logged['admin'] = member_auth( 1, true, true );

$_page['extraCodeInHead'] = <<<EOJ
<script type="text/javascript" src="{$site['plugins']}jquery/jquery.js"></script>
EOJ;

$aFields = array(
	'Value'  => 'The value stored in the database',
	'LKey'   => 'Primary language key used for displaying',
	'LKey2'  => 'Secondary language key used for displaying in some other places',
	'LKey3'  => 'Miscelaniuos language key used for displaying in some other places.',
	'Extra'  => 'Extra parameter. Used for example as link to profile image for Sex list.',
	'Extra2' => 'Miscelanious extra parameter',
	'Extra3' => 'Miscelanious extra parameter'
);

if(bx_get('popup') !== false && (int)bx_get('popup') == 1) {
	$iAmInPopup = true;
	
	$iNameIndex = 17;
    $_page = array(
        'name_index' => $iNameIndex,
        'css_name' => array('predefined_values.css'),
        'js_name' => array(),
        'header' => _t('_adm_page_cpt_pvalues_manage'),    
        'header_text' => _t('_adm_box_cpt_pvalues_manage'),    
    );
    $_page_cont[$iNameIndex]['page_main_code'] = PageCompPageMainCode();
} else {
	$iAmInPopup = false;
	
	$iNameIndex = 0;
    $_page = array(
        'name_index' => $iNameIndex,
        'css_name' => array('predefined_values.css'),
        'js_name' => array(),
        'header' => _t('_adm_page_cpt_pvalues_manage'),    
        'header_text' => _t('_adm_box_cpt_pvalues_manage'),    
    );
    $_page_cont[$iNameIndex]['page_main_code'] = PageCompPageMainCode();	
}

PageCodeAdmin();

function PageCompPageMainCode() {
	global $iAmInPopup;
	global $aFields;

	$sDeleteIcon = $GLOBALS['oAdmTemplate']->getImageUrl('minus1.gif');
	$sUpIcon = $GLOBALS['oAdmTemplate']->getImageUrl('arrow_up.gif');
	$sDownIcon = $GLOBALS['oAdmTemplate']->getImageUrl('arrow_down.gif');

    $sPopupAdd = $iAmInPopup ? '&popup=1' : '';
    $sResultMsg = '';

	if( isset( $_POST['action'] ) and $_POST['action'] == 'Save' and isset( $_POST['PreList'] ) and is_array( $_POST['PreList'] ) ) {
        if (true === saveList( $_POST['list'], $_POST['PreList'] ))
            $sResultMsg = _t('_Success');
        else
            $sResultMsg = _t('_Failed to apply changes');
	}

	//get lists
	$aLists = array( '' => '- Select -' );
	$aKeys = getPreKeys();
	foreach ($aKeys as $aList)
		$aLists[ $aList['Key'] ] = $aList['Key'];
	
	$sListIn = bx_get('list');
	if ($sListIn !== false) {
		$sList_db = process_db_input($sListIn);
		$sList    = process_pass_data($sListIn);
		
		$iCount = getPreValuesCount($sListIn);		
		if (!$iCount) //if no rows returned...
			$aLists[ $sList ] = $sList; //create new list
	} else {
		$sList = '';
	}

    ob_start();

    if ($sResultMsg)
        echo MsgBox($sResultMsg);
	?>	
	<script type="text/javascript">
		function createNewList() {
			var sNewList = prompt( 'Please enter name of new list' );
			
			if( sNewList == null )
				return false;
			
			sNewList = $.trim( sNewList );
			
			if( !sNewList.length ) {
				alert( 'You should enter correct name' );
				return false;
			}
			
			window.location = '<?=$GLOBALS['site']['url_admin'] . 'preValues.php'; ?>?list=' + encodeURIComponent( sNewList ) + '<?= $sPopupAdd ?>';
		}
		
		function addRow( eImg ) {

			$( eImg ).parent().parent().before(
				'<tr>' +
				<?
				foreach( $aFields as $sField => $sHelp ) {
					?>
					'<td><input type="text" class="value_input" name="PreList[' + iNextInd + '][<?= $sField ?>]" value="" /></td>' +
					<?
				}
				?>
					'<th>' +
						'<img src="<?= $sDeleteIcon ?>"     class="row_control" title="Delete"    alt="Delete" onclick="delRow( this );" />' +
						'<img src="<?= $sUpIcon ?>"   class="row_control" title="Move up"   alt="Move up" onclick="moveUpRow( this );" />' +
						'<img src="<?= $sDownIcon ?>" class="row_control" title="Move down" alt="Move down" onclick="moveDownRow( this );" />' +
					'</th>' +
				'</tr>'
			);
			
			iNextInd ++;
			
			sortZebra();
		}
		
		function delRow( eImg ) {
			$( eImg ).parent().parent().remove();
			sortZebra();
		}
		
		function moveUpRow( eImg ) {
			var oCur = $( eImg ).parent().parent();
			var oPrev = oCur.prev( ':not(.headers)' );
			if( !oPrev.length )
				return;
			
			// swap elements values
			var oCurElems  = $('input', oCur.get(0));
			var oPrevElems = $('input', oPrev.get(0));
			
			oCurElems.each( function(iInd) {
				var oCurElem  = $( this );
				var oPrevElem = oPrevElems.filter( ':eq(' + iInd + ')' );
				
				// swap them
				var sCurValue = oCurElem.val();
				oCurElem.val( oPrevElem.val() );
				oPrevElem.val( sCurValue );
			} );
		}
		
		function moveDownRow( eImg ) {
			var oCur = $( eImg ).parent().parent();
			var oPrev = oCur.next( ':not(.headers)' );
			if( !oPrev.length )
				return;
			
			// swap elements values
			var oCurElems  = $('input', oCur.get(0));
			var oPrevElems = $('input', oPrev.get(0));
			
			oCurElems.each( function(iInd) {
				var oCurElem  = $( this );
				var oPrevElem = oPrevElems.filter( ':eq(' + iInd + ')' );
				
				// swap them
				var sCurValue = oCurElem.val();
				oCurElem.val( oPrevElem.val() );
				oPrevElem.val( sCurValue );
			} );
		}
		
		function sortZebra() {
			$( '#listEdit tr:even' ).removeClass( 'even odd' ).addClass( 'even' );
			$( '#listEdit tr:odd'  ).removeClass( 'even odd' ).addClass( 'odd'  );
		}
		
		//just a design
		$( document ).ready( sortZebra );
	</script>
	
	<form action="<?=$GLOBALS['site']['url_admin'] . 'preValues.php'; ?>" method="post">
		<table id="listEdit" cellpadding="0" cellspacing="0">
			<tr>
				<th colspan="<?= count( $aFields ) + 1 ?>">
					Select a list:
					<select name="list"
					  onchange="if( this.value != '' ) window.location = '<?=$GLOBALS['site']['url_admin'] . 'preValues.php'; ?>' + '?list=' + encodeURIComponent( this.value ) + '<?= $sPopupAdd ?>';">
						<?= genListOptions( $aLists, $sList ) ?>
					</select>
					<input type="button" value="Create New" onclick="createNewList();" />
				</th>
			</tr>
	<?
	if( $sList !== '' ) {
		$iNextInd = genListRows( $sList_db );
		?>
			<tr>
				<th colspan="8">
					<input type="hidden" name="popup" value="<?= $iAmInPopup ?>" />
					<input type="submit" name="action" value="Save" />
				</th>
			</tr>
		<?
	} else
		$iNextInd = 0;
	?>
		</table>
		
		<script type="text/javascript">
			iNextInd = <?= $iNextInd ?>;
		</script>
	</form>
	<?
	return $GLOBALS['oAdmTemplate']->parseHtmlByName('design_box_content.html', array('content' => ob_get_clean()));
}

function genListOptions( $aLists, $sActive ) {
	$sRet = '';
	foreach( $aLists as $sKey => $sValue ) {
		$sRet .= '
			<option value="' .
			htmlspecialchars( $sKey ) .
			'"' . ( ( $sKey == $sActive ) ? ' selected="selected"' : '' ) .
			'>' . htmlspecialchars( $sValue ) . '</option>';
	}
	
	return $sRet;
}

function genListRows( $sList ) {
	global $aFields;

	$sDeleteIcon = $GLOBALS['oAdmTemplate']->getImageUrl('minus1.gif');
	$sUpIcon = $GLOBALS['oAdmTemplate']->getImageUrl('arrow_up.gif');
	$sDownIcon = $GLOBALS['oAdmTemplate']->getImageUrl('arrow_down.gif');
	
	$aRows = getPreValues($sList);
	
	?>
		<tr class="headers">
	<?
	foreach( $aFields as $sField => $sHelp ) {
		?>
			<th>
				<span class="tableLabel"
				  onmouseover="showFloatDesc( '<?= addslashes( htmlspecialchars( $sHelp ) ) ?>' );"
				  onmousemove="moveFloatDesc( event );"
				  onmouseout="hideFloatDesc();">
					<?= $sField ?>
				</span>
			</th>
		<?
	}
	?>
			<th>&nbsp;</th>
		</tr>
	<?
	
	$iCounter = 0;
	
	foreach ($aRows as $aRow) {
		?>
		<tr>
		<?
		foreach( $aFields as $sField => $sHelp ) {
			?>
			<td><input type="text" class="value_input" name="PreList[<?= $iCounter ?>][<?= $sField ?>]" value="<?= htmlspecialchars( $aRow[$sField] ) ?>" /></td>
			<?
		}
		?>
			<th><img src="<?=$sDeleteIcon?>"     class="row_control" title="Delete"    alt="Delete" onclick="delRow( this );" /><img src="<?= $sUpIcon ?>"   class="row_control" title="Move up"   alt="Move up" onclick="moveUpRow( this );" /><img src="<?= $sDownIcon ?>" class="row_control" title="Move down" alt="Move down" onclick="moveDownRow( this );" /></th>
		</tr>
		<?
		
		$iCounter ++;
	}
	?>
		<tr class="headers">
			<td colspan="<?= count( $aFields ) ?>">&nbsp;</td>
			<th>
                <img src="<?= $GLOBALS['oAdmTemplate']->getImageUrl('plus1.gif') ?>" class="row_control" title="Add" alt="Add" onclick="addRow( this );" />
			</th>
		</tr>
	<?
	
	return $iCounter;
}

function saveList( $sList, $aData ) {
	global $aFields;
	global $iAmInPopup;
	
	$sList_db = trim( process_db_input( $sList ) );
	
	if( $sList_db == '' )
		return false;
	
	$sQuery = "DELETE FROM `" . BX_SYS_PRE_VALUES_TABLE . "` WHERE `Key` = '$sList_db'";
	
	db_res( $sQuery );
	
	$sValuesAlter = '';
	
	foreach( $aData as $iInd => $aRow ) {
		$aRow['Value'] = str_replace( ',', '', trim( $aRow['Value'] ) );
		
		if( $aRow['Value'] == '' )
			continue;
		
		$sValuesAlter .= "'" . process_db_input( $aRow['Value'] ) . "', ";
		
		$sInsFields = '';
		$sInsValues = '';
		foreach( $aFields as $sField => $sTemp ) {
			$sValue = trim( process_db_input( $aRow[$sField] ) );
			
			$sInsFields .= "`$sField`, ";
			$sInsValues .= "'$sValue', ";
		}
		
		$sInsFields = substr( $sInsFields, 0, -2 ); //remove ', '
		$sInsValues = substr( $sInsValues, 0, -2 );
		
		$sQuery = "INSERT INTO `" . BX_SYS_PRE_VALUES_TABLE . "` ( `Key`, $sInsFields, `Order` ) VALUES ( '$sList_db', $sInsValues, $iInd )";
		
		db_res( $sQuery );
	}
	
	//alter Profiles table
	$sValuesAlter = substr( $sValuesAlter, 0, -2 ); //remove ', '
	$sQuery = "SELECT `Name` FROM `sys_profile_fields` WHERE `Type` = 'select_set' AND `Values` = '#!{$sList_db}'";
	$rFields = db_res( $sQuery );
	while( $aField = mysql_fetch_assoc( $rFields ) ) {
		$sField = $aField['Name'];
		
		$sQuery = "ALTER TABLE `Profiles` CHANGE `$sField` `$sField` set($sValuesAlter) NOT NULL default ''";
		db_res( $sQuery );
	}

	compilePreValues();

    if( $iAmInPopup )
        echo '<script type="text/javascript">window.close()</script>';

    return true;
}

?>
