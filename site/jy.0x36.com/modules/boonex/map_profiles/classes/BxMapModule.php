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

function bx_map_import ($sClassPostfix, $aModuleOverwright = array()) {
    global $aModule;
    $a = $aModuleOverwright ? $aModuleOverwright : $aModule;
    if (!$a || $a['uri'] != 'map_profiles') {
        $oMain = BxDolModule::getInstance('BxMapModule');
        $a = $oMain->_aModule;
    }
    bx_import ($sClassPostfix, $a) ;
}

require_once( BX_DIRECTORY_PATH_PLUGINS . 'Services_JSON.php' );

bx_import('BxDolModule');
bx_import('BxDolPaginate');
bx_import('BxDolAlerts');
bx_import('BxDolPrivacy');

define ('BX_MAP_ZOOM_PROFILES', 10);
define ('BX_MAP_ZOOM_CITIES', 5);
define ('BX_MAP_ZOOM_COUNTRIES', 1);
define ('BX_MAP_DEFAULT_PRIVACY', BX_DOL_PG_ALL); 

/**
 * Google Maps for Profiles module by BoonEx
 *
 * This module automatically geocode every user's location and diplay user's location in propfile.
 * Also user can point exact location on the map by clicking on it.
 * Map with all profiles is also available on homepage and on separate page.
 * Users are grouped by countries and cities on world map
 *
 * 
 *
 * Profile's Wall:
 * no wall events
 *
 *
 *
 * Spy:
 * The following qactivity is displayed for profile_activity:
 * location_manually_updated
 * 
 *
 *
 *
 * Memberships/ACL:
 * no acl's, user's privacy settings instead
 *
 * 
 *
 * Service methods:
 *
 * Homepage block with world map
 * @see BxMapModule::serviceHomepageBlock
 * BxDolService::call('map_profiles', 'homepage_block', array());
 *
 * Separate page block with world map
 * @see BxMapModule::serviceSeparatePageBlock
 * BxDolService::call('map_profiles', 'separate_page_block', array($fLat, $fLng, $iZoom, $sFilter));
 *
 * Profile block with user's location map
 * @see BxMapModule::serviceProfileBlock
 * BxDolService::call('map_profiles', 'profile_block', array($iProfileId));
 *
 * Get profile location info
 * @see BxMapModule::serviceGetLocation
 * BxDolService::call('map_profiles', 'get_location', array($iProfileId, $iViewer, $isCHeckPrivacy));
 *
 * Edit location block
 * @see BxMapModule::serviceEditLocation
 * BxDolService::call('map_profiles', 'edit_location', array($iProfileId));
 *
 * Removes any geocoding information associated with profile
 * @see BxMapModule::serviceDeleteProfileLocation
 * BxDolService::call('map_profiles', 'delete_profile_location', array($iProfileId));
 *
 * Update profile's location
 * @see BxMapModule::serviceUpdateProfileLocation
 * BxDolService::call('map_profiles', 'update_profile_location', array($iProfileId));
 * 
 * Update profile's location manually
 * @see BxMapModule::serviceUpdateProfileLocationManually
 * BxDolService::call('map_profiles', 'update_profile_location_manually', array ($iProfileId, $fLat, $fLng, $iZoom, $sMapType, $sCountry, $sAddress));
 *
 *
 * Alerts:
 * Alerts type/unit - 'bx_map'
 * The following alerts are rised
 *
 *  geolocate_profile - user's location was geocoded
 *      $iObjectId - user's id
 *      $iSenderId - logged in user's id
 *      $aExtras['location'] - geocoded information: lat, lng, country
 *
 *  geolocate_city - city was geocoded
 *      $iObjectId - city name
 *      $iSenderId - logged in user's id
 *      $aExtras['location'] - geocoded information: lat, lng, country
 *
 *  geolocate_country - country was geocoded
 *      $iObjectId - two letters country code
 *      $iSenderId - logged in user's id
 *      $aExtras['location'] - geocoded information: lat, lng
 *
 *  location_manually_updated - user manually updated his location, by pointing exact location on the map
 *      $iObjectId - user's id
 *      $iSenderId - logged in user's id
 *      $aExtras['location'] - geocoded information: lat, lng, zoom, map_type, address, country
 *
 * 
 */
class BxMapModule extends BxDolModule {

    var $_iProfileId;
    var $_oPrivacy;

    function BxMapModule(&$aModule) {        
        parent::BxDolModule($aModule);
        $GLOBALS['aModule'] = $aModule;
        $this->_iProfileId = getLoggedId();
        bx_map_import ('Privacy');
        $this->_oPrivacy = new BxMapPrivacy($this);
        $GLOBALS['oBxMapModule'] = &$this;
    }

