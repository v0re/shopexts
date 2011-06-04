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
bx_import('BxTemplProfileView');
bx_import('BxTemplFormView');
bx_import('BxTemplSearchResult');
bx_import('BxDolAdminSettings');;

class BxProfileCustomizeTemplate extends BxDolModuleTemplate {
    var $_oModule;

    /**
     * Constructor
     */
    function BxProfileCustomizeTemplate(&$oConfig, &$oDb) {
        parent::BxDolModuleTemplate($oConfig, $oDb);

        if (isset($GLOBALS['oAdmTemplate']))
            $GLOBALS['oAdmTemplate']->addDynamicLocation($this->_oConfig->getHomePath(), $this->_oConfig->getHomeUrl());
    }
    
    /**
     * @depricated
     */
    function init(&$oModule, &$oDb)
    {
        $this->_oModule = $oModule;
        $this->_oDb = $oDb;
    }
    
    function profileCustomizeBlock($aTopMenu, $sPage, $aTargets, $sTarget, $aVars)
    {
        $sContent = '';
        $sBlockName = 'profile_customize';
        $aMenuItems = array();
        $sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri();
        
        $aItems = array();
        
        foreach ($aTopMenu as $sName => $aItem)
        {
            $aItems[] = array(                
                'title' => $sName,
                'active' => $aItem['active'],
                'href' => $aItem['href'],
                'onclick' => 'oCustomizer.reloadCustomizeBlock(this.href, false); return false;'
            );
        }
                
        $sTopMenu = BxDolPageView::getBlockCaptionMenu('profile_customizer', $aItems);
        
        $sTopMenu .= $this->parseHtmlByName('close_button.html', array(
                'close_img' => $this->getImageUrl('close.gif'),
                'space_img' => $this->getIconUrl('spacer.gif'),
                'name_id' => $sBlockName
            )
        );
        
        // content for box
        if ($sPage != 'themes')
        {
            $sBoxContent = $this->parseHtmlByName('content_box.html', array(
                'bx_if:select_target' => array(
                    'condition' => !empty($aTargets),
                    'content' => array(
                        'name_box' => _t('_bx_profile_customize_select_target'),
                        'name_targets_box' => 'background_box',
                        'bx_repeat:targets' => $aTargets
                    )
                ),
                'content' => call_user_func_array(array($this, '_customPage' . ucfirst($sPage)), array($sPage, $sTarget, $aVars)),
                'bx_repeat:buttons' => array(
                    array(
                        'btn_type' => 'button',
                        'btn_name' => 'save',
                        'btn_value' => _t('_bx_profile_customize_btn_save'),
                        'btn_action' => "oCustomizer.reloadCustom('" . $sBaseUrl . "customizepage/" . $sPage . '/' . $sTarget . "', '" . $sBaseUrl . "profileblock/save');"
                    ),
                    array(
                        'btn_type' => 'button',
                        'btn_name' => 'reset',
                        'btn_value' => _t('_bx_profile_customize_btn_reset'),
                        'btn_action' => "oCustomizer.resetCustom('" . $sBaseUrl . "customizepage/" . $sPage . '/' . $sTarget . "', '" . $sBaseUrl . "profileblock/preview');"
                    ),
                    array(
                        'btn_type' => 'button',
                        'btn_name' => 'preview',
                        'btn_value' => _t('_bx_profile_customize_btn_preview'),
                        'btn_action' => "oCustomizer.reloadCustom('" . $sBaseUrl . "customizepage/" . $sPage . '/' . $sTarget . "', '" . $sBaseUrl . "profileblock/preview');"
                    ),
                    array(
                        'btn_type' => 'button',
                        'btn_name' => 'publish',
                        'btn_value' => _t('_bx_profile_customize_btn_publish'),
                        'btn_action' => "oCustomizer.showPublish('{$sBaseUrl}publish');"
                    ),
                )
            ));
        }
        else
        {
            $iUserId = $sTarget == 'my' ? $this->_oModule->iUserId : 0;
            $sPageThemes = $this->_customPageThemes($iUserId);
            
            if ($sPageThemes)
                $aButtons = array(
                    array(
                        'btn_type' => 'button',
                        'btn_name' => 'save',
                        'btn_value' => _t('_bx_profile_customize_btn_save'),
                        'btn_action' => "oCustomizer.saveTheme();"
                    ),
                    array(
                        'btn_type' => 'button',
                        'btn_name' => 'preview',
                        'btn_value' => _t('_bx_profile_customize_btn_preview'),
                        'btn_action' => "oCustomizer.previewTheme();"
                    ),
                );
            else
                $aButtons = array();
                
            if ($sPageThemes && $sTarget != 'shared')
                $aButtons[] = array(
                        'btn_type' => 'button',
                        'btn_name' => 'delete',
                        'btn_value' => _t('_bx_profile_customize_btn_delete'),
                        'btn_action' => "oCustomizer.deleteTheme('" . $sBaseUrl . "deletetheme/');"
                );
            
            $aButtons[] = array(
                'btn_type' => 'button',
                'btn_name' => 'reset',
                'btn_value' => _t('_bx_profile_customize_btn_reset_all'),
                'btn_action' => "oCustomizer.resetAll('{$sBaseUrl}resetall');"
            );
            
            $sBoxContent = $this->parseHtmlByName('content_box.html', array(
                'bx_if:select_target' => array(
                    'condition' => !empty($aTargets),
                    'content' => array(
                        'name_box' => _t('_bx_profile_customize_select_target'),
                        'name_targets_box' => 'background_box',
                        'bx_repeat:targets' => $aTargets
                    )
                ),
                'content' => $sPageThemes ? $sPageThemes : MsgBox(_t('_Empty')),
                'bx_repeat:buttons' => $aButtons
            ));
        }
        
        // customize box
        $sContent = $this->parseHtmlByName('customize_block.html', array(
            'box_content' => $GLOBALS['oFunctions']->transBox(
                DesignBoxContent(_t('_bx_profile_customize'), $sBoxContent, 1, $sTopMenu), false
            ),
            'name_id' => $sBlockName
        ));
        
        $this->addCss(array('main.css'));
        $this->addJs(array('colorinput.js', 'main.js'));
        
        return $sContent;
    }
    
