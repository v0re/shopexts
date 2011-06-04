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

bx_import('BxTemplSearchResultSharedMedia');

class BxPhotosSearch extends BxTemplSearchResultSharedMedia {
    function BxPhotosSearch ($sParamName = '', $sParamValue = '', $sParamValue1 = '', $sParamValue2 = '') {
        parent::BxTemplSearchResultSharedMedia('BxPhotosModule');
        $this->aConstants['linksTempl'] = array(
            'home' => 'home',
            'file' => 'view/{uri}',
            'category' => 'browse/category/{uri}',
            'browseAll' => 'browse/',
            'browseUserAll' => 'albums/browse/owner/{uri}',
            'browseAllTop' => 'browse/top',
            'tag' => 'browse/tag/{uri}',
            'album' => 'browse/album/{uri}',
            'add' => 'browse/my/add'
        );
        $aMain = array(
            'name' => 'bx_photos',
            'title' => '_bx_photos',
            'table' => 'bx_photos_main'
        );
        $this->aCurrent = array_merge($aMain, $this->aCurrent);
        $this->aCurrent['ownFields'] = array('ID', 'Title', 'Uri', 'Date', 'Size', 'Views', 'Rate', 'RateCount', 'Hash');
        $this->aCurrent['searchFields'] = array('Title', 'Tags', 'Desc', 'Categories');
        $this->aCurrent['rss']['title'] = _t('_bx_photos');
                
        // redeclaration some unique fav fields
        $this->aAddPartsConfig['favorite']['table'] = 'bx_photos_favorites';
        $this->aAddPartsConfig['favorite']['mainField'] = 'ID';
                
        $this->oTemplate = &$this->oModule->_oTemplate; 
        $this->aConstants['filesUrl'] = $this->oModule->_oConfig->getFilesUrl();
        $this->aConstants['filesDir'] = $this->oModule->_oConfig->getFilesPath();
        $this->aConstants['picPostfix'] = $this->oModule->_oConfig->aFilePostfix;
        
        $this->aCurrent['restriction']['albumType']['value'] = $this->aCurrent['name'];

        switch ($sParamName) {
            case 'calendar':
                $this->aCurrent['restriction']['calendar-min'] = array('value' => "UNIX_TIMESTAMP('{$sParamValue}-{$sParamValue1}-{$sParamValue2} 00:00:00')", 'field' => 'Date', 'operator' => '>=', 'no_quote_value' => true);
                $this->aCurrent['restriction']['calendar-max'] = array('value' => "UNIX_TIMESTAMP('{$sParamValue}-{$sParamValue1}-{$sParamValue2} 23:59:59')", 'field' => 'Date', 'operator' => '<=', 'no_quote_value' => true);
                $this->aCurrent['title'] = _t('_bx_photos_caption_browse_by_day') . sprintf("%04u-%02u-%02u", $sParamValue, $sParamValue1, $sParamValue2);
                break;
            case 'top':
                $this->aCurrent['sorting'] = 'top';
                break;
            case 'popular':
                $this->aCurrent['sorting'] = 'popular';
                break;
            case 'featured':
                $this->aCurrent['restriction']['featured'] = array(
                    'value'=>'1', 'field'=>'Featured', 'operator'=>'=', 'paramName'=>'bx_photos_mode'
                ); 
                break;
            case 'favorited':
				if (isset($this->aAddPartsConfig['favorite']) && !empty($this->aAddPartsConfig['favorite']) && getLoggedId() != 0) {
                    $this->aCurrent['join']['favorite'] = $this->aAddPartsConfig['favorite']; 
                    $this->aCurrent['restriction']['fav'] = array(
                        'value' => getLoggedId(),
                        'field' => $this->aAddPartsConfig['favorite']['userField'],
                        'operator' => '=',
                        'table' => $this->aAddPartsConfig['favorite']['table']
                    );
                }
                break;
            case 'album':
                $this->aCurrent['sorting'] = 'album_order';
                $this->aCurrent['restriction']['album'] = array(
                    'value'=>'', 'field'=>'Uri', 'operator'=>'=', 'paramName'=>'albumUri', 'table'=>'sys_albums'
                );
                $this->aCurrent['restriction']['albumType'] = array(
                    'value'=>$this->aCurrent['name'], 'field'=>'Type', 'operator'=>'=', 'paramName'=>'albumType', 'table'=>'sys_albums'
                );
                if ($sParamValue1 == 'owner' && strlen($sParamValue2) > 0) {
                    $this->aCurrent['restriction']['owner'] = array(
                        'value'=>$sParamValue2, 'field'=>'NickName', 'operator'=>'=', 'paramName'=>'ownerName', 'table' => 'Profiles'
                    );
                }
                break;
        }
    }
    
