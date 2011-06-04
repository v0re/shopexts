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

class BxOSiInstaller extends BxDolInstaller {
    function BxOSiInstaller($aConfig) {
        parent::BxDolInstaller($aConfig);

        $this->_aActions = array_merge($this->_aActions, array(        
            'check_requirements' => array(
                'title' => 'Check Opensocial requrements',
            ),
        ));
    }

    function actionCheckRequirements () {
        $iErrors = 0;
    	$iErrors += (function_exists('mysqli_init') && extension_loaded('mysqli')) ? 0 : 1;
    	$iErrors += (function_exists('json_decode') && extension_loaded('json')) ? 0 : 1;
    	$iErrors += (function_exists('openssl_open') && extension_loaded('openssl')) ? 0 : 1;
    	$iErrors += (function_exists('shell_exec')) ? 0 : 1;
        return array('code' => !$iErrors ? BX_DOL_INSTALLER_SUCCESS : BX_DOL_INSTALLER_FAILED, 'content' => '');
    }

    function actionCheckRequirementsFailed () {
        return '
            <div style="border:1px solid red; padding:10px;">

                Opensocial requres PHP OpenSSL, mysqli and JSON extensions. Please enable it first then proceed with installation. 

                <br /><br />

                To use PHP`s OpenSSL support you must also compile PHP --with-openssl[=DIR]. 

                <br />

                If you would like to install the mysql extension along with the mysqli extension you have to use the same client library to avoid any conflicts. 

                <br />

                To install the mysqli extension for PHP, use the 
                <pre>
                    --with-mysqli=mysql_config_path/mysql_config
                </pre>
                configuration option where mysql_config_path represents the location of the mysql_config program that comes with MySQL versions greater than 4.1. 

                If you would like to install the mysql extension along with the mysqli extension you have to use the same client library to avoid any conflicts. 

                <br />

                To install the JSON extension for PHP, use the 
                <pre>
                    --enable-json
                </pre>

                <br />

                shell_exec function is unvailable, this module require this function
                <pre>
                    --enable-json
                </pre>

                <br />
             
            </div>';
    }
}
?>