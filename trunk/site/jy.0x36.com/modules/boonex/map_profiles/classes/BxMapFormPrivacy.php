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

bx_import('BxTemplFormView');

class BxMapFormPrivacy extends BxTemplFormView {

    function BxMapFormPrivacy (&$oMain) {

        $aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'form_map_privacy',
                'action'   => '',
                'method'   => 'post',
            ),      

            'params' => array (
                'db' => array(
                    'table' => $oMain->_oConfig->getDbPrefix() . 'profiles',
                    'key' => 'id',
                    'submit_name' => 'submit_form',
                ),
            ),
                  
            'inputs' => array(

                'header_privacy' => array(
                    'type' => 'block_header',
                    'caption' => _t('_bx_map_privacy_view_location'),
                ),

                'allow_view_location_to' => $oMain->_oPrivacy->getGroupChooser($oMain->_iProfileId, 'map_profiles', 'view_location'),

                'Submit' => array (
                    'type' => 'submit',
                    'name' => 'submit_form',
                    'value' => _t('_Submit'),
                    'colspan' => true,
                ),
            ),            
        );

        $aCustomForm['inputs']['allow_view_location_to']['colspan'] = true;
        /*
        if (empty($aCustomForm['inputs']['allow_view_location_to']['value']) || !$aCustomForm['inputs']['allow_view_location_to']['value'])
            $aCustomForm['inputs']['allow_view_location_to']['value'] = BX_MAP_DEFAULT_PRIVACY;
        */
        parent::BxTemplFormView ($aCustomForm);
    }
}

?>