    function addCustomParts () {
        if (!$this->bCustomParts) {
            $this->bCustomParts = true;
            $this->oModule->_oTemplate->addCss(array('search.css'));
            return '';
        }
    }
    
    function getAlbumCovers ($iAlbumId, $aParams = array()) {
        $iAlbumId = (int)$iAlbumId;
    	$aPics = $this->oModule->oAlbums->getAlbumCoverFiles($iAlbumId, array('table'=>$this->aCurrent['table'], 'field'=>'ID', 'fields_list'=>array('Hash')), array(array('field'=>'Status', 'value'=>'approved')));
    	return $aPics;
    }
    
    function getAlbumCoverUrl (&$aIdent) {
    	return $this->getImgUrl($aIdent['Hash'], 'thumb');
    }
    
    function getImgUrl ($sHash, $sImgType = 'browse') {
	    return BX_DOL_URL_ROOT . $this->oModule->_oConfig->getBaseUri() . 'get_image/' . $sImgType .'/' . $sHash . '.jpg';
	}
        
    function getLength ($sSize) {
        return $sSize;
    }
    
    function getLatestFile () {
        $aParams['DisplayPagination'] = 0;
        $aParams['DisplayWhenAgo'] = 1;
        $aParams['DisplayViews'] = 0;
        $aParams['DisplayLink'] = 1;
        $aParams['DisplayProfile'] = 1;

        if (isset($this->aCurrent['restriction']['owner']['value']) && (int)$this->aCurrent['restriction']['owner']['value'] != 0)
            $aParams['PID'] = $this->aCurrent['restriction']['owner']['value'];
        
        if (isset($this->aCurrent['restriction']['category']['value']) && strlen($this->aCurrent['restriction']['category']['value']) > 0)
            $aParams['Category'] = $this->aCurrent['restriction']['category']['value'];
        
        if (isset($this->aCurrent['restriction']['tag']['value']) && strlen($this->aCurrent['restriction']['tag']['value']) > 0)
            $aParams['Tag'] = $this->aCurrent['restriction']['tag']['value'];
        
        return  '<div class="latestFile">'.$this->servicePhotoBlock($aParams).'</div>';
    }

    /**
     * Get image of the specified type by image id 
     * @param $aImageInfo image info array with the following info
     *          $aImageInfo['Avatar'] - photo id, NOTE: it not relatyed to profiles avataras module at all
     * @param $sImgType image type 
     */ 
    function serviceGetImage ($aImageInfo, $sImgType = 'thumb') {
        $iPicID = (int)$aImageInfo['Avatar'];
        $aImg = $this->_getImageFullInfo($iPicID, $sImgType);
        if (strlen($aImg['file']) > 0) {
            $sFileName = $aImg['file'];
            $isNoImage = false;
        }
        return array('file' => $sFileName, 'title' => $aImg['title'], 'width' => $aImg['width'], 'height' => $aImg['height'], 'no_image'=>$isNoImage);
    }
    
    function serviceGetPhotoArray($iId, $sType = 'thumb') {
        $aImageInfo = $this->_getImageFullInfo($iId, $sType);
        return empty($aImageInfo['file']) ? array() : $aImageInfo;
    }
    
