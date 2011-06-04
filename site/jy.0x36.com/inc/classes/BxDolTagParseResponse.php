<?php

/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -----------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2006 BoonEx Group
*     website              : http://www.boonex.com/
* This file is part of Dolphin - Smart Community Builder
*
* Dolphin is free software. This work is licensed under a Creative Commons Attribution 3.0 License. 
* http://creativecommons.org/licenses/by/3.0/
*
* Dolphin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
* See the Creative Commons Attribution 3.0 License for more details. 
* You should have received a copy of the Creative Commons Attribution 3.0 License along with Dolphin, 
* see license.txt file; if not, write to marketing@boonex.com
***************************************************************************/

require_once( BX_DIRECTORY_PATH_CLASSES . 'BxDolAlerts.php' );

class BxDolTagParseResponse extends BxDolAlertsResponse {
    var $aParseList = array(
        'tag' => array(
            'class' => 'BxDolTags',
            'file' => 'inc/classes/BxDolTags.php',
            'method' => 'reparseObjTags({sType}, {iId})'
        ),
        'category' => array(
            'class' => 'BxDolCategories',
            'file' => 'inc/classes/BxDolCategories.php',
            'method' => 'reparseObjTags({sType}, {iId})'
        )
    );
    
    var $aCurrent = array();
    
    function response ($oTag) {
        foreach ($this->aParseList as $sKey => $aValue) {        
            if (!class_exists($aValue['class']))
               require_once(BX_DIRECTORY_PATH_ROOT . $aValue['file']);
            $oParse = new $aValue['class']();
            $sMethod = $aValue['method'];
            
            $sMethod = str_replace('{sType}', "'".$oTag->sUnit."'", $sMethod);
            $sMethod = str_replace('{iId}', $oTag->iObject, $sMethod);
            $sMethod = str_replace('{iId}', $oTag->iObject, $sMethod);
            $sFullComm = '$oParse->'.$sMethod.'; ';
            eval($sFullComm);
        }
    }
}

?>