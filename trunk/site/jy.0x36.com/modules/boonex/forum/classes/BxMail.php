<?
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


//  mail sending class

class BxMail extends Mistake 
{	
    var $_sSenderName = 'Orca Forum';

	/**
	 * send mail with password
	 * @param $p	email template variables to replace
	 */ 
	function sendActivationMail (&$p)
	{
		global $gConf;

		$subj = "[L[Mail Subj Registration]]";

		$mailContent = <<<EOF
[L[Mail Body Registration]]
EOF;

        $p['site_url'] = $gConf['url']['base'];
		for (reset ($p) ; list ($k, $v) = each ($p); )
		{
			$mailContent = str_replace ('{'.$k.'}', $v, $mailContent);
		}
        

		$headers = "From: =?UTF-8?B?" . base64_encode($gConf['def_title']) . "?= <" . $gConf['email']['sender'] . ">\r\nContent-type: text/html; charset=UTF-8\r\n";				
        $subj = '=?UTF-8?B?' . base64_encode($subj) . '?=';
		return mail ($p['email'], $subj, $mailContent, $headers, '-f'.$gConf['email']['sender']);
	}
	
}
?>
