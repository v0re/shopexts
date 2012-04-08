<?php

$cert_file = "D:/server/wwwroot/lvsen/plugins/app/pay_abc/cert/lusen.pem";
$password = '888888';

$signature = null;
$toSign = "<Merchant><ECMerchantType>B2C</ECMerchantType><MerchantID>233010300330A01</MerchantID></Merchant><TrxRequest><TrxType>PayReq</TrxType><Order><OrderNo>ON200306300001</OrderNo><OrderAmount>280.0</OrderAmount><OrderDesc>Game Card Order</OrderDesc><OrderDate>2003/11/12</OrderDate><OrderTime>23:55:30</OrderTime><OrderURL>http://127.0.0.1/Merchant/MerchantQueryOrder.jsp?ON=ON200306300001&QueryType=1</OrderURL><OrderItems><OrderItem><ProductID>IP000001</ProductID><ProductName>中国移动IP卡</ProductName><UnitPrice>100.0</UnitPrice><Qty>1</Qty></OrderItem><OrderItem><ProductID>IP000002</ProductID><ProductName>网通IP卡</ProductName><UnitPrice>90.0</UnitPrice><Qty>2</Qty></OrderItem></OrderItems></Order><ProductType>1</ProductType><PaymentType>1</PaymentType><NotifyType>0</NotifyType><ResultNotifyURL>http://127.0.0.1/Merchant/MerchantResult.jsp</ResultNotifyURL><MerchantRemarks>Hi!</MerchantRemarks><PaymentLinkType>1</PaymentLinkType></TrxRequest>";

// $toSign签名后的数据应该是nfJAveUtLG1YHqsjUdopB8Jl9QX4ZtlQrUn+HoiCy0yS9An19z5IxTIVYOuQXjNnbMGgmZlCwK3dSSnRTLHxZMC3zJUiE58qEwxatOgHNFUhAHTBxkUMO5ikC7C5qm/9L67/Xp7kYvHK9Fo/8CyXckROb+w+eLYcPaYo6+Of2Dg=

$fp = fopen($cert_file, "rb");
$priv_key = fread($fp, 8192);
fclose($fp);
//获取pem文件中的私钥
$pkeyid = openssl_get_privatekey($priv_key,$password);
//使用用户私钥对消息进行签名
openssl_sign($toSign, $signature, $pkeyid);
// Free the key.
openssl_free_key($pkeyid);

$b64 = base64_encode( $signature );
echo $b64;
