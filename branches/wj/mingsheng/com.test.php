<?php

$pfxurl = dirname(__FILE__)."/xigou.pfx";
$certrul = dirname(__FILE__)."/cmbc-2.cer";
$keyPass = 'xxxxx';

$plaintext = 'test.com';

$minshengpay = new COM("Newcom2.seServer.1") or die("Unable to instanciate Word");
$minshengpay->readcert($pfxurl);
$pfx = $minshengpay->cert();

$minshengpay->readcert($certrul);
$bankcert = $minshengpay->cert();

$minshengpay->EnvelopData($plaintext,$bankcert,$pfx,$keyPass);
var_dump($minshengpay->EnveData());