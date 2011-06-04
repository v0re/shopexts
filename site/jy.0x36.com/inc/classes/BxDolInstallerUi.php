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

bx_import('BxDolDb');
bx_import('BxDolModuleDb');
bx_import('BxTemplSearchResult');

class BxDolInstallerUi extends BxDolDb {
    var $_sDefVersion;
    var $_aCheckPathes;
    
    function BxDolInstallerUi() {
        parent::BxDolDb();
        
        $this->_sDefVersion = '0.0.0';
        $this->_aCheckPathes = array();
    }
    function getUploader($sResult) {
        $aForm = array(
            'form_attrs' => array(
                'id' => 'module_upload_form',
                'action' => bx_html_attribute($_SERVER['PHP_SELF']),
                'method' => 'post',
                'enctype' => 'multipart/form-data',
            ),
            'inputs' => array (
                'header1' => array(
                    'type' => 'block_header',
                    'caption' => _t('_adm_txt_modules_package_to_upload'),
                ),
                'module' => array(
                    'type' => 'file',
                    'name' => 'module',
                    'caption' => _t('_adm_txt_modules_module'),
                ),
                'update' => array(
                    'type' => 'file',
                    'name' => 'update',
                    'caption' => _t('_adm_btn_modules_update'),
                ),
                'header2' => array(
                    'type' => 'block_header',
                    'caption' => _t('_adm_txt_modules_ftp_access'),                    
                ),
                'login' => array(
                    'type' => 'text',
                    'name' => 'login',
                    'caption' => _t('_adm_txt_modules_login'),
                    'value' => getParam('sys_ftp_login')
                ),
                'password' => array(
                    'type' => 'password',
                    'name' => 'password',
                    'caption' => _t('_Password'),
                    'value' => getParam('sys_ftp_password')
                ),
                'path' => array(
                    'type' => 'text',
                    'name' => 'path',
                    'caption' => _t('_adm_txt_modules_path_to_dolphin'),
                    'value' => !($sPath = getParam('sys_ftp_dir')) ? 'public_html/' : $sPath
                ),
                'submit_upload' => array(
                    'type' => 'submit',
                    'name' => 'submit_upload',
                    'value' => _t('_adm_box_cpt_upload'),
                )
            )
        );
        $oForm = new BxBaseFormView($aForm);
        $sContent = $oForm->getCode();
        
        if(!empty($sResult))
            $sContent = MsgBox(_t($sResult), 3) . $sContent;
        
        return $GLOBALS['oAdmTemplate']->parseHtmlByName('modules_uploader.html', array(
            'content' => $sContent
        ));
    }
    function getInstalled() {
        //--- Get Items ---//
        $oModules = new BxDolModuleDb();
        $aModules = $oModules->getModules();

        $aItems = array();
        foreach($aModules as $aModule) {
            $bNeedCheck = in_array($aModule['path'], $this->_aCheckPathes);
            $aCheckInfo = $bNeedCheck ? BxDolInstallerUi::checkForUpdates($aModule) : array();

            $aItems[] = array(
                'name' => $aModule['uri'],
                'value' => $aModule['path'],
                'title'=> _t('_adm_txt_modules_title_module', $aModule['title'], !empty($aModule['version']) ? $aModule['version'] : $this->_sDefVersion, $aModule['vendor']),
                'bx_if:update' => array(
                    'condition' => $bNeedCheck && !empty($aCheckInfo),
                    'content' => array(
                        'link' => empty($aCheckInfo['link']) ? '' : $aCheckInfo['link'],
                        'text' => _t('_adm_txt_modules_update_text', 
                        	empty($aCheckInfo['version']) ? '' : $aCheckInfo['version'])
                    )
                ),
                'bx_if:latest' => array(
                    'condition' => $bNeedCheck && empty($aCheckInfo),
                    'content' => array()
                )
            );
        }
        //--- Get Controls ---//
        $aButtons = array(
            'modules-update' => _t('_adm_btn_modules_update'),
            'modules-uninstall' => _t('_adm_btn_modules_uninstall'),
            'modules-recompile-languages' => _t('_adm_btn_modules_recompile_languages')
        );
        $sControls = BxTemplSearchResult::showAdminActionsPanel('modules-installed-form', $aButtons, 'pathes');
        
        return $GLOBALS['oAdmTemplate']->parseHtmlByName('modules_list.html', array(
            'type' => 'installed',
            'bx_repeat:items' => $aItems,
            'controls' => $sControls
        ));
    }
    function getNotInstalled($sResult) {
        //--- Get Items ---//
        $oModules = new BxDolModuleDb();
        $aModules = $oModules->getModules();
        
        $aInstalled = array();
        foreach($aModules as $aModule)
            $aInstalled[] = $aModule['path'];

        $aNotInstalled = array();
        $sPath = BX_DIRECTORY_PATH_ROOT . 'modules/';
        if($rHandleVendor = opendir($sPath)) {

            while(($sVendor = readdir($rHandleVendor)) !== false) {
                if(substr($sVendor, 0, 1) == '.' || !is_dir($sPath . $sVendor)) continue;
                
                if($rHandleModule = opendir($sPath . $sVendor)) {
                    while(($sModule = readdir($rHandleModule)) !== false) {
						if(!is_dir($sPath . $sVendor . '/' . $sModule) || substr($sModule, 0, 1) == '.' || in_array($sVendor . '/' . $sModule . '/', $aInstalled)) 
                            continue;

                        $sConfigPath = $sPath . $sVendor . '/' . $sModule . '/install/config.php';
                        if(!file_exists($sConfigPath)) continue;

                        include($sConfigPath);
                        $aNotInstalled[$aConfig['title']] = array(
                            'name' => $aConfig['home_uri'], 
                            'value' => $aConfig['home_dir'], 
                            'title' => _t('_adm_txt_modules_title_module', $aConfig['title'], !empty($aConfig['version']) ? $aConfig['version'] : $this->_sDefVersion, $aConfig['vendor']),
                            'bx_if:update' => array(
                                'condition' => false,
                                'content' => array()
                            ),
                            'bx_if:latest' => array(
                                'condition' => false,
                                'content' => array()
                            )
                        );
                    }
                    closedir($rHandleModule);
                }                
            }
            closedir($rHandleVendor); 
        }        
        ksort($aNotInstalled);
        
        //--- Get Controls ---//
        $aButtons = array(
            'modules-install' => _t('_adm_btn_modules_install'),
            'modules-delete' => _t('_adm_btn_modules_delete')
        );
        $sControls = BxTemplSearchResult::showAdminActionsPanel('modules-not-installed-form', $aButtons, 'pathes');
        
        if(!empty($sResult))
            $sResult = MsgBox(_t($sResult), 3);

        return $sResult . $GLOBALS['oAdmTemplate']->parseHtmlByName('modules_list.html', array(
            'type' => 'not-installed',
            'bx_repeat:items' => $aNotInstalled,
            'controls' => $sControls
        ));
    }
    function getUpdates($sResult) {
        $aUpdates = array();
        $sPath = BX_DIRECTORY_PATH_ROOT . 'modules/';
        if($rHandleVendor = opendir($sPath)) {
            while(($sVendor = readdir($rHandleVendor)) !== false) {
                if(substr($sVendor, 0, 1) == '.' || !is_dir($sPath . $sVendor)) 
                    continue;

                if($rHandleModule = opendir($sPath . $sVendor . '/')) {
                    while(($sModule = readdir($rHandleModule)) !== false) {
						if(!is_dir($sPath . $sVendor . '/' . $sModule) || substr($sModule, 0, 1) == '.') 
                            continue;
                            
                        if($rHandleUpdate = @opendir($sPath . $sVendor . '/' . $sModule . '/updates/')) {
                            while(($sUpdate = readdir($rHandleUpdate)) !== false) {
        						if(!is_dir($sPath . $sVendor . '/' . $sModule . '/updates/' . $sUpdate) || substr($sUpdate, 0, 1) == '.')
                                    continue;
                        
                                $sConfigPath = $sPath . $sVendor . '/' . $sModule . '/updates/' . $sUpdate . '/install/config.php';
                                if(!file_exists($sConfigPath)) 
                                    continue;
        
                                include($sConfigPath);
                                $sName = $aConfig['title'] . $aConfig['module_uri'] . $aConfig['version_from'] . $aConfig['version_to'];
                                $aUpdates[$sName] = array(
                                    'name' => md5($sName),
                                    'value' => $aConfig['home_dir'],
                                    'title' => _t('_adm_txt_modules_title_update', $aConfig['title'], $aConfig['version_from'], $aConfig['version_to']),
                                    'bx_if:update' => array(
                                        'condition' => false,
                                        'content' => array()
                                    ),
                                    'bx_if:latest' => array(
                                        'condition' => false,
                                        'content' => array()
                                    )
                                );
                            }
                            closedir($rHandleUpdate);
                        }
                    }
                    closedir($rHandleModule);
                }                
            }
            closedir($rHandleVendor); 
        }
        ksort($aUpdates);
        
        //--- Get Controls ---//
        $aButtons = array(
            'updates-install' => _t('_adm_btn_modules_install'),
            'updates-delete' => _t('_adm_btn_modules_delete')
        );
        $sControls = BxTemplSearchResult::showAdminActionsPanel('modules-updates-form', $aButtons, 'pathes');

        if(!empty($sResult))
            $sResult = MsgBox(_t($sResult), 3);

        return $sResult . $GLOBALS['oAdmTemplate']->parseHtmlByName('modules_list.html', array(
            'type' => 'updates',            
            'bx_repeat:items' => !empty($aUpdates) ? $aUpdates : MsgBox(_t('_Empty')),
            'controls' => $sControls
        ));
    }
    