    function _getImageFullInfo($iId, $sType = 'thumb') {
        // predefined sizes
        $aType2Image = array(
            'thumb' => 64, 'icon' => 32, 'file' => 600, 'browse' => 140, 'original' => 0
        );
        $aImageInfo = $this->_getImageDbInfo($iId);

        $iWidth = (int)$this->oModule->_oConfig->getGlParam($sType . '_width');
        $iHeight = (int)$this->oModule->_oConfig->getGlParam($sType . '_height');
        $iWidth = $iWidth == 0 ? $aType2Image[$sType] : $iWidth;
        $iHeight = $iHeight == 0 ? $aType2Image[$sType] : $iHeight;
        
        $sImageUrl = $sBrowseUrl = '';
        if(is_array($aImageInfo) && !empty($aImageInfo)) {
            $sImageUrl = $this->_getImageFullUrl($aImageInfo, $sType);
            $sBrowseUrl = !empty($aImageInfo['uri']) ? $this->getCurrentUrl('file', $iId, $aImageInfo['uri']) : '';
        }
        return array(
            'file' => $sImageUrl,
            'path' => $this->oModule->_oConfig->getFilesPath() . $aImageInfo['id'] . $this->aConstants['picPostfix'][$sType],
            'title' => $aImageInfo['title'],
            'owner' => $aImageInfo['owner'], 
            'description' => $aImageInfo['description'],
            'width' => $iWidth + 2 * 2, 
            'height' => $iHeight + 2 * 2, 
            'url' => $sBrowseUrl,
            'date' => $aImageInfo['date'],
            'rate' => $aImageInfo['rate'],
            'album_id' => $aImageInfo['album_id']
        );
    }
    
    function _getImageDbInfo ($iId) {
        $iId = (int)$iId;
        $sqlQuery = "SELECT a.`ID` as `id`,
        					a.`Ext` as `ext`,
        					a.`Title` as `title`,
        					a.`Desc` as `description`,
        					a.`Uri` as `uri`,
        					a.`Owner` as `owner`,
        					a.`Date` as `date`,
        					a.`Rate` as `rate`,
        					a.`Hash`,
        					b.`id_album` as `album_id`
							FROM `bx_photos_main` as a
							LEFT JOIN `sys_albums_objects` as b ON b.`id_object` = a.`ID`
							LEFT JOIN `sys_albums` as c ON c.`ID`=b.`id_album`
							WHERE a.`ID`='" . $iId . "' AND a.`Status`<>'disapproved' and c.`Type`='bx_photos'";
        $aImageInfo = ($iId) ? db_arr($sqlQuery) : null;
        return $aImageInfo;
    }
    
    // get image source url 
    function _getImageFullUrl ($aImageInfo, $sType = 'thumb') {
        $sName = $aImageInfo['id'] . $this->aConstants['picPostfix'][$sType];
        $sName = str_replace('{ext}', $aImageInfo['ext'], $sName);
        $sImageUrl = !empty($aImageInfo['id']) && extFileExists($this->oModule->_oConfig->getFilesPath() . $sName) ? $this->getImgUrl($aImageInfo['Hash'], $sType) : '';
        return $sImageUrl; 
    }
       
    function _getPseud () {
        return array(    
            'id' => 'ID',
            'title' => 'Title',
            'date' => 'Date',
            'size' => 'Size',
            'uri' => 'Uri',
            'view' => 'Views',
            'ownerId' => 'Owner',
            'ownerName' => 'NickName',
            'voteTime' => 'gal_date'
        );
    }
    
    function servicePhotoBlock ($aParams) {
        return $this->getPhotoBlock($aParams);
    }
    
    function serviceProfilePhotoBlock ($aParams) {
        $this->aCurrent['sorting'] = 'album_order';
        $sCaption = str_replace('{nickname}', getNickName($aParams['PID']), $this->oModule->_oConfig->getGlParam('profile_album_name'));
        $sUri = uriFilter($sCaption);
        $oAlbum = new BxDolAlbums('bx_photos');
        $aAlbumInfo = $oAlbum->getAlbumInfo(array('fileUri' => $sUri, 'owner' => $aParams['PID']), array('ID'));
        if (empty($aAlbumInfo) && $this->oModule->_iProfileId == (int)$aParams['PID']) {
            $aData = array(
                'caption' => $sCaption,
                'location' => _t('_' . $this->oModule->_oConfig->getMainPrefix() . '_undefined'),
                'owner' => $this->oModule->_iProfileId,
                'AllowAlbumView' => BX_DOL_PG_ALL,
            );
            $aAlbumInfo['ID'] = $oAlbum->addAlbum($aData, false);
        }
        if ($this->oModule->oAlbumPrivacy->check('album_view', $aAlbumInfo['ID'], $this->oModule->_iProfileId)) {
            $this->aCurrent['restriction']['album'] = array(
                'value'=>$sUri, 'field'=>'Uri', 'operator'=>'=', 'paramName'=>'albumUri', 'table'=>'sys_albums'
            );
            return $this->getPhotoBlock($aParams);
        }
    }
    