    function actionHome () {

        $this->_oTemplate->pageStart();

        bx_map_import ('PageMain');
        $oPage = new BxMapPageMain ($this);
        echo $oPage->getCode();

        $this->_oTemplate->addJs ('http://www.google.com/jsapi?key=' . getParam('bx_map_key'));
        $this->_oTemplate->addJs ('BxMap.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('search.css');
        $this->_oTemplate->addCss ('pageable_items.css');
        $this->_oTemplate->pageCode(_t('_bx_map'), false, false);
    }

    function actionEdit () {

        $aLocation = $this->_iProfileId ? $this->_oDb->getProfileById($this->_iProfileId) : false;
        if (!$this->_iProfileId || !$this->isAllowedEditOwnLocation($aLocation)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        } 

        $this->_oTemplate->pageStart();

        bx_map_import ('PageEdit');
        $oPage = new BxMapPageEdit ($this, $aLocation);
        echo $oPage->getCode();

        $this->_oTemplate->addJs ('http://www.google.com/jsapi?key=' . getParam('bx_map_key'));
        $this->_oTemplate->addJs ('BxMap.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('search.css');
        $this->_oTemplate->pageCode(_t('_bx_map_edit'), false, false);
    }    

    function actionSaveDataProfile ($iZoom, $sMapType, $fLat, $fLng, $sMapClassInstanceName, $sAddress, $sCountry) {

        $aLocation = $this->_iProfileId ? $this->_oDb->getProfileById($this->_iProfileId) : false;
        if (!$this->_iProfileId || !$this->isAllowedEditOwnLocation($aLocation))
            return;


        if (!$aLocation && ('null' == $fLat || 'null' == $fLng))
            return;
        
        $fLat = 'null' != $fLat ? (float)$fLat : $aLocation['lat'];
        $fLng = 'null' != $fLng ? (float)$fLng : $aLocation['lng'];
        $iZoom = 'null' != $iZoom ? (int)$iZoom : ($aLocation ? $aLocation['zoom'] : -1);
        $sMapType = 'null' != $sMapType ? $sMapType : ($aLocation ? $aLocation['type'] : '');
        $sAddress = 'null' != $sAddress ? process_db_input($sAddress, BX_TAGS_STRIP) : '';
        $sCountry = 'null' != $sCountry ? process_db_input($sCountry, BX_TAGS_STRIP) : '';

        switch ($sMapType) {
            case 'normal':
            case 'satellite':
            case 'hybrid':
                break;
            default:
                $sMapType = 'normal';
        }

        if ($this->_oDb->insertProfileLocation ($this->_iProfileId, $fLat, $fLng, $iZoom, $sMapType, $sAddress, $sCountry)) {
            $this->onEventProfileLocationManuallyUpdated ($this->_iProfileId, array ('lat' => $fLat, 'lng' => $fLng, 'zoom' => $iZoom, 'map_type' => $sMapType, 'address' => $sAddress, 'country' => $sCountry));
            echo 1;
        }
    }

    function actionSaveLocationHomepage ($iZoom, $sMapType, $fLat, $fLng) {
        $this->_saveLocationByPrefix ('bx_map_homepage', $iZoom, $sMapType, $fLat, $fLng);
    }

    function actionSaveLocationSeparatePage ($iZoom, $sMapType, $fLat, $fLng) {
        $this->_saveLocationByPrefix ('bx_map_separate', $iZoom, $sMapType, $fLat, $fLng);
    }

    function actionGetDataProfile ($iId, $sMapClassInstanceName) {
        
        $iProfileId = $iId ? (int)$iId : getLoggedId();
        $r = $this->_oDb->getProfileById($iProfileId);
        if (!$r || !$this->isAllowedViewLocation ($iProfileId, $r))
            return;        

        if (!$iId) {
            $aRet = array ();
            $aRet[] = array (
                    'lat' => $r['lat'],
                    'lng' => $r['lng'],
                    'data' => '',
                    'icon' => array ('w' => 0, 'h' => 0, 'sw' => 0, 'sh' => 0, 'sd' => '', 'url' => ''),
                );

            $oParser = new Services_JSON();
            echo $oParser->encode($aRet);
            return;
        }

        $aVars = array ('thumb' => get_member_thumbnail($r['id'], 'none', true));
        $sHtml = $this->_oTemplate->parseHtmlByName ('popup_profile', $aVars);

        $aIconJSON = false;
        if ($r['Avatar']) {

			include_once (BX_DIRECTORY_PATH_MODULES . 'boonex/avatar/include.php');
	        $sAvatarImg = BX_AVA_URL_USER_AVATARS . $r['Avatar'] . BX_AVA_EXT;
            $aIconJSON = array ('w' => 32, 'h' => 32, 'sw' => 34, 'sh' => 34, 'sd' => '1', 'url' => $sAvatarImg);
        }
        if (!$aIconJSON) {
            $sIconUser = $this->_oTemplate->getIconUrl ('user.png');
            $aIconJSON = array ('w' => 16, 'h' => 16, 'sw' => 24, 'sh' => 16, 'sd' => '', 'url' => $sIconUser);
        }

        $aRet = array ();
        $aRet[] = array (
                'lat' => $r['lat'],
                'lng' => $r['lng'],
                'data' => $sHtml,
                'icon' => $aIconJSON, 
            );

        $oParser = new Services_JSON();
        echo $oParser->encode($aRet);        
    }

    function actionGetHtmlProfiles ($sFilter) {        

        $aProfiles = $this->_getProfilesByFilter ($sFilter);
        if (!$aProfiles) {
            echo MsgBox(_t('_Empty'));
            return;
        }

        if (!$aProfiles) {
            echo MsgBox(_t('_Empty'));
            return;
        }

        bx_import ('BxTemplSearchProfile');
        $oBxTemplSearchProfile = new BxTemplSearchProfile();
        foreach ($aProfiles as $r) {
            $aProfileInfo = getProfileInfo($r['id']);
            $sProfiles .= $oBxTemplSearchProfile->displaySearchUnit($aProfileInfo);
        }

        echo $GLOBALS['oFunctions']->centerContent($sProfiles, '.searchrow_block_simple');
    }

    function actionGetData ($iZoom, $fLatMin, $fLatMax, $fLngMin, $fLngMax, $sMapClassInstanceName, $sFilter = '') {

        $fLatMin = (float)$fLatMin;
        $fLatMax = (float)$fLatMax;
        $fLngMin = (float)$fLngMin;
        $fLngMax = (float)$fLngMax;

        if ($sFilter) {
            echo $this->_getProfilesData('filter', $sFilter, 0, 0);
            return;
        }

        $iZoom = (int)$iZoom;
        if ($iZoom >= BX_MAP_ZOOM_COUNTRIES && $iZoom < BX_MAP_ZOOM_CITIES) {
            echo $this->_getCountriesData($fLatMin, $fLatMax, $fLngMin, $fLngMax, $sMapClassInstanceName);
        } elseif ($iZoom >= BX_MAP_ZOOM_CITIES && $iZoom < BX_MAP_ZOOM_PROFILES) {
            echo $this->_getCitiesData($fLatMin, $fLatMax, $fLngMin, $fLngMax, $sMapClassInstanceName);
        } elseif ($iZoom >= BX_MAP_ZOOM_PROFILES) {
            echo $this->_getProfilesData($fLatMin, $fLatMax, $fLngMin, $fLngMax);
        }
    }

    function _getProfilesData ($fLatMin, $fLatMax, $fLngMin, $fLngMax) {

        $sIconUser = $this->_oTemplate->getIconUrl ('user.png');
        $sIconGroup = $this->_oTemplate->getIconUrl ('group.png');

        if ('filter' == $fLatMin) {
            $a = $this->_getProfilesByFilter ($fLatMax); 
        } else {
            $a = $this->_oDb->getProfilesByBounds((float)$fLatMin, (float)$fLatMax, (float)$fLngMin, (float)$fLngMax);
        }

        $aa = array ();
        foreach ($a as $r) {
            $sKey = $r['lat'].'x'.$r['lng'];
            $aVars = array ('thumb' => get_member_thumbnail($r['id'], 'none', true));
            $aa[$sKey][] = array (
                'lat' => $r['lat'],
                'lng' => $r['lng'],
                'username' => $r['NickName'],
                'html' => $this->_oTemplate->parseHtmlByName ('popup_profile', $aVars),
            );
        }

        $aRet = array();
        foreach ($aa as $k => $a) {
            $sHtml = '';
            $aUsernames = array ();
            foreach ($a as $r) {
                $sHtml .= $r['html'];
                $aUsernames[] = $r['username'];
            }
            $aVars = array ('content' => $sHtml);
            $aRet[] = array (
                'lat' => $r['lat'],
                'lng' => $r['lng'],                
                'usernames' => $aUsernames,
                'data' => $this->_oTemplate->parseHtmlByName ('popup_profiles', $aVars),                
                'icon' => array ('w' => 16, 'h' => 16, 'sw' => 24, 'sh' => 16, 'sd' => '', 'url' => (count($a) > 1 ? $sIconGroup : $sIconUser)),
            );
        }
        $oParser = new Services_JSON();
        return $oParser->encode($aRet);
    }

    function _getCitiesData ($fLatMin, $fLatMax, $fLngMin, $fLngMax, $sMapClassInstanceName) {

        $sIconUrl = $this->_oTemplate->getIconUrl ('city.png');

        $a = $this->_oDb->getCitiesByBounds((float)$fLatMin, (float)$fLatMax, (float)$fLngMin, (float)$fLngMax);

        if (!preg_match('/^[A-Za-z0-9]+$/', $sMapClassInstanceName))
            return '';        

        $aRet = array();
        foreach ($a as $r) {
            $aVars = array (
                'url_flag' => genFlagUrl($r['country']),
                'country' => _t($GLOBALS['aPreValues']['Country'][$r['country']]['LKey']),
                'city' => trim($r['city']) ? $r['city'] : _t('_bx_map_unknown_city'),
                'num' => sprintf(_t('_bx_map_%d_members'), $r['num']),
                'lat' => $r['lat'],
                'lng' => $r['lng'],
                'zoom' => BX_MAP_ZOOM_PROFILES,
                'map_instance_name' => $sMapClassInstanceName,
            );
            $sHtml = $this->_oTemplate->parseHtmlByName ('popup_city', $aVars);
            $aRet[] = array (
                'lat' => $r['lat'],
                'lng' => $r['lng'],
                'data' => $sHtml,
                'icon' => array ('w' => 48, 'h' => 48, 'sw' => 72, 'sh' => 48, 'sd' => '', 'url' => $sIconUrl),
            );
        }
        $oParser = new Services_JSON();
        return $oParser->encode($aRet);
    }

    function _getCountriesData ($fLatMin, $fLatMax, $fLngMin, $fLngMax, $sMapClassInstanceName) {

        $a = $this->_oDb->getCountriesByBounds((float)$fLatMin, (float)$fLatMax, (float)$fLngMin, (float)$fLngMax);

        if (!preg_match('/^[A-Za-z0-9]+$/', $sMapClassInstanceName))
            return '';

        $aRet = array();
        foreach ($a as $r) {
            $sFlagUrl = genFlagUrl($r['country']);
            $aVars = array (
                'url_flag' => $sFlagUrl, 
                'country' => _t($GLOBALS['aPreValues']['Country'][$r['country']]['LKey']), 
                'num' => sprintf(_t('_bx_map_%d_members'), $r['num']),
                'lat' => $r['lat'],
                'lng' => $r['lng'],
                'zoom' => BX_MAP_ZOOM_CITIES,
                'map_instance_name' => $sMapClassInstanceName,
            );
            $sHtml = $this->_oTemplate->parseHtmlByName ('popup_country', $aVars);
            $aRet[] = array (
                'lat' => $r['lat'],
                'lng' => $r['lng'],
                'data' => $sHtml,
                'icon' => array ('w' => 18, 'h' => 12, 'sw' => 20, 'sh' => 14, 'sd' => 1, 'url' => $sFlagUrl),
            );
        }
        $oParser = new Services_JSON();
        return $oParser->encode($aRet);
    }

    // ================================== admin actions

    function actionClearTable ($sTable,  $isClearFailedOnly = true) {

        if (!$this->isAdmin() || 0 != strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        switch ($sTable) {
            case 'countries':
                $this->_oDb->clearCountries ($isClearFailedOnly ? true : false);
                $this->_oTemplate->displayMsg('Countries locations has been cleared');
                break;
            case 'cities':
                $this->_oDb->clearCities ($isClearFailedOnly ? true : false);
                $this->_oTemplate->displayMsg('Cities locations has been cleared');
                break;
            default:
                $this->_oTemplate->displayPageNotFound ();
        }
    }

    function actionUpdateCountries ($iLimit = 4, $iDelay = 6) { 

        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied ();
            return;            
        }

        $iLimit = (int)$iLimit;
        $iDelay = (int)$iDelay;

        $a = $this->_oDb->getUndefinedCountries ($iLimit);
        if ($a) {
            foreach ($a as $sCountryCode => $sCountryName) {
                $a = $this->_geocode ($sCountryName, $sCountryCode);                                
                if ($a) {                                        
                    $this->_oDb->insertCountryLocation ($sCountryCode, $a[0], $a[1]);
                } else {
                    $this->_oDb->insertCountryLocation ($sCountryCode, 0, 0, 1);
                }
                $this->onEventGeolocateCountry ($sCountryCode, array ('lat' => (isset($a[0]) ? $a[0] : false), 'lng' => (isset($a[1]) ? $a[1] : false)));
                if ($iDelay)
                    sleep ($iDelay);
            }

            $aVars = array (
                'refresh' => 1,
                'msg' => 'Countries update is in progress, please wait...',
            );
            echo $this->_oTemplate->parseHtmlByName ('updating', $aVars);
        } else {
            $this->_oTemplate->displayMsg('Countries locations update has been completed');
        }
    }

    function actionUpdateCities ($iLimit = 4, $iDelay = 6) {

        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied ();
            return;            
        }

        $iLimit = (int)$iLimit;
        $iDelay = (int)$iDelay;

        $a = $this->_oDb->getUndefinedCities ($iLimit);
        if ($a) {

            foreach ($a as $sCountryCode => $sCity) {
                $this->_updateCityLocation($iDelay, $sCity, $sCountryCode);
            }

            $aVars = array (
                'refresh' => 1,
                'msg' => 'Cities update is in progress, please wait...',
            );
            echo $this->_oTemplate->parseHtmlByName ('updating', $aVars);
        } else {
            $this->_oTemplate->displayMsg('Cities locations update has been completed');
        }
    }

    function actionUpdateProfiles ($iLimit = 4, $iDelay = 6) {

        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied ();
            return;            
        }

        $iLimit = (int)$iLimit;
        $iDelay = (int)$iDelay;

        $a = $this->_oDb->getUndefinedProfiles ($iLimit);
        if ($a) {
            foreach ($a as $iId => $r) {
                $this->_updateProfileLocation ($iDelay, $r);
            }

            $aVars = array (
                'refresh' => 1,
                'msg' => 'Profiles update is in progress, please wait...',
            );
            echo $this->_oTemplate->parseHtmlByName ('updating', $aVars);
        } else {
            $this->_oTemplate->displayMsg('Profiles locations update has been completed');
        }
    }

