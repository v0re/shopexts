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

require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolTags.php');

define("CATEGORIES_DIVIDER", ';');

class BxDolCategories extends BxDolTags {
    var $sAutoApprovePrefix;
    
    function BxDolCategories ($iPossOwner = 0) {
        parent::BxDolTags();
        $this->iViewer = (int)$iPossOwner > 0 ? (int)$iPossOwner : $this->iViewer;
        $this->sCacheFile = 'sys_objects_categories';
        $this->sNonParseParams = 'tags_non_parsable';
        $this->sCacheTable = 'sys_objects_categories';
        $this->sTagTable = 'sys_categories';
        $this->aTagFields = array(
            'id' => 'ID',
            'type' => 'Type',
            'tag' => 'Category',
            'owner' => 'Owner',
            'status' => 'Status',
            'date' => 'Date'
        );
        $this->sAutoApprovePrefix = 'category_auto_app_';
        $this->bToLower = false;
    }
    
    function getGroupChooser ($sType, $iOwnerId = 0, $bForm = false, $sCustomValues = '') {
        $a = $this->getCategoriesList ($sType, $iOwnerId, $bForm);
        $a = array('' => _t('_Please_Select_')) + (is_array($a) ? $a : array());

        $aCustomValues = $this->explodeTags($sCustomValues);
        foreach($aCustomValues as $iIndex => $sValue)
        	$a[$sValue] = $sValue;

        return array(
            'type' => 'select_box',
                'name' => 'Categories',
                'caption' => _t('_Categories'),
                'values' => $a,
                'required' => true,
                'checker' => array (
                    'func' => 'avail',
                    'error' => _t ('_sys_err_categories'),
                ), 
                'db' => array (
                    'pass' => 'Categories', 
                ),                    
            );
    }
    
    function getCategoriesList ($sType, $iOwnerId = 0, $bForm = false) {
        $this->getTagObjectConfig();
        $sType = array_key_exists($sType, $this->aTagObjects) === true ? $sType : 'bx_photos';
        $iOwnerId = (int)$iOwnerId;
        $sqlQuery = "SELECT `cat`.`{$this->aTagFields['tag']}`
                     FROM `{$this->sTagTable}` `cat`  
                     WHERE (`cat`.`{$this->aTagFields['owner']}` = 0 OR `cat`.`{$this->aTagFields['owner']}` = $iOwnerId)
                     AND `cat`.`{$this->aTagFields['type']}` = '$sType' AND `cat`.`{$this->aTagFields['status']}` = 'active' 
                     __sqlAdd__ 
                     GROUP BY `cat`.`{$this->aTagFields['tag']}`";
        
        $aAddSql = array();
          
        if (getParam($this->sAutoApprovePrefix . $sType) != 'on')
            $aAddSql[] = " AND `cat`.`{$this->aTagFields['status']}` = 'active'";
            
        $sqlAdd = '';
        foreach ($aAddSql as $sValue)
            $sqlAdd .= $sValue;
        
        $sqlQuery = str_replace('__sqlAdd__', $sqlAdd, $sqlQuery);
        
        $rData = db_res($sqlQuery);
        while ($aList = mysql_fetch_assoc($rData)) {
           if ($bForm)
            $aCatList[$aList[$this->aTagFields['tag']]] = $aList[$this->aTagFields['tag']];
           else
            $aCatList[] = $aList[$this->aTagFields['tag']];
        }
        return $aCatList;
    }
    
    function getTagList($aParam)
    {
        $sLimit = '';
        $aTotalTags = array();
        $sGroupBy = "GROUP BY `{$this->aTagFields['tag']}`";
        
        if (isset($aParam['limit']))
        {
            $sLimit = 'LIMIT ';
            if (isset($aParam['start']))
                $sLimit .= (int)$aParam['start'] . ', ';
            $sLimit .= (int)$aParam['limit'];
        }
        
        $sCondition = $this->_getSelectCondition($aParam);
        
        if (isset($aParam['orderby']))
        {
            if ($aParam['orderby'] == 'popular')
                $sGroupBy .= " ORDER BY `count` DESC, `{$this->aTagFields['tag']}` ASC";
            else if ($aParam['orderby'] == 'recent')
                $sGroupBy .= " ORDER BY `{$this->aTagFields['date']}` DESC, `{$this->aTagFields['tag']}` ASC";
        }
        
        $sDiffCount = '';
        
        $sqlQuery = "SELECT
            `tgs`.`{$this->aTagFields['tag']}` as `{$this->aTagFields['tag']}`,
            `tgs`.`{$this->aTagFields['date']}` as `{$this->aTagFields['date']}`,
            COUNT(`tgs`.`{$this->aTagFields['id']}`) AS `count`
            FROM `{$this->sTagTable}` `tgs` $sCondition $sGroupBy $sLimit";
        
        $rTags = db_res($sqlQuery);
        if (mysql_num_rows($rTags) > 0) {
            while ($aTag = mysql_fetch_assoc($rTags)) {
                if ((int)$aTag['count'] > 0)
                    $aTotalTags[$aTag[$this->aTagFields['tag']]] = $aTag['count'];
            }    
        }
        
        return $aTotalTags;
    }
        