    function serviceGetFilesInCat ($iId, $sCategory = '') {
        $aFiles = $this->getFilesInCatArray($iId, $sCategory);
        foreach ($aFiles as $k => $aRow) {
            $aFiles[$k]['icon']  = $this->getImgUrl($aRow['Hash'], 'icon');
            $aFiles[$k]['thumb'] = $this->getImgUrl($aRow['Hash'], 'thumb');
            $aFiles[$k]['file']  = $this->getImgUrl($aRow['Hash'], 'file');
        }
        return $aFiles;
    }
    
	function serviceGetFilesInAlbum ($iAlbumId, $isCheckPrivacy = false, $iViewer = 0, $aLimits = array()) {
		if (!$iViewer)
			$iViewer = $this->oModule->_iProfileId;
		if ($isCheckPrivacy && !$this->oModule->oAlbumPrivacy->check('album_view', (int)$iAlbumId, $iViewer))
			return array();
        $aFiles = $this->getFilesInAlbumArray($iAlbumId, $aLimits);
        foreach ($aFiles as $k => $aRow) {
            $aFiles[$k]['icon']  = $this->getImgUrl($aRow['Hash'], 'icon');
            $aFiles[$k]['thumb'] = $this->getImgUrl($aRow['Hash'], 'thumb');
            $aFiles[$k]['file']  = $this->getImgUrl($aRow['Hash'], 'file');
        }
        return $aFiles;
    }
    
    function serviceGetAllProfilePhotos ($iProfId, $aLimits = array()) {
    	$aFiles = $this->getProfileFiles($iProfId, $aLimits);
    	foreach ($aFiles as $k => $aRow) {
            $aFiles[$k]['icon']  = $this->getImgUrl($aRow['Hash'], 'icon');
            $aFiles[$k]['thumb'] = $this->getImgUrl($aRow['Hash'], 'thumb');
			$aFiles[$k]['browse'] = $this->getImgUrl($aRow['Hash'], 'browse');
            $aFiles[$k]['file']  = $this->getImgUrl($aRow['Hash'], 'file');
        }
        return $aFiles;
    }
    
    function serviceGetWallPost($aEvent) {
        $aOwner = db_assoc_arr("SELECT `ID` AS `id`, `NickName` AS `username` FROM `Profiles` WHERE `ID`='" . (int)$aEvent['owner_id'] . "' LIMIT 1");
	    $aPhoto = $this->serviceGetPhotoArray($aEvent['object_id'], 'browse');
	    if(empty($aOwner) || empty($aPhoto))
            return "";

        $sCss = "";
        if($aEvent['js_mode'])
            $sCss = $this->oModule->_oTemplate->addCss('wall_post.css', true);
        else 
            $this->oModule->_oTemplate->addCss('wall_post.css');
        
        $sAddedNewTxt = _t('_bx_photos_wall_added_new');
        if(!$this->oModule->oAlbumPrivacy->check('album_view', $aPhoto['album_id'], $this->oModule->_iProfileId)) {
        	$sPhotoTxt = _t('_bx_photos_wall_photo_private');
        	$aPhotoOut = array(
        		'title' => $aOwner['username'] . ' ' . $sAddedNewTxt . ' ' . $sPhotoTxt,
        		'content' => $sCss . $this->oTemplate->parseHtmlByName('wall_post_private.html', array(
	                'cpt_user_name' => $aOwner['username'],
	                'cpt_added_new' => $sAddedNewTxt . ' ' . $sPhotoTxt,
	                'post_id' => $aEvent['id']
	            ))
        	);
        }
        else {
	        $sPhotoTxt = _t('_bx_photos_wall_photo');
	        $aPhotoOut = array(
	            'title' => $aOwner['username'] . ' ' . $sAddedNewTxt . ' ' . $sPhotoTxt,
	            'description' => $aPhoto['description'],
	            'content' => $sCss . $this->oTemplate->parseHtmlByName('wall_post.html', array(
	                'cpt_user_name' => $aOwner['username'],
	                'cpt_added_new' => $sAddedNewTxt,
	                'cpt_photo_url' => $aPhoto['url'],
	                'cpt_photo' => $sPhotoTxt,
	                'cnt_photo_width' => $aPhoto['width'] + 4,
	                'cnt_photo_height' => $aPhoto['height'] + 4,
	                'cnt_photo_url' => $aPhoto['file'],
	                'cnt_photo_title' => $aPhoto['title'],
	                'post_id' => $aEvent['id']
	            ))
        	);
        }
        return $aPhotoOut;
    }
    
