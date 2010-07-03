<?php

$p = 47;
$q = 59;
$n = $p * $q; #modulus
$t = ($p - 1) * ($q -1 );
$e = 63; #public key
#find the private key $d
for($d=9999;$d>0;$d--){
	if ($d * $e % $t == 1) break;
}
$d = 847; #there is something wrong with $d cuculation
$m = 244;
print "modulus n = $n\n";
print "public key d = $d\n";
print "private key e = $e\n";
print "message m = $m\n";

$encrypt_msg = bcpowmod($m,$d,$n);

print "encrtyp message : $encrypt_msg \n";

$decrypt_msg = bcpowmod($encrypt_msg,$e,$n);

print "decrtyp message : $decrypt_msg \n";



