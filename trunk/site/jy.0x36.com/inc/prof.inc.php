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

define('BX_SYS_PRE_VALUES_TABLE', 'sys_pre_values');


$oCache = $GLOBALS['MySQL']->getDbCacheObject();
$GLOBALS['aPreValues'] = $oCache->getData($GLOBALS['MySQL']->genDbCacheKey('sys_pre_values'));
if (null === $GLOBALS['aPreValues'])
    compilePreValues();


function getPreKeys () {
	return $GLOBALS['MySQL']->fromCache('sys_prevalues_keys', 'getAll', "SELECT DISTINCT `Key` FROM `" . BX_SYS_PRE_VALUES_TABLE . "`");
}

function getPreValues ($sKey, $aFields = array(), $iTagsFilter = BX_TAGS_NO_ACTION) {
	$sKeyDb = process_db_input($sKey, $iTagsFilter);
	$sKey   = process_pass_data($sKey);
	$sqlFields = "*";
	if (is_array($aFields) && !empty($aFields)) {
		foreach ($aFields as $sValue)
			$sqlFields .= "`$sValue`, ";
		$sqlFields = trim($sqlFields, ', ');
	}
	$sqlQuery = "SELECT $sqlFields FROM `" . BX_SYS_PRE_VALUES_TABLE ."`
				WHERE `Key` = '$sKeyDb'
				ORDER BY `Order` ASC";
	return $GLOBALS['MySQL']->getAllWithKey($sqlQuery, 'Value');
}

function getPreValuesCount ($sKey, $aFields = array(), $iTagsFilter = BX_TAGS_NO_ACTION) {
	$sKeyDb = process_db_input($sKey, $iTagsFilter);
	return $GLOBALS['MySQL']->getOne("SELECT COUNT(*) FROM `" . BX_SYS_PRE_VALUES_TABLE . "` WHERE `Key` = '$sKeyDb'");
}

function compilePreValues() {

    $GLOBALS['MySQL']->cleanCache('sys_prevalues_keys');

    $aPreValues = array ();
    $aKeys = getPreKeys();

	foreach ($aKeys as $aKey) {

		$sKey = $aKey['Key'];
        $aPreValues[$sKey] = array ();

		$aRows = getPreValues($sKey);
		foreach ($aRows as $aRow) {

            $aPreValues[$sKey][$aRow['Value']] = array ();

			foreach ($aRow as $sValKey => $sValue) {
				if ($sValKey == 'Key' or $sValKey == 'Value' or $sValKey == 'Order')
					continue; //skip key, value and order. they already used

				if (!strlen($sValue))
					continue; //skip empty values

                $aPreValues[$sKey][$aRow['Value']][$sValKey] = $sValue;
			}
			
		}		

	}

    $oCache = $GLOBALS['MySQL']->getDbCacheObject();
    $oCache->setData ($GLOBALS['MySQL']->genDbCacheKey('sys_pre_values'), $aPreValues);

    $GLOBALS['aPreValues'] = $aPreValues;
}
	
?>