    function actionAdministration () {

        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();

		bx_import('BxTemplFormView');

        $aFormUpdateProfiles = array(
            'form_attrs' => array(
                'name'     => 'form_map_admin_update_profiles',
                'action'   => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'update_profiles',
                'method'   => 'post',
            ),                  
            'inputs' => array(
                'Submit' => array (
                    'type' => 'submit',
                    'name' => 'submit_form',
                    'value' => _t('_bx_map_admin_update_profiles'),
                    'colspan' => true,
                ),
            ),
        );		

        $aFormUpdateCities = array(
            'form_attrs' => array(
                'name'     => 'form_map_admin_update_profiles',
                'action'   => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'update_cities',
                'method'   => 'post',
            ),                  
            'inputs' => array(
                'Submit' => array (
                    'type' => 'submit',
                    'name' => 'submit_form',
                    'value' => _t('_bx_map_admin_update_cities'),
                    'colspan' => true,
                ),
            ),
        );

		$oFormUpdateProfiles = new BxTemplFormView ($aFormUpdateProfiles);
		$oFormUpdateCities = new BxTemplFormView ($aFormUpdateCities);

        $aVars = array (
            'module_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri(),
			'form_update_profiles' => $oFormUpdateProfiles->getCode(),
			'form_update_cities' => $oFormUpdateCities->getCode(),
        );
        $sContent = $this->_oTemplate->parseHtmlByName ('admin_links', $aVars);        

		echo $this->_oTemplate->adminBlock ($sContent, _t('_bx_map_admin_links'));


        bx_import('BxDolAdminSettings');

        $mixedResult = '';
        if(isset($_POST['save']) && isset($_POST['cat']) && (int)$_POST['cat']) {
	        $oSettings = new BxDolAdminSettings((int)$_POST['cat']);
            $mixedResult = $oSettings->saveChanges($_POST);
        }

        foreach ($_POST as $k => $v)
            unset ($_POST[$k]);

        $aCats = array (
            array (
                'cat' => 'Profiles Map General',
                'title' => _t('_bx_map_admin_settings_general'),
                'extra' => '',
            ),
            array (
                'cat' => 'Profiles Map Homepage',
                'title' => _t('_bx_map_admin_settings_homepage'),
                'extra' => 'return $this->_saveLocationForm ("Homepage", $this->serviceHomepageBlock ());',
            ),
            array (
                'cat' => 'Profiles Map Separate',
                'title' => _t('_bx_map_admin_settings_separate'),
                'extra' => 'return $this->_saveLocationForm ("Page", $this->serviceSeparatePageBlock ());',
            ),
            array (
                'cat' => 'Profiles Map Profile',
                'title' => _t('_bx_map_admin_settings_profile'),
                'extra' => '',
            ),
        );

        foreach ($aCats as $a) {
            $iId = $this->_oDb->getSettingsCategory($a['cat']);
            $oSettings = new BxDolAdminSettings($iId);
            $sResult = $oSettings->getForm();        	       
            if($mixedResult !== true && !empty($mixedResult) && $_POST['cat'] == $iId)
                $sResult = $mixedResult . $sResult;
            $sExtra = '';
            if ($a['extra']) {
                $aVars = array ('content' => eval($a['extra']));
                $sExtra = $this->_oTemplate->parseHtmlByName('extra_wrapper', $aVars);
            }
            $aVars = array ('content' => $sResult . $sExtra);
            echo $this->_oTemplate->adminBlock ($this->_oTemplate->parseHtmlByName('default_padding', $aVars), $a['title']); 
        }

        $this->_oTemplate->addJsAdmin ('http://www.google.com/jsapi?key=' . getParam('bx_map_key'));
        $this->_oTemplate->addJsAdmin (BX_DOL_URL_MODULES . $this->_aModule['path'] . 'js/BxMap.js');
        $this->_oTemplate->addCssAdmin ('main.css');
        $this->_oTemplate->addCssAdmin ('forms_adv.css');
        $this->_oTemplate->pageCodeAdmin (_t('_bx_map_administration'));
    }


