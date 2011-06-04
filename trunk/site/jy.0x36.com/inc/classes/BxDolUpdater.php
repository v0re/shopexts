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

bx_import('BxDolInstaller');

class BxDolUpdater extends BxDolInstaller {
    function BxDolUpdater($aConfig) {
        parent::BxDolInstaller($aConfig);        
        $this->_sModulePath = $this->_sBasePath . $aConfig['module_dir'];
        
        $this->_aActions = array_merge($this->_aActions, array(
            'check_module_exists' => array(
                'title' => _t('_adm_txt_modules_check_module_exists'),
            ),
            'check_module_version' => array(
                'title' => _t('_adm_txt_modules_check_module_version'),
            ),
            'check_module_hash' => array(
                'title' => _t('_adm_txt_modules_check_module_hash'),
            ),
            'update_files' => array(
                'title' => _t('_adm_txt_modules_update_files'),
            ),
        ));
    }
    function update($aParams) {
        global $MySQL;
        
        $aResult = array(
            'operation_title' => _t('_adm_txt_modules_operation_update', $this->_aConfig['title'], $this->_aConfig['version_from'], $this->_aConfig['version_to'])
        );
        
        //--- Check for module to update ---//
        $aModuleInfo = $MySQL->getRow("SELECT `id`, `version` FROM `sys_modules` WHERE `path`='" . $this->_aConfig['module_dir'] . "' AND `uri`='" . $this->_aConfig['module_uri'] . "' LIMIT 1");
        if(!$aModuleInfo)
            return array_merge($aResult, array(                
                'message' => $this->_displayResult('check_module_exists', false, '_adm_txt_modules_module_not_found'), 
                'result' => false
            ));

        //--- Check version ---//
        if($aModuleInfo['version'] != $this->_aConfig['version_from'])
            return array_merge($aResult, array(                
                'message' => $this->_displayResult('check_module_version', false, '_adm_txt_modules_wrong_version'), 
                'result' => false
            ));

        //--- Check hash ---//
        $aFilesOrig = $MySQL->getAllWithKey("SELECT `file`, `hash` FROM `sys_modules_file_tracks` WHERE `module_id`='" . $aModuleInfo['id'] . "'", "file");

        $aFiles = array();
        $this->_hash($this->_sModulePath, $aFiles);
        foreach($aFiles as $aFile)
            if(!isset($aFilesOrig[$aFile['file']]) || $aFilesOrig[$aFile['file']]['hash'] != $aFile['hash'])
                return array_merge($aResult, array(
                    'message' => $this->_displayResult('check_module_hash', false, '_adm_txt_modules_module_was_modified'), 
                    'result' => false
                ));
        
        //--- Perform action and check results ---//
        $aResult = array_merge($aResult, $this->_perform('install', 'Update'));
        if($aResult['result']) {
            $MySQL->query("UPDATE `sys_modules` SET `version`='" . $this->_aConfig['version_to'] . "' WHERE `id`='" . $aModuleInfo['id'] . "'");
            $MySQL->query("DELETE FROM `sys_modules_file_tracks` WHERE `module_id`='" . $aModuleInfo['id'] . "'");

            $aFiles = array();
            $this->_hash(BX_DIRECTORY_PATH_ROOT . 'modules/' . $this->_aConfig['home_dir'], $aFiles);
            foreach($aFiles as $aFile)
                $MySQL->query("INSERT IGNORE INTO `sys_modules_file_tracks`(`module_id`, `file`, `hash`) VALUES('" . $aModuleInfo['id'] . "', '" . $aFile['file'] . "', '" . $aFile['hash'] . "')");
        }

        return $aResult;
    }
        
