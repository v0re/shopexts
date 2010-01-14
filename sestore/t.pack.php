<?php

$username = 'kyle';
$passwd = 'fuckyou';

$lastact = pack('SCSa32a32',0x0040,0x00,0x0006,$username,$passwd );
var_export($lastact);
$ret = unpack('Sint1/Cchar1/Sint2/Cchar2/',$lastact);

var_export($ret);