    // ================================== service actions

    /**
     * Get profile location by user id
     * @param $iProfileId user's id 
     * @param $iViewerProfileId viewer profile id
     * @param $isClearFailedOnly check privacy settings before returning location
     * @return array with location info on success, false on error, -1 if access denied
     */ 
    function serviceGetLocation ($iProfileId, $iViewerProfileId = 0, $isCheckPrivacy = true) {
        $iProfileId = (int)$iProfileId;
        $iViewerProfileId = (int)$iViewerProfileId;
        if ($isCheckPrivacy) {
            if (!$iViewerProfileId)
                $iViewerProfileId = $this->_iProfileId; 
            if (!$this->_oPrivacy->check('view_location', $iProfileId, $iViewerProfileId))
                return -1;
        }

        $aLocation = $iProfileId ? $this->_oDb->getProfileById($iProfileId) : false;
        if (!$aLocation)
            return false;

        if (-1 == $aLocation['zoom'])
            $aLocation['zoom'] = getParam('bx_map_profile_zoom');

        if (!$aLocation['type'])
            $aLocation['type'] = getParam('bx_map_profile_map_type');

        return $aLocation;
    }

    /**
     * Edit location block
     * @param $iProfileId user's id which location is edited
     * @return html with clickable map
     */ 
    function serviceEditLocation ($iProfileId) {

        $iProfileId = (int)$iProfileId;

        $aLocation = $iProfileId ? $this->_oDb->getProfileById($iProfileId) : false;
        if (!$aLocation)
            return false;

        $fLat = false;
        $fLng = false;
        $iZoom = false;
        $sMapType = false;

        if ($aLocation) {
            $fLat = $aLocation['lat'];
            $fLng = $aLocation['lng'];
            $iZoom = $aLocation['zoom'];
            $sMapType = $aLocation['type'];
        }

        if (false === $fLat || false === $fLng) {
            $aProfile = getProfileInfo($iProfileId);
            $aLocationCountry = $this->_oDb->getCountryByCode($aProfile['Country'], false);
            $fLat = $aLocationCountry['lat'];
            $fLng = $aLocationCountry['lng'];
            $iZoom = BX_MAP_ZOOM_CITIES;            
        }

        if (false === $iZoom || -1 == $iZoom)
            $iZoom = getParam('bx_map_profile_zoom');

        if (!$sMapType)
            $sMapType = getParam('bx_map_profile_map_type');

        $aVars = array (
            'msg_incorrect_google_key' => _t('_bx_map_msg_incorrect_google_key'),
            'loading' => _t('_loading ...'),
            'map_control' => getParam('bx_map_profile_control_type'),
            'map_is_type_control' => getParam('bx_map_profile_is_type_control') == 'on' ? 1 : 0,
            'map_is_scale_control' => getParam('bx_map_profile_is_scale_control') == 'on' ? 1 : 0,
            'map_is_overview_control' => getParam('bx_map_profile_is_overview_control') == 'on' ? 1 : 0,
            'map_is_dragable' => getParam('bx_map_profile_is_map_dragable') == 'on' ? 1 : 0,
            'map_type' => $sMapType,
            'map_lat' => $fLat,
            'map_lng' => $fLng,
            'map_zoom' => $iZoom,
            'suffix' => 'Edit',
            'subclass' => 'bx_map_profile',
            'data_url' => BX_DOL_URL_MODULES . "?r=map_profiles/get_data_profile/0/{instance}/{ts}",
            'save_data_url' => BX_DOL_URL_MODULES . "?r=map_profiles/save_data_profile/{zoom}/{map_type}/{lat}/{lng}/{instance}/{address}/{country}/{ts}",
            'save_location_url' => '',
            'shadow_url' => '',
        );
        return  $this->_oTemplate->parseHtmlByName('map', $aVars);
    }

