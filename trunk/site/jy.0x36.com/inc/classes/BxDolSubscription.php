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

bx_import('BxDolMistake');
bx_import('BxDolSubscriptionQuery');
bx_import('BxDolEmailTemplates');

define('BX_DOL_SBS_TYPE_VISITOR', 0);
define('BX_DOL_SBS_TYPE_MEMBER', 1);

/**
 * Subscriptions for any content changes.
 *
 * Integration of the content with subscriptions engine allows 
 * site member and visitors to subscribe to any content changes.
 *
 * Related classes:
 *  BxDolSubscriptionQuery - database queries. 
 *
 * Example of usage:
 * 1. Register all your subscriptions in `sys_sbs_types` database table.
 * 2. Add necessary email templates in the `sys_email_templates` table.
 * 3. Add necessary HTML/JavaScript data on the page where the 'Subscribe' 
 *    button would be displayed. Use the following code
 *
 *    $oSubscription = new BxDolSubscription();
 *    $oSubscription->getData();
 *
 * 4. Add Subscribe/Unsubscribe button using the following code.
 *
 *    $oSubscription = new BxDolSubscription();
 *    $oSubscription->getButton($iUserId, $sUnit, $sAction, $iObjectId);
 * 
 * @see an example of integration in the default Dolphin's modules(feedback, news, etc)
 *
 *
 * Memberships/ACL:
 * Doesn't depend on user's membership.
 *
 *
 * Alerts:
 * no alerts available
 *
 */
class BxDolSubscription extends BxDolMistake {
    var $_oDb;
    var $_sJsObject;
    var $_sActionUrl;
    var $_sVisitorPopup;

