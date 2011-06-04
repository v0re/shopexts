<?
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

require_once(BX_DIRECTORY_PATH_INC . 'db.inc.php');
require_once(BX_DIRECTORY_PATH_INC . 'utils.inc.php');

bx_import('BxDolSubscription');

/**
 * Alert/Handler engine.
 * 
 * Is needed to fire some alert(event) in one place and caught it with a handler somewhere else.
 * 
 * Related classes:
 *  BxDolAlertsResponse - abstract class for all response classes.
 *  BxDolAlertsResponseUser - response class to process standard profile related alerts.
 * 
 * Example of usage:
 * 1. Fire an alert
 * 
 * bx_import('BxDolAlerts');
 * $oZ = new BxDolAlerts('unit_name', 'action', 'object_id', 'sender_id', 'extra_params');
 * $oZ->alert();
 * 
 * 2. Add handler and caught alert(s) @see BxDolAlertsResponseUser
 *  a. Create Response class extending BxDolAlertsResponse class. It should process all necessary alerts which are passed to it.
 *  b. Register your handler in the database by adding it in `sys_alerts_handlers` table.
 *  c. Associate necessary alerts with the handler by adding them in the `sys_alerts` table. 
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
class BxDolAlerts {
	
	var $sUnit;
	var $sAction;
	var $iObject;
	var $iSender;
	var $aExtras;

	var $_aAlerts;
	var $_aHandlers;
	
	/**
     * Constructor
	 * @param string $sType - system type
	 * @param string $sAction - system action
	 * @param int $iObjectId - object id
	 * @param int $iSenderId - sender (action's author) id
	 */
	function BxDolAlerts($sUnit, $sAction, $iObjectId, $iSender = 0, $aExtras = array()) {

        $oCache = $GLOBALS['MySQL']->getDbCacheObject();
        $aData = $oCache->getData($GLOBALS['MySQL']->genDbCacheKey('sys_alerts'));
        if (null === $aData)
            $aData = BxDolAlerts::cache();

        $this->_aAlerts = $aData['alerts'];
        $this->_aHandlers = $aData['handlers'];

		$this->sUnit = $sUnit;
		$this->sAction = $sAction;
		$this->iObject = (int)$iObjectId;
        $this->iSender = !empty($iSender) ? (int)$iSender : 
            (empty($_COOKIE['memberID']) ? 0 : (int)$_COOKIE['memberID']);
		$this->aExtras = $aExtras;
	}

	/**
	 * Notifies the necessary handlers about the alert.
	 */
    function alert() {
        $oSubscription = new BxDolSubscription();
        $oSubscription->send($this->sUnit, $this->sAction, $this->iObject, $this->aExtras);
        
        if(isset($this->_aAlerts[$this->sUnit]) && isset($this->_aAlerts[$this->sUnit][$this->sAction]))
            foreach($this->_aAlerts[$this->sUnit][$this->sAction] as $iHandlerId) {
                $aHandler = $this->_aHandlers[$iHandlerId];

                if(!empty($aHandler['file']) && !empty($aHandler['class']) && file_exists(BX_DIRECTORY_PATH_ROOT . $aHandler['file'])) {
                    if(!class_exists($aHandler['class']))
                        require_once(BX_DIRECTORY_PATH_ROOT . $aHandler['file']);

                    $oHandler = new $aHandler['class']();
                    $oHandler->response($this);
                }
                else if(!empty($aHandler['eval'])) {
                    eval($aHandler['eval']);
                }
            }
    }

    /**
     * Cache alerts and handlers.
     *
     * @return an array with all alerts and handlers.
     */
    function cache() {
        $aResult = array('alerts' => array(), 'handlers' => array());
        
        $rAlerts = db_res("SELECT `unit`, `action`, `handler_id` FROM `sys_alerts` ORDER BY `id` ASC");
        while($aAlert = mysql_fetch_assoc($rAlerts))
            $aResult['alerts'][$aAlert['unit']][$aAlert['action']][] = $aAlert['handler_id'];
            
        $rHandlers = db_res("SELECT `id`, `class`, `file`, `eval` FROM `sys_alerts_handlers` ORDER BY `id` ASC");
        while($aHandler = mysql_fetch_assoc($rHandlers))
            $aResult['handlers'][$aHandler['id']] = array('class' => $aHandler['class'], 'file' => $aHandler['file'], 'eval' => $aHandler['eval']);

       
        $oCache = $GLOBALS['MySQL']->getDbCacheObject();
        $oCache->setData ($GLOBALS['MySQL']->genDbCacheKey('sys_alerts'), $aResult); 

        return $aResult;
    }
}

class BxDolAlertsResponse {
    function BxDolAlertsResponse(){}
    function response($oAlert) {}
}
?>