    //--- Action Methods ---//
    function actionUpdateFiles($bInstall = true) {
        $sPath = $this->_sHomePath . 'source/';
        if(!file_exists($sPath))
            return BX_DOL_INSTALLER_FAILED;
            
        $oFtp = new BxDolFtp($_SERVER['HTTP_HOST'], getParam('sys_ftp_login'), getParam('sys_ftp_password'), getParam('sys_ftp_dir'));
        if($oFtp->connect() == false)
            return BX_DOL_INSTALLER_FAILED;

        return $oFtp->copy($sPath . '*', 'modules/' . $this->_aConfig['module_dir']) ? BX_DOL_INSTALLER_SUCCESS : BX_DOL_INSTALLER_FAILED;
    }
    function actionUpdateLanguages($bInstall = true) {
        global $MySQL;

        $aLanguages = $MySQL->getAll("SELECT `ID` AS `id`, `Name` AS `name`, `Title` AS `title` FROM `sys_localization_languages`");

        //--- Process languages' key=>value pears ---//
        $sModuleConfig = $this->_sHomePath .'install/config.php';
        if(!file_exists($sModuleConfig))
            return array('code' => BX_DOL_INSTALLER_FAILED, 'content' => '_adm_txt_modules_module_config_not_found');

        include($sModuleConfig);
        $iCategoryId = (int)$MySQL->getOne("SELECT `ID` FROM `sys_localization_categories` WHERE `Name`='" . $aConfig['language_category'] . "' LIMIT 1");
        
        foreach($aLanguages as $aLanguage)
            $this->_updateLanguage($bInstall, $aLanguage, $iCategoryId);

        //--- Recompile all language files ---//
        $aResult = array();
    	foreach($aLanguages as $aLanguage) {    		
    		$bResult = compileLanguage($aLanguage['id']);

    		if(!$bResult)
                $aResult[] = $aLanguage['title'];
    	}
    	return empty($aResult) ? BX_DOL_INSTALLER_SUCCESS : array('code' => BX_DOL_INSTALLER_FAILED, 'content' => $aResult);
    }

    //--- Protected methods ---//
    function _updateLanguage($bInstall, $aLanguage, $iCategoryId = 0) {
        global $MySQL;

        $sPath = $this->_sHomePath . 'install/langs/' . $aLanguage['name'] . '.php';
        if(!file_exists($sPath)) return false;

        include($sPath);

        //--- Process delete ---//
        if(isset($aLangContentDelete) && is_array($aLangContentDelete))
            foreach($aLangContentDelete as $sKey)
                $MySQL->query("DELETE FROM `sys_localization_keys`, `sys_localization_strings` USING `sys_localization_keys`, `sys_localization_strings` WHERE `sys_localization_keys`.`ID`=`sys_localization_strings`.`IDKey` AND `sys_localization_keys`.`Key`='" . $sKey . "' AND `sys_localization_strings`.`IDLanguage`='" . $aLanguage['id'] . "'");
                
        //--- Process add ---//        
        if(isset($aLangContentAdd) && is_array($aLangContentAdd))
            foreach($aLangContentAdd as $sKey => $sValue) {
                $mixedResult = $MySQL->query("INSERT IGNORE INTO `sys_localization_keys`(`IDCategory`, `Key`) VALUES('" . $iCategoryId . "', '" . $sKey . "')");
                if($mixedResult === false || $mixedResult <= 0) 
                    continue;

                $iLangKeyId = (int)$MySQL->lastId();
                $MySQL->query("INSERT INTO `sys_localization_strings`(`IDKey`, `IDLanguage`, `String`) VALUES('" . $iLangKeyId . "', '" . $aLanguage['id'] . "', '" . addslashes($sValue) . "')");
            }
        
        //--- Process Update ---//        
        if(isset($aLangContentUpdate) && is_array($aLangContentUpdate))
            foreach($aLangContentUpdate as $sKey => $sValue) {
                $iLangKeyId = (int)$MySQL->getOne("SELECT `ID` FROM `sys_localization_keys` WHERE `Key`='" . $sKey . "'");
                if($iLangKeyId == 0)
                    continue;

                $MySQL->query("UPDATE `sys_localization_strings` SET `String`='" . addslashes(clear_xss($sValue)) . "' WHERE `IDKey`='" . $iLangKeyId . "' AND `IDLanguage`='" . $aLanguage['id'] . "'");
            }

        return true;
    }
}
?>