<?php
require "rsa.php";
require "BigInteger.php";

$text = "hi man";

$public = 65537;
$modulus = "D192471B8699640F931FE6F4FACC3E990B894F894CEA5BEE0DCBD7A4B76752F7345CF9B5F1271001B724F7A0ABF0A6E911E309536F4BE4749E92DCC531B8E36B95969D206649C9DD2371B413A8DFD9B92569660B1499A5CD310B86A8FDE24988E456897A416D2E7B0B649F0714F322C57EF92563B21A448D1072FF3806C34C75";
$keylength = 1024;
$modulus_16 = new Math_BigInteger($modulus,16);
$mend = $modulus_16->toString();

echo "now we going to eccrypt $text \n";
$encrypted = rsa_encrypt($text, $public, $mend, $keylength);
echo bin2hex($encrypted); 
echo "\n";

