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

require_once(BX_DIRECTORY_PATH_INC . 'header.inc.php' );
bx_import('BxTemplCmtsView');
bx_import('BxTemplSearchResultText');

define('BX_BLOGS_IMAGES_PATH', BX_DIRECTORY_PATH_ROOT . "media/images/blog/");
define('BX_BLOGS_IMAGES_URL', BX_DOL_URL_ROOT . "media/images/blog/");

class BxBlogsSearchUnit extends BxTemplSearchResultText {
	var $sHomePath;
	var $sHomeUrl;
	var $iPostViewType;

	var $bAdminMode;
	var $bShowCheckboxes;

	var $aCurrent = array(
		'name' => 'blogposts',
		'title' => '_bx_blog_Blogs',
		'table' => 'bx_blogs_posts',
		'ownFields' => array('PostID', 'PostCaption', 'PostUri', 'PostDate', 'PostText', 'Tags', 'PostPhoto','PostStatus', 'Rate', 'RateCount', 'CommentsCount', 'Categories'),
		'searchFields' => array('PostCaption', 'PostText', 'Tags'),
		'join' => array(
			'profile' => array(
				'type' => 'left',
				'table' => 'Profiles',
				'mainField' => 'OwnerID',
				'onField' => 'ID',
				'joinFields' => array('NickName')
			)
		),
		'restriction' => array(
			'activeStatus' => array('value'=>'approval', 'field'=>'PostStatus', 'operator'=>'='),
			'featuredStatus' => array('value'=>'', 'field'=>'Featured', 'operator'=>'='),
			'owner' => array('value'=>'', 'field'=>'OwnerID', 'operator'=>'='),
			'tag' => array('value'=>'', 'field'=>'Tags', 'operator'=>'like'),
            'tag2' => array('value'=>'', 'field'=>'Tags', 'operator'=>'against', 'paramName'=>'tag'),
			'id'=> array('value'=>'', 'field'=>'PostID', 'operator'=>'='),
            'category_uri'=> array('value'=>'', 'field'=>'Categories', 'operator'=>'against', 'paramName'=>'uri'),
            'allow_view' => array('value'=>'', 'field'=>'allowView', 'operator'=>'in', 'table'=> 'bx_blogs_posts'),
		),
		'paginate' => array('perPage' => 4, 'page' => 1, 'totalNum' => 10, 'totalPages' => 1),
		'sorting' => 'last'
	);

	var $aPermalinks;

	//max sizes of pictures for resizing during upload
	var $iIconSize;
	var $iThumbSize;
	var $iBigThumbSize;
	var $iImgSize;

	var $sSearchedTag;
	
	function BxBlogsSearchUnit($oBlogObject = null) { //2 with author name; 3-without link at title and with image; 4- with member icon
		$this->bShowCheckboxes = false;
		$this->bAdminMode = false;

		$oMain = $this->getBlogsMain();

		$this->iIconSize = $oMain->iIconSize;
		$this->iThumbSize = $oMain->iThumbSize;
		$this->iBigThumbSize = $oMain->iBigThumbSize;
		$this->iImgSize = $oMain->iImgSize;

		if ($oMain->isAdmin()) {
			$this->bAdminMode = true;
			//$this->bShowCheckboxes = true;
		}

		$this->sHomeUrl = $oMain->_oConfig->getHomeUrl();
		$this->sHomePath = $oMain->_oConfig->getHomePath();

		$this->aPermalinks = array(
			'param' => 'permalinks_blogs',
			'enabled' => array(
				'file' => 'blogs/entry/{uri}',
				'category' => 'blogs/posts/{ownerName}/category/{uri}',
				'member' => 'blogs/posts/{ownerName}',
				'tag' => 'blogs/tag/{uri}',
				'browseAll' => 'blogs/',
				'admin_file' => 'blogs/entry/{uri}',
				'admin_category' => 'blogs/posts/{ownerName}/category/{uri}',
				'admin_member' => 'blogs/posts/{ownerName}',
				'admin_tag' => 'blogs/tag/{uri}',
				'admin_browseAll' => 'blogs/'
			),
			'disabled' => array(
				'file' => 'blogs.php?action=show_member_post&post_id={id}',
				'category' => 'blogs.php?action=show_member_blog&ownerID={ownerId}&category={id}',
				'member' => 'blogs.php?action=show_member_blog&ownerID={ownerId}',
				'tag' => 'blogs.php?action=search_by_tag&tagKey={uri}',
				'browseAll' => 'blogs.php',
				'admin_file' => 'blogs.php?action=show_member_post&post_id={id}',
				'admin_category' => 'blogs.php?action=show_member_blog&ownerID={ownerId}&category={id}',
				'admin_member' => 'blogs.php?action=show_member_blog&ownerID={ownerId}',
				'admin_tag' => 'blogs.php?action=search_by_tag&tagKey={uri}',
				'admin_browseAll' => 'blogs.php'
			)
		);

		if(!$oBlogObject) {
			$oBlogObject =  BxDolModule::getInstance('BxBlogsModule');
		}

		if ( $this->bAdminMode || ( is_object($oBlogObject) && ($oBlogObject -> isAllowedApprove() 
			|| $oBlogObject -> isAllowedPostEdit(-1) || $oBlogObject -> isAllowedPostDelete(-1)) )) {

			$this->aCurrent['restriction']['activeStatus'] = '';
		}
		parent::BxBaseSearchResultText();

		$this->iPostViewType = 4;
		$this->sSearchedTag = '';
	}

