<?php

$p = 47;
$q = 59;
$n = $p * $q; #modulus
$t = ($p - 1) * ($q -1 );
$e = 63; #public key
#find the private key $d ,we use Euclidean algorithm to make it see detail in 
#http://blog.csdn.net/xuanshayuan/archive/2008/03/14/2181807.aspx
for($d=2;$d<$t;$d++){
	if (bcmod($d  * $e, $t) == 1) break;
}
$m = 244;
print "modulus n = $n\n";
print "public key e = $e\n";
print "private key d = $d\n";
print "message m = $m\n";

$encrypt_msg = bcpowmod($m,$d,$n); #use private key $d and modulus $n for encrypt,take $d secret

print "encrtyp message : $encrypt_msg \n";

$decrypt_msg = bcpowmod($encrypt_msg,$e,$n); # use public key $e and modulus $e for decrypt,$e can be publish

print "decrtyp message : $decrypt_msg \n";



