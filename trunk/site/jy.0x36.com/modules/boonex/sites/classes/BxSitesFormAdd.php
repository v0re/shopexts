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

require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolProfileFields.php');

class BxSitesFormAdd extends BxTemplFormView {
    
    var $_oModule;
    var $_aParam;
    var $_aCustomForm;

    function BxSitesFormAdd($oModule, $aParam = array()) 
    {
        $this->_oModule = $oModule;
        $this->_aParam = $aParam;
        
        $this->_aCustomForm = count($this->_aParam) > 0 ? $this->getFullForm() : $this->getUrlForm();
        
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
    
    function getUrlForm()
    {
        return array(

            'form_attrs' => array(
                'name'     => 'form_site',
                'action'   => BX_DOL_URL_ROOT . $this->_oModule->_oConfig->getBaseUri() . 'browse/my/add',
                'method'   => 'post',
                'enctype' => 'multipart/form-data',
            ),      

            'params' => array (
                'db' => array(
                    'table' => 'bx_sites_main',
                    'key' => 'id',
                    'submit_name' => 'submit_form',
                ),
            ),
                  
            'inputs' => array(
                'url' => array(
                    'type' => 'text',
                    'name' => 'url',
                    'caption' => _t('_bx_sites_form_url'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,100),
                        'error' => _t('_bx_sites_form_field_err'),
                    ),
                    'db' => array(
                        'pass' => 'Xss'
                    ),
                    'display' => true,
                )                
            )            
        );
    }
    
    function getFullForm()
    {
        bx_import('BxDolCategories');        
        $oCategories = new BxDolCategories();
        $oCategories->getTagObjectConfig();
        
        $aForm = array(
            'form_attrs' => array(
                'name'     => 'form_site',
                'action'   => BX_DOL_URL_ROOT . $this->_oModule->_oConfig->getBaseUri() . 'browse/my/add',
                'method'   => 'post',
                'enctype' => 'multipart/form-data',
            ),      

            'params' => array (
                'db' => array(
                    'table' => 'bx_sites_main',
                    'key' => 'id',
                    'uri' => 'entryUri',
                    'uri_title' => 'title',
                    'submit_name' => 'submit_form',
                ),
            ),
                  
            'inputs' => array(
                'url' => array(
                    'type' => 'text',
                    'name' => 'url',
                    'value' => isset($this->_aParam['url']) ? $this->_aParam['url'] : '',
                    'caption' => _t('_bx_sites_form_url'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,100),
                        'error' => _t('_bx_sites_form_field_err'),
                    ),
                    'db' => array(
                        'pass' => 'Xss'
                    ),
                    'display' => true,
                ),                
                'title' => array(
                    'type' => 'text',
                    'name' => 'title',
                    'value' => isset($this->_aParam['title']) ? $this->_aParam['title'] : '',
                    'caption' => _t('_bx_sites_form_title'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,100),
                        'error' => _t('_bx_sites_form_field_err'),
                    ),
                    'db' => array(
                        'pass' => 'Xss'
                    ),
                    'display' => true,
                ),                
                'description' => array(
                    'type' => 'textarea',
                    'name' => 'description',
                    'value' => isset($this->_aParam['description']) ? $this->_aParam['description'] : '',
                    'caption' => _t('_bx_sites_form_description'),
                    'required' => true,
                    'html' => 1,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,64000),
                        'error' => _t('_bx_sites_form_field_err'),
                    ),
                    'db' => array(
                        'pass' => 'XssHtml'
                    )
                ),
                'thumbnail' => array(
                    'type' => 'custom',
                    'name' => 'thumbnail',
                    'content' => '',
                    'caption' => 'Thumbnail'
                ),
                'photo' => array(
                    'type' => 'file',
                    'name' => 'photo',
                    'caption' => _t('_bx_sites_form_photo'),
                ),                
                'tags' => array(
                    'type' => 'text',
                    'name' => 'tags',
                    'caption' => _t('_Tags'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t('_bx_sites_form_field_err'),
                    ),
                    'db' => array (
                        'pass' => 'Tags', 
                    ),
                    'info' => _t('_sys_tags_note')
                ),                
                'categories' => $oCategories->getGroupChooser('bx_sites', (int)$this->_oModule->iOwnerId, true),
                'allowView' => $this->_oModule->oPrivacy->getGroupChooser($this->_oModule->iOwnerId,
                    'bx_sites', 'view', array(), _t('_bx_sites_caption_allow_view')),
                'allowComments' => $this->_oModule->oPrivacy->getGroupChooser($this->_oModule->iOwnerId,
                    'bx_sites', 'comments', array(), _t('_bx_sites_caption_allow_comments')),
                'allowRate' => $this->_oModule->oPrivacy->getGroupChooser($this->_oModule->iOwnerId,
                    'bx_sites', 'rate', array(), _t('_bx_sites_caption_allow_rate')),
            ),            
        );
        
        if (isset($this->_aParam['thumbnail']))
        {
            $aForm['inputs']['thumbnail']['content'] = $this->_aParam['thumbnail'];
            $aForm['inputs']['photo']['caption'] = _t('_bx_sites_form_other_thumbnail');
            
            if (isset($this->_aParam['thumbnail_url']))
            {
                $aForm['inputs'] = array_merge($aForm['inputs'], array(
                    'thumbnail_url' => array (
                        'type' => 'hidden',
                        'name' => 'thumbnail_url',
                        'value' => $this->_aParam['thumbnail_url'],
                    )
                ));
            }
        }
        else
            unset($aForm['inputs']['thumbnail']);
        
        return $aForm;
    }
    
    function uploadPhoto ($sPath, $isRemote = false)  
    {
        $aFileInfo = array (
            'medTitle' => stripslashes($this->getCleanValue('title')),
            'medDesc' => stripslashes($this->getCleanValue('title')),
            'medTags' => 'sites',
            'Categories' => array('Sites'),
        );
        $sTmpFile = BX_DIRECTORY_PATH_ROOT . 'tmp/' . time() . (isset($_COOKIE['memberID']) ? $_COOKIE['memberID'] : '');
        $bResult = $isRemote ? copy($sPath,  $sTmpFile) : move_uploaded_file($_FILES['photo']['tmp_name'],  $sTmpFile);
        if ($bResult) {
            $iRet = BxDolService::call('photos', 'perform_photo_upload', array($sTmpFile, $aFileInfo, false), 'Uploader');
            if (!$iRet)
                @unlink ($sTmpFile);
            else
                return $iRet;
        }
        
        return 0;
    }
}

?>
