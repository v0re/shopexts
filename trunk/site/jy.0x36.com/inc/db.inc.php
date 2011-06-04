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

require_once("header.inc.php");
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
require_once( BX_DIRECTORY_PATH_CLASSES . 'BxDolDb.php' );

$GLOBALS['MySQL'] = new BxDolDb();

$site['title'] = getParam('site_title');
$site['email'] = getParam('site_email');
$site['email_notify'] = getParam('site_email_notify');

function db_list_tables( $error_checking = true ) {
    $GLOBALS['MySQL']->setErrorChecking ($error_checking);
	return $GLOBALS['MySQL']->listTables();
}

function db_get_encoding ( $error_checking = true ) {
    $GLOBALS['MySQL']->setErrorChecking ($error_checking);
    return $GLOBALS['MySQL']->getEncoding();
}

function db_res( $query, $error_checking = true ) {
    $GLOBALS['MySQL']->setErrorChecking ($error_checking);
    return $GLOBALS['MySQL']->res($query);
}

function db_last_id() {
    return $GLOBALS['MySQL']->lastId();
}

function db_affected_rows() {
    return $GLOBALS['MySQL']->getAffectedRows();
}

function db_res_assoc_arr( $query, $error_checking = true ) {
    $GLOBALS['MySQL']->setErrorChecking ($error_checking);
    return $GLOBALS['MySQL']->getAll($query);
}

function db_arr( $query, $error_checking = true ) {
    $GLOBALS['MySQL']->setErrorChecking ($error_checking);
    return $GLOBALS['MySQL']->getRow($query, MYSQL_BOTH);
}

function db_assoc_arr( $query, $error_checking = true ) {
    $GLOBALS['MySQL']->setErrorChecking ($error_checking);
    return $GLOBALS['MySQL']->getRow($query);
}

function db_value( $query, $error_checking = true, $index = 0 ) {
    $GLOBALS['MySQL']->setErrorChecking ($error_checking);
    return $GLOBALS['MySQL']->getOne($query, $index);
}

function fill_array( $res ) {
    return $GLOBALS['MySQL']->fillArray($res, MYSQL_BOTH);
}

function fill_assoc_array( $res ) {
    return $GLOBALS['MySQL']->fillArray($res, MYSQL_ASSOC);
}

function getParam( $param_name, $use_cache = true ) {
    return $GLOBALS['MySQL']->getParam($param_name, $use_cache);
}

function getParamDesc( $param_name ) {
    return $GLOBALS['MySQL']->getOne ("SELECT `desc` FROM `sys_options` WHERE `Name` = '$param_name'");
}

function setParam( $param_name, $param_val ) {
    return $GLOBALS['MySQL']->setParam($param_name, $param_val);
}

?>