    function getPhotoBlock ($aParams = array()) {
        $aShowParams = array('showRate' => 1, 'showPaginate' => 0, 'showViews' => 0, 'showDate' => 0, 'showLink' => 0, 'showFrom' => 0);
        if (count($aParams) > 0) {
            foreach( $aParams as $sKeyName => $sKeyValue ) {
                switch ($sKeyName) {
                    case 'PID':
                        $this->aCurrent['restriction']['owner']['value'] = (int)$sKeyValue;
                        break;
                    case 'Category':
                        $this->aCurrent['restriction']['category']['value'] = strip_tags($sKeyValue);
                        break;
                    case 'Tag':
                        $this->aCurrent['restriction']['tag']['value'] = strip_tags($sKeyValue);
                        break;
                    case 'Limit':
                        $this->aCurrent['paginate']['perPage'] = (int)$sKeyValue;
                        break;
                    case 'DisplayPagination':
                        if ($sKeyValue == 1)
                            $aShowParams['showPaginate'] = 1;
                        break;
                    case 'DisplayViews':
                        if ($sKeyValue == 1)
                            $aShowParams['showViews'] = 1;
                        break;
                    case 'DisplayWhenAgo':
                        if ($sKeyValue == 1)
                            $aShowParams['showDate'] = 1;
                        break;
                    case 'DisplayLink':
                        if ($sKeyValue == 1)
                            $aShowParams['showLink'] = 1;
                        break;
                    case 'DisplayProfile':
                        if ($sKeyValue == 1)
                            $aShowParams['showFrom'] = 1;
                        break;
                }
            }
        }
        $this->aCurrent['paginate']['perPage'] = 20;
        $aFilesList = $this->getSearchData();
        $iCnt = $this->aCurrent['paginate']['totalNum']; 
        if ($iCnt) {
            $aUnit = array();
            $aUnits = array();
            if (defined('BX_PROFILE_PAGE') || defined('BX_MEMBER_PAGE')) {
                $iPhotoWidth = 294;
                $sImgWidth = 'style="width:' . $iPhotoWidth . 'px;"';
            }
            else {
                $iPhotoWidth = (int)$this->oModule->_oConfig->getGlParam('file_width');
                $iPhotoWidth = ($iPhotoWidth > 1) ? $iPhotoWidth : 600;
                $sImgWidth = '';
            }
            foreach ($aFilesList as $iKey => $aData) {
                $sPicUrl = $this->getImgUrl($aData['Hash'], 'icon');
                $aUnits[] = array(
                    'imageId' => $iKey + 1,
                    'picUrl' => $sPicUrl
                );
                $sPicLinkElements .= 'aPicLink['.($iKey + 1).'] = '.$aData['id'].';';
                if ($iKey == 0) {
                    $aAdd = array('switchWidth' => ($iPhotoWidth + 2), 'imgWidth' => $sImgWidth);
                    $aUnit['switcherUnit'] = $this->getSwitcherUnit($aData, $aShowParams, $aAdd);
                }
            }
            $aUnit['moduleUrl'] = BX_DOL_URL_ROOT . $this->oModule->_oConfig->getBaseUri();
            $aUnit['bx_repeat:iconBlock'] = $aUnits;
            $aUnit['count'] = $iCnt;
            $aUnit['contWidth'] = $iCnt * 40;
            $aUnit['picWidth'] = $iPhotoWidth;
            $aUnit['picBoxWidth'] = $aUnit['switchWidth'] = $iPhotoWidth + 2;
            $aUnit['switchWidthOut'] = $aUnit['switchWidth'] + 4;
            if ($aUnit['contWidth'] > $aUnit['picWidth']) {
            	$bScroller =  true;
            	$aUnit['containerWidth'] = $aUnit['picBoxWidth'] - 72;            	
            }
            else {
            	$bScroller = false;
            	$aUnit['containerWidth'] = $aUnit['contWidth'];
            }
            $aUnit['bx_if:scrollerBack'] = array(
                'condition' => $bScroller,
                'content' => array(1)
            );
            $aUnit['bx_if:scrollerNext'] = array(
                'condition' => $bScroller,
                'content' => array(1),
            );
            
            $aUnit['picLinkElements'] = $sPicLinkElements;
            if ($aShowParams['showPaginate'] == 1) {
                $aLinkAddon = $this->getLinkAddByPrams();
                $oPaginate = new BxDolPaginate(array(
                    'page_url' => $aUnit['changeUrl'],
                    'count' => $iCnt,
                    'info' => false,
                    'per_page' => 1,
                    'page' => $this->aCurrent['paginate']['page'],
                    'per_page_changer' => false,
                    'page_reloader' => false,
                    'on_change_page' => 'getCurrentImage({page})',
                ));
                $aUnit['paginate'] = $oPaginate->getPaginate();
            }
            else
                $aUnit['paginate'] = '';
                    
            $this->oTemplate->addCss('search.css');
            return $this->oTemplate->parseHtmlByName('photo_switcher.html', $aUnit);
        }
        elseif ($this->oModule->_iProfileId != 0 && $this->oModule->_iProfileId == (int)$this->aCurrent['restriction']['owner']['value']) {
            ob_start();
            ?>
            <div class="paginate">
                <div class="view_all" style="background-image:url(__img_src__)">            
                    <a href="__lnk_url__" title="__lnk_title__">__lnk_content__</a>
                </div>
             </div>
            <?
            $sCode = ob_get_clean();
            $sLinkTitle = _t('_bx_photos_add');
            $sNickName = getNickName($this->oModule->_iProfileId);
            $sCaption = uriFilter(str_replace('{nickname}', $sNickName, $this->oModule->_oConfig->getGlParam('profile_album_name')));
            $aUnit = array(
                'img_src' => $this->oTemplate->getIconUrl('more.png'),
                'lnk_url' => $this->oModule->_oConfig->getBaseUri() . 'albums/my/add_objects/' . $sCaption . '/owner/' . $sNickName,
                'lnk_title' => $sLinkTitle,
                'lnk_content' => $sLinkTitle
            );
            return MsgBox(_t('_Empty')) . $this->oTemplate->parseHtmlByContent($sCode, $aUnit);
        }
        return MsgBox(_t('_Empty'));
    }
    
