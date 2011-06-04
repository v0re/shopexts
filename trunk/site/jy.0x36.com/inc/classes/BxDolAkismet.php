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


/**
 * Spam detection based on the message conetnt and logged in user
 */
class BxDolAkismet extends BxDolMistake
{
    var $oAkismet = null;

	/**
	 * Constructor
	 */
	public function BxDolAkismet($iProfileID = 0)
	{
        $sKey = getParam('sys_akismet_api_key');
        if ($sKey) {
            require_once (BX_DIRECTORY_PATH_PLUGINS . 'akismet/Akismet.class.php');
            $this->oAkismet = new Akismet(BX_DOL_URL_ROOT, $sKey);
            $aProfile = getProfileInfo($iProfileID);
            if ($aProfile) {
                $this->oAkismet->setCommentAuthor($aProfile['NickName']);
                $this->oAkismet->setCommentAuthorEmail($aProfile['Email']);
                $this->oAkismet->setCommentAuthorURL(getProfileLink($aProfile['ID']));
            }
        }
	}

    public function isSpam ($s, $sPermalink = false) {
        
        if (!$this->oAkismet)
            return false;

        $this->oAkismet->setCommentContent($s);
        if ($sPermalink)
            $this->oAkismet->setPermalink($sPermalink);

        return $this->oAkismet->isCommentSpam();
    }

    public function onPositiveDetection ($sExtraData = '') {
        $o = bx_instance('BxDolDNSBlacklists');
        $o->onPositiveDetection (getVisitorIP(), $sExtraData, 'akismet');
    }
}

?>
