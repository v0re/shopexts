<?php

$realpath = dirname(__FILE__);
$prikey = $realpath."/cert/private.key";
$password = "12345678";

$message = '<?xml version="1.0" encoding="GBK" standalone="no"?><B2CReq><interfaceName>ICBC_PERBANK_B2C</interfaceName><interfaceVersion>1.0.0.11</interfaceVersion><orderInfo><orderDate>20120325101655</orderDate><curType>001</curType><merID>0200EC23763717</merID><subOrderInfoList><subOrderInfo><orderid>133264181557391124</orderid><amount>1</amount><installmentTimes>1</installmentTimes><merAcct>0200004519000100173</merAcct><goodsID></goodsID><goodsName>20120307163030</goodsName><goodsNum></goodsNum><carriageAmt></carriageAmt></subOrderInfo></subOrderInfoList></orderInfo><custom><verifyJoinFlag>0</verifyJoinFlag><Language>ZH_CN</Language></custom><message><creditType>2</creditType><notifyType>HS</notifyType><resultType>0</resultType><merReference>ecstore.lenovo.chensg.com</merReference><merCustomIp>127.0.0.1</merCustomIp><goodsType>1</goodsType><merCustomID></merCustomID><merCustomPhone></merCustomPhone><goodsAddress></goodsAddress><merOrderRemark></merOrderRemark><merHint></merHint><remark1></remark1><remark2></remark2><merURL>http://ecstore.lenovo.chensg.com/index.php/openapi/ectools_payment/parse/b2c/icbc_payment_plugin_icbc/callback/</merURL><merVAR>eyJmYV9pZCI6IjEifQ==</merVAR></message></B2CReq>';

$expect_sign_message = 'VtEC3STrgH/Qu3hGd3GNtFXdbeBe+vQoXUVr71Qud2/OzhWVYQ0gb452G3/MRz4OlRHgNgRXjC9LZXwxU1JdlYHc0pjKz8KnbckpAukL6pX6Rxeqajy17Ibg9Puk4N341hBrf9O7JcjE67Hk07MPIMire+WK6uRLIjmeuews4Wg=';

$merSignMsg = icbc_sign($prikey,$password,$message);
print $merSignMsg;

echo "<hr>all done";

/*
* Comment for icbc_sign
* @access public
* @param String $prikey	 私钥文件的绝对路径
* @param String $password	 打开私钥文件的密码
* @param String message     需要加密的明文
* @return String     加密后的密文
* @工行验签函数
*/

function icbc_sign($prikey,$password,$message){
    
    $path = realpath(dirname(__FILE__)."/lib");    
    $glob_path = getenv('PATH');
    $path = $path.':'.$glob_path; 
    
    $cmd = realpath(dirname(__FILE__))."/bin/icbc_sign";
        
    $cmd = "$cmd '{$prikey}' '{$password}' '{$message}'";
    $handle = popen($cmd, 'r');
    $merSignMsg = fread($handle, 2096);
    return $merSignMsg;
}

