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

define('BX_DOL_LANGUAGE_CATEGORY_SYSTEM', 1);

if (!defined ('BX_SKIP_INSTALL_CHECK')) {
	$sCurrentLanguage = getCurrentLangName(false);
	if( !$sCurrentLanguage ) {
		echo '<br /><b>Fatal error:</b> Cannot apply localization.';
		exit;
	}
	require_once( BX_DIRECTORY_PATH_ROOT . "langs/lang-{$sCurrentLanguage}.php" );
}

require_once(BX_DIRECTORY_PATH_INC . 'db.inc.php');
require_once(BX_DIRECTORY_PATH_INC . 'utils.inc.php');
require_once(BX_DIRECTORY_PATH_INC . 'profiles.inc.php');
require_once(BX_DIRECTORY_PATH_INC . 'params.inc.php');

if (!defined ('BX_SKIP_INSTALL_CHECK')) {
	getCurrentLangName(true);

    if (isset($_GET['lang'])) {
        bx_import('BxDolPermalinks');
        $oPermalinks = new BxDolPermalinks();
        if ($oPermalinks->redirectIfNecessary(array('lang')))
            exit;
    }
}

function getCurrentLangName($isSetCookie = true) {
	$sLang = '';
	
	if( !$sLang && !empty($_GET['lang']) ) $sLang = tryToGetLang( $_GET['lang'], $isSetCookie );
	if( !$sLang && !empty($_POST['lang']) ) $sLang = tryToGetLang( $_POST['lang'], $isSetCookie );
	if( !$sLang && !empty($_COOKIE['lang']) ) $sLang = tryToGetLang( $_COOKIE['lang'] );
	if( !$sLang && !empty($_SERVER['HTTP_ACCEPT_LANGUAGE']) ) $sLang = tryToGetLang( $_SERVER['HTTP_ACCEPT_LANGUAGE'] );
	if( !$sLang ) $sLang = tryToGetLang( getParam( 'lang_default' ) );
	if( !$sLang ) $sLang = tryToGetLang( 'en' );

    setlocale(LC_TIME, $sLang.'_'.strtoupper($sLang).'.utf-8', $sLang.'_'.strtoupper($sLang).'.utf8', $sLang.'.utf-8', $sLang.'.utf8', $sLang);

	return $sLang;
}

function tryToGetLang( $sLangs, $bSetCookie = false ) {
	$sLangs = trim( $sLangs );
	if( !$sLangs )
		return '';
	
	$sLangs = preg_replace( '/[^a-zA-Z0-9,;-]/m', '', $sLangs ); // we do not need 'q=0.3'. we are using live queue :)
	$sLangs = strtolower( $sLangs );
	
	if( !$sLangs )
		return '';
	
	$aLangs = explode( ',', $sLangs ); // ru,en-us;q=0.7,en;q=0.3 => array( 'ru' , 'en-us;q=0.7' , 'en;q=0.3' );
	foreach( $aLangs as $sLang ) {
		if( !$sLang ) continue;
		
		list( $sLang ) = explode( ';', $sLang, 2 ); // en-us;q=0.7 => en-us
		if( !$sLang ) continue;
		
		// check with country
		if( checkLangExists( $sLang ) ) {
			if( $bSetCookie && ($_COOKIE['lang'] != $sLang) && ($GLOBALS['glLangSet'] != $sLang) ) {
				setLangCookie( $sLang );
                $GLOBALS['glLangSet'] = $sLang;
            }
			return $sLang;
		}
		
		//drop country
		list( $sLang, $sCntr ) = explode( '-', $sLang, 2 ); // en-us => en
		if( !$sLang or !$sCntr ) continue; //no lang or nothing changed
		
		//check again. without country
		if( checkLangExists( $sLang ) ) {
			if( $bSetCookie )
				setLangCookie( $sLang );
			return $sLang;
		}
	}
	
	return '';
}

function checkLangExists( $sLang ) {
    if (!preg_match('/^[A-Za-z0-9_]+$/', $sLang))
        return false;
	if( file_exists( BX_DIRECTORY_PATH_ROOT . "langs/lang-{$sLang}.php" ) )
		return true;

	// $sQuery = "SELECT `ID` FROM `sys_localization_languages` WHERE `Name` = '$sLang'";
	// $iLangID = (int)db_value( $sQuery );

	$iLangID = (int)$GLOBALS['MySQL']->fromCache('checkLangExists_'.$sLang, 'getOne', "SELECT `ID` FROM `sys_localization_languages` WHERE `Name` = '$sLang'");
	
	if( !$iLangID )
		return false;
	
	if( compileLanguage( $iLangID ) )
		return true;
	
	return false;
}

