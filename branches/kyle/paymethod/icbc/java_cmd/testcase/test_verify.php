<?php


$ret = array (
  'notifyMsg' => '301330373729512|12611968908647|3220.00|CNY|20091219| |20091219|123114|52715984|1|00000000016.1000|0| | |218.0.46.8|shop.green3c.com|MIIElAYJKoZIhvcNAQcCoIIEhTCCBIECAQExCzAJBgUrDgMCGgUAMAsGCSqGSIb3DQEHAaCCA3swggN3MIICX6ADAgECAgRMUsnpMA0GCSqGSIb3DQEBBQUAMDUxCzAJBgNVBAYTAkNOMRQwEgYDVQQKEwtCQU5LQ09NTSBDQTEQMA4GA1UEAxMHQk9DT01DQTAeFw0wNjA4MDkwNzA3MzhaFw0xMTA4MDkwNzA3MzhaMGAxCzAJBgNVBAYTAkNOMRQwEgYDVQQKEwtCQU5LQ09NTSBDQTERMA8GA1UECxMIQkFOS0NPTU0xEjAQBgNVBAsTCU1lcmNoYW50czEUMBIGA1UEAxMLQm9jb21OZXRQYXkwgZ8wDQYJKoZIhvcNAQEBBQADgY0AMIGJAoGBANvUrS4YSg27BxgVr5fZpaDGpadQzchJRxrn1t8TDpaB0PhWmVy9V/jdkPi8NlEdp+29QutV3dDFKkZDJ+aP5D1vseScuLXV53zK7JSD9fYfQTbSxlYIpFql/Z40JFB5vc/qTIdNjm9t1grlJsUv7yhenOH+CUQg3vUi/ZpxYRLfAgMBAAGjgecwgeQwHwYDVR0jBBgwFoAU0rPRsTlHqTd5d+MkTWO1+ELLmXMwPQYDVR0gBDYwNDAyBgRVHSAAMCowKAYIKwYBBQUHAgEWHGh0dHA6Ly8xOTIuMTY4LjMuMTEwL2Nwcy5odG0wVQYDVR0fBE4wTDBKoEigRqREMEIxCzAJBgNVBAYTAkNOMRQwEgYDVQQKEwtCQU5LQ09NTSBDQTEMMAoGA1UECxMDY3JsMQ8wDQYDVQQDEwZjcmwyNTkwDAYDVR0PBAUDAwf5gDAdBgNVHQ4EFgQUzJEULsAx9VwDtbDDP2xiy3Hdx44wDQYJKoZIhvcNAQEFBQADggEBAAkoQoKeRR505ewoIgqn7MVbZRvEQpa/DMycnHu89zGt9VzHX9X1N7bXalZ1u0ib2wA4uvT+H3awGlg6bGWdTjrPSthyrsGCR7KiMOvPSG2dH5Zvi7BS0SQUNJfGRdBeJrC5Hig8XBY0Odkm+XEHOsl/e5Tdpme7rFOVBK8cdzouOxk6a/NDOTY9aeW1+d46feeftPpi7KUf6pMHIsfk056hZdbwGt0u9MPvE7Nna77Uh3iBtxjnQRqklwTGu4Yqla2jfehQ/UgvGxtaAc9P0hx6t6wIYMyD5jyZzAMXshkRjU8/zv67S0vaTkCF6Swo14euPe2rBsM3+z8tsbl4DFExgeIwgd8CAQEwPTA1MQswCQYDVQQGEwJDTjEUMBIGA1UEChMLQkFOS0NPTU0gQ0ExEDAOBgNVBAMTB0JPQ09NQ0ECBExSyekwCQYFKw4DAhoFADANBgkqhkiG9w0BAQEFAASBgAXneRyQJfWFoNyq4iYa9Pc9gwL62qMoZbH4pF7SfC+mibQMOp0kkdKK/zI9NY9eii+vVB1MrUSyaBWcuezr3e1OALy5I/yTw9jVDVMBxUp9ARyY7dlk/JoDWkd6xv2YonxtwNj/wX779IAIDRVneCNpZh8oMB8zbbnczi5K7V5a',
);

$returnmsg=explode("|",$ret['notifyMsg']);
$signMsg = array_pop($returnmsg);
$srcMsg = implode("|",$returnmsg);
$srcMsg .= "|";

$ini_1 = "green3c/B2CMerchant.xml";

$rst = bocomm_verify($ini_1,$srcMsg,$signMsg);

var_export($rst);

function bocomm_verify($config,$src,$sigend){
	$cmd  = "java  bocomm_verify {$config} \"{$src}\" \"{$sigend}\"";
	$handle = popen($cmd, 'r');
	while(!feof($handle)){ 
		$merSignMsg .= fread($handle,1024);
	}
	pclose($handle);
	if(preg_match('/<message>(.+)<\/message>/',$merSignMsg,$match)){
		$verifycode = $match[1];
		return $verifycode;
	}
	return false;
	
}



?>