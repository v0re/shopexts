<?
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

bx_import('BxDolModuleTemplate');
bx_import('BxDolCategories');
bx_import('BxTemplVotingView');

class BxDolFilesTemplate extends BxDolModuleTemplate {
	/**
	 * Constructor
	 */
	function BxDolFilesTemplate (&$oConfig, &$oDb) {
	    parent::BxDolModuleTemplate($oConfig, $oDb);
	}
	
	/**
	 * @deprecated
	 */
    function init (&$oDb) {
        $this->_oDb = &$oDb;
    }

    // function of output
    function pageCode ($aPage = array(), $aPageCont = array(), $aCss = array(), $aJs = array(), $bAdminMode = false, $isSubActions = true) {
        if (!empty($aPage)) {
            foreach ($aPage as $sKey => $sValue)
                $GLOBALS['_page'][$sKey] = $sValue;
        }
        if (!empty($aPageCont)) {
            foreach ($aPageCont as $sKey => $sValue)
                $GLOBALS['_page_cont'][$aPage['name_index']][$sKey] = $sValue;
        }
        if (!empty($aCss))
            $this->addCss($aCss);
        if (!empty($aJs))
            $this->addJs($aJs);
            
        if ($isSubActions) {
            $aVars = array ('BaseUri' => $this->_oConfig->getBaseUri());
            $GLOBALS['oTopMenu']->setCustomSubActions($aVars, $this->_oConfig->getMainPrefix() . '_title', false);
        }
                        
        if (!$bAdminMode)
            PageCode($this);
        else
            PageCodeAdmin();
    }
    
    function getFileInfo ($aInfo) {
        if (empty($aInfo))
            return '';

        $aMediaInfo = array();
        $aMediaInfo['memberPic'] = get_member_thumbnail($aInfo['medProfId'], 'none', false);
        $aMediaInfo['memberUrl'] = getProfileLink($aInfo['medProfId']);
        $aMediaInfo['memberNick'] = $aInfo['NickName'];

        $aMediaInfo['dateIcon'] = $this->getIconUrl('clock.png');
        $aMediaInfo['dateInfo'] = getLocaleDate($aInfo['medDate'], BX_DOL_LOCALE_DATE_SHORT);
        $aMediaInfo['dateInfoAgo'] = defineTimeInterval($aInfo['medDate']);
        return $this->parseHtmlByName('media_info.html', $aMediaInfo);
    }
    
    function getBasicFileInfoForm (&$aInfo, $sUrlPref = '') {
        $aForm = array(
            'title' => array(
                'type' => 'value',
                'value' => $aInfo['medTitle'],
                'caption' => _t('_Title'),
            ),
            'album' => array(
                'type' => 'value',
                'value' => '<a href = "' . $sUrlPref . 'browse/album/' . $aInfo['albumUri'] . '/owner/' . $aInfo['NickName'] . '">' . $aInfo['albumCaption'] . '</a>',
                'caption' => _t('_sys_album'),
            ),
            'desc' => array(
                'type' => 'value',
                'value' => process_text_withlinks_output($aInfo['medDesc']),
                'caption' => _t('_Description'),
            ),
            'category' => array(
                'type' => 'value',
                'value' => getLinkSet($aInfo['Categories'], $sUrlPref . 'browse/category/', CATEGORIES_DIVIDER),
                'caption' => _t('_Category'),
            ),
            'tags' => array(
                'type' => 'value',
                'value' => getLinkSet($aInfo['medTags'], $sUrlPref . 'browse/tag/'),
                'caption' => _t('_Tags'),
            ),
            'url' => array(
                'type' => 'text',
                'value' => $sUrlPref . 'view/' . $aInfo['medUri'],
                'attrs' => array(
                  'onclick' => 'this.focus(); this.select();',
                  'readonly' => 'readonly',
                ),
                'caption'=> _t('_URL')
            ),
        );
        return $aForm;
    }
    
    function getCompleteFileInfoForm (&$aInfo, $sUrlPref = '') {
        return $this->getBasicFileInfoForm($aInfo, $sUrlPref);
    }
    
