<?php

set_time_limit(0);
$datafile = 'simplehash.hdb';
				
require('simplehash.php');

$hs = new simplehash;
$hs->workat($datafile);

$hs->dump();

?>