function setLangCookie( $sLang ) {

    $iProfileId = getLoggedId();

	if ($iProfileId) {
		$iLangID = db_value( "SELECT `ID` FROM `sys_localization_languages` WHERE `Name` = '" . process_db_input($sLang) . "'" );
		if (!$iLangID) 
			$iLangID = 0 ;

		db_res( 'UPDATE `Profiles` SET `LangID` = ' . (int) $iLangID . ' WHERE `ID` = ' . (int) $_COOKIE['memberID'] );	

        // recompile profile cache ;
        createUserDataFile($iProfileId);
    }

	setcookie( 'lang', '',     time() - 60*60*24,    '/' );
	setcookie( 'lang', $sLang, time() + 60*60*24*365, '/' );
}

/**
 * Function will generate list of installed languages list;
 *
 * @return : Html presentation data;
 */
function getLangSwitcher() {
    global $site;

    $aLangs = getLangsArr();
	if( count( $aLangs ) < 2 ) {
		return ;
    }

    $sOutputCode = null;
    foreach( $aLangs as $sName => $sLang ) {
        $sFlag  = $site['flags'] . $sName . '.gif';
        $aTemplateKeys = array (

            'bx_if:item_img' => array (
                'condition' =>  ( $sFlag ),
                'content'   => array (
                    'item_img_src'      => $sFlag,
                    'item_img_alt'      => $sName,
                    'item_img_width'    => 18,
                    'item_img_height'   => 12,
                ),
            ),

           'item_link'    => BX_DOL_URL_ROOT . 'index.php?lang=' . $sName,
            'item_onclick' => null,
            'item_title'   => $sLang,
            'extra_info'   => null,
        );

        $sOutputCode .= $GLOBALS['oSysTemplate']->parseHtmlByName( 'member_menu_sub_item.html', $aTemplateKeys );   
    }

    return $sOutputCode;
}

function getLangsArr( $bAddFlag = false, $bRetIDs = false ) {
	$rLangs = db_res('SELECT * FROM `sys_localization_languages` ORDER BY `Title` ASC');
	
	$aLangs = array();
	while( $aLang = mysql_fetch_assoc($rLangs) ) {
		$sFlag = '';
		$sFlag = $bAddFlag ? ( $aLang['Flag'] ? $aLang['Flag'] : 'xx' ) : '';
		
		$sKey = ($bRetIDs) ? $aLang['ID'] : $aLang['Name'];
		$aLangs[ $sKey ] = $aLang['Title'] . $sFlag;
	}
	
	return $aLangs;
}

