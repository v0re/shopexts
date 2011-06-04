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

class BxSoundsSearch extends BxTemplSearchResultSharedMedia {
    function BxSoundsSearch($sParamName = '', $sParamValue = '', $sParamValue1 = '', $sParamValue2 = '') {
        parent::BxTemplSearchResultSharedMedia('BxSoundsModule');
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
        // main part of aCurrent settings, usual most unique part of every module
        $aMain = array(
            'name' => 'bx_sounds',
            'title' => '_bx_sounds',
            'table' => 'RayMp3Files'
        );
        
        $this->aCurrent = array_merge($aMain, $this->aCurrent);
        $this->aCurrent['ownFields'][] = 'Listens';
        $this->aCurrent['rss']['title'] = _t('_bx_sounds');
        
        $this->aAddPartsConfig['favorite']['table'] = 'bx_sounds_favorites';
        $this->oModule = BxDolModule::getInstance('BxSoundsModule');
        $this->oTemplate = &$this->oModule->_oTemplate; 
        $this->aConstants['filesUrl'] = $this->oModule->_oConfig->getFilesUrl();
        $this->aConstants['filesDir'] = $this->oModule->_oConfig->getFilesPath();
        $this->aConstants['picPostfix'] = '.jpg';
        
        $this->aCurrent['restriction']['albumType']['value'] = $this->aCurrent['name'];
        
        switch ($sParamName) {
            case 'calendar':
                $this->aCurrent['restriction']['calendar-min'] = array('value' => "UNIX_TIMESTAMP('{$sParamValue}-{$sParamValue1}-{$sParamValue2} 00:00:00')", 'field' => 'Date', 'operator' => '>=', 'no_quote_value' => true);
                $this->aCurrent['restriction']['calendar-max'] = array('value' => "UNIX_TIMESTAMP('{$sParamValue}-{$sParamValue1}-{$sParamValue2} 23:59:59')", 'field' => 'Date', 'operator' => '<=', 'no_quote_value' => true);
                $this->aCurrent['title'] = _t('_bx_sounds_caption_browse_by_day') . sprintf("%04u-%02u-%02u", $sParamValue, $sParamValue1, $sParamValue2);
                break;
            case 'top':
                $this->aCurrent['sorting'] = 'top';
                break;
            case 'popular':
                $this->aCurrent['sorting'] = 'popular';
                break;
            case 'featured':
                $this->aCurrent['restriction']['featured'] = array(
                    'value'=>'1', 'field'=>'Featured', 'operator'=>'=', 'paramName'=>'bx_sounds_mode'
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
                if ($sParamValue1 == 'owner' && strlen($sParamValue2) > 0) {
                    $this->aCurrent['restriction']['owner'] = array(
                        'value'=>$sParamValue2, 'field'=>'NickName', 'operator'=>'=', 'paramName'=>'ownerName', 'table' => 'Profiles'
                    );
                }
                break;
        }
    }
    
    function _getPseud () {
        return array(    
            'id' => 'ID',
            'title' => 'Title',
            'date' => 'Date',
            'size' => 'Time',
            'uri' => 'Uri',
            'ownerId' => 'Owner',
            'ownerName' => 'NickName',
            'view' => 'Listens',
            'voteTime' => 'gal_date'
        );
    }
    
    function getImgUrl ($iId, $sImgType = 'browse') {
        $iId = (int)$iId;
        switch ($sImgType) {
            case 'file':
                $sImgUrl = $this->aConstants['filesUrl'] . 'files/' . $iId . '.mp3';
                break;
            default:
                $sImgUrl = $this->aConstants['filesUrl'].'image.php?id=' . $iId;
        }
        return $sImgUrl;
    }
    
    function serviceGetFileUrl ($iId, $sImgType = 'browse') {
        return $this->getImgUrl($iId, $sImgType);
    }
    
    function serviceGetMusicArray ($iPicId, $sImgType) {
        $iPicId = (int)$iPicId;
        $sqlQuery = "SELECT a.`ID` as `id`,
							a.`Title` as `title`,
							a.`Description` as `description`,
							a.`Uri` as `uri`,
							a.`Owner` as `owner`,
							a.`Date` as `date`,
							b.`id_album` as `album_id`
						FROM `RayMp3Files` as a
						LEFT JOIN `sys_albums_objects` as b ON b.`id_object` = a.`ID`
						LEFT JOIN `sys_albums` as c ON c.`ID`=b.`id_album`
						WHERE a.`ID`='$iPicId' AND a.`Status`='approved' AND c.`Type`='bx_sounds'";
        $aImageInfo = db_arr($sqlQuery);
        if(empty($aImageInfo) || !is_array($aImageInfo))
            return array();

        $sFileName = $this->getImgUrl($iPicId, $sImgType);
        $sUrl = $this->getCurrentUrl('file', $iPicId, $aImageInfo['uri']); 
        return array(
            'file' => $sFileName, 
            'title' => $aImageInfo['title'], 
            'owner' => $aImageInfo['owner'], 
            'description' => $aImageInfo['description'],
            'width' => (int)$this->oModule->_oConfig->getGlParam($sImgType . '_width') + 2 * 2, 
            'height' => (int)$this->oModule->_oConfig->getGlParam($sImgType . '_height') + 2 * 2, 
            'url' => $sUrl,
            'album_id' => $aImageInfo['album_id']
        );
    }
    
    function serviceGetFilesInCat ($iId, $sCategory = '') {
        $aFiles = $this->getFilesInCatArray($iId, $sCategory);
        foreach ($aFiles as $k => $aRow) {
            $aFiles[$k]['thumb'] = $this->getImgUrl($aRow['id'], 'browse');
            $aFiles[$k]['file'] = $this->getImgUrl($aRow['id'], 'file');
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
            $aFiles[$k]['thumb'] = $this->getImgUrl($aRow['id'], 'browse');
            $aFiles[$k]['file'] = $this->getImgUrl($aRow['id'], 'file');
        }
        return $aFiles;
    }
	
	function serviceGetAllProfileSounds ($iProfId, $aLimits = array()) {
    	$aFiles = $this->getProfileFiles($iProfId, $aLimits);
    	foreach ($aFiles as $k => $aRow) {
            $aFiles[$k]['thumb'] = $this->getImgUrl($aRow['id'], 'browse');
            $aFiles[$k]['file'] = $this->getImgUrl($aRow['id'], 'file');
        }
        return $aFiles;
    }

    function serviceGetWallPost($aEvent) {
        $aOwner = db_assoc_arr("SELECT `ID` AS `id`, `NickName` AS `username` FROM `Profiles` WHERE `ID`='" . (int)$aEvent['owner_id'] . "' LIMIT 1");
	    $aMusic = $this->serviceGetMusicArray($aEvent['object_id'], 'browse');
	    if(empty($aOwner) || empty($aMusic))
            return "";

        $sCss = "";
        if($aEvent['js_mode'])
            $sCss = $this->oModule->_oTemplate->addCss('wall_post.css', true);
        else 
            $this->oModule->_oTemplate->addCss('wall_post.css');
        
        $sAddedNewTxt = _t('_bx_sounds_wall_added_new');
        if(!$this->oModule->oAlbumPrivacy->check('album_view', $aMusic['album_id'], $this->oModule->_iProfileId)) {
        	$sMusicTxt = _t('_bx_sounds_wall_music_private');
        	$aOut = array(
        		'title' => $aOwner['username'] . ' ' . $sAddedNewTxt . ' ' . $sMusicTxt,
        		'content' => $sCss . $this->oTemplate->parseHtmlByName('wall_post_private.html', array(
	                'cpt_user_name' => $aOwner['username'],
	                'cpt_added_new' => $sAddedNewTxt . ' ' . $sMusicTxt,
	                'post_id' => $aEvent['id']
	            ))
        	);
        }
        else {        
	        $sMusicTxt = _t('_bx_sounds_wall_music');	        
	        $aOut = array(
	            'title' => $aOwner['username'] . ' ' . $sAddedNewTxt . ' ' . $sMusicTxt,
	            'description' => $aMusic['description'],
	            'content' => $sCss . $this->oTemplate->parseHtmlByName('wall_post.html', array(
	                'cpt_user_name' => $aOwner['username'],
	                'cpt_added_new' => $sAddedNewTxt,
	                'cpt_music_url' => $aMusic['url'],
	                'cpt_music' => $sMusicTxt,
	                'cnt_player' => $this->_getSharedThumb($aEvent['object_id'], $aMusic['url']),
	                'post_id' => $aEvent['id']
	            ))
	        );
        }
        return $aOut;
    }
    
    function serviceProfileSoundBlock($iProfileId) {
        if(!$this->checkMemAction($iProfileId, 'view'))
        	return '';
    	$aVars = array (
            'title' => false,
            'prefix' => 'id' . time() . '_' . rand(1, 999999),
            'bx_repeat:sounds' => array (),
            'bx_repeat:icons' => array (),
        );
    
        $aFiles = $this->serviceGetProfileAlbumFiles($iProfileId);
        foreach($aFiles as $aFile) {
            $aVars['bx_repeat:sounds'][] = array (
                'style' => false === $aVars['title'] ? '' : 'display:none;',
                'id' => $aFile['id'],
                'sound' => getApplicationContent('mp3', 'player', array('id' => $aFile['id'], 'user' => getLoggedId(), 'password' => getLoggedPassword()), true),
            );            
            $aVars['bx_repeat:icons'][] = array (
                'id' => $aFile['id'],
                'icon_url' => $aFile['file'],
                'title' => $aFile['title'],
            );
            if (false === $aVars['title'])
                $aVars['title'] = $aFile['title'];
        }
        
        if (!$aVars['bx_repeat:icons'])
            return '';

        $this->oTemplate->addCss('entry_view.css');
        return $this->oTemplate->parseHtmlByName('entry_view_block_sounds.html', $aVars);
    }
    
    function getAlterOrder() {  
		$aSql = array();
        switch ($this->aCurrent['sorting']) {
            case 'popular':
                $aSql['order'] = " ORDER BY `Listens` DESC";
                break;
            default:
            	$aSql = parent::getAlterOrder();
        }
        return $aSql;
    }  
}

?>
