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

global $aModule;
bx_import('FormAdd', $aModule);
bx_import('BxDolCategories');

class BxSitesFormEdit extends BxSitesFormAdd {
    
    function BxSitesFormEdit($oModule, $aParam = array())
    {
        $this->_oModule = $oModule;
        $this->_aParam = $aParam;
        
        if (count($aParam) && isset($aParam['photo']) && $aParam['photo'] != 0)
        {
            $aFile = BxDolService::call('photos', 'get_photo_array', array($aParam['photo'], 'browse'), 'Search');
            
            if (!$aFile['no_image'])
            {
                $aParam = array_merge($aParam, array(
                    'thumbnail' => $GLOBALS['oBxSitesModule']->_oTemplate->parseHtmlByName('thumb110.html', array(
                        'image' => $aFile['file'],
                        'spacer' => getTemplateIcon('spacer.gif')
                    ))
                ));
            }
        }
        
        $this->_aCustomForm = $this->getFullForm();
        $this->_aCustomForm['form_attrs']['action'] = BX_DOL_URL_ROOT . $this->_oModule->_oConfig->getBaseUri() . 'edit/' . $aParam['id']; 

        $oCategories = new BxDolCategories();
        $oCategories->getTagObjectConfig ();
        $this->_aCustomForm['inputs']['categories'] = $oCategories->getGroupChooser ('bx_sites', (int)$this->_oModule->iOwnerId, true, $this->_aParam['categories']);
        
        $aFormInputsSubmit = array (
            'Submit' => array (
                'type' => 'submit',
                'name' => 'submit_form',
                'value' => _t('_Submit'),
                'colspan' => false,
            ),            
        );
        
        $this->_aCustomForm['inputs'] = array_merge($this->_aCustomForm['inputs'], $aFormInputsSubmit);

        parent::BxTemplFormView ($this->_aCustomForm);
    }

    function checkUploadPhoto()
    {
        $aFileInfo = array (
            'medTitle' => stripslashes($this->getCleanValue('title')),
            'medDesc' => stripslashes($this->getCleanValue('title')),
            'medTags' => 'sites',
            'Categories' => array('Sites'),
        );
        $sTmpFile = BX_DIRECTORY_PATH_ROOT . 'tmp/' . time() . $this->_oModule->iOwnerId;
        
        if (move_uploaded_file($_FILES['photo']['tmp_name'],  $sTmpFile)) 
        {
            if ($this->_aParam['photo'] != 0)
                BxDolService::call('photos', 'remove_object', array($this->_aParam['photo']), 'Module');
                
            $iRet = BxDolService::call('photos', 'perform_photo_upload', array($sTmpFile, $aFileInfo, false), 'Uploader');
            if (!$iRet)
                @unlink ($sTmpFile);
                
            return $iRet;
        }
        
        return $this->_aParam['photo'];
    }
}

?>
