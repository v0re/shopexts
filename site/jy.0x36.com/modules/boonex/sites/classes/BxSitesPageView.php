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

require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolPageView.php');
bx_import('BxDolSubscription');

class BxSitesPageView extends BxDolPageView {
        
    var $_oSites;
    var $_aSite;
    var $_oTemplate;
    var $_oConfig;
    
    function BxSitesPageView(&$oSites, $aSite) 
    {
        parent::BxDolPageView('bx_sites_view');
                
        $this->_oSites = &$oSites;
        $this->_aSite = $aSite;
        
        $this->_oTemplate = $oSites->_oTemplate;
        $this->_oConfig = $oSites->_oConfig;
    }
    
    function getBlockCode_ViewActions() 
    {
        global $oFunctions;
        
        if ($this->_oSites->iOwnerId || $this->_oSites->isAdmin())
        {
            $aInfo = array(
                'iViewer' => $this->_oSites->iOwnerId,
                'ownerID' => (int)$this->_aSite['ownerid'],
                'ID' => (int)$this->_aSite['id'],
                'TitleEdit' => $this->_oSites->isAllowedEdit($this->_aSite) ? _t('_bx_sites_action_title_edit') : '',
                'TitleDelete' => $this->_oSites->isAllowedDelete($this->_aSite) ? _t('_bx_sites_action_title_delete') : '',
                'TitleShare' => $this->_oSites->isAllowedShareSite($this->_aSite) ? _t('_bx_sites_action_title_share') : '',
                'AddToFeatured' => ($this->_oSites->isAllowedMarkAsFeatured($this->_aSite) && (int)$this->_aSite['allowView'] == BX_DOL_PG_ALL) ? 
                                    ((int)$this->_aSite['featured'] == 1  ? _t('_bx_sites_action_remove_from_featured') : _t('_bx_sites_action_add_to_featured')) : ''
            );
            
            $oSubscription = new BxDolSubscription();
            $aButton = $oSubscription->getButton($this->_oSites->iOwnerId, 'bx_sites', '', $this->_aSite['id']);

            $aInfo['sbs_sites_title'] = $aButton['title'];
            $aInfo['sbs_sites_script'] = $aButton['script'];
            
            if (!$aInfo['TitleEdit'] && !$aInfo['TitleDelete'] && !$aInfo['TitleShare'] && !$aInfo['AddToFeatured'] && !$aInfo['sbs_sites_title'])
                return '';
                
            $sScript = '';
            if ($aInfo['TitleShare']) {
                $sUrlSharePopup = BX_DOL_URL_ROOT . $this->_oSites->_oConfig->getBaseUri() . "share_popup/" . $this->_aSite['id'];
                $sScript = <<<EOF
                    <script type="text/javascript">
                    function bx_site_show_share_popup () {
                        if (!$('#bx_sites_share_popup').length) {
                            $('<div id="bx_sites_share_popup" style="display: none;"></div>').prependTo('body');
                        }
                        
                        $('#bx_sites_share_popup').load(
                            '{$sUrlSharePopup}',
                            function() {
                                $(this).dolPopup({fog: {color: '#fff', opacity: .7}});
                            }
                        );
                    }
                    </script>
EOF;
            }
                
            return $oSubscription->getData() . $sScript . $oFunctions->genObjectsActions($aInfo, 'bx_sites');
        }
        
        return '';
    }
    
    function getBlockCode_ViewInformation() 
    {
        return $this->_oTemplate->blockInformation($this->_aSite);;
    }
    
    function getBlockCode_ViewImage()
    {
        $aFile = BxDolService::call('photos', 'get_photo_array', array($this->_aSite['photo'], 'file'), 'Search');
        $sImage = $aFile['no_image'] ? '' : $aFile['file'];
        $sVote = '';
        $sSiteUrl = $this->_aSite['url'];
        
        if (strncasecmp($sSiteUrl, 'http://', 7) != 0)
            $sSiteUrl = 'http://' . $sSiteUrl;
        
        if ($this->_oConfig->isVotesAllowed() && 
            $this->_oSites->oPrivacy->check('rate', 
            $this->_aSite['id'], $this->_oSites->iOwnerId))
        {
            bx_import('BxTemplVotingView');
            $oVotingView = new BxTemplVotingView('bx_sites', $this->_aSite['id']);
            
            if ($oVotingView->isEnabled())
                $sVote = $oVotingView->getBigVoting();
        }
        
        return $this->_oTemplate->parseHtmlByName('view_image.html', 
            array(
                'title' => $this->_aSite['title'],
                'site_url' => $sSiteUrl,
                'site_url_view' => $this->_aSite['url'],
                'image' => $sImage ? $sImage : $this->_oTemplate->getIconUrl('no-photo-110.png'),
                'image_fave' => $this->_oTemplate->getIconUrl('action_fave.png'),
                'image_view' => $this->_oTemplate->getIconUrl('eye.png'),
                'vote' => $sVote,
                'view_count' => $this->_aSite['views']
            ));
    }
     
    function getBlockCode_ViewDescription()
    {
        return $this->_oTemplate->parseHtmlByName('view_description.html', 
            array(
                'description' => $this->_aSite['description']
            ));
    }
     
    function getBlockCode_ViewComments() 
    {
        if ($this->_oConfig->isCommentsAllowed() && 
            $this->_oSites->oPrivacy->check('comments', 
            $this->_aSite['id'], $this->_oSites->iOwnerId))
        {
            bx_import('BxTemplCmtsView');
            $o = new BxTemplCmtsView ('bx_sites', $this->_aSite['id']);
            
            if ($o->isEnabled())
                return $o->getCommentsFirst();
        }
        
        return '';
    }
}
?>