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

bx_import('BxDolModule');
bx_import('BxTemplSearchResult');

class BxProfileCustomizeSearchResult extends BxTemplSearchResult {
    var $aCurrent = array(
        'name' => 'bx_profile_customize',
        'title' => '_bx_profile_customize',
        'table' => 'bx_profile_custom_units',
        'ownFields' => array('id', 'name', 'caption', 'css_name', 'type'),
        'searchFields' => array(),
        'restriction' => array(
            'type' => array('value' => '', 'field' => 'type', 'operator' => '='),
        ),
        'ident' => 'id'
    );      
    var $aPermalinks;
    
    var $_oModule;
    var $_sType;
    
    function BxProfileCustomizeSearchResult($sType, $oModule = null) 
    {
        parent::BxTemplSearchResult();
        
        if(!empty($oModule))
            $this->_oModule = $oModule;
        else 
            $this->_oModule = &BxDolModule::getInstance('BxProfileCustomizeModule');
            
        $this->aCurrent['restriction']['type']['value'] = $sType;
        $this->_sType = $sType;
    }
    
    function displaySearchUnit($aData) 
    {
        return $this->_oModule->_oTemplate->parseHtmlByName('admin_unit.html', array(
            'caption' => $aData['caption'],
            'value' => $aData['id'],
            'edit_url' => BX_DOL_URL_ROOT . $this->_oModule->_oConfig->getBaseUri() . 'administration/' . $this->_sType . '/' . $aData['id'],
            'edit_str' => _t('_bx_profile_customize_edit')
        ));
    }
    
    function displayResultBlock() 
    {
        $sResult = parent::displayResultBlock();

        return $sResult;
    }
    
    function getAlterOrder () 
    {
        return array(
            'order' => " ORDER BY `id`"
        );
    }
}

?>