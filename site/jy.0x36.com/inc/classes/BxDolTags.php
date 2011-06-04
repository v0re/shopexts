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

require_once(BX_DIRECTORY_PATH_INC . 'db.inc.php');
require_once(BX_DIRECTORY_PATH_INC . 'profiles.inc.php');
require_once(BX_DIRECTORY_PATH_INC . 'utils.inc.php');
bx_import('BxDolMistake');
define('BX_DOL_TAGS_DIVIDER', ';,');
class BxDolTags extends BxDolMistake {
    var $iViewer;
    
    var $sCacheFile;
    var $sNonParseParams;
    
    var $sCacheTable;
    var $sTagTable;
    var $aTagFields;
    
    var $aTagObjects = array();
    
    var $sTagsDivider = BX_DOL_TAGS_DIVIDER;
    var $bToLower = true;
    
    function BxDolTags () {
        $this->iViewer = getLoggedId();
        
        $this->sCacheFile = 'sys_objects_tag';
        $this->sNonParseParams = 'tags_non_parsable';
        $this->sCacheTable = 'sys_objects_tag';
        $this->sTagTable = 'sys_tags';
        $this->aTagFields = array(
            'id' => 'ObjID',
            'type' => 'Type',
            'tag' => 'Tag',
            'date' => 'Date'
        );
        $this->aObjFields = array(
            'id' => 'ID',
            'name' => 'ObjectName',
            'query' => 'Query',
            'perm_param' => 'PermalinkParam',
            'perm_enable' => 'EnabledPermalink',
            'perm_disable' => 'DisabledPermalink',
            'lang_key' => 'LangKey'
        );
    }
    
    function getTagObjectConfig ($aParam = array()) 
    {
        if (!empty($aParam))
        {
            $sqlQuery = "SELECT obj.*   
                FROM  `{$this->sCacheTable}` obj LEFT JOIN  `{$this->sTagTable}` tgs 
                ON obj.`{$this->aObjFields['name']}` = tgs.`{$this->aTagFields['type']}` " .
                $this->_getSelectCondition($aParam) . " GROUP BY obj.`{$this->aObjFields['name']}` ORDER BY obj.`ID`";
            $rResult = db_res($sqlQuery);
            while( $aRow = mysql_fetch_assoc($rResult ) )
                $this->aTagObjects[$aRow['ObjectName']] = $aRow;
        }
        else
            $this->aTagObjects = $GLOBALS['MySQL']->fromCache($this->sCacheFile, 'getAllWithKey', 
                "SELECT * FROM `{$this->sCacheTable}`", 'ObjectName');
    }
    
    function explodeTags ($sText) {
        $aTags = preg_split( '/['.$this->sTagsDivider.']/', $sText, 0, PREG_SPLIT_NO_EMPTY );
        foreach( $aTags as $iInd => $sTag ) {
            if( strlen( $sTag ) < 3 )
                unset( $aTags[$iInd] );
            else
                $aTags[$iInd] = $this->bToLower ? mb_strtolower( $sTag , 'UTF-8') : $sTag;
        }
        $aTags = array_unique($aTags);
        $sTagsNotParsed = getParam( $this->sNonParseParams );
        $aTagsNotParsed = preg_split( '/[, ]/', $sTagsNotParsed, 0, PREG_SPLIT_NO_EMPTY );
        
        $aTags = array_diff( $aTags, $aTagsNotParsed ); //drop non parsable tags
        return $aTags;
    }
    
    function reparseObjTags( $sType, $iID ) {
        $this->getTagObjectConfig();
        
        $iID = (int)$iID;
        if ($iID > 0 && array_key_exists($sType, $this->aTagObjects) && isset($this->aTagObjects[$sType]['Query'])) {
            db_res( "DELETE FROM `{$this->sTagTable}` WHERE `{$this->aTagFields['id']}` = $iID AND `{$this->aTagFields['type']}` = '$sType'" );
            $sqlQuery = str_replace('{iID}', $iID, $this->aTagObjects[$sType]['Query']);
            $sTags = db_value( $sqlQuery );
            if( !strlen( $sTags ) )
                return;
            $aTagsSet = array(
                'id' => $iID,
                'type' => $sType,
                'tagString' => $sTags,
                'date' => 'CURRENT_TIMESTAMP'
            );
            $this->_insertTags($aTagsSet);
        }
    }