    /**
     * Homepage block with world map
     * @return html with world map
     */ 
    function serviceHomepageBlock () {
        $aVars = array (
            'msg_incorrect_google_key' => _t('_bx_map_msg_incorrect_google_key'),
            'loading' => _t('_loading ...'),
            'map_control' => getParam('bx_map_homepage_control_type'),
            'map_is_type_control' => getParam('bx_map_homepage_is_type_control') == 'on' ? 1 : 0,
            'map_is_scale_control' => getParam('bx_map_homepage_is_scale_control') == 'on' ? 1 : 0,
            'map_is_overview_control' => getParam('bx_map_homepage_is_overview_control') == 'on' ? 1 : 0,
            'map_is_dragable' => getParam('bx_map_homepage_is_map_dragable') == 'on' ? 1 : 0,
            'map_type' => getParam('bx_map_homepage_map_type'),
            'map_lat' => getParam('bx_map_homepage_lat'),
            'map_lng' => getParam('bx_map_homepage_lng'),
            'map_zoom' => getParam('bx_map_homepage_zoom'),
            'suffix' => 'Homepage',
            'subclass' => 'bx_map_homepage',
            'data_url' => BX_DOL_URL_MODULES . "?r=map_profiles/get_data/{zoom}/{lat_min}/{lat_max}/{lng_min}/{lng_max}/{instance}",
            'save_data_url' => '',
            'save_location_url' => $this->isAdmin() ? BX_DOL_URL_MODULES . "?r=map_profiles/save_location_homepage/{zoom}/{map_type}/{lat}/{lng}" : '',
            'shadow_url' => $this->_oTemplate->getIconUrl ('flag_icon_shadow.png'),
        );
        $this->_oTemplate->addJs ('http://www.google.com/jsapi?key=' . getParam('bx_map_key'));
        $this->_oTemplate->addJs ('BxMap.js');
        $this->_oTemplate->addCss ('main.css');
        return $this->_oTemplate->parseHtmlByName('map', $aVars);
    }