    function getFileInfoMain (&$aInfo) {
        $sUrlPref = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri();
        $aForm = array(
            'form_attrs' => array('id' => $this->_oConfig->getMainPrefix() . '_upload_form'),
            'params'=> array('remove_form'=>true),
            'inputs' => $this->getCompleteFileInfoForm($aInfo, $sUrlPref)
        );
        $oForm = new BxTemplFormView($aForm);
        return $oForm->getCode();
    }
    
    function getRate ($iFile) {
        $iFile = (int)$iFile;
        $sCode = '<center>' . _t('_rating not enabled') . '</center>';
        $oVotingView = new BxTemplVotingView($this->_oConfig->getMainPrefix(), $iFile);
        if ($oVotingView->isEnabled())
            $sCode = $oVotingView->getBigVoting ();
        return $sCode;
    }
    
    function getFilePic ($iFile) {
        $aIdent = array('fileId' => (int)$iFile);
        return $this->_oDb->getFileInfo($aIdent, true, array('medID', 'Type'));
    }
    
    function getFilesBox ($aCode, $sWrapperId = '') {
    	if (!is_array($aCode))
    		return '';
		else {
			ob_start();
			?>
			<div class="searchContentBlock">
				__code__
			</div>
			__paginate__
			<?
			$sCode = ob_get_clean();
			foreach ($aCode as $sKey => $sValue)
				$sCode = str_replace('__' . $sKey . '__', $sValue, $sCode);
			if (strlen($sWrapperId) > 0)
				$sCode = '<div id="' . $sWrapperId . '">' . $sCode . '</div>';
		}
		return $sCode;
    }
          
    function getAdminShort ($iNumber, $sAlbumUri, $sNickName) {
        $iNumber     = (int)$iNumber;
        $sAlbumUri   = process_db_input($sAlbumUri, BX_TAGS_STRIP);
        $sNickName   = process_db_input($sNickName, BX_TAGS_STRIP);
        $sLinkPref   = $this->_oConfig->getBaseUri(); 
        $sLinkAdd    = $sLinkPref . 'albums/my/add_objects/' . $sAlbumUri . '/owner/' . $sNickName;
        $sLinkBrowse = $sLinkPref . 'albums/my/manage_objects/' . $sAlbumUri . '/owner/' . $sNickName; 
        $aUnit = array(
            'fileStatCount' => _t('_' . $this->_oConfig->getMainPrefix() . '_count_info', $iNumber, $sLinkBrowse),
            'fileStatAdd' => _t('_' . $this->_oConfig->getMainPrefix() . '_add_info', $sLinkAdd),
        );
        return $this->parseHtmlByName('admin_short.html', $aUnit);
    }
    
    function getAdminAlbumShort ($iNumber) {
        $iNumber = (int)$iNumber;
        $sLinkPref = $this->_oConfig->getBaseUri(); 
        $sLinkAdd = $sLinkPref . 'albums/my/add/';
        $sLinkBrowse = $sLinkPref . 'albums/my/manage/'; 
        $aUnit = array(
            'fileStatCount' => _t('_' . $this->_oConfig->getMainPrefix() . '_albums_count_info', $iNumber, $sLinkBrowse),
            'fileStatAdd' => _t('_' . $this->_oConfig->getMainPrefix() . '_albums_add_info', $sLinkAdd),
        );
        return $this->parseHtmlByName('admin_short.html', $aUnit);
    }
        
    function getSitesSetBox ($sLink) {
        require_once(BX_DIRECTORY_PATH_INC . 'shared_sites.inc.php');
        $aSites = getSitesArray($sLink);
        $sSpacer = $this->getIconUrl('spacer.gif');
        $aUnits = array();
        foreach ($aSites as $aValue) {
            $aUnits[] = array(
                'icon' => $this->getIconUrl($aValue['icon']),
                'href' => $aValue['url'],
                'spacer' => $sSpacer
            );
        }
        return $this->parseHtmlByName('sites_set_box.html', array('bx_repeat:iconBlock' => $aUnits), array('{','}'));
    }
        
