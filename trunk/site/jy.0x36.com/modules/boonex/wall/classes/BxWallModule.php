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

bx_import('BxDolModule');
bx_import('BxDolPaginate');
bx_import('BxDolRssFactory');
bx_import('BxDolAdminSettings');

require_once('BxWallResponse.php');
require_once('BxWallCmts.php');

define('BX_WALL_FILTER_ALL', 'all');
define('BX_WALL_FILTER_OWNER', 'owner');
define('BX_WALL_FILTER_OTHER', 'other');

define('BX_WALL_MEDIA_CATEGORY_NAME', 'wall');

class BxWallModule extends BxDolModule {
    var $_bJsMode;
    var $_iOwnerId;
    var $_aHandlers;
    var $_sJsPostObject;
    var $_sJsViewObject;
    var $_aPostElements;    
       
    var $_sDividerTemplate;
	var $_sBalloonTemplate;
	var $_sCmtPostTemplate;
	var $_sCmtViewTemplate;
	var $_sCmtTemplate;
		
	/**
	 * Constructor
	 */
	function BxWallModule($aModule) {
	    parent::BxDolModule($aModule);
	    $this->_oConfig->init($this->_oDb);
        
        $this->_bJsMode = false;
	    $this->_iOwnerId = 0;
	    
	    $this->_aHandlers = array();
	    foreach($this->_oDb->getHandlers() as $aHandler)
	       $this->_aHandlers[$aHandler['alert_unit'] . '_' . $aHandler['alert_action']] = $aHandler;

	    $this->_sJsPostObject = 'oWallPost';
	    $this->_sJsViewObject = 'oWallView';

        //--- Define Membership Actions ---//
        defineMembershipActions(array('wall post comment', 'wall delete comment'), 'ACTION_ID_');
	}	
	
	/**
	 * 
	 * Admin Settings Methods
	 * 
	 */
	function getSettingsForm($mixedResult) {
	    $iId = (int)$this->_oDb->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name`='Wall'");
	    if(empty($iId))
	       return MsgBox('_wall_msg_no_results');
	       
        $oSettings = new BxDolAdminSettings($iId, BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'admin');
        $sResult = $oSettings->getForm();
        	       
        if($mixedResult !== true && !empty($mixedResult))
            $sResult = $mixedResult . $sResult;

        return $sResult;
	}
	function setSettings($aData) {
	    $iId = (int)$this->_oDb->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name`='Wall'");
	    if(empty($iId))
	       return MsgBox(_t('_wall_msg_no_results'));
	       
	    $oSettings = new BxDolAdminSettings($iId);
	    return $oSettings->saveChanges($_POST);
	}
	