	/**
	 * constructor
	 */
	function BxDolSubscription() {
	    parent::BxDolMistake();

	    $this->_oDb = new BxDolSubscriptionQuery($this);
	    $this->_sJsObject = 'oBxDolSubscription';
	    $this->_sActionUrl = $GLOBALS['site']['url'] . 'subscription.php';
	    $this->_sVisitorPopup = 'sbs_visitor_popup';
	}
	function getMySubscriptions() {
	    $aUserInfo = getProfileInfo();

	    $aSubscriptions = $this->_oDb->getSubscriptionsByUser((int)$aUserInfo['ID']);
        if(empty($aSubscriptions))
            return MsgBox(_t('_Empty'));
	    
	    $aForm = array(
            'form_attrs' => array(
                'id' => 'sbs-subscriptions-form',
                'name' => 'sbs-subscriptions-form',
                'action' => bx_html_attribute($_SERVER['PHP_SELF']),
                'method' => 'post',
                'enctype' => 'multipart/form-data'
            ),
            'params' => array(),
            'inputs' => array()
        );
        $sUnit = '';
        $bCollapsed = true;
        foreach($aSubscriptions as $aSubscription) {
            if($sUnit != $aSubscription['unit']) {
                if(!empty($sUnit)) 
                    $aForm['inputs'][$sUnit . '_end'] = array(
                        'type' => 'block_end'
                    );
                $aForm['inputs'][$aSubscription['unit'] . '_begin'] = array(
                    'type' => 'block_header',
                    'caption' => _t('_sbs_txt_title_' . $aSubscription['unit']),
                    'collapsable' => true,
                    'collapsed' => $bCollapsed
                );
                
                $sUnit = $aSubscription['unit'];
                $bCollapsed = true;
            }
            
            $oFunction = create_function('$arg1, $arg2, $arg3', $aSubscription['params']);
            $aParams = $oFunction($aSubscription['unit'], $aSubscription['action'], $aSubscription['object_id']);
            
            $sName = 'sbs-subscription_' . $aSubscription['entry_id'];
            $aForm['inputs'][$sName] = array(
                'type' => 'custom',
                'name' => $sName,
                'content' => '<a href="' . $aParams['template']['ViewLink'] . '">' . $aParams['template']['Subscription'] . '</a> <a href="javascript:void(0)" onclick="return unsubscribeConfirm(\'' . $this->_getUnsubscribeLink((int)$aSubscription['entry_id']) . '\');">' . _t('_sys_btn_sbs_unsubscribe') . '</a>',
                'colspan' => true
            );
        }
        //'' .  . ''
        $aForm['inputs'][$sUnit . '_end'] = array(
            'type' => 'block_end'
        );

        $oForm = new BxTemplFormView($aForm);
        $sContent = $oForm->getCode();
        
        $GLOBALS['oSysTemplate']->addJsTranslation('_sbs_wrn_unsubscribe');
        ob_start();
        ?>
            <script language="javascript" type="text/javascript">
            <!--
                function unsubscribeConfirm(sUrl){
           	 		if(confirm(_t('_sbs_wrn_unsubscribe'))) {
						$.get(
							sUrl + '&js=1',
							{},
							function(oData){
								alert(oData.message);

								if(oData.code == 0)
									window.location.href = window.location.href; 
							},
							'json'
						);

						return true;
           	 		}
           	 		else
               	 		return false;
                }
            -->
            </script>            
        <?     
        $sContent .= ob_get_clean();

	    return $sContent;
	}
	function getData() {
	    ob_start();
        ?>
            <script language="javascript" type="text/javascript">
            <!--
                var <?=$this->_sJsObject; ?> = new BxDolSubscription({
                    sActionUrl: '<?=$this->_sActionUrl; ?>',
                    sObjName: '<?=$this->_sJsObject; ?>',
                    sVisitorPopup: '<?=$this->_sVisitorPopup; ?>'
                });
            -->
            </script>            
        <?     
        $sContent = ob_get_clean();   

        $aForm = array(
            'form_attrs' => array(
                'id' => 'sbs_form',
                'name' => 'sbs_form',
                'action' => $this->_sActionUrl,
                'method' => 'post',
                'enctype' => 'multipart/form-data',                
                'onSubmit' => 'javascript: return ' . $this->_sJsObject . '.send(this);'
                
            ),
            'inputs' => array (
                'direction' => array (
                    'type' => 'hidden',
                    'name' => 'direction',
                    'value' => ''
                ),
                'unit' => array (
                    'type' => 'hidden',
                    'name' => 'unit',
                    'value' => ''
                ),
                'action' => array (
                    'type' => 'hidden',
                    'name' => 'action',
                    'value' => ''
                ),
                'object_id' => array (
                    'type' => 'hidden',
                    'name' => 'object_id',
                    'value' => ''
                ),
                'user_name' => array (
                    'type' => 'text',
                    'name' => 'user_name',
                    'caption' => _t('_sys_txt_sbs_name'),
                    'value' => '',
                    'attrs' => array (
                        'id' => 'sbs_name'
                    )
                ),
                'user_email' => array (
                    'type' => 'text',
                    'name' => 'user_email',
                    'caption' => _t('_sys_txt_sbs_email'),
                    'value' => '',
                    'attrs' => array (
                        'id' => 'sbs_email'
                    )
                ),
                'sbs_controls' => array (
                    'type' => 'input_set',
                    array (
                        'type' => 'submit',
                        'name' => 'sbs_subscribe',
                        'value' => _t('_sys_btn_sbs_subscribe'),
                        'attrs' => array(
                            'onClick' => 'javascript:$("#' . $this->_sVisitorPopup . ' [name=\'direction\']").val(\'subscribe\')',
                        )
                    ),
                    array (
                        'type' => 'submit',
                        'name' => 'sbs_unsubscribe',
                        'value' => _t('_sys_btn_sbs_unsubscribe'),
                        'attrs' => array(
                            'onClick' => 'javascript:$("#' . $this->_sVisitorPopup . ' [name=\'direction\']").val(\'unsubscribe\')',
                        )
                    ),
                )
                
            )
	    );
        $oForm = new BxTemplFormView($aForm);
        $sContent .= PopupBox($this->_sVisitorPopup, _t('_sys_bcpt_subscribe'), $oForm->getCode());
        
        $GLOBALS['oSysTemplate']->addCss('subscription.css');
        $GLOBALS['oSysTemplate']->addJs('BxDolSubscription.js');        
	    return  $sContent;
	}
    function getButton($iUserId, $sUnit, $sAction = '', $iObjectId = 0) {
        if($this->_oDb->isSubscribed(array('user_id' => $iUserId, 'unit' => $sUnit, 'action' => $sAction, 'object_id' => $iObjectId)))
            $aResult = array(
                'title' => _t('_sys_btn_sbs_unsubscribe'),
                'script' => $this->_sJsObject . ".unsubscribe(" . $iUserId . ", '" . $sUnit . "', '" . $sAction . "', " . $iObjectId . ")"
            );
        else
            $aResult = array(
                'title' => _t('_sys_btn_sbs_subscribe'),
                'script' => $this->_sJsObject . ".subscribe(" . $iUserId . ", '" . $sUnit . "', '" . $sAction . "', " . $iObjectId . ")"
            );
            
        return $aResult;
    }
    