    function profilePage($iUserId, $sCss)
    {
        global $p_arr;
        
        $oProfile = new BxBaseProfileGenerator($iUserId);
        $oPPV = new BxTemplProfileView($oProfile, $site, $dir);
        
        $oProfile->oCmtsView->getExtraCss();
        $oProfile->oCmtsView->getExtraJs();
        $oProfile->oVotingView->getExtraJs();
        $p_arr = $oProfile->_aProfile;
        
        return $this->parseHtmlByName('profile_page.html', array(
            'page_main_css' => $sCss,
            'page_main_code' => $oPPV->getCode()
        ));
    }
    
    function getPublishForm($sComplete = '')
    {
        $sName = 'dynamicPopup';
        $sContent = '';
        
        if ($sComplete)
        {
            $sContent = $this->parseHtmlByName('confirm_box.html', array(
                'text' => $sComplete,
                'btn_value' => _t('_bx_profile_customize_btn_close'),
                'box_name' => $sName
            ));
        }
        else
        {
            $oForm = new BxTemplFormView(array(
            
                'form_attrs' => array(
                    'name'     => 'publish_form',
                    'action'   => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'publish/1',
                    'method'   => 'post',
                    'enctype' => 'multipart/form-data',
                ),      
    
                'params' => array (),
                      
                'inputs' => array(
                    'name_theme' => array(
                        'type' => 'text',
                        'name' => 'name_theme',
                        'value' => '',
                        'caption' => _t('_bx_profile_customize_name_theme'),
                        'display' => true,
                    ),
                    'thumbnail' => array(
                        'type' => 'file',
                        'name' => 'thumbnail',
                        'value' => '',
                        'caption' => _t('_bx_profile_customize_thumbnail'),
                        'display' => true,
                    ),   
                    'submit' => array (
                        'type' => 'button',
                        'name' => 'submit_form',
                        'value' => _t('_bx_profile_customize_btn_save'),
                        'colspan' => true,
                        'attrs' => array(
                            'onclick' => "oCustomizer.savePublish();"
                        ),
                    ),            
                )            
            ));
            
            $sContent = $oForm->getCode();
        }
        
        return PopupBox(
            $sName, _t('_bx_profile_customize_publish'), $sContent
        );
    }
    
    /**
     * Methods for admin-panel
     */
    
    function addCssAdmin ($sName) 
    {
        if (empty($GLOBALS['oAdmTemplate'])) 
            return;
                    
        $GLOBALS['oAdmTemplate']->addCss($sName);
    }