    /**
     * Separate page block with world map
     * @return html with world map
     */     
    function serviceSeparatePageBlock ($fLat = false, $fLng = false, $iZoom = false, $sFilter = '') {

        if (false === $fLat)
            $fLat = getParam('bx_map_separate_lat');
        if (false === $fLng)
            $fLng = getParam('bx_map_separate_lng');
        if (false === $iZoom)
            $iZoom = getParam('bx_map_separate_zoom');

        $aVars = array (
            'msg_incorrect_google_key' => _t('_bx_map_msg_incorrect_google_key'),
            'loading' => _t('_loading ...'),
            'map_control' => getParam('bx_map_separate_control_type'),
            'map_is_type_control' => getParam('bx_map_separate_is_type_control') == 'on' ? 1 : 0,
            'map_is_scale_control' => getParam('bx_map_separate_is_scale_control') == 'on' ? 1 : 0,
            'map_is_overview_control' => getParam('bx_map_separate_is_overview_control') == 'on' ? 1 : 0,
            'map_is_dragable' => getParam('bx_map_separate_is_map_dragable') == 'on' ? 1 : 0,
            'map_type' => getParam('bx_map_separate_map_type'),
            'map_lat' => $fLat,
            'map_lng' => $fLng,
            'map_zoom' => $iZoom,
            'suffix' => 'Page',
            'subclass' => 'bx_map_separate',
            'data_url' => BX_DOL_URL_MODULES . "?r=map_profiles/get_data/{zoom}/{lat_min}/{lat_max}/{lng_min}/{lng_max}/{instance}/$sFilter&page={page}",
            'save_data_url' => '',
            'save_location_url' => $this->isAdmin() ? BX_DOL_URL_MODULES . "?r=map_profiles/save_location_separate_page/{zoom}/{map_type}/{lat}/{lng}" : '',
            'shadow_url' => $this->_oTemplate->getIconUrl ('flag_icon_shadow.png'),
        );
        return $this->_oTemplate->parseHtmlByName('map', $aVars);
    }

    /**
     * Profile block with user's location map
     * @param $iProfileId user's id which location is shown on the map
     * @return html with user's location map
     */         
    function serviceProfileBlock ($iProfileId) {

        $iProfileId = (int)$iProfileId;        
        $r = $this->_oDb->getProfileById($iProfileId);

        $sBoxContent = '';
        if ($r && $this->isAllowedViewLocation ($iProfileId, $r)) {

            $aVars = array (
                'msg_incorrect_google_key' => _t('_bx_map_msg_incorrect_google_key'),
                'loading' => _t('_loading ...'),
                'map_control' => getParam('bx_map_profile_control_type'),
                'map_is_type_control' => getParam('bx_map_profile_is_type_control') == 'on' ? 1 : 0,
                'map_is_scale_control' => getParam('bx_map_profile_is_scale_control') == 'on' ? 1 : 0,
                'map_is_overview_control' => getParam('bx_map_profile_is_overview_control') == 'on' ? 1 : 0,
                'map_is_dragable' => getParam('bx_map_profile_is_map_dragable') == 'on' ? 1 : 0,
                'map_lat' => $r['lat'],
                'map_lng' => $r['lng'],
                'map_zoom' => -1 == $r['zoom'] ? getParam('bx_map_profile_zoom') : $r['zoom'],
                'map_type' => !$r['type'] ? getParam('bx_map_profile_map_type') : $r['type'],
                'suffix' => 'Profile',
                'subclass' => 'bx_map_profile',
                'data_url' => BX_DOL_URL_MODULES . "' + '?r=map_profiles/get_data_profile/" . $iProfileId . "/{instance}",
                'save_data_url' => '',
                'save_location_url' => '',
                'shadow_url' => $this->_oTemplate->getIconUrl ('profile_icon_shadow.png'),
            );
            $this->_oTemplate->addJs ('http://www.google.com/jsapi?key=' . getParam('bx_map_key'));
            $this->_oTemplate->addJs ('BxMap.js');
            $this->_oTemplate->addCss ('main.css');

            $aVars2 = array (
                'text' => $r['address'] ? $r['address'] : _t('_bx_map_the_same_address'), 
                'map' => $this->_oTemplate->parseHtmlByName('map', $aVars),
            );
            $sBoxContent = $this->_oTemplate->parseHtmlByName('user_location', $aVars2);
        }

        $sBoxFooter = '';
        if ($iProfileId == $this->_iProfileId) {
            $aVars = array (
                'icon' => $this->_oTemplate->getIconUrl('more.png'),
                'url' => $this->_oConfig->getBaseUri() . 'edit',
                'title' => _t('_bx_map_box_footer_edit'),
            );
            $sBoxFooter = $this->_oTemplate->parseHtmlByName('box_footer', $aVars);
            if (!$sBoxContent)
                $sBoxContent = MsgBox(_t('_bx_map_msg_locations_is_not_defined'));
        }

        if ($sBoxContent || $sBoxFooter)
            return array($sBoxContent, array(), $sBoxFooter);
        return '';
    }

    /**
     * Removes any geocoding information associated with profile
     * @param $iProfileId user's id which location going to be removed
     * @return true if location existed and was deleted, false on error or location didn't exist
     */ 
    function serviceDeleteProfileLocation ($iProfileId) {
        return $this->_oDb->deleteLocation((int)$iProfileId);
    }

    /**
     * Update profile's location
     * @param $iProfileId user's id which location going to be removed
     * @return true if position was successfully geocoded, and false if doesn't
     */     
    function serviceUpdateProfileLocation ($iProfileId) {
        $iProfileId = (int)$iProfileId;
        $a = getProfileInfo($iProfileId);
        if ($this->_updateProfileLocation(0, $a)) {
            if (!$this->_oDb->isCityLocationExists($a['Country'], process_db_input($a['City'], BX_TAGS_NO_ACTION, BX_SLASHES_NO_ACTION)))
                $this->_updateCityLocation(0, $a['City'], $a['Country']);
            return true;
        }
        return false;
    }