    function getTagList($aParam)
    {
        $sLimit = '';
        $aTotalTags = array();
        $sJoin = isset($aParam['admin']) ? $this->_getProfileJoin(isset($aParam['admin'])) : '';
        $sGroupBy = "GROUP BY `{$this->aTagFields['tag']}`";
        
        if (isset($aParam['limit']))
        {
            $sLimit = 'LIMIT ';
            if (isset($aParam['start']))
                $sLimit .= $aParam['start'] . ', ';
            $sLimit .= $aParam['limit'];
        }
        
        $sCondition = $this->_getSelectCondition($aParam);
        
        if (isset($aParam['orderby']))
        {
            if ($aParam['orderby'] == 'popular')
                $sGroupBy .= " ORDER BY `{$this->aTagFields['tag']}` ASC, `count` DESC";
            else if ($aParam['orderby'] == 'recent')
                $sGroupBy .= " ORDER BY `{$this->aTagFields['date']}` DESC, `{$this->aTagFields['tag']}` ASC";
        }
        
        $sqlQuery = "SELECT
            `tgs`.`{$this->aTagFields['tag']}` as `{$this->aTagFields['tag']}`,
            `tgs`.`{$this->aTagFields['date']}` as `{$this->aTagFields['date']}`,
            COUNT(`tgs`.`{$this->aTagFields['id']}`) AS `count`
            FROM `{$this->sTagTable}` `tgs` $sJoin $sCondition $sGroupBy $sLimit";
        
        $rTags = db_res($sqlQuery);
        if (mysql_num_rows($rTags) > 0) {
            while ($aTag = mysql_fetch_assoc($rTags)) {
                $aTotalTags[$aTag[$this->aTagFields['tag']]] = $aTag['count'];
            }    
        }
        
        return $aTotalTags;
    }
    
    function getTagsCount($aParam)
    {
        $sCondition = $this->_getSelectCondition($aParam);
        $sJoin = isset($aParam['admin']) ? $this->_getProfileJoin(isset($aParam['admin'])) : '';
        $sqlQuery = "SELECT count(DISTINCT `tgs`.`{$this->aTagFields['tag']}`) AS `count` FROM 
            `{$this->sTagTable}` `tgs` $sJoin {$sCondition}";
        
        return db_value($sqlQuery);
    }
    
    function getFirstObject()
    {
        if ($this->aTagObjects)
        {
            $aKeys = array_keys($this->aTagObjects);
            return $aKeys[0];
        }
        
        return '';
    }
    
    function getHrefWithType($sType)
    {
        $aCurrent = $this->aTagObjects[$sType];
        $bPermalinks = getParam($aCurrent['PermalinkParam'])=='on' ? true : false;
        
        return $bPermalinks ? $aCurrent['EnabledPermalink'] : $aCurrent['DisabledPermalink'];
    }
    
    function _getSelectCondition($aParam)
    {
        $sCondition = "WHERE `tgs`.`{$this->aTagFields['tag']}` IS NOT NULL";
        
        if (!$aParam)
            return $sCondition;
        
        if (isset($aParam['type']) && $aParam['type'])
            $sCondition .= " AND `tgs`.`{$this->aTagFields['type']}` = '{$aParam['type']}'";
            
        if (isset($this->aTagFields['owner']))
        {
            $sCondition .= ' AND ';
            
            if (isset($aParam['admin']))
            {
                if ($aParam['admin'])
                    $sCondition .= '(`profiles`.`Role` & ' . BX_DOL_ROLE_ADMIN . ')';
                else
                    $sCondition .= 'NOT (`profiles`.`Role` & ' . BX_DOL_ROLE_ADMIN . ')';
            }
            else
                $sCondition .= "`tgs`.`{$this->aTagFields['owner']}` <> 0";
        }
        
        if (isset($aParam['filter']) && $aParam['filter'])
            $sCondition .= " AND `tgs`.`{$this->aTagFields['tag']}` LIKE '%{$aParam['filter']}%'";
            
        if (isset($aParam['date']) && $aParam['date'])
            $sCondition .= " AND DATE(`tgs`.`{$this->aTagFields['date']}`) = DATE('{$aParam['date']['year']}-{$aParam['date']['month']}-{$aParam['date']['day']}')";
        
        return $sCondition;
    }
    
    function _getProfileJoin($bAdmin)
    {
        if (isset($this->aTagFields['owner']) && $bAdmin)
            return "INNER JOIN `Profiles` `profiles` ON `tgs`.`{$this->aTagFields['owner']}` = `profiles`.`ID`";
        
        return '';
    }
    
    function _insertTags ($aTagsSet) {
        $aTags = $this->explodeTags( $aTagsSet['tagString'] );
        if( !$aTags )
            return;

        $sFields = '';

        foreach ($this->aTagFields as $sKey => $sValue)
            $sFields .= $sValue .', ';
        
        $sFields = trim($sFields, ', ');
        $sValues = '';

        foreach( $aTags as $sTag ) {
            $sTag = trim( addslashes($sTag) );
            $aTagsSet['tag'] = $sTag;

            $sQuery = "SELECT COUNT(*) FROM `sys_tags` 
            	WHERE `ObjID` = '{$aTagsSet['id']}' AND `Type` = '{$aTagsSet['type']}' AND `Tag` = '{$aTagsSet['tag']}'";

            if( !db_value($sQuery) ) {
                 $sValues  = "('{$aTagsSet['id']}', '{$aTagsSet['type']}', '{$aTagsSet['tag']}', {$aTagsSet['date']})"; 
			     $sqlQuery = "INSERT INTO `{$this->sTagTable}` ($sFields) VALUES $sValues";
			     db_res($sqlQuery);
            }
        }
    }    
}

?>
