<?php
/**
* 
* 程序处理文件
*
* @package  ShopEx网上商店系统
* @version  4.6
* @author   ShopEx.cn <develop@shopex.cn>
* @url        http://www.shopex.cn/
* @since    PHP 4.3
* @copyright ShopEx.cn
*
**/

//根据指定键删除数组中的元素;
function array_droppay($arr,$key){
    $i = 0;
    while(list($k,$v)=each($arr)){
        if($k!=$key){
            $ret_arr[$k] = $v;
        }
    }
    return $ret_arr;
}
/***************************************************/
//支付接口类型
$CON_PAYMENT['ADVANCE'] = $PROG_TAGS["ptag_1508"];
$CON_PAYMENT['OFFLINE'] = $PROG_TAGS["ptag_1452"];
$CON_PAYMENT['CN']        = $PROG_TAGS['ptag_CN'];
$CON_PAYMENT['ALIPAY']    = $PROG_TAGS["ptag_1985"];
$CON_PAYMENT['ALIPAYTRAD']    = $PROG_TAGS["ptag_trade"];
$CON_PAYMENT['TENPAY']    = $PROG_TAGS["ptag_tenpay"];
$CON_PAYMENT['TENPAYTRAD']    = $PROG_TAGS["ptag_tenpay_db"];
$CON_PAYMENT['99BILL_NEW']    = $PROG_TAGS["ptag_99bill_new"];
$CON_PAYMENT['YEEPAY'] = $PROG_TAGS["ptag_1481"];
$CON_PAYMENT['PAYPAL_CN'] = $PROG_TAGS["ptag_paypalcn"];
$CON_PAYMENT['CNCARD'] = $PROG_TAGS["ptag_2145"];
$CON_PAYMENT['SHOUXIN'] = $PROG_TAGS["ptag_1475"];
$CON_PAYMENT['ICBC'] = $PROG_TAGS["ptag_2000"];
$CON_PAYMENT['WANGJIN']    = $PROG_TAGS["ptag_1480"];
$CON_PAYMENT['WANGJIN_OUT']= $PROG_TAGS["ptag_2295"];
$CON_PAYMENT['IPS']    = $PROG_TAGS["ptag_1473"];
$CON_PAYMENT['IPS3']    = $PROG_TAGS["ptag_1473"]." 3.0"; 
$CON_PAYMENT['CHINAPAY'] = $PROG_TAGS["ptag_1482"];
$CON_PAYMENT['CHINAPNR'] = $PROG_TAGS["ptag_chinapnr"];
$CON_PAYMENT['99BILL']    = $PROG_TAGS["ptag_1981"];
$CON_PAYMENT['NPS']    = $PROG_TAGS["ptag_1479"];
$CON_PAYMENT['NPS_OUT']    = $PROG_TAGS["ptag_1878"];
$CON_PAYMENT['NPAY'] = "NPAY";
//$CON_PAYMENT['FREEPAY'] = "FREEPAY";
$CON_PAYMENT['XPAY'] = "XPAY";
$CON_PAYMENT['IPAY']    = $PROG_TAGS["ptag_1474"];
$CON_PAYMENT['EPAY']    = $PROG_TAGS["ptag_1478"];
$CON_PAYMENT['IEPAY'] = "IEPAY";
$CON_PAYMENT['PAY100'] = "PAY100";
$CON_PAYMENT['UDPAY'] = $PROG_TAGS["ptag_udpayname"];
$CON_PAYMENT['800PAY'] = $PROG_TAGS["ptag_800pay"];
//$CON_PAYMENT['ENTER'] = "ENTER Payment";
$CON_PAYMENT['8848']    = $PROG_TAGS["ptag_1477"];
$CON_PAYMENT['6688']    = $PROG_TAGS["ptag_1476"];
$CON_PAYMENT['HOMEWAY'] = $PROG_TAGS["ptag_1877"];
$CON_PAYMENT['ABANK'] = $PROG_TAGS["ptag_abank"];
$CON_PAYMENT['HYL'] = $PROG_TAGS["ptag_hyl"];
$CON_PAYMENT['HKTW']    = $PROG_TAGS["ptag_HKTW"];
$CON_PAYMENT['PAYDOLLAR'] = "PayDollar";
$CON_PAYMENT['GWPAY'] = "Green World";
$CON_PAYMENT['TWV'] = $PROG_TAGS["ptag_1819"];
$CON_PAYMENT['OVERSEA'] = $PROG_TAGS["ptag_OVERSEA"];
$CON_PAYMENT['PAYPAL']    = $PROG_TAGS["ptag_1562"];
$CON_PAYMENT['GOOGLE'] = "Google Checkout";
$CON_PAYMENT['MONEYBOOKERS'] = "MONEYBOOKERS";
$CON_PAYMENT['2CHECKOUT'] = "2CHECKOUT";
$CON_PAYMENT['EGOLD'] = "EGOLD";
$CON_PAYMENT['ENETS'] = "eNETS";
$CON_PAYMENT['MOBILE88'] = "MOBILE88";
$CON_PAYMENT['ECS'] = "ECS";
$CON_PAYMENT['NOCHEK'] = $PROG_TAGS["ptag_1879"];
//支付接口类型说明
$CON_PAYMEMO['WANGJIN']    = $PROG_TAGS["ptag_1491"];
$CON_PAYMEMO['WANGJIN_OUT']    = $PROG_TAGS["ptag_1491"];
$CON_PAYMEMO['ALIPAY']    = $PROG_TAGS["ptag_1986"];
$CON_PAYMEMO['ALIPAYTRAD']    = $PROG_TAGS["ptag_trade1"];
$CON_PAYMEMO['IPS']    = $PROG_TAGS["ptag_1484"];
$CON_PAYMEMO['IPS3']    = $PROG_TAGS["ptag_1484"]." 3.0";
$CON_PAYMEMO['IPAY']    = $PROG_TAGS["ptag_1485"];
$CON_PAYMEMO['TENPAY']    = $PROG_TAGS["ptag_tenpay_desc"];
$CON_PAYMEMO['TENPAYTRAD'] = $PROG_TAGS["ptag_tenpaytrad_desc"];
$CON_PAYMEMO['SHOUXIN'] = $PROG_TAGS["ptag_1486"];
$CON_PAYMEMO['6688']    = $PROG_TAGS["ptag_1487"];
$CON_PAYMEMO['8848']    = $PROG_TAGS["ptag_1488"];
$CON_PAYMEMO['EPAY']    = $PROG_TAGS["ptag_1489"];
$CON_PAYMEMO['NPS']    = $PROG_TAGS["ptag_1490"];
$CON_PAYMEMO['NPS_OUT']    = $PROG_TAGS["ptag_1490"];
$CON_PAYMEMO['YEEPAY'] = $PROG_TAGS["ptag_1492"];
$CON_PAYMEMO['CHINAPAY'] = $PROG_TAGS["ptag_1493"];
$CON_PAYMEMO['CNCARD'] = $PROG_TAGS["ptag_20080331"];
$CON_PAYMEMO['PAYPAL'] = $PROG_TAGS["ptag_1563"];
$CON_PAYMEMO['PAYPAL_CN'] = $PROG_TAGS["ptag_1564"];
$CON_PAYMEMO['HOMEWAY'] = $PROG_TAGS["ptag_1877"];
$CON_PAYMEMO['NOCHEK'] = $PROG_TAGS["ptag_1879"];
$CON_PAYMEMO['99BILL'] = $PROG_TAGS["ptag_2254"];
$CON_PAYMEMO['99BILL_NEW'] = $PROG_TAGS["ptag_2254"];
$CON_PAYMEMO['NPAY'] = $PROG_TAGS["ptag_062901"];
$CON_PAYMEMO['EGOLD'] = $PROG_TAGS["ptag_062902"];
$CON_PAYMEMO['IEPAY'] = $PROG_TAGS["ptag_062903"];
//$CON_PAYMEMO['FREEPAY'] = $PROG_TAGS["ptag_062904"];
$CON_PAYMEMO['XPAY'] = "";
$CON_PAYMEMO['PAYDOLLAR'] = "";
$CON_PAYMEMO['ECS'] = "";
$CON_PAYMEMO['TWV'] = "";
$CON_PAYMEMO['GWPAY'] = "";
$CON_PAYMEMO['ENETS'] = "Website: https://www.enetspayments.com.sg/";
$CON_PAYMEMO['PAY100'] = "PAY100.COM";
$CON_PAYMEMO['MONEYBOOKERS'] = "WWW.MONEYBOOKERS.COM";
$CON_PAYMEMO['2CHECKOUT'] = "2Checkout.com";
$CON_PAYMEMO['MOBILE88'] = "www.mobile88.com";
$CON_PAYMEMO['GOOGLE'] = $PROG_TAGS["ptag_googlememo"];
$CON_PAYMEMO['UDPAY'] = $PROG_TAGS["ptag_udpayname"];
$CON_PAYMEMO['CHINAPNR'] = $PROG_TAGS["ptag_chinapnr_comment"];
$CON_PAYMEMO['800PAY'] = $PROG_TAGS["ptag_800pay_comment"];
$CON_PAYMEMO['ADVANCE'] = $PROG_TAGS["ptag_2067"];
$CON_PAYMEMO['OFFLINE'] = $PROG_TAGS["ptag_1494"];
$CON_PAYMEMO['ICBC'] = $PROG_TAGS["ptag_2001"];
$CON_PAYMEMO['ABANK'] = "";
$CON_PAYMEMO['HYL'] = $PROG_TAGS["ptag_hylmemo"];

