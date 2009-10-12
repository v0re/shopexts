<?php


$key = "12345678";
$message = "kyle";

$cmd = "java cibsign \"{$message}\" \"{$key}\"";
$handle = popen($cmd, 'r');
$mac = fread($handle, 32);
pclose($handle);

echo $mac;

echo "<br>";

$cmd = "java cibverify \"{$key}\" \"{$message}\" \"{$mac}\"";
$handle = popen($cmd, 'r');
$isok = fread($handle, 8);
pclose($handle);


echo $isok;
echo "<br>";
?>