    //--- Get/Set methods ---//
    function setCheckPathes($aPathes) {        
        $this->_aCheckPathes = is_array($aPathes) ? $aPathes : array();
    }
    
    //--- Actions ---//
    function actionUpload($sType, $aFile, $aFtpInfo) {
        $sLogin = htmlspecialchars_adv(clear_xss($aFtpInfo['login']));
        $sPassword = htmlspecialchars_adv(clear_xss($aFtpInfo['password']));
        $sPath = htmlspecialchars_adv(clear_xss($aFtpInfo['path']));
        
        setParam('sys_ftp_login', $sLogin);
        setParam('sys_ftp_password', $sPassword);
        setParam('sys_ftp_dir', $sPath);
    
        $sResult = '_adm_txt_modules_success_upload';
        
        $sName = mktime();
        $sAbsolutePath = BX_DIRECTORY_PATH_ROOT . "tmp/" . $sName . '.zip';
        if($this->_isArchive($aFile['type']) && move_uploaded_file($aFile['tmp_name'], $sAbsolutePath)) {
            exec('unzip ' . $sAbsolutePath . ' -d ' . BX_DIRECTORY_PATH_ROOT . 'tmp/', $aOutput);

            $aMatches = array();        
            if(isset($aOutput[1])) {
                preg_match("/^.*(" . str_replace('/', '\/', BX_DIRECTORY_PATH_ROOT) . "tmp\/)([\w-]*)/", $aOutput[1], $aMatches);
                $sDirectory = $aMatches[2];

                $oFtp = new BxDolFtp($_SERVER['HTTP_HOST'], $sLogin, $sPassword, $sPath);
                if($oFtp->connect() != false) {
                    $sConfigPath = BX_DIRECTORY_PATH_ROOT . "tmp/" . $sDirectory . '/install/config.php';
                    if(file_exists($sConfigPath)) {
                        include($sConfigPath);
                        $oFtp->copy(BX_DIRECTORY_PATH_ROOT . "tmp/" . $sDirectory . '/', 'modules/' . $aConfig['home_dir']);
                    }
                    else
                        $sResult = '_adm_txt_modules_wrong_package_format';
                }
                else 
                    $sResult = '_adm_txt_modules_cannot_connect_to_ftp';

                exec('rm -rf ' . BX_DIRECTORY_PATH_ROOT . 'tmp/' . $sDirectory . '/');
                exec('rm -f ' . $sAbsolutePath);
            }
            else 
                $sResult = '_adm_txt_modules_cannot_unzip_package';
        }
        else 
            $sResult = '_adm_txt_modules_cannot_upload_package';

        return $sResult;
    }
    function actionInstall($aDirectories) {
        return $this->_perform($aDirectories, 'install');
    }
    function actionUninstall($aDirectories) {
        return $this->_perform($aDirectories, 'uninstall');
    }
    function actionRecompile($aDirectories) {
        return $this->_perform($aDirectories, 'recompile');
    }
    function actionUpdate($aDirectories) {
        return $this->_perform($aDirectories, 'update');
    }
    function actionDelete($aDirectories) {
        $oFtp = new BxDolFtp($_SERVER['HTTP_HOST'], getParam('sys_ftp_login'), getParam('sys_ftp_password'), getParam('sys_ftp_dir'));
        if(!$oFtp->connect()) 
            return '_adm_txt_modules_cannot_connect_to_ftp';
            
        foreach($aDirectories as $sDirectory)
            if(!$oFtp->delete('modules/' . $sDirectory))
                return '_adm_txt_modules_cannot_remove_package';
                
        return '_adm_txt_modules_success_delete';
    }
    