    function getSwitcherUnit (&$aData, $aShowParams = array(), $aAddElems = array()) {
        if (!is_array($aData))
            return;
        $iWidth = (int)$aAddElems['switchWidth'] > 0 ? (int)$aAddElems['switchWidth'] : 602;
        $aUnit = array(
            'switchWidth' => $iWidth,
            'switchWidthOut' => $iWidth + 4,
            'imgWidth' => strlen($aAddElems['imgWidth']) > 0 ? $aAddElems['imgWidth']: '',
            'picUrl' => $this->getImgUrl($aData['Hash'], 'file'),
            'bx_if:href' => array(
                'condition' => (int)$aShowParams['showLink'] != 0,
                'content' => array(
                    'href' => $this->getCurrentUrl('file', $aData['id'], $aData['uri']),
                    'title' => $aData['title'] 
                )
            ),
            'bx_if:rate' => array(
                'condition' => (int)$aShowParams['showRate'] != 0,
                'content' => array(
                    'rate' => $this->oRate && $this->oRate->isEnabled() ? $this->oRate->getJustVotingElement(1, $aData['id'], $aData['Rate']) : $this->oRate->getJustVotingElement(0, 0, $aData['Rate']) 
                )
            ),
            'bx_if:date' => array(
                'condition' => (int)$aShowParams['showDate'] != 0,
                'content' => array(
                    'date' => defineTimeInterval($aData['date'])
                )
            ),
            'bx_if:from' => array(
                'condition' => (int)$aShowParams['showFrom'] != 0,
                'content' => array(
                    'fromKey' => _t('_from'),
                    'profileUrl' => getProfileLink($aData['ownerId']),
                    'nick' => $aData['ownerName']  
                )
            )
        );
        return $this->oTemplate->parseHtmlByName('switcher_unit.html', $aUnit);
    }
    
    function getModuleFolder () {
        return 'boonex/photos';
    }    
}

?>