    /**
     * Update profile's location manually
     * @param $iProfileId user's id which location going to be updated 
     * @param $fLat latitude
     * @param $gLng longitude
     * @param $iZoom zoom level
     * @param $sMapType map type (hybrid, satellite or standard)
     * @param $sCountry 2 letters country code
     * @param $sAddress address string
     * @return true if position was successfully updated, and false if doesn't
     */     
    function serviceUpdateProfileLocationManually ($iProfileId, $fLat, $fLng, $iZoom, $sMapType, $sCountry, $sAddress) {
        $a = getProfileInfo((int)$iProfileId);
        $aLocationOld = $this->_oDb->getProfileById((int)$iProfileId);

        if (!$iZoom)
            $iZoom = $aLocationOld && $aLocationOld['zoom'] ? $aLocationOld['zoom'] : -1;
        switch ($sMapType) {
        case 'hybrid':
        case 'satellite':
        case 'standard':
        break;
        default:
            $sMapType = 'standard'; 
        }

        $fLat = (float)$fLat;
        $fLng = (float)$fLng;
        $iZoom = (int)$iZoom;

        $iRet = $this->_oDb->insertProfileLocation (
            $iProfileId, 
            $fLat, 
            $fLng, 
            $iZoom, 
            $sMapType, 
            '', // address 
            '', // country 
            $aLocationOld && $aLocationOld['allow_view_location_to'] ? $aLocationOld['allow_view_location_to'] : BX_MAP_DEFAULT_PRIVACY);

        if ($iRet) {
            $this->onEventProfileLocationManuallyUpdated ($iProfileId, array ('lat' => $fLat, 'lng' => $fLng, 'zoom' => $iZoom, 'map_type' => $sMapType, 'address' => '', 'country' => ''));
            return true;
        }

        return false;
    }

    function serviceGetSpyPost($sAction, $iObjectId = 0, $iSenderId = 0, $aExtraParams = array())
    {
        if (!($aProfile = getProfileInfo($iSenderId)))
            return array();

        return array(
            'lang_key' => '_bx_map_spy_location_updated', 
            'params' => array(
                'profile_link' => getProfileLink($iSenderId), 
                'profile_nick' => $aProfile['NickName'],
            ),
            'recipient_id' => 0,
            'spy_type' => 'profiles_activity',
        );
    }
    

    function serviceGetSpyData () {
        return array(
            'handlers' => array(
                array('alert_unit' => 'bx_map', 'alert_action' => 'location_manually_updated', 'module_uri' => $this->_aModule['uri'], 'module_class' => 'Module', 'module_method' => 'get_spy_post'),
            ),
            'alerts' => array(
                array('unit' => 'bx_map', 'action' => 'location_manually_updated'),
            )
        );
    }

    // ================================== events

    function onEventGeolocateProfile ($iProfileId, $aLocation) {
		$oAlert = new BxDolAlerts('bx_map', 'geolocate_profile', $iProfileId, $this->_iProfileId, array('location' => $aLocation));
		$oAlert->alert();
    }

    function onEventGeolocateCity ($sCity, $aLocation) {
		$oAlert = new BxDolAlerts('bx_map', 'geolocate_city', $sCity, $this->_iProfileId, array('location' => $aLocation));
		$oAlert->alert();
    }

    function onEventGeolocateCountry ($sCountryCode, $aLocation) {
		$oAlert = new BxDolAlerts('bx_map', 'geolocate_country', $sCountryCode, $this->_iProfileId, array('location' => $aLocation));
		$oAlert->alert();
    }

    function onEventProfileLocationManuallyUpdated ($iProfileId, $aLocation) {
		$oAlert = new BxDolAlerts('bx_map', 'location_manually_updated', $iProfileId, $this->_iProfileId, array('location' => $aLocation));
		$oAlert->alert();
    }

    // ================================== permissions  

    function isAllowedEditOwnLocation (&$aLocation) {
        if(!$this->_iProfileId) 
            return false;
        if (!$aLocation || $aLocation['id'] == $this->_iProfileId) {
            return true;
        }        
        return false;
    }

    function isAllowedViewLocation ($iProfileId, &$aLocation) {
        if (!$aLocation) 
            return false;
        if ($iProfileId == $this->_iProfileId || $this->isAdmin())
            return true;
        return $this->_oPrivacy->check('view_location', $iProfileId, $this->_iProfileId); 
    }

    function isAdmin () {
        return $GLOBALS['logged']['admin'] || $GLOBALS['logged']['moderator'];
    }             

    // ================================== other 

    function _geocode ($sAddress, $sCountryCode = '') {

        $iRet = 404;

        $sAddress = rawurlencode($sAddress);
        $sUrl = "http://maps.google.com/maps/geo";

        $s = bx_file_get_contents($sUrl, array(
            'q' => $sAddress,
            'output' => 'xml',
            'key' => getParam('bx_map_key')
        ));
        
        if (preg_match ('/<code>(\d+)<\/code>/', $s, $m))
        {
            $iRet = $m[1];
            if (200 != $iRet) return false;

            if (preg_match_all ('/<CountryNameCode>([A-Za-z]+)<\/CountryNameCode>/', $s, $mCountry) &&
                preg_match_all ('/<coordinates>([0-9,\.-]+)<\/coordinates>/', $s, $mCoord))
            {
                if (isset($mCountry[1]) && is_array($mCountry[1]))
                    $mCountry = $mCountry[1];

                foreach ($mCountry AS $k => $v)
                {
                    if (!$sCountryCode || ($sCountryCode && 0 == strcasecmp($sCountryCode, $v))) 
                    {
                        // Parse coordinate string
                        list ($fLongitude, $fLatitude, $fAltitude) = explode(",", $mCoord[1][$k]);
                        return array ($fLatitude, $fLongitude, $v);
                    }
                }
            }
        }
        return false;
    }

