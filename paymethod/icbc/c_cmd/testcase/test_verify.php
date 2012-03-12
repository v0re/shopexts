<?php

$realpath = dirname(__FILE__);
$pubcert = $realpath."/cert/private.crt";  #下面的$sigend密文是用用户的私钥文件生成的，所以这里是用户的公钥。正式环境返回的密文是工行私钥生成的，所以记得换成工行的公钥
$message = '<?xml version="1.0" encoding="GBK" standalone="no"?><B2CReq><interfaceName>ICBC_PERBANK_B2C</interfaceName><interfaceVersion>1.0.0.11</interfaceVersion><orderInfo><orderDate>20120325101655</orderDate><curType>001</curType><merID>0200EC23763717</merID><subOrderInfoList><subOrderInfo><orderid>133264181557391124</orderid><amount>1</amount><installmentTimes>1</installmentTimes><merAcct>0200004519000100173</merAcct><goodsID></goodsID><goodsName>20120307163030</goodsName><goodsNum></goodsNum><carriageAmt></carriageAmt></subOrderInfo></subOrderInfoList></orderInfo><custom><verifyJoinFlag>0</verifyJoinFlag><Language>ZH_CN</Language></custom><message><creditType>2</creditType><notifyType>HS</notifyType><resultType>0</resultType><merReference>ecstore.lenovo.chensg.com</merReference><merCustomIp>127.0.0.1</merCustomIp><goodsType>1</goodsType><merCustomID></merCustomID><merCustomPhone></merCustomPhone><goodsAddress></goodsAddress><merOrderRemark></merOrderRemark><merHint></merHint><remark1></remark1><remark2></remark2><merURL>http://ecstore.lenovo.chensg.com/index.php/openapi/ectools_payment/parse/b2c/icbc_payment_plugin_icbc/callback/</merURL><merVAR>eyJmYV9pZCI6IjEifQ==</merVAR></message></B2CReq>';
$sigend = "VtEC3STrgH/Qu3hGd3GNtFXdbeBe+vQoXUVr71Qud2/OzhWVYQ0gb452G3/MRz4OlRHgNgRXjC9LZXwxU1JdlYHc0pjKz8KnbckpAukL6pX6Rxeqajy17Ibg9Puk4N341hBrf9O7JcjE67Hk07MPIMire+WK6uRLIjmeuews4Wg=";

$rst = icbc_verify($pubcert,$message,$sigend);
var_export($rst);

/*
* Comment for icbc_verify
* @access public
* @param String $pubcert	 公钥文件的绝对路径，注意这里应该是工行的公钥文件ebb2cpublic.crt 
* @param String $message	 需要验签的明文
* @param String enc_text     验签的密文
* @return int    验签成功返回1，失败返回0
* @工行验签函数
*/
function icbc_verify($pubcert,$message,$enc_text){
    $cmd = "/bin/icbc_verify '{$pubcert}' '{$message}' '{$enc_text}'";
    $handle = popen($cmd, 'r');
    $isok = fread($handle, 8);
    pclose($handle);
    
    return $isok;
}



