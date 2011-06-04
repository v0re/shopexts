<?php

require_once(BX_DIRECTORY_PATH_INC . "membership_levels.inc.php");
require_once(BX_DIRECTORY_PATH_INC . "match.inc.php");
require_once(BX_DIRECTORY_PATH_BASE . 'scripts/BxBaseSearchResultText.php');

class BxBaseSearchProfile extends BxBaseSearchResultText {
	var $aCurrent = array(
		'name' => 'profile',
		'title' => '_People',
		'table' => 'Profiles',
		'ownFields' => array('ID', 'NickName', 'NickName', 'Headline', 'DescriptionMe', 'Country', 'City', 'Tags', 'DateReg', 'DateOfBirth', 'Sex', 'Couple'),
		'searchFields' => array('NickName', 'Headline', 'DescriptionMe', 'City', 'Tags'),
		'restriction' => array(
			'activeStatus' => array('value'=>'Active', 'field'=>'Status', 'operator'=>'='),
		),
		'paginate' => array('perPage' => 10, 'page' => 1, 'totalNum' => 10, 'totalPages' => 1),
		'sorting' => 'last'
	);

	var $aPermalinks = array(
		'param' => 'enable_modrewrite',
		'enabled' => array(
			'file' => '{uri}',
			'browseAll' => 'browse.php'
		),
		'disabled' => array(
			'file' => 'profile.php?ID={id}',
			'browseAll' => 'browse.php'
		)
	);

	var $aAllovedAvtionsOfVisitor;

	function BxBaseSearchProfile ($sParamName = '', $sParamValue = '', $sParamValue1 = '', $sParamValue2 = '') {
		parent::BxBaseSearchResultText();
		$this->iRate = 0;

		$this->aAllovedAvtionsOfVisitor = array();
		switch ($sParamName) {
            case 'calendar':
                $GLOBALS ['_page']['header'] =  _t('_sys_profiles_caption_browse_by_day')
                    . ': ' . getLocaleDate( strtotime("{$sParamValue}-{$sParamValue1}-{$sParamValue2}")
                        , BX_DOL_LOCALE_DATE_SHORT);

                $sParamValue = (int)$sParamValue;
                $sParamValue1 = (int)$sParamValue1;
                $sParamValue2 = (int)$sParamValue2;
                $this->aCurrent['restriction']['calendar-min'] = array('value' => "'{$sParamValue}-{$sParamValue1}-{$sParamValue2} 00:00:00'", 'field' => 'DateReg', 'operator' => '>=', 'no_quote_value' => true);
                $this->aCurrent['restriction']['calendar-max'] = array('value' => "'{$sParamValue}-{$sParamValue1}-{$sParamValue2} 23:59:59'", 'field' => 'DateReg', 'operator' => '<=', 'no_quote_value' => true);
                $this->aCurrent['title'] = $GLOBALS ['_page']['header'];
                break;
		}
	}

	function displaySearchUnit($aData, $aExtendedCss = array()) {
		$sCode = '';
		$sOutputMode = (isset ($_GET['search_result_mode']) && $_GET['search_result_mode']=='ext') ? 'ext' : 'sim';

		$sTemplateName = ($sOutputMode == 'ext') ? 'search_profiles_ext.html' : 'search_profiles_sim.html';

		if ($sTemplateName) {
			if ($aData['Couple'] > 0) {
				$aProfileInfoC = getProfileInfo( $aData['Couple'] );
				$sCode .= $this->PrintSearhResult( $aData, $aProfileInfoC, $aExtendedCss, $sTemplateName );
			} else {
				$sCode .= $this->PrintSearhResult( $aData, array(), $aExtendedCss, $sTemplateName );
			}
		}
		return $sCode;
	}