    function getBlogsMain() {
        return BxDolModule::getInstance('BxBlogsModule');
    }

	function addCustomParts() {
		$oMain = $this->getBlogsMain();
		return $oMain->serviceGetCommonCss();
	}

	function PerformObligatoryInit(&$oBlogsModule, $iPostViewType = 2) {
		$GLOBALS['oBxBlogsModule'] = $oBlogsModule;
		$oMain = $this->getBlogsMain();

		$this->sHomePath = $oMain->_oConfig->getHomePath();
		$this->sHomeUrl = $oMain->_oConfig->getHomeUrl();

		$this->iPostViewType = $iPostViewType;
	}

   	function getCurrentUrl($sType, $iId, $sUri, $aOwner = '') {
		if ($this->bAdminMode) {
			$sType = 'admin_' . $sType;
		}

   		$sLink = $this->aConstants['linksTempl'][$sType];
		$sLink = str_replace('{id}', $iId, $sLink);
		$sLink = str_replace('{uri}', $sUri, $sLink);
		if (is_array($aOwner) && !empty($aOwner)) {
			$sLink = str_replace('{ownerName}', $aOwner['ownerName'], $sLink);
			$sLink = str_replace('{ownerId}', $aOwner['ownerId'], $sLink);
		}

		$oMain = $this->getBlogsMain();
		return ($oMain->isPermalinkEnabled()) ? BX_DOL_URL_ROOT . $sLink : $this->sHomeUrl . $sLink;
	}

    function displaySearchBox ($sCode, $sPaginate = '') {
        $sCode = DesignBoxContent(_t($this->aCurrent['title']), '<div class="dbContent">'.$sCode .'<div class="clear_both"></div></div>'. $sPaginate, 1);
        if (true !== bx_get('searchMode'))
            $sCode = '<div id="page_block_'.$this->id.'">'.$sCode.'<div class="clear_both"></div></div>';
        return $sCode;
    }

