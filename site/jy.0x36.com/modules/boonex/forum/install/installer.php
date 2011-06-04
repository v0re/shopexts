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

require_once(BX_DIRECTORY_PATH_CLASSES . "BxDolInstaller.php");

function is_compiled_template ($s) {
    return preg_match ('/\/[a-z]+_[a-z]+$/', $s);
}

class BxOrcaInstaller extends BxDolInstaller {
    function BxOrcaInstaller($aConfig) {
        parent::BxDolInstaller($aConfig);
        $this->_aActions = array_merge($this->_aActions, array(        
            'check_requirements' => array(
                'title' => 'Check Orca Forum requrements',
            ),
        ));
    }

    function install($aParams) {

        $aResult = parent::install($aParams);

        $this->addHtmlFields (array ('POST.topic_text', 'REQUEST.topic_text', 'POST.post_text', 'REQUEST.post_text')); 
		$this->updateEmailTemplatesExceptions ();

        return $aResult;
    }    
    
    function uninstall() {        

        $ret = parent::uninstall(array());

        $this->removeHtmlFields ();
		$this->updateEmailTemplatesExceptions ();

        $sPath = BX_DIRECTORY_PATH_MODULES . 'boonex/forum/';

        $a = $this->_read_in_dir ("{$sPath}cachejs/", 'is_file');
        array_walk($a, array($this, '_unlink'));

        $a = $this->_read_in_dir ("{$sPath}classes/", 'is_dir');
        array_walk($a, array($this, '_rmdir_rf'));

        $a = $this->_read_in_dir ("{$sPath}conf/", 'is_file');
        array_walk($a, array($this, '_unlink'));

        $a = $this->_read_in_dir ("{$sPath}js/", 'is_dir');
        array_walk($a, array($this, '_rmdir_rf'));

        $a = $this->_read_in_dir ("{$sPath}log/", 'is_file');
        array_walk($a, array($this, '_unlink'));

        $a = $this->_read_in_dir ("{$sPath}layout/", 'is_compiled_template');
        array_walk($a, array($this, '_rmdir_rf'));

        return $ret;        
    }

    function _read_in_dir ($sDir, $sFunc) {
        $aRet = array ();
        if ($h = opendir($sDir)) {
            while (false !== ($sFile = readdir($h))) {
                if ($sFile != '.' && $sFile != '..' && $sFile[0] != '.' && $sFunc($sDir.$sFile)) {
                    $aRet[] = $sDir.$sFile;
                }
            }
            closedir($h);
        }
        return $aRet;
    }

    function _rmdir_rf($dirname) {
        if ($dirHandle = opendir($dirname)) {
            chdir($dirname);
            while ($file = readdir($dirHandle)) {
                if ($file == '.' || $file == '..') continue;
                if (is_dir($file)) $this->_rmdir_rf($file);
                else unlink($file);
            }
            chdir('..');
            closedir($dirHandle);
            @rmdir($dirname);            
        }
    }    

    function _unlink($s) {
        unlink ($s);
    }

    function actionCheckRequirements () {
        $iErrors = 0;
    	if (((int)phpversion()) >= 5) { // PHP 5
	    	$iErrors += (class_exists('XsltProcessor')) ? 0 : 1;
    	} else { // PHP 4
		    $iErrors += (function_exists('domxml_xslt_stylesheet_file')) ? 0 : 1;
	    	$iErrors += (function_exists('xslt_create')) ? 0 : 1;
        }
        return array('code' => !$iErrors ? BX_DOL_INSTALLER_SUCCESS : BX_DOL_INSTALLER_FAILED, 'content' => '');
    }

    function actionCheckRequirementsFailed () {
        return '
            <div style="border:1px solid red; padding:10px;">

                Orca requres PHP XSLT extension. Please enable it first then proceed with installation. 

                <br /><br />

                For <u>PHP4</u>, make sure that PHP is compiled with the following extensions:
                <pre>
                    --with-dom --enable-xslt --with-xslt-sablot 
                </pre>
                If these PHP extensions are enabled, then you can see <u>domxml</u> and <u>xslt</u> extensions in phpinfo() with the following options:
                <pre>
                    DOM/XML: enabled
                    XSLT support: enabled
                </pre>

                <br /><br />

                For <u>PHP5</u>, make sure that PHP is compiled with the following extension:
                <pre>
                    --with-xsl 
                </pre>
                If this PHP extension is enabled, then you can see <u>xslt</u> extensions in phpinfo() with the following option:
                <pre>
                    XSLT support: enabled
                </pre>                
            </div>';
    }
}
?>
