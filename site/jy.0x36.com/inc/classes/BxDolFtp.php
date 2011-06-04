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

class BxDolFtp {
    var $_sHost;
    var $_sLogin;
    var $_sPassword;
    var $_sPath;
    var $_rStream;
    
    function BxDolFtp($sHost, $sLogin, $sPassword, $sPath = '/') {
        $this->_sHost = $sHost;
        $this->_sLogin = $sLogin;
        $this->_sPassword = $sPassword;
        $this->_sPath = $sPath;
    }
    function connect() {
        $this->_rStream = ftp_connect($this->_sHost);
        return ftp_login($this->_rStream, $this->_sLogin, $this->_sPassword);
    }
    function copy($sFilePathFrom, $sFilePathTo) {
        $sFilePathTo = $this->_sPath . $sFilePathTo;
        return $this->_copyFile($sFilePathFrom, $sFilePathTo);
    }
    function delete($sPath) {
        $sPath = $this->_sPath . $sPath;
        return $this->_deleteDirectory($sPath);
    }
    
    function _copyFile($sFilePathFrom, $sFilePathTo) {
        if(substr($sFilePathFrom, -1) == '*')
            $sFilePathFrom = substr($sFilePathFrom, 0, -1);

        $bResult = false;        
        if(is_file($sFilePathFrom)) {
            if($this->_isFile($sFilePathTo)) {
                $aFileParts = $this->_parseFile($sFilePathTo);
                if(isset($aFileParts[0]))
                    @ftp_mkdir($this->_rStream, $aFileParts[0]);
                
                $bResult = @ftp_put($this->_rStream, $sFilePathTo, $sFilePathFrom, FTP_BINARY);
            }
            else if($this->_isDirectory($sFilePathTo)) {
                @ftp_mkdir($this->_rStream, $sFilePathTo);
                    
                $aFileParts = $this->_parseFile($sFilePathFrom);
                if(isset($aFileParts[1]))
                    $bResult = @ftp_put($this->_rStream, $this->_validatePath($sFilePathTo) . $aFileParts[1], $sFilePathFrom, FTP_BINARY);
            }                        
        }
        else if(is_dir($sFilePathFrom) && $this->_isDirectory($sFilePathTo)) {
            @ftp_mkdir($this->_rStream, $sFilePathTo);

            $aInnerFiles = $this->_readDirectory($sFilePathFrom);
            foreach($aInnerFiles as $sFile)
                $bResult = $this->_copyFile($this->_validatePath($sFilePathFrom) . $sFile, $this->_validatePath($sFilePathTo) . $sFile);
        }
        else 
            $bResult = false;

        return $bResult;
    }
    function _readDirectory($sFilePath) {
        if(!is_dir($sFilePath) || !($rSource = opendir($sFilePath))) return false;         

        $aResult = array();
        while(($sFile = readdir($rSource)) !== false) {
            if($sFile == '.' || $sFile =='..' || $sFile[0] == '.') continue;
            $aResult[] = $sFile;
        }
        closedir($rSource);
        
        return $aResult;        
    }
    function _deleteDirectory($sPath) {
        if($this->_isDirectory($sPath)) {
            if(substr($sPath, -1) != '/')
                $sPath .= '/';

            if(($aFiles = @ftp_nlist($this->_rStream, $sPath)) !== false) {
                foreach($aFiles as $sFile)
					if($sFile != '.' && $sFile != '..')
                        $this->_deleteDirectory($sPath . $sFile);

                if(!@ftp_rmdir($this->_rStream, $sPath))
                    return false;
            }
        }
        else if(!@ftp_delete($this->_rStream, $sPath))
            return false;
            
        return true;
    }
    function _validatePath($sPath) {
        if($sPath && substr($sPath, -1) != '/' && $this->_isDirectory($sPath))
            $sPath .= '/';

        return $sPath;
    }
    function _parseFile($sFilePath) {
        $aParts = array();
        preg_match("/^([a-zA-Z0-9@~_\.\\\\\/:-]+[\\\\\/])([a-zA-Z0-9~_-]+\.[a-zA-Z]{2,8})$/", $sFilePath, $aParts) ? true : false;
        return count($aParts) > 1 ? array_slice($aParts, 1) : false;
    }
    function _isFile($sFilePath) {        
        return preg_match("/^([a-zA-Z0-9@~_\.\\\\\/:-]+)\.([a-zA-Z]){2,8}$/", $sFilePath) ? true : false;   
    }
    function _isDirectory($sFilePath) {     
        return preg_match("/^([a-zA-Z0-9@~_\.\\\\\/:-]+)[\\\\\/]([a-zA-Z0-9~_-]+)[\\\\\/]?$/", $sFilePath) ? true : false;   
    }
}
?>