    function getSearchForm ($aRedInputs = array(), $aRedForm = array()) {
    	$aForm = array(
            'form_attrs' => array(
               'id' => 'searchForm',
               'action' => '',
               'method' => 'post',
               'enctype' => 'multipart/form-data',
               'onsubmit' => '',
            ),
            'inputs' => array(
                'keyword' => array(
                    'type' => 'text',
                    'name' => 'keyword',
                    'caption' => _t('_Keyword')
                ),
                'ownerName' => array(
                    'type' => 'text',
                    'name' => 'owner',
                    'caption' => _t('_Member'),
                    'attrs' => array('id'=>'ownerName')
                ),
                'status' => array(
                    'type' => 'select',
                    'name' => 'status',
                    'caption' => _t('_With status'),
                    'values' => array(
                        'any'=> _t('_' . $this->_oConfig->getMainPrefix() . '_any'),
                        'approved' => _t('_' . $this->_oConfig->getMainPrefix() . '_approved'),
                        'disapproved'=> _t('_' . $this->_oConfig->getMainPrefix() . '_disapproved'),
                        'pending'=> _t('_' . $this->_oConfig->getMainPrefix() . '_pending')
                    ),
                ),
                'search' => array(
                    'type' => 'submit',
                    'name' => 'search',
                    'value' => _t('_Search')
                )
            )
        );
        if (!empty($aRedInputs) && is_array($aRedInputs))
        	$aForm['inputs'] = array_merge($aForm['inputs'], $aRedInputs);
    	if (!empty($aRedForm) && is_array($aRedForm))
        	$aForm['form_attrs'] = array_merge($aForm['form_attrs'], $aRedForm);
    	$oForm = new BxTemplFormView($aForm);
        return $oForm->getCode();
    }

    function getHeaderCode () {
        $aUnit = array(
            'site_admin_url' => BX_DOL_URL_ADMIN,
            'site_plugins' => BX_DOL_URL_PLUGINS,
            'users_processing' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/',
            'current_tmpl' => $GLOBALS['tmpl']
        );
        return $this->parseHtmlByName('media_admin_header.html', $aUnit, array('{','}'));
    }
    
    function getBrowseBlock ($sContent, $iUnitWidth, $iUnitCount, $iWishWidth = 0) {
        $iAllWidth = $iWishWidth > 0 ? $iWishWidth : (int)getParam('main_div_width');
        $iDestWidth = getBlockWidth($iAllWidth, $iUnitWidth, $iUnitCount);
        $aUnit = array(
            'content' => $sContent,
            'bx_if:dest_width' => array(
                'condition' => $iDestWidth > 0,
                'content' => array('width' => $iDestWidth) 
            )
        );
        return $this->parseHtmlByName('centered_block.html', $aUnit);
    }
    
    function getExtraTopMenu ($aMenu = array(), $sUrlPrefix = BX_DOL_URL_ROOT) {
        $aUnits = array();
        foreach ($aMenu as $aValue) {
            $aValue['link'] = $sUrlPrefix . $aValue['link'];
            $aUnits[] = array(
                'bx_if:active' => array(
                    'condition' => $aValue['active'] == true,
                    'content' => $aValue
                ),
                'bx_if:not_active' => array(
                    'condition' => $aValue['active'] == false,
                    'content' => $aValue
                )
            );
        }
        $aUnit['bx_repeat:menu'] = $aUnits;
        return $this->parseHtmlByName('extra_top_menu.html', $aUnit);
    }
    
    function getExtraSwitcher ($aMenu = array(), $sHeadKey, $iBoxId = 1) {
        $aUnits = array();
        foreach ($aMenu as $sName => $aItem) {
            $aUnits[] = array(
                'href' => $aItem['href'],
                'name' => $sName,
                'selected' => $aItem['active'] == true ? 'selected' : ''
            );
        }
        $aUnit = array(
            'bx_repeat:options' => $aUnits,
            'head_key' => _t($sHeadKey),
            'block_id' => $iBoxId
        );
        return $this->parseHtmlByName('extra_switcher.html', $aUnit);
    }
	
