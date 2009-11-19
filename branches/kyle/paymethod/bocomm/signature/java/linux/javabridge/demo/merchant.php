<?php
    // PHP version of merchant.jsp
    //����B2CAPI��php���ò���
    //��    �ߣ�����
    //����ʱ�䣺2008-12-02
?>

<html>
    <head>
        <title>�̻���������</title>

        <meta http-equiv = "Content-Type" content = "text/html; charset=gb2312">
    </head>

    <?php
        //define("JAVA_DEBUG", true); //��������	
        //define("JAVA_HOSTS", "127.0.0.1:8080"); //����javabridge�����˿ڣ��������javabridge.jar���õĶ˿ڲ���8080����ͨ����������

        require_once("java/Java.inc"); //php����java�Ľӿڣ������

        $here=realpath(dirname($_SERVER["SCRIPT_FILENAME"]));

        if (!$here)
            $here=getcwd();

        java_set_library_path("$here/lib"); //����java������·��
        java_set_file_encoding("GBK");      //����java����

$aConfig = array(
'ApiURL'=>'https://ebanktest.95559.com.cn/corporbank/NsTrans',
'OrderURL'=>'https://pbanktest.95559.com.cn/netpay/MerPayB2C',
'EnableLog'=>'true',
'LogPath'=>'',
'SettlementFilePath'=>'',
'MerchantCertFile'=>'',
'MerchantCertPassword'=>'',
'RootCertFile'=>'',
);
$aConfig['LogPath'] = $realpath."/log";
$aConfig['SettlementFilePath'] = $realpath."/settlement";
$aConfig['MerchantCertFile'] = $realpath."/cert/301310063009501.PFX";
$aConfig['MerchantCertPassword'] = '12345678';
$aConfig['RootCertFile'] = $realpath."/cert/test_root.cer";


define('CONFIG',$here."/ini/B2CMerchant.xml");

makeConfig(CONFIG,$aConfig);

        //���java����
        $BOCOMSetting=java("com.bocom.netpay.b2cAPI.BOCOMSetting");
        $client=new java("com.bocom.netpay.b2cAPI.BOCOMB2CClient");
        $ret=$client->initialize(CONFIG);
				$ret = java_values($ret);
        if ($ret != "0")
            {
            $err=$client->getLastErr();
            //Ϊ��ȷ��ʾ���ĶԷ���java��������ת���������java_set_file_encoding���й�ת�������ٴ�ת��
            //$err = java_values($err->getBytes("GBK")); 
            $err=java_values($err);
            print "��ʼ��ʧ��,������Ϣ��" . $err . "<br>";
            exit(1);
            }

        //��ñ�������������
        $interfaceVersion=$_REQUEST["interfaceVersion"];
        $merID=$BOCOMSetting->MerchantID; //�̻���Ϊ�̶�
        $orderid=$_REQUEST["orderid"];
        $orderDate=$_REQUEST["orderDate"];
        $orderTime=$_REQUEST["orderTime"];
        $tranType=$_REQUEST["tranType"];
        $amount=$_REQUEST["amount"];
        $curType=$_REQUEST["curType"];
        $orderContent=$_REQUEST["orderContent"];
        $orderMono=$_REQUEST["orderMono"];
        $phdFlag=$_REQUEST["phdFlag"];
        $notifyType=$_REQUEST["notifyType"];
        $merURL=$_REQUEST["merURL"];
        $goodsURL=$_REQUEST["goodsURL"];
        $jumpSeconds=$_REQUEST["jumpSeconds"];
        $payBatchNo=$_REQUEST["payBatchNo"];
        $proxyMerName=$_REQUEST["proxyMerName"];
        $proxyMerType=$_REQUEST["proxyMerType"];
        $proxyMerCredentials=$_REQUEST["proxyMerCredentials"];
        $netType=$_REQUEST["netType"];
        $source="";

        //�����ַ���
        $source=$interfaceVersion . "|" . $merID . "|" . $orderid . "|" . $orderDate . "|" . $orderTime . "|"
                    . $tranType . "|" . $amount . "|" . $curType . "|" . $orderContent . "|" . $orderMono . "|"
                    . $phdFlag . "|" . $notifyType . "|" . $merURL . "|" . $goodsURL . "|" . $jumpSeconds . "|"
                    . $payBatchNo . "|" . $proxyMerName . "|" . $proxyMerType . "|" . $proxyMerCredentials . "|"
                    . $netType;

        $sourceMsg=new java("java.lang.String", $source);

        //��Ϊ��������ǩ��
        $nss=new java("com.bocom.netpay.b2cAPI.NetSignServer");

        $merchantDN=$BOCOMSetting->MerchantCertDN;
        $nss->NSSetPlainText($sourceMsg->getBytes("GBK"));

        $bSignMsg=$nss->NSDetachedSign($merchantDN);
        $signMsg=new java("java.lang.String", $bSignMsg, "GBK");


function makeConfig($file,$arr){
	$xmlstring = arrayToXml($arr);
	$xml = <<<EOF
<?xml version="1.0" encoding="gb2312"?>
<BOCOMB2C>	
{$xmlstring}
</BOCOMB2C>
EOF;
	file_put_contents($file,$xml);
}

function arrayToXml($arr){
	foreach($arr as $key=>$val){
		$ret .= "\t<$key>$val</$key>\r\n";
	}
	return $ret;
}


    ?>

    <body bgcolor = "#FFFFFF" text = "#000000" onload = " javascript: form1.submit()">
        <form name = "form1" method = "post" action = "<?php echo($BOCOMSetting->OrderURL); ?>">
            <input type = "hidden" name = "interfaceVersion" value = "<?php echo($interfaceVersion); ?>">
            <input type = "hidden" name = "merID" value = "<?php echo($merID); ?>">
            <input type = "hidden" name = "orderid" value = "<?php echo($orderid); ?>">
            <input type = "hidden" name = "orderDate" value = "<?php echo($orderDate); ?>">
            <input type = "hidden" name = "orderTime" value = "<?php echo($orderTime); ?>">
            <input type = "hidden" name = "tranType" value = "<?php echo($tranType); ?>">
            <input type = "hidden" name = "amount" value = "<?php echo($amount); ?>">
            <input type = "hidden" name = "curType" value = "<?php echo($curType); ?>">
            <input type = "hidden" name = "orderContent" value = "<?php echo($orderContent); ?>">
            <input type = "hidden" name = "orderMono" value = "<?php echo($orderMono); ?>">
            <input type = "hidden" name = "phdFlag" value = "<?php echo($phdFlag); ?>">
            <input type = "hidden" name = "notifyType" value = "<?php echo($notifyType); ?>">
            <input type = "hidden" name = "merURL" value = "<?php echo($merURL); ?>">
            <input type = "hidden" name = "goodsURL" value = "<?php echo($goodsURL); ?>">
            <input type = "hidden" name = "jumpSeconds" value = "<?php echo($jumpSeconds); ?>">
            <input type = "hidden" name = "payBatchNo" value = "<?php echo($payBatchNo); ?>">
            <input type = "hidden" name = "proxyMerName" value = "<?php echo($proxyMerName); ?>">
            <input type = "hidden" name = "proxyMerType" value = "<?php echo($proxyMerType); ?>">
            <input type = "hidden" name = "proxyMerCredentials" value = "<?php echo($proxyMerCredentials); ?>">
            <input type = "hidden" name = "netType" value = "<?php echo($netType); ?>">
            <input type = "hidden" name = "merSignMsg" value = "<?php echo($signMsg); ?>">
        </form>
    </body>
</html>