    //--- Static methods ---//
    function checkForUpdates($aModule) {
        $sData = bx_file_get_contents($aModule['update_url'], array(
            'uri' => $aModule['uri'], 
            'path' => $aModule['path'], 
            'version' => $aModule['version'],
            'domain' => $_SERVER['HTTP_HOST']
        ));
        
        $aValues = $aIndexes = array();
        $rParser = xml_parser_create('UTF-8');
        xml_parse_into_struct($rParser, $sData, $aValues, $aIndexes);
        xml_parser_free($rParser);
        
        $aInfo = array();
        if(isset($aIndexes['VERSION']) && isset($aIndexes['LINK'])) {
            $aInfo['version'] = $aValues[$aIndexes['VERSION'][0]]['value'];
            $aInfo['link'] = $aValues[$aIndexes['LINK'][0]]['value'];
        }
        
        return $aInfo;
    }

    //--- Protected methods ---//
    function _perform($aDirectories, $sOperation, $aParams = array()) {
        $sConfigFile = 'install/config.php';
        $sInstallerFile = 'install/installer.php';
        $sInstallerClass = $sOperation == 'update' ? 'Updater' : 'Installer';

        $aPlanks = array();
        foreach($aDirectories as $sDirectory) {
            $sPathConfig = BX_DIRECTORY_PATH_MODULES . $sDirectory . $sConfigFile;
            $sPathInstaller = BX_DIRECTORY_PATH_MODULES . $sDirectory . $sInstallerFile;
            if(file_exists($sPathConfig) && file_exists($sPathInstaller)) {
                include($sPathConfig);
                require_once($sPathInstaller);
                
                $sClassName = $aConfig['class_prefix'] . $sInstallerClass;
                $oInstaller = new $sClassName($aConfig);
            	$aResult = $oInstaller->$sOperation($aParams);
    
            	if(!$aResult['result'] && empty($aResult['message'])) 
            	   continue;
            }
            else 
                $aResult = array(
                    'operation_title' => _t('_adm_txt_modules_process_operation_failed', $sOperation, $sDirectory),
                    'message' => ''
                );
            
            
        	$aPlanks[] = array(
                'operation_title' => $aResult['operation_title'],
                'bx_if:operation_result_success' => array(
                    'condition' => $aResult['result'],
                    'content' => array()
                ),
                'bx_if:operation_result_failed' => array(
                    'condition' => !$aResult['result'],
                    'content' => array()
                ),
                'message' => $aResult['message']
            );
        }

        return $GLOBALS['oAdmTemplate']->parseHtmlByName('modules_results.html', array(
            'bx_repeat:planks' => $aPlanks
        ));
    }
    function _isArchive($sType) {
        $bResult = false;
        switch($sType) {
            case 'application/zip':
            case 'application/x-zip-compressed':
                $bResult = true;
                break;        
        }
        return $bResult;
    }
}
?>