    function getTagsCount($aParam)
    {
        $sCondition = $this->_getSelectCondition($aParam);
        $sqlQuery = "SELECT count(DISTINCT `tgs`.`{$this->aTagFields['tag']}`) AS `count` FROM 
            `{$this->sTagTable}` `tgs` {$sCondition}";
        
        return db_value($sqlQuery);
    }
    
    function _getSelectCondition($aParam)
    {
        $sCondition = "WHERE `tgs`.`{$this->aTagFields['owner']}` != 0";
        
        if (!$aParam)
            return $sCondition;
            
        if (isset($aParam['common'])) {
            $aUnitsCommon = $this->_getCommonCategories($aParam['type']);
			$sCatsList = "";
			if (is_array($aUnitsCommon)) {
				foreach ($aUnitsCommon as $sUnit)
					$sCatsList .= process_db_input($sUnit, BX_TAGS_NO_ACTION, BX_SLASHES_NO_ACTION) . "','";
				$sCatsList = substr($sCatsList, 0, -3);
			}
        	$sCondition .= " AND `tgs`.`{$this->aTagFields['tag']}` " . (!$aParam['common'] ? 'NOT' : '') . 
                " IN ('" . $sCatsList . "')";
        }
            
        if (isset($aParam['type']) && $aParam['type'])
            $sCondition .= " AND `tgs`.`{$this->aTagFields['type']}` = '{$aParam['type']}'";
            
        if (isset($aParam['status']) && $aParam['status'])
            $sCondition .= " AND tgs.`{$this->aTagFields['status']}` = '{$aParam['status']}'";
        else
            $sCondition .= " AND tgs.`{$this->aTagFields['status']}` = 'active'";
        
        if (isset($aParam['filter']) && $aParam['filter'])
            $sCondition .= " AND `tgs`.`{$this->aTagFields['tag']}` LIKE '%{$aParam['filter']}%'";
            
        if (isset($aParam['date']) && $aParam['date'])
            $sCondition .= " AND DATE(`tgs`.`{$this->aTagFields['date']}`) = DATE('{$aParam['date']['year']}-{$aParam['date']['month']}-{$aParam['date']['day']}')";
            
        return $sCondition;
    }
    
    function _getCommonCategories($sModule = '')
    {
        $aResult = array();
        $sCondModule = strlen($sModule) > 0 ? "AND `Type` = '$sModule'" : '';
        $aCtegories = db_res("SELECT `Category` FROM `sys_categories` WHERE `Owner` = 0 $sCondModule");
        
        if (mysql_num_rows($aCtegories) > 0) 
        {
            while ($aCategory = mysql_fetch_assoc($aCtegories)) 
                $aResult[] = $aCategory['Category'];
        }
        
        return $aResult;
    }
    
    function _insertTags ($aTagsSet) {
        $aTags = $this->explodeTags($aTagsSet['tagString']);
        if( !$aTags )
            return;
        $sFields = '';
        foreach ($this->aTagFields as $sKey => $sValue)
            $sFields .= $sValue .', ';
        
        $aCommonCat = $this->_getCommonCategories($aTagsSet['type']);
        $bAutoApprove = getParam($this->sAutoApprovePrefix . $aTagsSet['type']) == 'on';
        $aTagsSet['owner'] = $this->iViewer;
        $sFields = trim($sFields, ', ');
        $sValues = '';
        foreach( $aTags as $sTag ) 
        {
            $aTagsSet['tag'] = addslashes( $sTag );
            $aTagsSet['status'] = $bAutoApprove || in_array($aTagsSet['tag'], $aCommonCat) ? 'active' : 'passive';
            $sValues .= "('{$aTagsSet['id']}', '{$aTagsSet['type']}', '{$aTagsSet['tag']}', '{$aTagsSet['owner']}', '{$aTagsSet['status']}', CURRENT_TIMESTAMP), "; 
        }
        $sValues = trim($sValues, ', ');
        
        $sqlQuery = "INSERT INTO `{$this->sTagTable}` ($sFields) VALUES $sValues";
        db_res($sqlQuery);
    }
}

?>