    function _updateCityLocation($iDelay, &$sCity, $sCountryCode) {

        $iDelay = (int)$iDelay;
        if ($iDelay) sleep ($iDelay);
        $a = $this->_geocode ($sCity . ' ' . $sCountryCode, $sCountryCode);        
        if ($a) {
            $this->_oDb->insertCityLocation ($sCountryCode, process_db_input($sCity, BX_TAGS_NO_ACTION, BX_SLASHES_NO_ACTION), $a[0], $a[1]);
            $bRet = true;
        } else {
            $this->_oDb->insertCityLocation ($sCountryCode, process_db_input($sCity, BX_TAGS_NO_ACTION, BX_SLASHES_NO_ACTION), 0, 0, 1);
            $bRet = false;
        }
        $this->onEventGeolocateCity ($sCity, array ('lat' => (isset($a[0]) ? $a[0] : false), 'lng' => (isset($a[1]) ? $a[1] : false), 'country' => $sCountryCode));
        return $bRet;
    }

    function _updateProfileLocation($iDelay, &$r) {

        $iDelay = (int)$iDelay;

        $iId = (int)$r['ID'];
        $a = false;

        $sAddressField = getParam('bx_map_address_field');
        if ($sAddressField && isset ($r[$sAddressField]) && trim($r[$sAddressField])) {
            if ($iDelay) sleep ($iDelay);
            $a = $this->_geocode ($r[$sAddressField] . ' ' . $r['City'] . ' ' . $r['Country'], $r['Country']);
        }

        if (!$a && isset($r['zip']) && trim($r['zip'])) {
            if ($iDelay) sleep ($iDelay);
            $a = $this->_geocode ($r['zip'] . ' ' . $r['Country'], $r['Country']);
        }

        if (!$a) {
            if ($iDelay) sleep ($iDelay);
            $a = $this->_geocode ($r['City'] . ' ' . $r['Country'], $r['Country']);
        }
        
        if ($a) {
            $this->_oDb->insertProfileLocation ($iId, $a[0], $a[1], -1, '', process_db_input($r['City'] . ', ' . $r['Country'], BX_TAGS_NO_ACTION, BX_SLASHES_NO_ACTION), process_db_input($r['Country'], BX_TAGS_NO_ACTION, BX_SLASHES_NO_ACTION), BX_MAP_DEFAULT_PRIVACY);
            $bRet = true;
        } else {
            $this->_oDb->insertProfileLocation ($iId, 0, 0, -1, '', '', '', BX_MAP_DEFAULT_PRIVACY, 1);
            $bRet = false;
        }          
        $this->onEventGeolocateProfile ($iId, array ('lat' => (isset($a[0]) ? $a[0] : false), 'lng' => (isset($a[1]) ? $a[1] : false), 'country' => $sCountryCode));
        return $bRet;
    }

    function _saveLocationByPrefix ($sPrefix, $iZoom, $sMapType, $fLat, $fLng) {

        if (!$this->isAdmin()) {
            echo 'Access denied';
            return;
        }

        if ($iZoom = (int)$iZoom)
            setParam($sPrefix . '_zoom', $iZoom);

        switch ($sMapType) {
            case 'normal':
            case 'satellite':
            case 'hybrid':
                setParam($sPrefix . '_map_type', $sMapType);
        }                

        if ($fLat = (float)$fLat)
            setParam($sPrefix . '_lat', $fLat);

        if ($fLng = (float)$fLng)
            setParam($sPrefix . '_lng', $fLng);

        echo 'ok';
    }    

    function _saveLocationForm ($sSuffix, $sMap) {

        if (!preg_match('/^[A-Za-z0-9]+$/', $sSuffix))
            return '';

        $aCustomForm = array(
            
            'form_attrs' => array(
                'name'     => "bx_map_save_location_{$sSuffix}",
                'onsubmit'   => "return glBxMap{$sSuffix}.saveLocation();",
                'method'   => 'post',
            ),

            'inputs' => array(

                'Map' => array (
                    'type' => 'custom',
                    'content' => "<div class=\"bx_map_form_map\">$sMap</div>",
                    'name' => 'Map',
                    'caption' => _t('_bx_map_admin_map'),
                ),

                'Submit' => array (
                    'type' => 'submit',
                    'name' => 'submit_form',
                    'value' => _t('_bx_map_admin_save_location'),
                    'colspan' => true,
                ),                            
            ),
        );

        $f = new BxTemplFormView ($aCustomForm);
        return $f->getCode();
    }

    function _getProfilesByFilter ($sFilter) {

        $aGetParams = unserialize(base64_decode(str_replace('-slash-', '/',$sFilter)));
        if ($aGetParams && is_array($aGetParams))
            $aGetParams = $_REQUEST = $_GET = array_merge ($_REQUEST, $aGetParams);

        bx_import('BxDolProfileFields');
        $oPF = new BxDolProfileFields(9);
        $aRequestParams = $oPF->collectSearchRequestParams();

        bx_import ('BxTemplProfileView');
        $oProfileGenerator = new BxBaseProfileGenerator(getLoggedId());
        list ($aWhere, $sJoin, $sPossibleOrder) = $oProfileGenerator->GenSqlConditions($oPF->aBlocks, $aRequestParams);

        $iPage = isset($aGetParams['page']) && (int)$aGetParams['page'] > 0 ? (int)$aGetParams['page'] : 1;
        $iPerPage = isset($aGetParams['per_page']) && (int)$aGetParams['per_page'] > 0 ? (int)$aGetParams['per_page'] : getParam('bx_map_per_page'); 
        $iStart = ($iPage - 1) * $iPerPage;            
        
        $sQuery = 'SELECT DISTINCT `Profiles`.`ID`, `Profiles`.`Avatar`, `Profiles`.`NickName`, `m`.`id`, `m`.`lat`, `m`.`lng`
            FROM `Profiles` 
            INNER JOIN `' . $this->_oDb->_sPrefix . 'profiles` AS `m` ON (`Profiles`.`ID` = `m`.`id`) 
            ' . $sJoin . ' 
            WHERE ' . implode( ' AND ', $aWhere ) . ' AND `m`.`failed` = 0 ' . $sPossibleOrder . " 
            LIMIT $iStart, $iPerPage";

        // ID is ambiguous
        $sQuery = str_replace (' `ID`', ' `Profiles`.`ID`', $sQuery);
        
        $aProfiles = $GLOBALS['MySQL']->getAll($sQuery);

        return $aProfiles;
    }
}

?>