    function addJsAdmin ($sName) 
    {        
        if (empty($GLOBALS['oAdmTemplate'])) 
            return;
                    
        $GLOBALS['oAdmTemplate']->addJs($sName);
    }
    
    function adminBlock($sContent, $sTitle, $aMenu = array()) 
    {
        return DesignBoxAdmin($sTitle, $sContent, $aMenu);
    }

    function pageCodeAdmin($sTitle, $sType = '', $iUnitId = '', $sResult = '') 
    {
        global $_page;        
        global $_page_cont;

        $_page['name_index'] = 9; 

        $_page['header'] = $sTitle ? $sTitle : $GLOBALS['site']['title'];
        $_page['header_text'] = $sTitle;
        
        $_page_cont[$_page['name_index']]['page_main_code'] = ($sResult ? MsgBox($sResult) : '') . $this->getAdminPage($sType, $iUnitId);

        PageCodeAdmin();
    }
    
    function getAdminPage($sType = '', $iUnitId = '')
    {
        if (!$sType)
            $sType = 'background';
            
        if ($iUnitId)
            $sCaption = _t('_bx_profile_customize_form_edit');
        else
            $sCaption = _t('_bx_profile_customize_form_add');
            
        $sContent = $this->adminBlock($this->getAdminBlockForm($sType, $iUnitId), $sCaption);
        
        $aMenu = array();
        $aItems = array('background', 'font', 'border');
        if (in_array($sType, $aItems))
            $sSelType = $sType;
        else
            $sSelType = $aItems[0];
        foreach ($aItems as $sPageType)
        {
            $aMenu[$sPageType] = array(
                'title' => _t('_bx_profile_customize_page_' . $sPageType),
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/' . $sPageType,
                'active' => $sSelType == $sPageType ? 1 : 0
            );
        }
        $sContent .= $this->adminBlock($this->getAdminBlockUnits($sSelType), _t('_bx_profile_customize_units'), $aMenu);
        
        $aMenu = array();
        $aItems = array('themes', 'import');
        if (in_array($sType, $aItems))
            $sSelType = $sType;
        else
            $sSelType = $aItems[0];
        foreach ($aItems as $sPageType)
        {
            $aMenu[$sPageType] = array(
                'title' => _t('_bx_profile_customize_page_' . $sPageType),
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/' . $sPageType,
                'active' => $sSelType == $sPageType ? 1 : 0
            );
        }
        
        switch ($sSelType)
        {
            case 'themes':
                $sContent .= $this->adminBlock($this->getAdminPageThemes($sSelType), _t('_bx_profile_customize_page_themes'), $aMenu);
                break;
                
            case 'import':
                $sContent .= $this->adminBlock($this->getAdminPageImport($sSelType), _t('_bx_profile_customize_page_import'), $aMenu);
                break;
        }
        
        return $sContent;
    }
    
    function getAdminBlockForm($sType = '', $iUnitId = '')
    {
        if ($iUnitId)
        {
            $aUnit = $this->_oDb->getUnitById($iUnitId);
        }
        else
            $aUnit = array(
                'type' => $sType
            );
        
        $oForm = $this->_getCustomUnitForm($aUnit);
        $oForm->initChecker();
        
        if ($oForm->isSubmittedAndValid())
        {
            if ($iUnitId)
                $iRes = $oForm->update($iUnitId);
            else
                $iRes = $oForm->insert();
                
            if ($iRes)
                header('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/' . $sType);
        }
        
        return $GLOBALS['oAdmTemplate']->parseHtmlByName('design_box_content.html', array('content' => $oForm->getCode()));
    }
    
    function getAdminBlockUnits($sType)
    {
        // check delete
        if ($_POST['action_delete'] && is_array($_POST['entry'])) 
        {
            foreach ($_POST['entry'] as $iUnitId)
                $this->_oDb->deleteUnit($iUnitId);
        }
        
        $oMain = BxDolModule::getInstance('BxProfileCustomizeModule');
        bx_import ('SearchResult', $oMain->_aModule);
        $oSearch = new BxProfileCustomizeSearchResult($sType);
        $sUnits = $oSearch->displayResultBlock();
        if ($sUnits)
        {
            $sFormName = 'custom_units_form';
            $aButtons['action_delete'] = '_bx_profile_customize_btn_delete';
            $aPageTypes = array();
            
            foreach (array('background', 'font', 'border') as $sPageType)
            {
                $aPageTypes[] = array(
                    'value' => $sPageType,
                    'caption' => _t('_bx_profile_customize_page_' . $sPageType),
                    'selected' => $sType == $sPageType ? 'selected="selected"' : ''
                );
            }
            
            $sContent = $this->parseHtmlByName('admin_form_units.html', array(
                'form_name' => $sFormName,
                'action' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/' . $sType,
                'units' => $sUnits,
                'actions_panel' => $oSearch->showAdminActionsPanel($sFormName, $aButtons),
            ));
        }
        else
            $sContent = MsgBox(_t('_Empty'));
        
        return $sContent;
    }
    