function deleteLanguage($langID = 0) {
	$langID = (int)$langID;

	if($langID <= 0) return false;

	$resLangs = db_res('
			SELECT	`ID`, `Name`
			FROM	`sys_localization_languages`
			WHERE	`ID` = '.$langID);

	if(mysql_num_rows($resLangs) <= 0) return false;

	$arrLang = mysql_fetch_assoc($resLangs);

	$numStrings = db_res('
		SELECT COUNT(`IDKey`)
		FROM `sys_localization_strings`
		WHERE `IDLanguage` = '.$langID);
	$numStrings = mysql_fetch_row($numStrings);
	$numStrings = $numStrings[0];

	db_res('DELETE FROM `sys_localization_strings` WHERE `IDLanguage` = '.$langID);

	if(db_affected_rows() < $numStrings) return false;

	db_res('DELETE FROM `sys_localization_languages` WHERE `ID` = '.$langID);

	if(db_affected_rows() <= 0) return false;

	@unlink( BX_DIRECTORY_PATH_ROOT . 'langs/lang-'.$arrLang['Name'].'.php');

	// delete from email templates
	$sQuery = "DELETE FROM `sys_email_templates` WHERE `LangID` = '{$langID}'";
	db_res($sQuery);

	return true;
}

function getLocalizationKeys() {
	$resKeys = db_res('SELECT `ID`, `IDCategory`, `Key` FROM `sys_localization_keys`');

	$arrKeys = array();

	while($arr = mysql_fetch_assoc($resKeys)) {
		$ID = $arr['ID'];
		unset($arr['ID']);
		$arrKeys[$ID] = $arr;
	}

	return $arrKeys;
}

function getLocalizationStringParams($keyID) {
	$keyID = (int)$keyID;

	$resParams = db_res("
		SELECT	`IDParam`,
				`Description`
		FROM	`sys_localization_string_params`
		WHERE	`IDKey` = $keyID
		ORDER BY `IDParam`
	");

	$arrParams = array();

	while ($arr = mysql_fetch_assoc($resParams)) {
		$arrParams[(int)$arr['IDParam']] = $arr['Description'];
	}

	return $arrParams;
}

function getLocalizationCategories() {
	$resCategories = db_res('SELECT `ID`, `Name` FROM `sys_localization_categories` ORDER BY `Name`');

	$arrCategories = array();

	while ($arr = mysql_fetch_assoc($resCategories)) {
		$arrCategories[$arr['ID']] = $arr['Name'];
	}

	return $arrCategories;
}

function compileLanguage($langID = 0) {
	$langID = (int)$langID;

	$newLine = "\r\n";
	
	if($langID <= 0) {
		$resLangs = db_res('SELECT `ID`, `Name` FROM `sys_localization_languages`');
	} else {
		$resLangs = db_res('
			SELECT	`ID`, `Name`
			FROM	`sys_localization_languages`
			WHERE	`ID` = '.$langID
		);
	}

	if ( mysql_num_rows($resLangs) <= 0 )
		return false;

	while($arrLanguage = mysql_fetch_assoc($resLangs)) {
		$resKeysStrings = db_res("
			SELECT	`sys_localization_keys`.`Key` AS `Key`,
					`sys_localization_strings`.`String` AS `String`
			FROM	`sys_localization_strings` INNER JOIN
					`sys_localization_keys` ON
					`sys_localization_keys`.`ID` = `sys_localization_strings`.`IDKey`
			WHERE `sys_localization_strings`.`IDLanguage` = {$arrLanguage['ID']}");

		$handle = fopen( BX_DIRECTORY_PATH_ROOT . "langs/lang-{$arrLanguage['Name']}.php", 'w');

		if($handle === false) return false;

		$fileContent = "<?{$newLine}\$LANG = array(";

		while($arrKeyString = mysql_fetch_assoc($resKeysStrings)) {
			$langKey = str_replace("\\", "\\\\", $arrKeyString['Key']);
			$langKey = str_replace("'", "\\'", $langKey);

			$langStr = str_replace("\\", "\\\\", $arrKeyString['String']);
			$langStr = str_replace("'", "\\'", $langStr);

			$fileContent .= "{$newLine}\t'$langKey' => '$langStr',";
		}

		$fileContent = trim($fileContent, ',');

		$writeResult = fwrite($handle, $fileContent."{$newLine});?>");
		if($writeResult === false) return false;

		if(fclose($handle) === false) return false;

		@chmod( BX_DIRECTORY_PATH_ROOT . "langs/lang-{$arrLanguage['Name']}.php", 0666);
	}

	return true;
}

function addStringToLanguage($langKey, $langString, $langID = -1, $categoryID = BX_DOL_LANGUAGE_CATEGORY_SYSTEM) {
	// input validation
	$langID = (int)$langID;
	$categoryID = (int)$categoryID;

	if ( $langID == -1 ) {
		$resLangs = db_res('SELECT `ID`, `Name` FROM `sys_localization_languages`');
	} else {
		$resLangs = db_res('
			SELECT	`ID`, `Name`
			FROM	`sys_localization_languages`
			WHERE	`ID` = '.$langID);
	}

	$langKey = process_db_input($langKey, BX_TAGS_STRIP);
	$langString = process_db_input($langString, BX_TAGS_VALIDATE);

	$resInsertKey = db_res( "
		INSERT INTO	`sys_localization_keys`
		SET			`IDCategory` = $categoryID,
					`Key` = '$langKey'", false );
	if ( !$resInsertKey || db_affected_rows() <= 0 )
		return false;

	$keyID = db_last_id();

	while($arrLanguage = mysql_fetch_assoc($resLangs)) {
		$resInsertString = db_res( "
			INSERT INTO	`sys_localization_strings`
			SET			`IDKey` = $keyID,
						`IDLanguage` = {$arrLanguage['ID']},
						`String` = '$langString'", false );
		if ( !$resInsertString || db_affected_rows() <= 0 )
			return false;
			
        compileLanguage($arrLanguage['ID']);
	}

	return true;
}

function updateStringInLanguage($langKey, $langString, $langID = -1) {
	// input validation
	$langID = (int)$langID;

	if ( $langID == -1 ) {
		$resLangs = db_res('SELECT `ID`, `Name` FROM `sys_localization_languages`');
	} else {
		$resLangs = db_res('
			SELECT	`ID`, `Name`
			FROM	`sys_localization_languages`
			WHERE	`ID` = '.$langID);
	}

	$langKey = process_db_input($langKey, BX_TAGS_STRIP);
	$langString = process_db_input($langString, BX_TAGS_VALIDATE);

	$arrKey = db_arr( "
		SELECT	`ID`
		FROM	`sys_localization_keys`
		WHERE	`Key` = '$langKey'", false );

	if ( !$arrKey )
		return false;

	$keyID = $arrKey['ID'];

	while($arrLanguage = mysql_fetch_assoc($resLangs)) {
		$resUpdateString = db_res( "
			UPDATE	`sys_localization_strings`
			SET			`String` = '$langString'
			WHERE		`IDKey` = $keyID
			AND			`IDLanguage` = {$arrLanguage['ID']}", false );
		if ( !$resUpdateString || db_affected_rows() <= 0 )
			return false;
	}

	return true;
}

function deleteStringFromLanguage($langKey, $langID = -1) {
	// input validation
	$langID = (int)$langID;

	if ( $langID == -1 ) {
		$resLangs = db_res('SELECT `ID`, `Name` FROM `sys_localization_languages`');
	} else {
		$resLangs = db_res('
			SELECT	`ID`, `Name`
			FROM	`sys_localization_languages`
			WHERE	`ID` = '.$langID);
	}

	$langKey = process_db_input($langKey, BX_TAGS_STRIP);
	$langString = empty($langString) ? '' : process_db_input($langString, BX_TAGS_VALIDATE);

	$arrKey = db_arr( "
		SELECT	`ID`
		FROM	`sys_localization_keys`
		WHERE	`Key` = '$langKey'", false );

	if ( !$arrKey )
		return false;

	$keyID = $arrKey['ID'];

	while($arrLanguage = mysql_fetch_assoc($resLangs)) {
		$resDeleteString = db_res( "
			DELETE	FROM `sys_localization_strings`
			WHERE		`IDKey` = $keyID
			AND			`IDLanguage` = {$arrLanguage['ID']}", false );
		if ( !$resDeleteString || db_affected_rows() <= 0 )
			return false;
	}
	
	$resDeleteKey = db_res( "
		DELETE FROM `sys_localization_keys`
		WHERE	`Key` = '$langKey' LIMIT 1", false );

	return !$resDeleteKey || db_affected_rows() <= 0 ? false : true;
}

function _t_action( $str, $arg0 = "", $arg1 = "", $arg2 = "" ) {
    return MsgBox( _t($str,$arg0,$arg1,$arg2) );
}

function _t_echo_action( $str, $arg0 = "", $arg1 = "", $arg2 = "" ) {
    return MsgBox( _t($str,$arg0,$arg1,$arg2) );
}

function echo_t_err( $str, $arg0 = "", $arg1 = "", $arg2 = "" ) {
    return MsgBox( _t($str,$arg0,$arg1,$arg2) );
}

function _t_err( $str, $arg0 = "", $arg1 = "", $arg2 = "" ) {
    return MsgBox( _t($str,$arg0,$arg1,$arg2) );
}

function _t($key, $arg0 = "", $arg1 = "", $arg2 = "") {
	global $LANG;

	if(isset($LANG[$key])) {
		$str = $LANG[$key];
		$str = str_replace('{0}', $arg0, $str);
		$str = str_replace('{1}', $arg1, $str);
		$str = str_replace('{2}', $arg2, $str);
		return $str;
	} else {
		return $key;
	}
}

function _t_ext($key, $args) {
	global $LANG;

	if(isset($LANG[$key])) {
		$str = $LANG[$key];

		if(!is_array($args)) {
			return str_replace('{0}', $args, $str);
		}

		foreach ($args as $key => $val) {
			$str = str_replace('{'.$key.'}', $val, $str);
		}

		return $str;
	} else {
		return $key;
	}
}

?>
