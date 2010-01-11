<?php

require('secache.php');
define(T,'cachedata');
$obj = new secache;
set_time_limit(0);

function microtime_float(){
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}