    function getAdminPageThemes($sType)
    {
        $sPageThemes = $this->_customPageThemes(0);
        if ($sPageThemes)
        {
            $sFormName = 'custom_themes_form';
            $aButtons = array(
                'action_theme_export' => '_bx_profile_customize_btn_export',
                'action_theme_delete' => '_bx_profile_customize_btn_delete'
            );
            
            $sContent = $this->parseHtmlByName('admin_form_units.html', array(
                'form_name' => $sFormName,
                'action' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/' . $sType,
                'units' => $sPageThemes,
                'actions_panel' => BxTemplSearchResult::showAdminActionsPanel($sFormName, $aButtons, 'entry', false),
            ));
        }
        else
            $sContent = MsgBox(_t('_Empty'));
        
        return $sContent;
    }
    
    function getAdminPageImport($sType)
    {
        $sResult = '';
        $sFile = 'theme_file';
        $oForm = $this->_getImportForm($sType);
        
        return $sResult . $GLOBALS['oAdmTemplate']->parseHtmlByName('design_box_content.html', array('content' => $oForm->getCode()));
    }
    
    /**
     * Internal methods
     */
    
    function _getImportForm($sType)
    {
        return new BxTemplFormView(array(
        
            'form_attrs' => array(
                'name'     => 'publish_form',
                'action'   => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/' . $sType,
                'method'   => 'post',
                'enctype' => 'multipart/form-data',
            ),      

            'inputs' => array(
                'theme_file' => array(
                    'type' => 'file',
                    'name' => 'theme_file',
                    'value' => '',
                    'caption' => _t('_bx_profile_customize_theme'),
                    'display' => true,
                ),
                'submit' => array (
                    'type' => 'submit',
                    'name' => 'submit_import',
                    'value' => _t('_bx_profile_customize_btn_import'),
                    'colspan' => true,
                ),            
            )            
        ));
    }
    