//支付接口支持的货币种类
$CON_PAYCUR['WANGJIN'] = array("CNY"=>"0");
$CON_PAYCUR['WANGJIN_OUT'] = array("CNY"=>"CNY");
$CON_PAYCUR['ALIPAY'] = array("CNY"=>"01");
$CON_PAYCUR['ALIPAYTRAD'] = array("CNY"=>"01");
$CON_PAYCUR['IPS'] = array("CNY"=>"01", "USD"=>"02");
$CON_PAYCUR['IPS3'] = array("CNY"=>"01", "USD"=>"02");
$CON_PAYCUR['IPAY'] = array("CNY"=>"");
$CON_PAYCUR['SHOUXIN'] = array("CNY"=>"0", "USD"=>"1");
$CON_PAYCUR['6688'] = array("CNY"=>"");
$CON_PAYCUR['8848'] = array("CNY"=>"");
$CON_PAYCUR['EPAY'] = array("CNY"=>"");
$CON_PAYCUR['TENPAY'] = array("CNY"=>"1");
$CON_PAYCUR['TENPAYTRAD'] = array("CNY"=>"1");
$CON_PAYCUR['NPS'] = array("CNY"=>"CNY");
$CON_PAYCUR['NPS_OUT'] = array("CNY"=>"CNY", "HKD"=>"HKD", "USD"=>"USD", "EUR"=>"EUR");
$CON_PAYCUR['YEEPAY'] = array("CNY"=>"");
$CON_PAYCUR['CHINAPAY'] = array("CNY"=>"156");
$CON_PAYCUR['CNCARD'] = array("CNY"=>"CNY");
$CON_PAYCUR['PAYPAL'] = array("USD"=>"USD", "CAD"=>"CAD", "EUR"=>"EUR", "GBP"=>"GBP", "JPY"=>"JPY", "AUD"=>"AUD","NZD"=>"NZD","CHF"=>"CHF","HKD"=>"HKD","SGD"=>"SGD","SEK"=>"SEK","DKK"=>"DKK","PLZ"=>"PLZ","NOK"=>"NOK","HUF"=>"HUF","CSK"=>"CSK");
$CON_PAYCUR['PAYPAL_CN'] = array("CNY"=>"CNY");
$CON_PAYCUR['HOMEWAY'] = array("CNY"=>"2002");
$CON_PAYCUR['NOCHEK'] = array("GBP"=>"GBP");
$CON_PAYCUR['99BILL'] = array("CNY"=>"1");
$CON_PAYCUR['99BILL_NEW'] = array("CNY"=>"1");