	function displaySearchUnit($aResSQL) {
		$iVisitorID = (int)$_COOKIE['memberID'];

		$oMain = $this->getBlogsMain();

		$sTagsSmallIcon = $sClockIcon = $sCommentsIcon = $sCategoryIcon = '';
		if ($oMain->_oTemplate) {
			$sTagsSmallIcon = $oMain->_oTemplate->getIconUrl('tgs.png');
			$sClockIcon = $oMain->_oTemplate->getIconUrl('clock.png');
			$sCommentsIcon = $oMain->_oTemplate->getIconUrl('comments.png');
			$sCategoryIcon = $oMain->_oTemplate->getIconUrl('folder_small.png');
		}

		$iPostID = (int)$aResSQL['id'];
		$sBlogsImagesUrl = BX_BLOGS_IMAGES_URL;

        $bPossibleToView = $oMain->oPrivacy->check('view', $iPostID, $oMain->_iVisitorID);
        if (! $bPossibleToView) return $oMain->_oTemplate->parseHtmlByName('browse_unit_private.html', array('extra_css_class' => ''));

		$sCategories = $aResSQL['Categories'];
		$aCategories = $oMain->getTagLinks($aResSQL['Categories'], 'category', CATEGORIES_DIVIDER);

		$sFriendStyle = '';
		$sPostVote = '';
		$sPostMode = '';
		$sVotePostRating = $this->oRate->getJustVotingElement(0, 0, $aResSQL['Rate']);

		$aProfileInfo = getProfileInfo($aResSQL['ownerId']);
		$sOwnerNickname = process_line_output($aProfileInfo['NickName']);

		$sCategoryName = $aResSQL['Categories'];
		$sPostLink = $this->getCurrentUrl('file', $iPostID, $aResSQL['uri']) . $sCategoryUrlAdd;

		$sAllCategoriesLinks = '';
		if (count($aCategories)>0) {
			foreach ($aCategories as $iKey => $sCatValue) {
				$sCatLink = $this->getCurrentUrl('category', title2uri($sCatValue), title2uri($sCatValue), array('ownerId' => $aResSQL['ownerId'], 'ownerName' => $sOwnerNickname));
				$sCatName = process_line_output($sCatValue);
				$aAllCategoriesLinks[] = '<a href="' . $sCatLink . '">' . $sCatName . '</a>';
			}
			$aAllCategoriesLinkHrefs = implode(", ", $aAllCategoriesLinks);
			$sAllCategoriesLinks = <<<EOF
<span class="margined">
	<span>{$aAllCategoriesLinkHrefs}</span>
</span>
EOF;
		}

		$sAdminCheck = $sAdminStatus = '';
		if ($this->bShowCheckboxes) {
			$sAdminCheck = <<<EOF
<div class="browseCheckbox"><input id="ch{$iPostID}" type="checkbox" name="bposts[]" value="{$iPostID}" /></div>
EOF;

			$sPostStatus = process_line_output($aResSQL['PostStatus']);
			$sAdminStatus = <<<EOF
&nbsp;({$sPostStatus})
EOF;
		}

		$sPostCaption = process_line_output($aResSQL['title']);
		$sPostCaptionHref = <<<EOF
<a class="actions" href="{$sPostLink}">{$sPostCaption}</a>{$sAdminStatus}
EOF;

		if ($this->iPostViewType==3) {
			$sFriendStyle="2";
			$sPostMode = '_post';
			$sPostCaptionHref = '<div class="actions">'.$sPostCaption.'</div>';
		}

		$sDateTime = defineTimeInterval($aResSQL['date']);

		//$oCmtsView = new BxTemplCmtsView ('blogposts', (int)$iPostID);
		$iCommentsCnt = (int)$aResSQL['CommentsCount'];

		$sAuthor = '';
		if ($this->iPostViewType==2) {
			$sAuthor = getProfileLink($aResSQL['ownerId']);
			$sAuthor = '<a href="'.$sAuthor.'">'.$sOwnerNickname.'</a>';
		}

		$sTagsCommas = $aResSQL['tag'];
		//$aTags = split(',', $sTagsCommas);
		$aTags = preg_split("/[;,]/", $sTagsCommas);

		//search by tag skiping
		if ( $this->sSearchedTag != '' && in_array($this->sSearchedTag,$aTags)==false ) return;

		$sTagsHrefs = '';
		$aTagsHrefs = array();
		foreach($aTags as $sTagKey) {
			if ($sTagKey != '') {
				$sTagLink = $this->getCurrentUrl('tag', $iPostID, htmlspecialchars(title2uri($sTagKey)));
				$sTagsHrefAny = <<<EOF
<a href="{$sTagLink}" title="{$sTagKey}">{$sTagKey}</a>
EOF;
				$aTagsHrefs[] = $sTagsHrefAny;
			}
		}
		$sTagsHrefs = implode(", ", $aTagsHrefs);

		$sTags = <<<EOF
<span class="margined">
	<span>{$sTagsHrefs}</span>
</span>
EOF;

		$sPostText = process_html_output($aResSQL['bodyText']);
		//$sPostText = addslashes( clear_xss( trim( process_pass_data($aResSQL['bodyText']))));

		$bFriend = is_friends( $iVisitorID, $aResSQL['ownerId'] );
		$bOwner = ($iVisitorID==$aResSQL['ownerId']) ? true : false;

		$sOwnerThumb = $sPostPicture = '';
		if ($aResSQL['PostPhoto'] && $this->iPostViewType==3) {
			$sSpacerName = getTemplateIcon('spacer.gif');
			$sPostPicture = <<<EOF
<div class="marg_both_left">
	<img alt="{$aResSQL['PostPhoto']}" style="width:{$this->iThumbSize}px; height:{$this->iThumbSize}px; background-image: url({$sBlogsImagesUrl}big_{$aResSQL['PostPhoto']});cursor:pointer;" src="{$sSpacerName}" onclick="javascript: window.open( '{$sBlogsImagesUrl}orig_{$aResSQL['PostPhoto']}', 'blog post', 'width={$this->iImgSize}, height={$this->iImgSize}, menubar=no,status=no,resizable=yes,scrollbars=yes,toolbar=no,location=no' );" />
</div>
EOF;
		}

		if ($this->iPostViewType==4) {
			$sOwnerThumb = $GLOBALS['oFunctions']->getMemberIcon($aResSQL['ownerId'], 'left');
		}

		if ($this->iPostViewType==4 || $this->iPostViewType==1) {
			$iBlogLimitChars = (int)getParam('max_blog_preview');
			$iBlogLimitChars = 200;
			if (strlen($aResSQL['bodyText']) > $iBlogLimitChars) {
				//$sLinkMore = "... <a href=\"".$sPostLink."\">"._t('_Read more')."</a>";
				$sLinkMore = '';
				$sPostText = html_entity_decode( process_line_output($aResSQL['bodyText']) );
				$sPostText = mb_substr( strip_tags($sPostText), 0, $iBlogLimitChars );
			}
		}

		$aUnitReplace = array();
		$aUnitReplace['checkbox'] = $sAdminCheck;
		$aUnitReplace['post_caption'] = $sPostCaptionHref;
		$aUnitReplace['author'] = $sAuthor;
		$aUnitReplace['clock_icon'] = $sClockIcon;
		$aUnitReplace['post_date'] = strtolower($sDateTime);
		$aUnitReplace['category_icon'] = $sCategoryIcon;
		$aUnitReplace['all_categories'] = $sAllCategoriesLinks;
		$aUnitReplace['comments_icon'] = $sCommentsIcon;
		$aUnitReplace['comments_count'] = $iCommentsCnt;
		$aUnitReplace['post_tags'] = $sTags;
		$aUnitReplace['friend_style'] = $sFriendStyle;
		$aUnitReplace['post_uthumb'] = $sOwnerThumb;
		$aUnitReplace['post_picture2'] = $sPostPicture;
		$aUnitReplace['post_description'] = $sPostText;
		$aUnitReplace['post_vote'] = $sVotePostRating;
		$aUnitReplace['post_mode'] = $sPostMode;

		return $oMain->_oTemplate->parseHtmlByTemplateName('blogpost_unit', $aUnitReplace);
	}

