<?php 
if (!defined('IN_ASSIS_SERVICE')) exit();
require_once(CORE_DIR.'/func_ext.php');
require_once(CORE_DIR.'/include/shopCore.php');
class assisCore extends shopCore{
    function run(){}
}
$system = new assisCore(array());
$GLOBALS['system'] = &$system;
$GLOBALS['as_debug'] = false;

if (!defined('DIRECTORY_SEPARATOR')) define('DIRECTORY_SEPARATOR', '/');

define('AS_DIR', dirname(__FILE__));
define('AS_SERVICE_DIR', AS_DIR.'/service/');
define('AS_VALIDATOR_DIR', AS_DIR.'/validator/');
define('AS_TMP_DIR', HOME_DIR.'/tmp/');
define('AS_LOG_DIR', HOME_DIR.'/logs/');
define('AS_SYNC_DELETED', -1);
define('AS_SYNC_UNCHANGED', 0);
define('AS_SYNC_ADDED', 1);
define('AS_SYNC_MODIFIED', 2);    
define('DATABACK_DIR', HOME_DIR.'/backup/');
define('AS_TOKEN_TIMEOUT', 30);

$token    = isset($_GET['token']) ? $_GET['token'] : '';
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : '';
if (!empty($redirect) && !empty($token)){
    $token_file = AS_TMP_DIR . 'astoken.php';
    if (file_exists($token_file)) include($token_file);    
    if (isset($redirect_tokes) && is_array($redirect_tokes))
    {        
        foreach ($redirect_tokes as $item){            
            if ($item['token'] == $token && (time()-$item['time']) <= AS_TOKEN_TIMEOUT)
            {                             
                $db = $system->database();
                $aResult = $db->selectrow("select * from sdb_operators where status=1 and username=".$db->quote($item['user']));                
                if ($aResult){
                    $session = $system->loadModel('opSession');
                    $session->start();
                    unset($_SESSION["loginmsg"]);
                    unset($_SESSION['_PageData']);
                    unset($_SESSION['OPID']);
                    unset($_SESSION['SUPER']);
                    $profile = $system->loadModel('adminProfile');    
                    $profile->load($aResult['op_id']);
                    $_SESSION['OPID'] = $aResult['op_id'];
                    $_SESSION['SUPER'] = $aResult['super'];
                    $_SESSION['profile'] = &$profile;                    
                    $session->login();                    
                }                    
            }
        }                
    }
    header('location:'.$redirect);
    exit();
}

require_once(AS_DIR.'/lib/LogUtils.php');
require_once(AS_DIR.'/lib/GeneralFunc.php');
require_once(AS_DIR.'/lib/nudime.php');
require_once(AS_DIR.'/lib/ServerUtils.php');
require_once(AS_DIR.'/lib/TextUtils.php');
require_once(AS_DIR.'/lib/BaseService.php');
require_once(AS_DIR.'/lib/BaseValidator.php');

$server = new nusoapserverdime();
$server->configureWSDL('shopexapiwsdl', 'urn:shopexapi');
$server->wsdl->schemaTargetNamespace = 'urn:shopexapi';
$server->charset = 'utf-8';
$server->validate_factory = 'validate_soap';
$GLOBALS['as_server'] = &$server;

function validate_soap($clientid,&$body,$signature,$DigestMethod,$methodname,$DigestOpts)
{                            
    if (@ini_get('magic_quotes_gpc'))
    {
        $data = stripcslashes($data);
    }            
    $clintid_arr = split(':', $clientid);    
    if (is_array($clintid_arr) && count($clintid_arr) > 1)
    {
        $clientid = $clintid_arr[0];        
        if( md5($clintid_arr[1]) == '2331b2ae67da3312f33dd4c79bd1c49a')                
        {
            $GLOBALS['as_debug'] = true;            
        }
    }            
    LogUtils::log_str('start auth cert');    
    $sys = &$GLOBALS['system'];
    LogUtils::log_str('start set sql_mode');
    $db = $sys->database();
    if ($db) $db->exec("set sql_mode=''");
    LogUtils::log_str('start load model certificate');
    $certs = $sys->loadModel('service/certificate');        
    if ($certs && ($clientid == $certs->getCerti()) )
    {
        if (strtolower($DigestMethod) == "md5")
            return md5($body.$certs->getToken()) == $signature;
    }    
        
    return false;
}

foreach (as_find_files(AS_SERVICE_DIR, '/service.([a-zA-Z0-9_]*).php/') as $file => $matches)
{    
    include_once(AS_SERVICE_DIR.$file);
    $clsname = $matches[1].'Service';
    if (class_exists($clsname))
    {
        $cls = new $clsname();
        if (is_a($cls, 'BaseService'))
        {
            $cls->init($server);
        }
    }     
}

$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
if (empty($HTTP_RAW_POST_DATA)){
    $fp = fopen("php://input",'rb');
    while(!feof($fp)) $HTTP_RAW_POST_DATA .= fread($fp,4096);
    fclose($fp);    
}
$server->service($HTTP_RAW_POST_DATA);
?>