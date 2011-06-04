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


// error handling functions

class Mistake extends ThingPage
{

// private variables
	
	var $_error;							// current error string

// public functions

	/**
	 * constructor
	 */
	function Mistake ()
	{
	}


	/**
	 *	set error string for the object
	 */
	function log ($s)
	{
		global $gConf;

		if (strlen ($gConf['dir']['error_log']))
		{
			$fp = @fopen ($gConf['dir']['error_log'], "a");
			if ($fp)
			{
				@fwrite ($fp, date ('Y-m-d H:i:s', time ()) . "\t$s\n");
				@fclose ($fp);
			}
		}


		if($gConf['debug'])
			$this->displayError($s);		

		$this->_error = $s;
	}	


	function displayError ($s)
	{
		global $gConf;

		transCheck ($this->getErrorPageXML ($s), $gConf['dir']['xsl'] . 'default_error.xsl', 1);

		exit;
	}


	/**
	 * returns page XML
	 */
	function getErrorPageXML ($s)
	{
		return $this->addHeaderFooter ($s, $s);
	}

// private functions


}





?>