	/**
	 * @description : function will generate profile block (used the profile template );
	 * @return 		: Html presentation data ;
	*/
	function PrintSearhResult($aProfileInfo, $aCoupleInfo = '', $aExtendedKey = null, $sTemplateName = '', $oCustomTemplate = null) {
		global $site;
		global $aPreValues;

		$iVisitorID = getLoggedId();
		$bExtMode = ( !empty($_GET['mode']) && $_GET['mode']=='extended') || 
			( !empty($_GET['search_result_mode']) && $_GET['search_result_mode'] == 'ext');
		$enable_zodiac = ($bExtMode) ? getParam('zodiac') : false;

		if ($bExtMode && count($this->aAllovedAvtionsOfVisitor)==0) { //like init
			$aCheckGreet = checkAction($iVisitorID, ACTION_ID_SEND_VKISS);
			$this->aAllovedAvtionsOfVisitor['GREET'] = $aCheckGreet;

			$aCheckMess = checkAction($iVisitorID, ACTION_ID_SEND_MESSAGE);
			$this->aAllovedAvtionsOfVisitor['MESS'] = $aCheckMess;
		}

		$aOnline = array();
		if (isset($aProfileInfo['is_online'])) {
			$aOnline['is_online'] = $aProfileInfo['is_online'];
		}

		$sProfileThumb = get_member_thumbnail( $aProfileInfo['ID'], 'none', ! $bExtMode, 'visitor', $aOnline );

		// profile Nick/Age/Sex etc.
		$sAgeStr = ($aProfileInfo['DateOfBirth'] != "0000-00-00" ? (_t("_y/o", age( $aProfileInfo['DateOfBirth'] )) .' ') : "");
		$sAgeOnly = ($aProfileInfo['DateOfBirth'] != "0000-00-00" ? ( age( $aProfileInfo['DateOfBirth'] )) : "");
		$y_o_sex = $sAgeStr ;

		$city =  _t("_City").": ".process_line_output($aProfileInfo['City']);
	
		$country = $aProfileInfo['Country'] ? 
		_t("_Country").": "._t($aPreValues['Country'][$aProfileInfo['Country']]['LKey']).'&nbsp;<img src="'. ($site['flags'].strtolower($aProfileInfo['Country'])) .'.gif" alt="flag" />'
		: '';
		
		// country flag
		$sFlag = ($aProfileInfo['Country'] != '') ? '&nbsp;<img src="'. ($site['flags'].strtolower($aProfileInfo['Country'])) .'.gif" alt="flag" />' : '';

		$sCityName = ($aProfileInfo['City']) ? process_line_output($aProfileInfo['City']) . ', ' : null ;

        if (!empty($aProfileInfo['Country']))
            $city_con = $sFlag . ' ' . $sCityName . _t( $aPreValues['Country'][$aProfileInfo['Country']]['LKey'] );
        else 
            $city_con = '';
				
		$id = _t("_ID").": ".$aProfileInfo['ID'];

		// description
		$i_am = $i_am2 = _t("_I am");
		$i_am_desc = trim( strip_tags($aProfileInfo['DescriptionMe']) );

		if ( mb_strlen($i_am_desc) > 130 )
			$i_am_desc = mb_substr( $i_am_desc, 0, 130 ) . '...';

		$you_are = $you_are2 = _t("_You are");

		$sCity = $aProfileInfo['City'];

		//--- Greeting start ---//
		if( $bExtMode &&  ( $aAllovedAvtionsOfVisitor['GREET'][CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED )  && ( $iVisitorID != $aProfileInfo['ID'] ) ) {
			$iKissIcon = getTemplateIcon('action_greet_small.gif');
			$sAiKiss = "<img alt=\"" . _t("_Greet") . "\" class=\"links_image\" name=i01$aProfileInfo[ID] src=\"" . $iKissIcon . "\" />";
			$al_kiss = '<a target=_blank href="greet.php?sendto=' . $aProfileInfo[ID] . '"';
			$al_kiss .= ">";
			$al_kiss = "<span class=\"links_span\">" . $sAiKiss . $al_kiss . _t("_Greet")."</a></span>";
		} else {
			$al_kiss =	''; 
		}
		//--- Greeting end ---//

		//--- Contact start ---//
		if( $bExtMode && ( $aAllovedAvtionsOfVisitor['MESS'][CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED )  && ( $iVisitorID != $aProfileInfo['ID'] ) ) {
			$sSendMsgIcon = getTemplateIcon('action_send_small.gif');
			$ai_sendmsg  = "<img alt=\""._t("_SEND_MESSAGE")."\" name=i02$aProfileInfo[ID] src=\"{$sSendMsgIcon}\" class=\"links_image\" />";
			$al_sendmsg  = "<a href=\"compose.php?ID=$aProfileInfo[ID]\"";
			$al_sendmsg .= ">";
			$al_sendmsg  = "<span class=\"links_span\">" . $ai_sendmsg . $al_sendmsg . _t("_Contact")."</a></span>";
		} else {
			$al_sendmsg = '';
		}
		//--- Contact end ---//		


		$more = '<a href="' . getProfileLink( $aProfileInfo['ID'] ) . '" target="_blank">';
			$more .= '<img src="' . $site['icons'] . 'desc_more.gif" alt="' . _t('_more') . '" />';
		$more .= '</a>';


		$sProfile2ASc1 = $sProfile2ASc2 = $sProfile2Nick = $sProfile2AgeSex = $sProfile2CityCon = $sProfile2Desc = $sProfile2Match = '';
		if ($aCoupleInfo) {
			// profile Nick/Age/Sex etc.
			$sNickName2 = '<a href="' . getProfileLink( $aCoupleInfo['ID'] ) . '">' . $aCoupleInfo['NickName'] . '</a>';

			$sAgeStr2 = ($aCoupleInfo['DateOfBirth'] != "0000-00-00" ? (_t("_y/o", age( $aCoupleInfo['DateOfBirth'] )) .' ') : "");
			$sAgeOnly2 = ($aCoupleInfo['DateOfBirth'] != "0000-00-00" ? ( age( $aCoupleInfo['DateOfBirth'] )) : "");
			$y_o_sex2 = $sAgeStr2;// . _t("_".$aCoupleInfo['Sex']);

			$city2 =  _t("_City").": ".process_line_output($aCoupleInfo['City']);
			$country2 = _t("_Country").": "._t($aPreValues['Country'][$aCoupleInfo['Country']]['LKey']).'&nbsp;<img src="'. ($site['flags'].strtolower($aCoupleInfo['Country'])) .'.gif" alt="flag" />';
			$city_con2 = '&nbsp;&nbsp;' . $sFlag . ' ' . process_line_output($aCoupleInfo['City']).", "._t($aPreValues['Country'][$aCoupleInfo['Country']]['LKey']);
			$city_con2 = preg_replace("/,$/", '', trim($city_con2) );

			$id2 = _t("_ID").": ".$aCoupleInfo['ID'];

			// description
			$i_am = $i_am2 = _t("_I am");
			$i_am_desc2 = trim( strip_tags($aCoupleInfo['DescriptionMe']) );

			if ( mb_strlen($i_am_desc2) > 130 )
				$i_am_desc2 = mb_substr( $i_am_desc2, 0, 130 ) . '...';
				
			$sCity2 = $aCoupleInfo['City'];

			$sProfile2ASc1 = 'float:left;width:31%;margin-right:10px;';
			$sProfile2ASc2 = 'float:left;width:31%;display:block;';

			$sProfile2Nick = $sNickName2;
			$sProfile2AgeSex = $y_o_sex2;

			$sProfile2CityCon = $city_con2;
			$sProfile2Desc = $i_am_desc2;

			$sProfile2Match = ($bExtMode && isLogged() && ( $iVisitorID != $aCoupleInfo['ID'] ) && getParam( 'view_match_percent' )) ? $GLOBALS['oFunctions']->getProfileMatch( $iVisitorID, $aCoupleInfo['ID'] ) : '';
		} else {
			$sProfile2ASc2 = 'display:none;';
		}

		// match progress bar
		$sProfileMatch = ($bExtMode && isLogged() && ( $iVisitorID != $aProfileInfo['ID'] ) && getParam( 'view_match_percent' )) ? $GLOBALS['oFunctions']->getProfileMatch( $iVisitorID, $aProfileInfo['ID'] ) : '';

		$sHeadline = null;
		if ($aProfileInfo['Headline']) {
			$sHeadline = ( mb_strlen($aProfileInfo['Headline']) > 30 ) ? mb_substr($aProfileInfo['Headline'], 0, 30) . '...' : $aProfileInfo['Headline'];
		}

		$sNickName = getNickName($aProfileInfo['ID']);
		$Link = getProfileLink($aProfileInfo['ID']);

		$sProfileNickname = ($sHeadline) ? "<a href='$Link'>" . $sNickName . '</a> :' : "<a href='$Link'>" . $sNickName . '</a>';
		$sSexIcon = $GLOBALS['oFunctions']->genSexIcon($aProfileInfo['Sex']);
		$sProfileZodiac = ($enable_zodiac) ? $GLOBALS['oFunctions']->getProfileZodiac($aProfileInfo['DateOfBirth']) : '';

		$aKeys = array(
			'thumbnail' => $sProfileThumb,
			'nick' => $sProfileNickname,
			'head_line' => $sHeadline,
			'age' => $y_o_sex,
			'city_con' => $city_con,
			'i_am_desc' => $i_am_desc,
			'sex_image' => $sSexIcon,

			'add_style_c1' => $sProfile2ASc1,
			'add_style_c2' => $sProfile2ASc2,
			'nick2' => $sProfile2Nick,
			'age_sex2' => $sProfile2AgeSex,
			'city_con2' => $sProfile2CityCon,
			'i_am_desc2' => $sProfile2Desc,
			'match2' => $sProfile2Match,
			'row_title' => process_line_output($aProfileInfo['Headline']),
			'thumbnail' => $sProfileThumb,
			'match' => $sProfileMatch,
			'sex_image' => $sSexIcon,
			'age' => $y_o_sex,
			'city' => $city,
			'just_city' => $sCity,
			'just_age' => $sAgeOnly,
			'city_con' => $city_con,
			'country' => $country,
			'id' => $id,
			'zodiac_sign' => $sProfileZodiac,
			'i_am' => $i_am,
			'i_am_desc' => $i_am_desc,
			'you_are' => $you_are,
			'ai_kiss' => empty($sAiKiss) ? '' : $sAiKiss,
			'al_kiss' => $al_kiss,
			'ai_sendmsg' => empty($ai_sendmsg) ? '' : $ai_sendmsg,
			'al_sendmsg' => $al_sendmsg,
			'more' => $more,
			'images' => $site['images']
		);

		if ( $aExtendedKey and is_array($aExtendedKey) and !empty($aExtendedKey) ) {
			foreach($aExtendedKey as $sKey => $sValue )
				$aKeys[$sKey] = $sValue;
		} else {
			$aKeys['ext_css_class'] = '';
		}

		return ($oCustomTemplate) ? $oCustomTemplate->parseHtmlByName($sTemplateName, $aKeys) : $GLOBALS['oSysTemplate']->parseHtmlByName($sTemplateName, $aKeys);
	}
	
    function displaySearchBox ($sCode, $sPaginate = '') {
        $sCode = $GLOBALS['oFunctions']->centerContent($sCode, '.searchrow_block_simple');
		$sClearBoth = '<div class="clear_both"></div>';
        $sCode = DesignBoxContent(_t($this->aCurrent['title']), '<div class="searchContentBlock">'.$sCode.$sClearBoth.'</div>'. $sPaginate, 1);
        if (!isset($_GET['searchMode']))
           $sCode = '<div id="page_block_'.$this->id.'">'.$sCode.$sClearBoth.'</div>';
        return $sCode;
    }
    
    function _getPseud () {
        return array(
            'date' => 'DateReg'
        );
    }
}
?>