	function getAlbumFeed (&$aUnits) {
		$sItemType = $this->getItemType();
		ob_start();
		?>
		<item>
            <title>__title__</title>
            <media:description>__title__</media:description>
            <link>__link__</link>
            <media:thumbnail url="__thumb__"/>
            <media:content <?=$sItemType?> url="__main__"/>
        </item>
		<?
		$sTempl = ob_get_clean();
		$sCode = _t('_Empty');
		if (is_array($aUnits) && !empty($aUnits)) {
			foreach ($aUnits as $aData) {
				$aData['link'] = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aData['uri'];
				if (!isset($aData['main']))
					$aData['main'] = $aData['file'];
				$sCode .= $this->parseHtmlByContent($sTempl, $aData);
			}
		}
		return $this->getRssTemplate($sCode);
	}
	
	function getItemType () {
		return '';
	}
	
	function getRssTemplate ($sContent = '') {
		ob_start();
		?>
		<rss version="2.0" xmlns:media="http://search.yahoo.com/mrss/" xmlns:atom="http://www.w3.org/2005/Atom">
			<channel>
				<?=$sContent?>
			</channel>
		</rss>
		<?
        $sCode = ob_get_clean();

		return '<?xml version="1.0" encoding="utf-8" standalone="yes"?>' . $sCode;
	}
	
	function getAlbumPreview ($sRssLink) {
		$aUnits = array(
			'rss_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . $sRssLink,
			'swf_url' => BX_DOL_URL_PLUGINS . 'cooliris/swf/cooliris.swf',
			'height'  => $this->_oConfig->getGlParam('album_slideshow_height'),
		);
		return $this->parseHtmlByName('album_preview.html', $aUnits);
	}
	
	function getAlbumFormAddArray ($aReInputs = array(), $aReForm = array()) {
		$aForm = array(
            'form_attrs' => array(
                'id' => $this->_oConfig->getMainPrefix() . '_upload_form',
                'method' => 'post',
                'action' => $this->_oConfig->getBaseUri() . 'albums/my/add'
            ),
            'params' => array (
		        'db' => array(
		            'submit_name' => 'submit',
		        ),
		        'checker_helper' => 'BxSupportCheckerHelper',
		    ),
            'inputs' => array(
                'header' => array(
                    'type' => 'block_header',
                    'caption' => _t('_Info'),
                ),
                'title' => array(
                    'type' => 'text',
                    'name' => 'caption',
                    'caption' => _t('_Title'),
                    'required' => true,
                    'checker' => array (  
					    'func' => 'length',
					    'params' => array(3, 128),
					    'error' => _t('_td_err_incorrect_length'),
					),
                ),
                'location' => array(
                    'type' => 'text',
                    'name' => 'location',
                    'caption' => _t('_Location'),
                ),
                'description' => array(
                    'type' => 'textarea',
                    'name' => 'description',
                    'caption' => _t('_Description'),
                ),
                'allow_view' => array(),
                'owner' => array(
                    'type' => 'hidden',
                    'name' => 'owner',
                    'value' => $this->_oDb->iViewer,
                ),
                'submit' => array(
                    'type' => 'submit',
                    'name' => 'submit',
                    'value' => _t('_Submit'),
                ),
            ),
        );
        if (is_array($aReInputs) && !empty($aReInputs))
        	$aForm['inputs'] = array_merge($aForm['inputs'], $aReInputs);
    	if (is_array($aReForm) && !empty($aReForm))
        	$aForm['form_attrs'] = array_merge($aForm['form_attrs'], $aReForm);
    	return $aForm;
	}
	
	function getAlbumFormEditArray ($aReInputs = array(), $aReForm = array()) {
		$aForm = $this->getAlbumFormAddArray(array(), $aReForm);
		if (is_array($aReInputs) && !empty($aReInputs)) {
			foreach ($aReInputs as $sKey => $aValue) {
				if (array_key_exists($sKey, $aForm['inputs']))
					$aForm['inputs'][$sKey] = array_merge($aForm['inputs'][$sKey], $aValue);
				else
					$aForm['inputs'][$sKey] = $aValue;
			}
		}
		return $aForm;
	}
}

?>