	function subscribeVisitor($sUserName, $sUserEmail, $sUnit, $sAction, $iObjectId = 0) {
	    $aResult = $this->_processVisitor('add', $sUserName, $sUserEmail, $sUnit, $sAction, $iObjectId);
        return $aResult;
	}	
	function unsubscribeVisitor($sUserName, $sUserEmail, $sUnit, $sAction, $iObjectId = 0) {
        return $this->_processVisitor('delete', $sUserName, $sUserEmail, $sUnit, $sAction, $iObjectId);
	}
	function subscribeMember($iUserId, $sUnit, $sAction, $iObjectId = 0) {
        return $this->_processMember('add', $iUserId, $sUnit, $sAction, $iObjectId);
	}
	function unsubscribeMember($iUserId, $sUnit, $sAction, $iObjectId = 0) {
        return $this->_processMember('delete', $iUserId, $sUnit, $sAction, $iObjectId);
	}
	function unsubscribe($aParams) {
	    $aRequest = array();
	    
	    switch($aParams['type']) {
            case 'sid';
                $aRequest = array('sid' => $aParams['sid']);
                break;
            case 'object_id';
                $aRequest = array('unit' => $aParams['unit'], 'object_id' => $aParams['object_id']);
                break;
            case 'visitor':
                $aRequest = array(
                    'type' => BX_DOL_SBS_TYPE_VISITOR,
                    'user_id' => $aParams['id']
                );
                break;
            case 'member':
                $aRequest = array(
                    'type' => BX_DOL_SBS_TYPE_MEMBER,
                    'user_id' => $aParams['id']
                );
                break;
	    }
	    return $this->_oDb->deleteSubscription($aRequest);
	}
	function send($sUnit, $sAction, $iObjectId = 0, $aExtras = array()) {
	    return $this->_oDb->sendDelivery(array(
            'unit' => $sUnit,
            'action' => $sAction,
            'object_id' => $iObjectId
        ));
	}
	function getSubscribersCount($iType = BX_DOL_SBS_TYPE_VISITOR) {
	    return $this->_oDb->getSubscribersCount($iType);
	}
	function getSubscribers($iType = BX_DOL_SBS_TYPE_VISITOR, $iStart = 0, $iCount = 1) {
	    return $this->_oDb->getSubscribers($iType, $iStart, $iCount);
	}
	
	function _processMember($sDirection, $iUserId, $sUnit, $sAction, $iObjectId) {
        $sMethodName = $sDirection . 'Subscription';
        return $this->_oDb->$sMethodName(array(
            'type' => BX_DOL_SBS_TYPE_MEMBER,
            'user_id' => $iUserId,
            'unit' => $sUnit,
            'action' => $sAction,
            'object_id' => $iObjectId
        ));
	}
	function _processVisitor($sDirection, $sUserName, $sUserEmail, $sUnit, $sAction, $iObjectId) {
	    $sMethodName = $sDirection . 'Subscription';
	    return $this->_oDb->$sMethodName(array(
            'type' => BX_DOL_SBS_TYPE_VISITOR,
            'user_name' => $sUserName,
            'user_email' => $sUserEmail,
            'unit' => $sUnit,
            'action' => $sAction,
            'object_id' => $iObjectId
        ));
	}
	
	function _getUnsubscribeLink($mixedIds) {
	    $aIds = array();
	    if(is_int($mixedIds))
            $aIds = array($mixedIds);
	    else if(is_string($mixedIds))
            $aIds = explode(",", $mixedIds);
        else if(is_array($mixedIds))
            $aIds = $mixedIds;

        return !empty($aIds) ? $this->_sActionUrl . '?sid=' . urlencode(base64_encode(implode(",", $aIds))) : '';
	}
}
