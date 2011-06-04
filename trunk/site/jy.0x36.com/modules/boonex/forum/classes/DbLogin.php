<?php
/***************************************************************************
*                            Orca Interactive Forum Script
*                              -----------------
*     begin                : Fr Nov 10 2006
*     copyright            : (C) 2006 BoonEx Group
*     website              : http://www.boonex.com/
* This file is part of Orca - Interactive Forum Script
*
* Orca is free software. This work is licensed under a Creative Commons Attribution 3.0 License. 
* http://creativecommons.org/licenses/by/3.0/
*
* Orca is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
* See the Creative Commons Attribution 3.0 License for more details. 
* You should have received a copy of the Creative Commons Attribution 3.0 License along with Orca, 
* see license.txt file; if not, write to marketing@boonex.com
***************************************************************************/


define ('TF_FORUM_USER',	'`'.$gConf['db']['prefix'].'forum_user`');

// join/login operations with database

class DbLogin extends BxDb
{

	/**
	 * constructor
	 */
	function DbLogin ()
	{
		global $gConf;
		parent::BxDb ($gConf['db']['db'], $gConf['db']['user'], $gConf['db']['pwd'], $gConf['db']['host'], $gConf['db']['port'], $gConf['db']['sock']);
	}

	function getUserByName ($s)
	{
		$sql = "SELECT `user_name` FROM " . TF_FORUM_USER . " WHERE `user_name` = '$s' LIMIT 1";				
		return $this->getOne ($sql);
	}

	function getUserByEmail ($s)
	{
		$sql = "SELECT `user_email` FROM " . TF_FORUM_USER . " WHERE `user_email` = '$s' LIMIT 1";		
		return $this->getOne ($sql);		
	}
	
	function insertUser ($p)
	{
		$sql = "INSERT INTO " . TF_FORUM_USER . " SET `user_name` = '{$p['username']}', `user_email` = '{$p['email']}', `user_pwd` = MD5('{$p['pwd']}'), `user_join_date` = UNIX_TIMESTAMP()";		
		return $this->query($sql);
	}

	function checkLogin ($p)
	{
		$sql = "SELECT `user_name` FROM " . TF_FORUM_USER . " WHERE `user_name` = '{$p['username']}' AND `user_pwd` = '{$p['pwd']}' LIMIT 1";
		return $this->getRow ($sql);
	}

	function getUserJoinDate ($u)
	{
		global $gConf;
		return $this->getOne ("SELECT DATE_FORMAT(FROM_UNIXTIME(`user_join_date`),'{$gConf['date_format']}') AS `user_join_date` FROM " . TF_FORUM_USER . " WHERE `user_name` = '$u' LIMIT 1");
	}

// private functions

}





?>
