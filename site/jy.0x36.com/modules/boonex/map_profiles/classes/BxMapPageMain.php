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

bx_import('BxDolPageView');

class BxMapPageMain extends BxDolPageView {	

    var $oMain;
    var $oTemplate;
    var $oConfig;
    var $oDb;
    var $sUrlStart;

    function BxMapPageMain(&$oModule) {        
        $this->oMain = &$oModule;
        $this->oTemplate = $oModule->_oTemplate;
        $this->oConfig = $oModule->_oConfig;
        $this->oDb = $oModule->_oDb;
		parent::BxDolPageView('bx_map');
	}

    function getBlockCode_Map() {

        $fLat = false;
        $fLng = false;
        $iZoom = false;
        $sFilter = '';

        bx_import('BxDolProfileFields');
        $oPF = new BxDolProfileFields(9);
        $aRequestParams = $oPF->collectSearchRequestParams();

        $aCountryLocation = false;
        if ($aRequestParams && isset($aRequestParams['Country']) && 1 == count($aRequestParams['Country']) && preg_match('/^[a-zA-Z]+$/', $aRequestParams['Country'][0])) {
            $r = $this->oDb->getCountryByCode($aRequestParams['Country'][0]);
            if ($r) {
                $fLat = $r['lat'];
                $fLng = $r['lng'];
                $iZoom = BX_MAP_ZOOM_CITIES;
            }
        }

        $sProfiles = '';
        if ($aRequestParams) {

            $sFilter = str_replace('/', '-slash-', base64_encode(serialize($_GET)));
            $sUrlGetProfiles = $this->oConfig->getBaseUri() . 'get_html_profiles/' . $sFilter . '?page={page}&ts={ts}';
            $aVars = array (
                'content_id' => 'bx_map_profiles',
                'content' => "<script>glBxMapProfilesMapOnLoadCallback = function () { glBxMapPage.loadProfilesList('bx_map_profiles', '$sUrlGetProfiles'); }; bx_map_loading('bx_map_profiles', 1) </script>",
                'prev_title' => _t('_Prev'),
                'prev_onclick' => "glBxMapPage.changePage('bx_map_profiles', '{$sUrlGetProfiles}', -1);",
                'prev_href' => 'javascript:void(0);',
                'next_title' => _t('_Next'),
                'next_onclick' => "glBxMapPage.changePage('bx_map_profiles', '{$sUrlGetProfiles}', 1);",
                'next_href' => 'javascript:void(0);',
            );
            $sProfiles = $this->oTemplate->parseHtmlByName ('pageable_items', $aVars);
        }


        return $sProfiles . $this->oMain->serviceSeparatePageBlock ($fLat, $fLng, $iZoom, $sFilter);
    }

    function getBlockCode_Search() {

        global $logged;

        $aProfile = $logged['member'] ? getProfileInfo((int)$_COOKIE['memberID']) : array();

        // default params for search form

        $sCountry = '';
		$mixedCountry = bx_get('Country');
        if ($mixedCountry && is_array($mixedCountry)) {
            $sCountry = process_pass_data($mixedCountry[0], 1);
        } elseif ($mixedCountry && is_string($mixedCountry)) {
            $sCountry = process_pass_data($mixedCountry, 1);
        }

        $sSex = '';
        $mixedSex = bx_get('Sex');
        if ($mixedSex && is_array($mixedSex)) {
            $sSex = array();
            foreach ($mixedSex as $v)
                $sSex[] = process_pass_data($v, 1);
        } elseif ($mixedSex && is_string($mixedSex)) {
            $sSex = process_pass_data($mixedSex, 1);
        }

        $aDefaultParams = array(
            'LookingFor'  => bx_get('LookingFor') ? process_pass_data(bx_get('LookingFor'), 1) : ($aProfile['Sex'] ? $aProfile['Sex'] : 'male'),
            'Sex'         => $sSex ? $sSex : ($aProfile['LookingFor'] ? $aProfile['LookingFor'] : 'female'),
            'Country'     => $sCountry ? $sCountry : ($aProfile['Country'] ? $aProfile['Country'] : getParam('default_country')),
            'DateOfBirth' => bx_get('DateOfBirth') ? process_pass_data(bx_get('DateOfBirth'), 1) : getParam('search_start_age') . '-' . getParam('search_end_age'),
            'Tags'        => bx_get('Tags') ? process_pass_data(bx_get('Tags'), 1) : '',
            'online_only' => bx_get('online_only') ? process_pass_data(bx_get('online_only'), 1) : '',
            'photos_only' => bx_get('photos_only') ? process_pass_data(bx_get('photos_only'), 1) : '',
        );

        bx_import('BxDolProfileFields');
        $oPF = new BxDolProfileFields(9);
        $a = array(
            'default_params' => $aDefaultParams, 
            'form_attrs' => array (             
                'action' => $this->oConfig->getBaseUri(),
            ),
            'inputs' => array ( // this is needed when permalinks are disabled
                array(
                    'type'  => 'hidden',
                    'name'  => 'r',
                    'value' => $this->oMain->_aModule['uri'],
                ),
            ),
        );
        $aVars = array (
            'content' => $oPF->getFormCode($a),
        );
        return $this->oTemplate->parseHtmlByName('search_form_wrapper', $aVars);
    }
}

?>
