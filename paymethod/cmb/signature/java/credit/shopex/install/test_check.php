<?php

$text =  '<$Head$><$Version$>1.0.0.0</$Version$></$Head$><$Body$><$ResultFlag$>Y</$ResultFlag$><$MchMchNbr$>888801</$MchMchNbr$><$MchBllNbr$>000000</$MchBllNbr$><$MchBllDat$>20080908</$MchBllDat$><$MchBllTim$>140130</$MchBllTim$><$TrxTrxCcy$>156</$TrxTrxCcy$><$TrxBllAmt$>100.00</$TrxBllAmt$><$TrxPedCnt$>3</$TrxPedCnt$><$MchNtfPam$>中文的参数abc，和更多的英文</$MchNtfPam$><$CrdBllNbr$>0000000000</$CrdBllNbr$><$CrdBllRef$>REFaaaaaaa</$CrdBllRef$><$CrdAutCod$>000000</$CrdAutCod$></$Body$>';

$sign = "5CF26349F50ECFA9EEC46ABC93B0D9F8038B06DDB5C016522187DECDF17B9166A78AA67F685CE4EC4E2D8F05A8C60BE8056181F0AAE3A2845EC1F922B8C474CC";

$public_key = realpath(dirname(__FILE__)."/public.key");

#$cmd = "java  Check \"{$text}\" \"{$sign}\" \"{$public_key}\"" ;
$cmd = "java  -jar Check.jar \"{$text}\" \"{$sign}\" \"{$public_key}\"" ;

echo $cmd;
$handle = popen($cmd, 'r');
$isok = fread($handle, 8);
pclose($handle);


var_export($isok);

?>