    function _getCustomUnitForm($aUnit = array())
    {
        $aForm = array(
        
            'form_attrs' => array(
                'name'     => 'unit_form',
                'action'   => $_SERVER['REQUEST_URI'],
                'method'   => 'post',
                'enctype' => 'multipart/form-data',
            ),      

            'params' => array (
                'db' => array(
                    'table' => 'bx_profile_custom_units',
                    'key' => 'id',
                    'submit_name' => 'submit_save',
                ),
            ),
                  
            'inputs' => array(
                
                'name' => array(
                    'type' => 'text',
                    'name' => 'name',
                    'value' => isset($aUnit['name']) ? $aUnit['name'] : '',
                    'caption' => _t('_bx_profile_customize_name'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,100),
                        'error' => _t('_bx_profile_customize_form_field_err'),
                    ),
                    'db' => array(
                        'pass' => 'Xss'
                    ),
                    'display' => true,
                ),
                'caption' => array(
                    'type' => 'text',
                    'name' => 'caption',
                    'value' => isset($aUnit['caption']) ? $aUnit['caption'] : '',
                    'caption' => _t('_bx_profile_customize_caption'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,100),
                        'error' => _t('_bx_profile_customize_form_field_err'),
                    ),
                    'db' => array(
                        'pass' => 'Xss'
                    ),
                    'display' => true,
                ),
                'css_name' => array(
                    'type' => 'text',
                    'name' => 'css_name',
                    'value' => isset($aUnit['css_name']) ? $aUnit['css_name'] : '',
                    'caption' => _t('_bx_profile_customize_css_name'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,500),
                        'error' => _t('_bx_profile_customize_form_field_err'),
                    ),
                    'db' => array(
                        'pass' => 'Xss'
                    ),
                    'display' => true,
                ),
                'type' => array(
                    'type' => 'select',
                    'name' => 'type',
                    'required' => true,
                    'values' => array(
                            'background' => _t('_bx_profile_customize_page_background'),
                            'font' => _t('_bx_profile_customize_page_font'),
                            'border' => _t('_bx_profile_customize_page_border'),
                        ),
                    'value' => isset($aUnit['type']) ? $aUnit['type'] : '',
                    'caption' => _t('_bx_profile_customize_type'),
                    'attrs' => array(
                            'multiplyable' => false
                        ),
                    'display' => true,
                    'db' => array(
                        'pass' => 'Xss'
                    ),
                ), 
                'submit' => array (
                    'type' => 'submit',
                    'name' => 'submit_save',
                    'value' => isset($aUnit['id']) ? _t('_bx_profile_customize_btn_save') : _t('_bx_profile_customize_btn_add'),
                    'colspan' => true,
                ),            
            )                
        );
        
        return new BxTemplFormView($aForm);
    }
    
    function _customPageThemes($iUserId = 0)
    {
        $aItems = array();
        $aThemes = $this->_oDb->getAllThemesByUserId($iUserId);
        
        if (!empty($aThemes))
        {
            foreach ($aThemes as $aTheme)
            {
                $sFileName = BX_PROFILE_CUSTOMIZE_THEME_PREFIX . $aTheme['id'] . BX_PROFILE_CUSTOMIZE_THUMB_EXT;
                if (file_exists($this->_oModule->_getImagesDir() . $sFileName))
                    $sThumb = $this->_oModule->_getImagesPath() . $sFileName;
                else
                    $sThumb = $this->getIconUrl('no-photo-64.png');
                    
                $aItems[] = array(
                    'id' => $aTheme['id'],
                    'name' => $aTheme['name'],
                    'thumbnail' => $sThumb,
                    'spacer' => $this->getImageUrl('spacer.gif')
                );
            }
            
            $aParam = array(
                'bx_repeat:items' => $aItems,
                'form_name' => 'themes_form',
                'preview_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'profileblock/theme/',
                'save_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'savetheme/'
            );
            
            return $this->parseHtmlByName('themes_box.html', $aParam);
        }
        
        return '';
    }
    
    function _customPageBackground($sPage, $sTarget, $aVars)
    {
        $aForm = array(
        
            'form_attrs' => array(
                'name'     => 'background_form',
                'action'   => $sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'save',
                'method'   => 'POST',
                'enctype' => 'multipart/form-data',
                'id' => 'custom_themes_form',
            ),      

            'params' => array (),
                  
            'inputs' => array(
                
                'color' => array(
                    'type' => 'text',
                    'name' => 'color',
                    'value' => isset($aVars['color']) ? $aVars['color'] : '',
                    'caption' => _t('_bx_profile_customize_color'),
                    'display' => true,
                ),
                'useimage' => array(
                    'type' => 'custom',
                    'name' => 'useimage',
                    'caption' => _t('_bx_profile_customize_use_image'),
                    'content' => ''
                ),
                'image' => array(
                    'type' => 'file',
                    'name' => 'image',
                    'value' => isset($aVars['image']) ? $aVars['image'] : '',
                    'caption' => _t('_bx_profile_customize_image'),
                    'display' => true,
                ),                
                'repeat' => array(
                    'type' => 'select',
                    'name' => 'repeat',
                    'values' => array(
                            'default' => _t('_bx_profile_customize_default'),
                            'no-repeat' => _t('_bx_profile_customize_no'),
                            'repeat' => _t('_bx_profile_customize_repeat'),
                            'repeat-x' => _t('_bx_profile_customize_repeat_x'),
                            'repeat-y' => _t('_bx_profile_customize_repeat_y'),
                        ),
                    'value' => isset($aVars['repeat']) ? $aVars['repeat'] : '',
                    'caption' => _t('_bx_profile_customize_repeat'),
                    'attrs' => array(
                            'multiplyable' => false
                        ),
                    'display' => true,
                ),                
                'position' => array(
                    'type' => 'select',
                    'name' => 'position',
                    'values' => array(
                        'default' => _t('_bx_profile_customize_default'),
                        'left top' => _t('_bx_profile_customize_top_left'),
                        'center top' => _t('_bx_profile_customize_top_center'),
                        'right top' => _t('_bx_profile_customize_top_right'),
                        'left center' => _t('_bx_profile_customize_center_left'),
                        'center center' => _t('_bx_profile_customize_center'),
                        'right center' => _t('_bx_profile_customize_center_right'),
                        'left bottom' => _t('_bx_profile_customize_bottom_left'),
                        'center bottom' => _t('_bx_profile_customize_bottom_center'),
                        'right bottom' => _t('_bx_profile_customize_bottom_right')
                    ),
                    'value' => isset($aVars['position']) ? $aVars['position'] : 'default',
                    'caption' => _t('_bx_profile_customize_position'),
                    'attrs' => array(
                            'multiplyable' => false
                        ),
                    'display' => true,
                ),
                'page' => array(
                    'type' => 'hidden',
                    'name' => 'page',
                    'value' => $sPage,
                ),                
                'trg' => array(
                    'type' => 'hidden',
                    'name' => 'trg',
                    'value' => $sTarget,
                ),
            )                
        );
        
        if (isset($aVars['image']) && 
            file_exists($this->_oModule->_getImagesDir() . BX_PROFILE_CUSTOMIZE_SMALL_PREFIX . $aVars['image']))
        {
            $aForm['inputs']['useimage']['content'] = $this->parseHtmlByName('thumb.html', array(
                'thumbnail' => $this->_oModule->_getImagesPath() . BX_PROFILE_CUSTOMIZE_SMALL_PREFIX . $aVars['image'],
                'spacer' => $this->getImageUrl('spacer.gif'),
                'name' => 'useimage',
                'checked' => isset($aVars['useimage']) ? 'checked="1"' : '',
            
            ));
            $aForm['inputs']['image']['caption'] = _t('_bx_profile_customize_other_image');
        }
        else
            unset($aForm['inputs']['useimage']);
            
        $oForm = new BxTemplFormView($aForm);
        
        return $oForm->getCode();
    }
    
    function _customPageFont($sPage, $sTarget, $aVars)
    {
        $oForm = new BxTemplFormView(array(
        
            'form_attrs' => array(
                'name'     => 'fonts_form',
                'action'   => $sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'save',
                'method'   => 'POST',
                'enctype' => 'multipart/form-data',
            ),      

            'params' => array (),
                  
            'inputs' => array(
                'size' => array(
                    'type' => 'select',
                    'name' => 'size',
                    'values' => array(
                            -1 => _t('_bx_profile_customize_default'),
                            8 => '8',
                            9 => '9',
                            10 => '10',
                            11 => '11',
                            12 => '12',
                            14 => '14',
                            16 => '16',
                            18 => '18',
                            20 => '20',
                            22 => '22',
                            24 => '24',
                        ),
                    'value' => isset($aVars['size']) ? (int)$aVars['size'] : -1,
                    'caption' => _t('_bx_profile_customize_size'),
                    'attrs' => array(
                            'multiplyable' => false
                        ),
                    'display' => true,
                ),                
                'color' => array(
                    'type' => 'text',
                    'name' => 'color',
                    'value' => isset($aVars['color']) ? $aVars['color'] : '',
                    'caption' => _t('_bx_profile_customize_color'),
                    'display' => true,
                ),
                'name' => array(
                    'type' => 'select',
                    'name' => 'name',
                    'values' => array(
                            'inherit' => _t('_bx_profile_customize_default'),
                            'Arial, Helvetica, sans-serif' => 'Arial',
                            'comicsans' => 'Comic Sans',
                            'Courier New, Courier, monospace' => 'Courier New',
                            'Georgia, lucida grande, Times New Roman, Times, serif' => 'Georgia',
                            'Tahoma, Verdana, Arial, Helvetica, sans-serif' => 'Tahoma',
                            'Times New Roman, Times, serif' => 'Times Roman',
                            'Trebuchet, Trebuchet MS, Helvetica, sans-serif' => 'Trebuchet',
                            'Verdana, Arial, Helvetica, sans-serif' => 'Verdana'
                        ),
                    'value' => isset($aVars['name']) ? $aVars['name'] : '',
                    'caption' => _t('_bx_profile_customize_name'),
                    'attrs' => array(
                            'multiplyable' => false
                        ),
                    'display' => true,
                ),  
                'style' => array(
                    'type' => 'select',
                    'name' => 'style',
                    'values' => array(
                            'default' => _t('_bx_profile_customize_default'),
                            'normal' => _t('_bx_profile_customize_normal'),
                            'bold' => _t('_bx_profile_customize_bold'),
                            'italic' => _t('_bx_profile_customize_italic')
                        ),
                    'value' => isset($aVars['style']) ? $aVars['style'] : 'default',
                    'caption' => _t('_bx_profile_customize_style'),
                    'attrs' => array(
                            'multiplyable' => false
                        ),
                    'display' => true,
                ),   
                'page' => array(
                    'type' => 'hidden',
                    'name' => 'page',
                    'value' => $sPage,
                ),                
                'trg' => array(
                    'type' => 'hidden',
                    'name' => 'trg',
                    'value' => $sTarget,
                )                
            )            
        ));
        
        return $oForm->getCode();
    }
    
    function _customPageBorder($sPage, $sTarget, $aVars)
    {
        $oForm = new BxTemplFormView(array(
        
            'form_attrs' => array(
                'name'     => 'border_form',
                'action'   => $sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'save',
                'method'   => 'POST',
                'enctype' => 'multipart/form-data',
            ),      

            'params' => array (),
                  
            'inputs' => array(
            
                'size' => array(
                    'type' => 'select',
                    'name' => 'size',
                    'values' => array(
                            -1 => _t('_bx_profile_customize_default'),
                            0 => _t('_bx_profile_customize_none'),
                            1 => '1',
                            2 => '2',
                            3 => '3',
                            4 => '4',
                            5 => '5',
                            6 => '6',
                            7 => '7',
                            8 => '8',
                            9 => '9',
                            10 => '10',
                        ),
                    'value' => isset($aVars['size']) ? (int)$aVars['size'] : -1,
                    'caption' => _t('_bx_profile_customize_size'),
                    'attrs' => array(
                            'multiplyable' => false
                        ),
                    'display' => true,
                ),                
                'color' => array(
                    'type' => 'text',
                    'name' => 'color',
                    'value' => isset($aVars['color']) ? $aVars['color'] : '',
                    'caption' => _t('_bx_profile_customize_color'),
                    'display' => true,
                ),
                'style' => array(
                    'type' => 'select',
                    'name' => 'style',
                    'values' => array(
                            'default' => _t('_bx_profile_customize_default'),
                            'dotted' => _t('_bx_profile_customize_dotted'),
                            'dashed' => _t('_bx_profile_customize_dashed'),
                            'solid' => _t('_bx_profile_customize_solid'),
                            'double' => _t('_bx_profile_customize_double'),
                            'groove' => _t('_bx_profile_customize_groove'),
                            'ridge' => _t('_bx_profile_customize_ridge'),
                            'inset' => _t('_bx_profile_customize_inset'),
                            'outset' => _t('_bx_profile_customize_outset'),
                        ),
                    'value' => isset($aVars['style']) ? $aVars['style'] : 'default',
                    'caption' => _t('_bx_profile_customize_style'),
                    'attrs' => array(
                            'multiplyable' => false
                        ),
                    'display' => true,
                ),     
                'position' => array(
                    'type' => 'select',
                    'name' => 'position',
                    'values' => array(
                        'default' => _t('_bx_profile_customize_default'),
                        'full' => _t('_bx_profile_customize_border_full'),
                        'top' => _t('_bx_profile_customize_border_top'),
                        'right' => _t('_bx_profile_customize_border_right'),
                        'bottom' => _t('_bx_profile_customize_border_bottom'),
                        'left' => _t('_bx_profile_customize_border_left'),
                        'left_right' => _t('_bx_profile_customize_border_left_right'),
                        'top_bottom' => _t('_bx_profile_customize_border_top_bottom'),
                        'top_right' => _t('_bx_profile_customize_border_top_right'),
                        'right_bottom' => _t('_bx_profile_customize_border_right_bottom'),
                        'bottom_left' => _t('_bx_profile_customize_border_bottom_left'),
                        'left_top' => _t('_bx_profile_customize_border_left_top'),
                    ),
                    'value' => isset($aVars['position']) ? $aVars['position'] : 'default',
                    'caption' => _t('_bx_profile_customize_position'),
                    'attrs' => array(
                            'multiplyable' => false
                        ),
                    'display' => true,
                ),
                'page' => array(
                    'type' => 'hidden',
                    'name' => 'page',
                    'value' => $sPage,
                ),                
                'trg' => array(
                    'type' => 'hidden',
                    'name' => 'trg',
                    'value' => $sTarget,
                ),                
            )            
        ));
        
        return $oForm->getCode();
    }
    
    
}
?>