	/**
	 * ACTION METHODS
	 * Post somthing on the wall.
	 *
	 * @return string with JavaScript code.
	 */
    function actionPost() {
    	$sResult = "parent." . $this->_sJsPostObject . "._loading(null, false);\n";

        $this->_iOwnerId = (int)$_POST['WallOwnerId'];
    	if (!$this->_isCommentPostAllowed(true))
			return "<script>" . $sResult . "alert('" . bx_js_string(_t('_wall_msg_not_allowed_post')) . "');</script>";

	    $sPostType = process_db_input($_POST['WallPostType'], BX_TAGS_STRIP);
	    $sContentType = process_db_input($_POST['WallContentType'], BX_TAGS_STRIP);

	    $sMethod = "_process" . ucfirst($sPostType) . ucfirst($sContentType);
	    if(method_exists($this, $sMethod)) {
	        $aResult = $this->$sMethod();
	        if((int)$aResult['code'] == 0) {
		        $iId = $this->_oDb->insertEvent(array(
	    	       'owner_id' => $this->_iOwnerId,
	    	       'object_id' => $aResult['object_id'],
	    	       'type' => $this->_oConfig->getCommonPostPrefix() . $sPostType,
	    	       'action' => '',
	    	       'content' => process_db_input($aResult['content'], BX_TAGS_NO_ACTION, BX_SLASHES_NO_ACTION),
	    	       'title' => process_db_input($aResult['title'], BX_TAGS_NO_ACTION, BX_SLASHES_NO_ACTION),
	    	       'description' => process_db_input($aResult['description'], BX_TAGS_NO_ACTION, BX_SLASHES_NO_ACTION)
	    	    ));
	
				$sResult = "parent.$('form#WallPost" . ucfirst($sPostType) . "').find(':input:not(:button,:submit,[type = hidden],[type = radio],[type = checkbox])').val('');\n";
	    	    $sResult .= "parent." . $this->_sJsPostObject . "._getPost(null, " . $iId . ");";
	        }
	        else
	        	$sResult .= "alert('" . bx_js_string(_t($aResult['message'])) . "');";
	    }
	    else 
	       $sResult .= "alert('" . bx_js_string(_t('_wall_msg_failed_post')) . "');";

	    return '<script>' . $sResult . '</script>';
	}
	/**
     * Delete post from the wall. Allow to wall owner only.
	 *
	 * @return string with JavaScript code.
	 */
	function actionDelete() {	    
	    $this->_iOwnerId = (int)$_POST['WallOwnerId'];
	    if(!$this->_isCommentDeleteAllowed(true))
            return '{code: 1}';

        $iEventId = (int)$_POST['WallEventId'];
	    $bResult = $this->_oDb->deleteEvent(array('id' => $iEventId));

	    if($bResult)
            return '{code: 0, id: ' . $iEventId . '}';
        else 
            return '{code: 2}';
	}
	/**
	 * Get post content. 
	 *
	 * @return string with post.
	 */
	function actionGetPost() {
	    $this->_bJsMode = true;
	    $this->_iOwnerId = (int)$_POST['WallOwnerId'];
	    $iPostId = (int)$_POST['WallPostId'];

	    $aEvents = $this->_oDb->getEvents(array('type' => 'id', 'object_id' => $iPostId));
	    return $this->getCommon($aEvents[0]);
	}
	/**
	 * Get posts content.
	 *
	 * @return string with posts.
	 */
	function actionGetPosts() {
	    $this->_bJsMode = true;
	    $this->_iOwnerId = (int)$_POST['WallOwnerId'];
	    $iStart = (int)$_POST['WallStart'];
	    $iPerPage = isset($_POST['WallPerPage']) ? (int)$_POST['WallPerPage'] : $this->_oConfig->getPerPage();
	    $sFilter = isset($_POST['WallFilter']) ? process_db_input($_POST['WallFilter'], BX_TAGS_STRIP) : BX_WALL_FILTER_ALL;

	    return $sContent = $this->_getPosts('desc', $iStart, $iPerPage, $sFilter);
	}
	/**
	 * Get paginate content.
	 *
	 * @return string with paginate.
	 */
	function actionGetPaginate() {
	    $this->_iOwnerId = (int)$_POST['WallOwnerId'];
	    $iStart = (int)$_POST['WallStart'];
	    $iPerPage = isset($_POST['WallPerPage']) ? (int)$_POST['WallPerPage'] : $this->_oConfig->getPerPage();
	    $sFilter = isset($_POST['WallFilter']) ? process_db_input($_POST['WallFilter'], BX_TAGS_STRIP) : BX_WALL_FILTER_ALL;
	    
	    $oPaginate = $this->_getPaginate($sFilter);
	    return $oPaginate->getPaginate($iStart, $iPerPage);
	}
	/**
	 * Get photo uploading form.
	 *
	 * @return string with form.
	 */
	function actionGetPhotoUploaders($iOwnerId) {
        $this->_iOwnerId = $iOwnerId;        
        return BxDolService::call('photos', 'get_uploader_form', array(array('mode' => 'single', 'category' => 'wall', 'album'=>_t('_wall_photo_album', getNickName(getLoggedId())), 'from_wall' => 1, 'owner_id' => $this->_iOwnerId)), 'Uploader');
	}
	/**
	 * Get music uploading form.
	 *
	 * @return srting with form.
	 */	
	function actionGetMusicUploaders($iOwnerId) {
        $this->_iOwnerId = $iOwnerId;
        return BxDolService::call('sounds', 'get_uploader_form', array(array('mode' => 'single', 'category' => 'wall', 'album'=>_t('_wall_sound_album', getNickName(getLoggedId())), 'from_wall' => 1, 'owner_id' => $this->_iOwnerId)), 'Uploader');
	}	
	/**
	 * Get video uploading form.
	 *
	 * @return string with form.
	 */	
	function actionGetVideoUploaders($iOwnerId) {
	    $this->_iOwnerId = $iOwnerId;
        return BxDolService::call('videos', 'get_uploader_form', array(array('mode' => 'single', 'category' => 'wall', 'album'=>_t('_wall_video_album', getNickName(getLoggedId())), 'from_wall' => 1, 'owner_id' => $this->_iOwnerId)), 'Uploader');
	}
	/**
	 * Get RSS for specified owner.
	 *
	 * @param string $sUsername wall owner username
	 * @return string with RSS.
	 */
	function actionRss($sUsername) {
	    $aOwner = $this->_oDb->getUser($sUsername, 'username');
	    
	    $aEvents = $this->_oDb->getEvents(array(
            'type' => 'owner', 
            'owner_id' => $aOwner['id'], 
            'order' => 'desc', 
            'start' => 0, 
            'count' => $this->_oConfig->getRssLength(), 
            'filter' => ''
        ));
        
        $sRssBaseUrl = $this->_oConfig->getBaseUri() . 'index/' . $aOwner['username'] . '/';
        $aRssData = array();
        foreach($aEvents as $aEvent) {
            if(empty($aEvent['title'])) continue;

        	$aRssData[$aEvent['id']] = array(
        	   'UnitID' => $aEvent['id'],
        	   'OwnerID' => $aOwner['id'],
        	   'UnitTitle' => $aEvent['title'],
        	   'UnitLink' => BX_DOL_URL_ROOT . $sRssBaseUrl . '#wall-event-' . $aEvent['id'],
        	   'UnitDesc' => $aEvent['description'],
        	   'UnitDateTimeUTS' => $aEvent['date'],
        	   'UnitIcon' => ''
            );
        }
        

	    $oRss = new BxDolRssFactory();
	    return $oRss->GenRssByData($aRssData, $aOwner['username'] . ' ' . _t('_wall_rss_caption'), $sRssBaseUrl);
	}
	
	
	