	function displayMenu () {
 		$aDBTopMenu = $this->getTopMenu();
		
 		return array( $aDBTopMenu, array() );
	}

	function getTopMenu () {
		$aDBTopMenu = array();
		foreach(array( 'last', 'top') as $sMyMode ) {
			switch( $sMyMode ) {
				case 'top':
					$OrderBy = '`num_com` DESC';
					$sModeTitle  = _t( '_Top' );
				break;
				case 'last':
					$OrderBy = '`PostDate` DESC';
					$sModeTitle  = _t( '_Latest' );
				break;
				/*case 'rand':
					$OrderBy = 'RAND()';
					$sModeTitle  = _t( '_Random' );
				break;*/
			}

			$sLink  = bx_html_attribute($_SERVER['PHP_SELF']) . "?";
			$sLink .= "blogs_mode=".$sMyMode;
			$aDBTopMenu[$sModeTitle] = array('href' => $sLink, 'dynamic' => true, 'active' => ( $sMyMode == $this->aCurrent['sorting'] ));
		}

		return $aDBTopMenu;
	}

	function setSorting () {
		$this->aCurrent['sorting'] = (false !== bx_get('blogs_mode')) ? bx_get('blogs_mode') : $this->aCurrent['sorting'];

		if( $this->aCurrent['sorting'] != 'top' && $this->aCurrent['sorting'] != 'last' && $this->aCurrent['sorting'] != 'score' && $this->aCurrent['sorting'] != 'popular')
			$this->aCurrent['sorting'] = 'last';
	}