$CON_PAYCUR['NPAY'] = array("CNY"=>"CNY");
$CON_PAYCUR['EGOLD'] = array("USD"=>"USD", "EUR"=>"EUR", "GBP"=>"GBP", "CAD"=>"CAD", "AUD"=>"AUD", "JPY"=>"JPY");
$CON_PAYCUR['IEPAY'] = array("TWD"=>"TWD");
//$CON_PAYCUR['FREEPAY'] = array("TWD"=>"TWD");

$CON_PAYCUR['XPAY'] = array("CNY"=>"1");
$CON_PAYCUR['PAYDOLLAR'] = array("CNY"=>"156", "HKD"=>"344", "USD"=>"840", "SGD"=>"702", "JPY"=>"392", "TWD"=>"901", "AUD"=>"036", "EUR"=>"978", "GBP"=>"826", "CAD"=>"124");
$CON_PAYCUR['ECS'] = array("USD"=>"USD", "EUR"=>"EUR");
$CON_PAYCUR['TWV'] = array("TWD"=>"TWD");
$CON_PAYCUR['GWPAY'] = array("TWD"=>"TWD");
$CON_PAYCUR['ENETS'] = array("USD"=>"USD","SGD"=>"SGD");
$CON_PAYCUR['PAY100'] = array("CNY"=>"1001");
$CON_PAYCUR['MONEYBOOKERS'] = array("AUD"=>"AUD", "CAD"=>"CAD", "EUR"=>"EUR", "GBP"=>"GBP", "HKD"=>"HKD", "JPY"=>"JPY", "KRW"=>"KRW", "TWD"=>"TWD", "SGD"=>"SGD", "USD"=>"USD");
$CON_PAYCUR['2CHECKOUT'] = array("AUD"=>"AUD", "CAD"=>"CAD", "EUR"=>"EUR", "GBP"=>"GBP", "HKD"=>"HKD", "JPY"=>"JPY", "KRW"=>"KRW", "SGD"=>"SGD", "USD"=>"USD");
$CON_PAYCUR['MOBILE88'] = array("MYR"=>"MYR");
$CON_PAYCUR['GOOGLE'] = array("USD"=>"USD","GBP"=>"GBP");
$CON_PAYCUR['UDPAY'] = array("CNY"=>"156");
$CON_PAYCUR['CHINAPNR'] = array("CNY"=>"156");
$CON_PAYCUR['800PAY'] = array("CNY"=>"RMB","USD"=>"USD","KRW"=>"KRW");
$CON_PAYCUR['ADVANCE'] = array("CNY"=>"CNY","USD"=>"USD","EUR"=>"EUR","GBP"=>"GBP","CAD"=>"CAD","AUD"=>"AUD","NZD"=>"NZD","RUB"=>"RUB","HKD"=>"HKD","TWD"=>"TWD","KRW"=>"KRW","SGD"=>"SGD","JPY"=>"JPY","MYR"=>"MYR","CHF"=>"CHF","SEK"=>"SEK","DKK"=>"DKK","PLZ"=>"PLZ","NOK"=>"NOK","HUF"=>"HUF","CSK"=>"CSK");
$CON_PAYCUR['OFFLINE'] = array("CNY"=>"CNY","USD"=>"USD","EUR"=>"EUR","GBP"=>"GBP","CAD"=>"CAD","AUD"=>"AUD","NZD"=>"NZD","RUB"=>"RUB","HKD"=>"HKD","TWD"=>"TWD","KRW"=>"KRW","SGD"=>"SGD","JPY"=>"JPY","MYR"=>"MYR","CHF"=>"CHF","SEK"=>"SEK","DKK"=>"DKK","PLZ"=>"PLZ","NOK"=>"NOK","HUF"=>"HUF","CSK"=>"CSK","MOP"=>"MOP");
$CON_PAYCUR['ICBC'] = array("CNY"=>"001");
$CON_PAYCUR['ABANK'] = array("CNY"=>"1","USD"=>"2");
$CON_PAYCUR['HYL'] = array("CNY"=>"CNY");
if(file_exists(dirname(__FILE__)."/disabled_payments.txt")){
    $disabled_payments = file (dirname(__FILE__)."/disabled_payments.txt");
    while(list($pk,$pv)=each($disabled_payments)){
        $CON_PAYMENT = array_droppay($CON_PAYMENT,trim($pv));
    }
}

#
$CON_PAYMENT['GSPAY'] = "GSPAY中文版";
$CON_PAYMEMO['GSPAY'] = "GSPAY — 可靠的高风险账号服务和第三方支付服务";
$CON_PAYCUR['GSPAY'] = array("USD"=>"USD");
?>