	/**
	 * SERVICE METHODS
	 * Process alert.
	 *
	 * @param BxDolAlerts $oAlert an instance with accured alert.
	 */
	function serviceResponse($oAlert) {
        $oResponse = new BxWallResponse($this);
        $oResponse->response($oAlert);
	}
	/**
	 * Display Post block on profile page.
	 *
	 * @param integer $mixed - owner ID or Username.
	 * @return array containing block info.
	 */
	function servicePostBlock($mixed, $sType = 'id') {
        $aOwner = $this->_oDb->getUser($mixed, $sType);
	    $this->_iOwnerId = $aOwner['id'];

        if($aOwner['id'] != $this->_getAuthorId() && !$this->_isCommentPostAllowed())
            return ""; 

	    $aTopMenu = array(            
            'wall-ptype-text' => array('href' => 'javascript:void(0)', 'onclick' => 'javascript:' . $this->_sJsPostObject . '.changePostType(this)', 'class' => 'wall-ptype-ctl', 'icon' => $this->_oTemplate->getIconUrl('post_text.png'), 'title' => _t('_wall_write'), 'active' => 1),
            'wall-ptype-link' => array('href' => 'javascript:void(0)', 'onclick' => 'javascript:' . $this->_sJsPostObject . '.changePostType(this)', 'class' => 'wall-ptype-ctl', 'icon' => $this->_oTemplate->getIconUrl('post_link.png'), 'title' => _t('_wall_share_link'))
	    );	    
        
        if($this->_oDb->isModule('photos'))
            $aTopMenu['wall-ptype-photo'] = array('href' => 'javascript:void(0)', 'onclick' => 'javascript:' . $this->_sJsPostObject . '.changePostType(this)', 'class' => 'wall-ptype-ctl', 'icon' => $this->_oTemplate->getIconUrl('post_photo.png'), 'title' => _t('_wall_add_photo'));
        if($this->_oDb->isModule('sounds'))
            $aTopMenu['wall-ptype-music'] = array('href' => 'javascript:void(0)', 'onclick' => 'javascript:' . $this->_sJsPostObject . '.changePostType(this)', 'class' => 'wall-ptype-ctl', 'icon' => $this->_oTemplate->getIconUrl('post_music.png'), 'title' => _t('_wall_add_music'));
        if($this->_oDb->isModule('videos'))
            $aTopMenu['wall-ptype-video'] = array('href' => 'javascript:void(0)', 'onclick' => 'javascript:' . $this->_sJsPostObject . '.changePostType(this)', 'class' => 'wall-ptype-ctl', 'icon' => $this->_oTemplate->getIconUrl('post_video.png'), 'title' => _t('_wall_add_video'));        

	    //--- Prepare JavaScript paramaters ---//
        ob_start();
?>
        var <?=$this->_sJsPostObject; ?> = new BxWallPost({
            sActionUrl: '<?=BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri(); ?>',
            sObjName: '<?=$this->_sJsPostObject; ?>',
            iOwnerId: <?=$this->_iOwnerId; ?>,
            sAnimationEffect: '<?=$this->_oConfig->getAnimationEffect(); ?>',
            iAnimationSpeed: '<?=$this->_oConfig->getAnimationSpeed(); ?>'
        });        
<?
		$sJsContent = ob_get_clean();

		//--- Parse template ---//		
		$sHomeUrl = $this->_oConfig->getHomeUrl();
	    $aVariables = array (
            'post_js_content' => $sJsContent,
            'post_js_object' => $this->_sJsPostObject,

            'post_wall_text' => $this->_getWriteForm(),
            'post_wall_link' => $this->_getShareLinkForm(),
            'post_wall_photo' => '',
            'post_wall_video' => '',
            'post_wall_music' => '',
        );                

        $GLOBALS['oTopMenu']->setCurrentProfileID((int)$this->_iOwnerId);

        $this->_oTemplate->addCss('post.css');
        $this->_oTemplate->addJs(array('main.js', 'post.js'));
	    $sRes = array($this->_oTemplate->parseHtmlByName('post.html', $aVariables), $aTopMenu, array(), true, 'getBlockCaptionMenu');
	    return $sRes;
	}
	function serviceViewBlock($mixed, $iStart = -1, $iPerPage = -1, $sFilter = '', $sType = 'id') {
	    $aOwner = $this->_oDb->getUser($mixed, $sType);
	    $this->_iOwnerId = $aOwner['id'];

	    $oSubscription = new BxDolSubscription();
	    $aButton = $oSubscription->getButton($this->getUserId(), 'bx_wall', '', $this->_iOwnerId);

	    $aTopMenu = array(            
            'wall-view-all' => array('href' => 'javascript:void(0)', 'onclick' => 'javascript:' . $this->_sJsViewObject . '.filterPosts(this)', 'title' => _t('_wall_view_all'), 'active' => 1),
            'wall-view-owner' => array('href' => 'javascript:void(0)', 'onclick' => 'javascript:' . $this->_sJsViewObject . '.filterPosts(this)', 'title' => _t('_wall_view_owner', $aOwner['username'])),
            'wall-view-other' => array('href' => 'javascript:void(0)', 'onclick' => 'javascript:' . $this->_sJsViewObject . '.filterPosts(this)', 'title' => _t('_wall_view_other')),
            'wall-get-rss' => array('href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'rss/' . $aOwner['username'] . '/', 'target' => '_blank', 'title' => _t('_wall_get_rss')),
	    	'wall-subscription' => array('href' => 'javascript:void(0);', 'onclick' => 'javascript:' . $aButton['script'] . '', 'title' => $aButton['title']),
	    );
	    
	    if($iStart == -1)
	       $iStart = 0;
	    if($iPerPage == -1)
	       $iPerPage = $this->_oConfig->getPerPage();
        if(empty($sFilter))
            $sFilter = BX_WALL_FILTER_ALL;
	       	    
        $sContent = $this->_getPosts('desc', $iStart, $iPerPage, $sFilter);
        
        //--- Prepare JavaScript paramaters ---//
        ob_start();
?>
        var <?=$this->_sJsViewObject; ?> = new BxWallView({
            sActionUrl: '<?=BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri(); ?>',
            sObjName: '<?=$this->_sJsViewObject; ?>',
            iOwnerId: <?=$this->_iOwnerId; ?>,
            sAnimationEffect: '<?=$this->_oConfig->getAnimationEffect(); ?>',
            iAnimationSpeed: '<?=$this->_oConfig->getAnimationSpeed(); ?>'
        });
<?      
		$sJsContent = ob_get_clean();

        $sHomeUrl = $this->_oConfig->getHomeUrl();
        $oPaginate = $this->_getPaginate($sFilter);
        $aVariables = array(
            'content' => $sContent,
            'view_js_content' => $sJsContent,
            'view_js_object' => $this->_sJsViewObject,
            'paginate' => $oPaginate->getPaginate()
        );
        
        $GLOBALS['oTopMenu']->setCurrentProfileID((int)$this->_iOwnerId);

        $this->_oTemplate->addCss('view.css');
        $this->_oTemplate->addJs(array('main.js', 'view.js'));
        return array($this->_oTemplate->parseHtmlByName('view.html', $aVariables), $aTopMenu, array(), true, 'getBlockCaptionMenu');
	}
	
	function serviceUpdateHandlers($sModuleUri = 'all', $bInstall = true) {
        $aModules = $sModuleUri == 'all' ? $this->_oDb->getModules() : array($this->_oDb->getModuleByUri($sModuleUri));
	    
	    foreach($aModules as $aModule) {
	       if(!BxDolRequest::serviceExists($aModule, 'get_wall_data')) continue;
	       
           $aData = BxDolService::call($aModule['uri'], 'get_wall_data');
           if($bInstall)
	           $this->_oDb->insertData($aData);
           else
               $this->_oDb->deleteData($aData);
        }

        BxDolAlerts::cache();
	}

    function serviceGetMemberMenuItem() {

        $oMemberMenu = bx_instance('BxDolMemberMenu');

        $aLanguageKeys = array(
            'wall' => _t( '_wall_pc_view' ),
        );

        // fill all necessary data;
        $aLinkInfo = array(
            'item_img_src'  => $this -> _oTemplate -> getIconUrl ('member_menu_sub_wall.png'),
            'item_img_alt'  => $aLanguageKeys['wall'],
            'item_link'     => BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri(), 
            'item_onclick'  => null,
            'item_title'    => $aLanguageKeys['wall'],
            'extra_info'    => null,
        );

        return $oMemberMenu -> getGetExtraMenuLink($aLinkInfo);
    }
	function serviceGetSubscriptionParams($sUnit, $sAction, $iObjectId) {
		$sUnit = str_replace('bx_', '_', $sUnit);
		if(empty($sAction))
			$sAction = 'main';

		$aEvents = $this->_oDb->getEvents(array('type' => 'id', 'object_id' => $iObjectId));
		if(empty($aEvents[0]) || !is_array($aEvents[0]))
			return array(
				'template' => array(
					'Subscription' => '', 
					'ViewLink' => ''
				)
			);

		$aProfileInfo = getProfileInfo($aEvents[0]['owner_id']);
		return array(
			'template' => array(
				'Subscription' => _t($sUnit . '_sbs_' . $sAction, $aProfileInfo['NickName']), 
				'ViewLink' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri()  . 'index/' . $aProfileInfo['NickName']
			)
		);
	}

	/**
	 * Common public methods.
	 * Is used to display events on the Wall.
	 */
	function getSystem($aEvent) {
	    $sResult = "";
	    
	    $sMethod = 'display' . str_replace(' ', '', ucwords(str_replace('_', ' ', $aEvent['type'] . '_' . $aEvent['action'])));
        if(method_exists($this->_oTemplate, $sMethod))
            $aResult = $this->_oTemplate->$sMethod($aEvent);
        else if(isset($this->_aHandlers[$aEvent['type'] . '_' . $aEvent['action']])){
            $aEvent['js_mode'] = $this->_bJsMode;
            
            $aHandler = $this->_aHandlers[$aEvent['type'] . '_' . $aEvent['action']];            
            $aResult = BxDolService::call($aHandler['module_uri'], $aHandler['module_method'], array($aEvent), $aHandler['module_class']);
        }
        
        if((empty($aEvent['title']) && !empty($aResult['title'])) || (empty($aEvent['description']) && !empty($aResult['description'])))
            $this->_oDb->updateEvent(array(
				'title' => process_db_input($aResult['title'], BX_TAGS_STRIP), 
				'description' => process_db_input($aResult['description'], BX_TAGS_STRIP)
			), $aEvent['id']);
        
        $oComments = new BxWallCmts($this->_oConfig->getCommentSystemName(), $aEvent['id']);
        return $this->_oTemplate->parseHtmlByContent($aResult['content'], array(
        	'post_id' => $aEvent['id'],
        	'bx_if:post_delete' => array(
                'condition' => $this->_isCommentDeleteAllowed(),
                'content' => array(
        			'js_view_object' => $this->_sJsViewObject,
		        	'post_id' => $aEvent['id'],
                    'post_delete_txt' => _t('_wall_post_delete')
		        )
		    ), 
            'comments_content' => $oComments->getCommentsFirst('comment')
        ));
	}
	function getCommon($aEvent) {	    
	    $sPrefix = $this->_oConfig->getCommonPostPrefix();	    
	    if(strpos($aEvent['type'], $sPrefix) !== 0) return "";
        	    
	    if((int)$aEvent['content'] > 0 && in_array($aEvent['type'], array($sPrefix . 'photos', $sPrefix . 'sounds', $sPrefix . 'videos'))) {
	        $sMediaType = str_replace($sPrefix, '', $aEvent['type']);
	        $aEvent = array_merge($aEvent, $this->_getCommonMedia($sMediaType, (int)$aEvent['content']));
            
            if((int)$aEvent['content'] > 0)
                $aEvent['content'] = _t('_wall_content_not_ready');
            else 
                $this->_oDb->updateEvent(array(
					'content' => process_db_input($aEvent['content'], BX_TAGS_VALIDATE), 
					'title' => process_db_input($aEvent['title'], BX_TAGS_STRIP), 
					'description' => process_db_input($aEvent['description'], BX_TAGS_STRIP)
				), $aEvent['id']);
	    }
	    	    
	    $aAuthor = $this->_oDb->getUser($aEvent['object_id']);	    
	    $aVariables = array (
            'author_thumbnail' => get_member_icon($aAuthor['id']),
            'author_url' => getProfileLink($aAuthor['id']),
            'author_username' => $aAuthor['username'],
            'post_id' => $aEvent['id'],                    
            'post_ago' => $aEvent['ago'],
            'bx_if:post_delete' => array(
                'condition' => $this->_isCommentDeleteAllowed(),
                'content' => array(
	    			'js_view_object' => $this->_sJsViewObject,
                    'post_id' => $aEvent['id'],
                    'post_delete_txt' => _t('_wall_post_delete')
                )
            ),
            'post_content' => $aEvent['content'],
        );
        switch(str_replace($sPrefix, '', $aEvent['type'])) {
            case 'text':
                $aVariables = array_merge($aVariables, array('post_wrote' => _t("_wall_wrote"), 'post_content' => $aVariables['post_content']));
                break;
            case 'link':
                $aVariables = array_merge($aVariables, array('post_wrote' => _t("_wall_shared_link")));
                break;
            case 'photos':
                $aVariables = array_merge($aVariables, array('post_wrote' => _t("_wall_added_photo")));
                break;
            case 'videos':
                $aVariables = array_merge($aVariables, array('post_wrote' => _t("_wall_added_video")));
                break;
            case 'sounds':
                $aVariables = array_merge($aVariables, array('post_wrote' => _t("_wall_added_music")));
                break;
        }
        
        $sType = isset($aEvent['action']) && empty($aEvent['action']) ? 'reply' : 'comment';	    
        $oComments = new BxWallCmts($this->_oConfig->getCommentSystemName(), $aEvent['id']);
        $aVariables = array_merge($aVariables, array('comments_content' => $oComments->getCommentsFirst($sType)));	    
                
        return $this->_oTemplate->parseHtmlByTemplateName('balloon', $aVariables);
	}
	/**
	 * Private Methods
	 * Is used for actions processing.
	 */		 
	function _processTextUpload() {
	    $aOwner = $this->_oDb->getUser($this->_getAuthorId());
	    
        $sContent = get_magic_quotes_gpc() ? stripslashes($_POST['content']) : $_POST['content'];
        $sContent = nl2br(strip_tags($sContent));

	    if(empty($sContent))
	    	return array(
	    		'code' => 1,
	    		'message' => '_wall_msg_text_empty_message'
	    	);
	    
	    return array(
	    	'code' => 0,
            'object_id' => $aOwner['id'],
            'content' => $sContent,
            'title' => $aOwner['username'] . ' ' . _t('_wall_wrote'),
            'description' => $sContent
        );
	}
	function _processLinkUpload() {
	    $aOwner = $this->_oDb->getUser($this->_getAuthorId());
	    
	    $sUrl = trim(process_db_input($_POST['url'], BX_TAGS_STRIP));
	    if(empty($sUrl))
	    	return array(
	    		'code' => 1,
	    		'message' => '_wall_msg_link_empty_link'
	    	);

	    $sContent = bx_file_get_contents($sUrl);

        preg_match("/<title>(.*)<\/title>/", $sContent, $aMatch);
        $sTitle = $aMatch[1];
        
        preg_match("/<meta.*name[='\" ]+description['\"].*content[='\" ]+(.*)['\"].*><\/meta>/", $sContent, $aMatch);
        $sDescription = $aMatch[1];

	    return array(
	       'object_id' => $aOwner['id'],
	       'content' => $this->_oTemplate->parseHtmlByTemplateName('common_link', array(
    	       'title' => $sTitle,
    	       'url' => strpos($sUrl, 'http://') === false && strpos($sUrl, 'https://') === false ? 'http://' . $sUrl : $sUrl,
    	       'description' => $sDescription
	       )),
	       'title' => $aOwner['username'] . ' ' . _t('_wall_shared_link'),
	       'description' => $sUrl . ' - ' . $sTitle
	    );
	}	

	/**
	 * Private Methods
	 * Is used for content displaying 
	 */
	function _getCommonMedia($sType, $iObject) {
	    $aConverter = array('photos' => 'photo', 'sounds' => 'music', 'videos' => 'video');
	    
	    $aMediaInfo = BxDolService::call($sType, 'get_' . $aConverter[$sType] . '_array', array($iObject, 'browse'), 'Search');
	    $aOwner = $this->_oDb->getUser($aMediaInfo['owner']);

	    $sAddedMediaTxt = _t('_wall_added_' . $sType);
	    
	    $sContent = '';
	    if(!empty($aMediaInfo) && is_array($aMediaInfo) && !empty($aMediaInfo['file']))
    	    $aContent = array(
                'title' => $aOwner['username'] . ' ' . $sAddedMediaTxt,
                'description' => $aMediaInfo['description'],
                'content' => $this->_oTemplate->parseHtmlByTemplateName('common_media', array(
                    'image_url' =>  isset($aMediaInfo['file']) ? $aMediaInfo['file'] : '',
                    'image_width' => isset($aMediaInfo['width']) ? (int)$aMediaInfo['width'] : 0,
                    'image_height' => isset($aMediaInfo['height']) ? (int)$aMediaInfo['height'] : 0,
                    'link' => isset($aMediaInfo['url']) ? $aMediaInfo['url'] : '',
                    'title' => isset($aMediaInfo['title']) ? bx_html_attribute($aMediaInfo['title']) : '',
                    'description' => isset($aMediaInfo['description']) ? $aMediaInfo['description'] : ''
                ))
            );
        else 
            $aContent = array('title' => '', 'description' => '', 'content' => $iObject);
            
        return $aContent;
	}
	function _getPaginate($sFilter) {
       return new BxDolPaginate(array(
       		'page_url' => 'javascript:void(0);',
            'start' => 0, 
            'count' => $this->_oDb->getEventsCount($this->_iOwnerId, $sFilter),
            'per_page' => $this->_oConfig->getPerPage(),            
            'on_change_page' => $this->_sJsViewObject . '.changePage({start}, {per_page}, \'' . $sFilter . '\')',
            'on_change_per_page' => $this->_sJsViewObject . '.changePerPage(this, \'' . $sFilter . '\')',
            'page_reloader' => true
        ));	    
	}
	function _getPosts($sOrder, $iStart, $iPerPage, $sFilter) {
        $aEvents = $this->_oDb->getEvents(array('type' => 'owner', 'owner_id' => $this->_iOwnerId, 'order' => $sOrder, 'start' => $iStart, 'count' => $iPerPage, 'filter' => $sFilter));

        $iDays = -1;
        $sDividerIcon = $this->_oTemplate->getIconUrl('divider_caption.png');
        $sContent = $this->_oTemplate->parseHtmlByTemplateName('divider', array('cpt_class' => 'wall-divider-today ' . ($aEvents[0]['days'] == $aEvents[0]['today'] ? 'visible' : 'hidden'), 'cpt_icon_url' => $sDividerIcon, 'content' => _t("_wall_today")));
        foreach($aEvents as $aEvent) {
            if(!$this->_oConfig->useFullCompilation() && !empty($aEvent['action']))
                $aEvent['content'] = $this->getSystem($aEvent);
            else if(empty($aEvent['action']))
                $aEvent['content'] = $this->getCommon($aEvent);

            $sContent .= !empty($aEvent['content']) ? $this->_getDivider($iDays, $aEvent, $sDividerIcon) : "";
            $sContent .= $aEvent['content'];
        }
        return $sContent;
	}
	function _getDivider(&$iDays, &$aEvent, $sIcon) {
	    $sResult = "";        
	    
	    if($iDays != $aEvent['days']) {
            if($aEvent['days'] == $aEvent['today']) {
                $iDays = $aEvent['days'];
                return "";
            }
            
            $aDate = split(' ', $aEvent['print_date']);
            $sDate = (substr($aDate[0], 0, 1) == '_' ? _t($aDate[0]) : $aDate[0]) . ' ' . $aDate[1];

            $sResult = $this->_oTemplate->parseHtmlByTemplateName('divider', array('cpt_class' => 'wall-divider', 'cpt_icon_url' => $sIcon, 'content' => $sDate));
            $iDays = $aEvent['days'];
        }
        else 
            $sResult = $this->_oTemplate->parseHtmlByTemplateName('divider', array('cpt_class' => 'wall-divider-nerrow', 'cpt_icon_url' => '', 'content' => ''));
        return $sResult;
	}	
	function _getWriteForm() {
	    $aForm = array(
            'form_attrs' => array(
                'name' => 'WallPostText',
                'action' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'post/',
                'method' => 'post',
                'enctype' => 'multipart/form-data',
                'target' => 'WallPostIframe',
                'onsubmit' => 'javascript:return ' . $this->_sJsPostObject . '.postSubmit(this);'
            ),
            'inputs' => array(                
                'content' => array(
                    'type' => 'textarea',
                    'name' => 'content',
                    'caption' => '',
                    'colspan' => true
                ),
                'submit' => array(
                    'type' => 'submit',
                    'name' => 'submit',
                    'value' => _t('_wall_post'),
                    'colspan' => true
                )
            ),
        );
        $aForm['inputs'] = array_merge($aForm['inputs'], $this->_addHidden('text'));
        
        $oForm = new BxTemplFormView($aForm);
        return $oForm->getCode();
	}
	function _getShareLinkForm() {
        $aForm = array(
            'form_attrs' => array(
                'name' => 'WallPostLink',
                'action' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'post/',
                'method' => 'post',
                'enctype' => 'multipart/form-data',
                'target' => 'WallPostIframe',
                'onsubmit' => 'javascript:return ' . $this->_sJsPostObject . '.postSubmit(this);'
            ),
            'inputs' => array(
                'title' => array(
                    'type' => 'text',
                    'name' => 'url',
                    'caption' => _t('_wall_link_url'),
                ),
                'submit' => array(
                    'type' => 'submit',
                    'name' => 'submit',
                    'value' => _t('_wall_post'),
                    'colspan' => true
                )
            ),
        );
        $aForm['inputs'] = array_merge($aForm['inputs'], $this->_addHidden('link'));
        
        $oForm = new BxTemplFormView($aForm);
        return $oForm->getCode();
    }
    function _addHidden($sPostType = "photos", $sContentType = "upload", $sAction = "post") {
        return array(
            'WallOwnerId' => array (
                'type' => 'hidden',
                'name' => 'WallOwnerId',
                'value' => $this->_iOwnerId,
            ),
            'WallPostAction' => array (
                'type' => 'hidden',
                'name' => 'WallPostAction',
                'value' => $sAction,
            ),
            'WallPostType' => array (
                'type' => 'hidden',
                'name' => 'WallPostType',
                'value' => $sPostType,
            ),
            'WallContentType' => array (
                'type' => 'hidden',
                'name' => 'WallContentType',
                'value' => $sContentType,
            ),
        );
    }
    function _isCommentPostAllowed($bPerform = false) {
	    if(isAdmin())
            return true;
        
	    $iAuthorId = $this->_getAuthorId();	    
	    if($iAuthorId == 0 && getParam('wall_enable_guest_comments') == 'on')
       		return true;
        
       	if(isBlocked($this->_iOwnerId, $iAuthorId))
			return false;
		
	    $aCheckResult = checkAction($iAuthorId, ACTION_ID_WALL_POST_COMMENT, $bPerform);
        return $aCheckResult[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
	}	
	function _isCommentDeleteAllowed($bPerform = false) {
	    if(isAdmin())
            return true;

        $iUserId = (int)$this->_getAuthorId();
	    if($this->_iOwnerId == $iUserId)
	       return true;

	    $aCheckResult = checkAction($iUserId, ACTION_ID_WALL_DELETE_COMMENT, $bPerform);
        return $aCheckResult[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
	}
	function _getAuthorId() {
		return !isLogged() ? 0 : getLoggedId();
	}
	function _getAuthorPassword() {
		return !isLogged() ? '' : getLoggedPassword();
	}
}
?>