	function getAlterOrder() {
		if ($this->aCurrent['sorting'] == 'popular') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `CommentsCount` DESC, `PostDate` DESC";
			return $aSql;
		}
	    return array();
	}

	function showPagination($bAdmin = false) {
        $aLinkAddon = $this->getLinkAddByPrams();
        $oPaginate = new BxDolPaginate(array(
            'page_url' => $this->aCurrent['paginate']['page_url'],
            'count' => $this->aCurrent['paginate']['totalNum'],
            'per_page' => $this->aCurrent['paginate']['perPage'],
            'page' => $this->aCurrent['paginate']['page'],
            'per_page_changer' => true,
            'page_reloader' => true,
            'on_change_page' => 'return !loadDynamicBlock('.$this->id.', \'searchKeywordContent.php?searchMode=ajax&blogs_mode='.$this->aCurrent['sorting'].'&section[]=blog&keyword='.bx_get('keyword').$aLinkAddon['params'].'&page={page}&per_page={per_page}\');',
            'on_change_per_page' => 'return !loadDynamicBlock('.$this->id.', \'searchKeywordContent.php?searchMode=ajax&blogs_mode='.$this->aCurrent['sorting'].'&section[]=blog&keyword='.bx_get('keyword').$aLinkAddon['params'].'&page=1&per_page=\' + this.value);'
        ));
        $sPaginate = '<div class="clear_both"></div>'.$oPaginate->getPaginate();
        
        return $sPaginate;
    }

	function showPagination3($bAdmin = false) {
		bx_import('BxDolPaginate');
        $aLinkAddon = $this->getLinkAddByPrams();

		$oPaginate = new BxDolPaginate(array(
            'page_url' => $this->aCurrent['paginate']['page_url'],
            'count' => $this->aCurrent['paginate']['totalNum'],
            'per_page' => $this->aCurrent['paginate']['perPage'],
            'page' => $this->aCurrent['paginate']['page'],
            'per_page_changer' => true,
            'page_reloader' => true
        ));

        $sPaginate = '<div class="clear_both"></div>'.$oPaginate->getPaginate();
        
        return $sPaginate;
    }

	function showPagination2($bAdmin = false) {
		bx_import('BxDolPaginate');
		$aLinkAddon = $this->getLinkAddByPrams();

		$sAllUrl = $this->getCurrentUrl('browseAll', 0, '');

		$oPaginate = new BxDolPaginate(array(
			'page_url' => $this->aCurrent['paginate']['page_url'],
			'count' => $this->aCurrent['paginate']['totalNum'],
			'per_page' => $this->aCurrent['paginate']['perPage'],
			'page' => $this->aCurrent['paginate']['page'],
			'per_page_changer' => true,
			'page_reloader' => true,
			'on_change_page' => 'return !loadDynamicBlock({id}, \''.bx_html_attribute($_SERVER['PHP_SELF']).'?blogs_mode='.$this->aCurrent['sorting'].$aLinkAddon['params'].'&page={page}&per_page={per_page}\');',
		));

		$sPaginate = '<div class="clear_both"></div>'.$oPaginate->getSimplePaginate($sAllUrl);

		return $sPaginate;
	}
	
	function _getPseud () {
	  return array(  
            'id' => 'PostID',
            'title' => 'PostCaption',
            'date' => 'PostDate',
            'uri' => 'PostUri',
            'categoryName' => 'CategoryName',
            'categoryUri' => 'CategoryUri',
            'ownerId' => 'OwnerID',
            'ownerName' => 'NickName',
            'bodyText' => 'PostText',
            'countComment' => 'cmt_id',
            'tag' => 'Tags'
     );  
	}
}

?>