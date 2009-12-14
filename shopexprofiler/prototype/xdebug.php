<?php

$datadir = dirname(__FILE__)."/data";

ini_set("xdebug.profiler_output_dir",$datadir."/profiler");
ini_set("xdebug.trace_output_dir",$datadir."/trace");

$uri = $_SERVER['REQUEST_URI'];

$peak_memory_usage = xdebug_peak_memory_usage();
$escape_time =  xdebug_time_index();

$peak_memory_usage = number_format($peak_memory_usage);
$escape_time = number_format($escape_time * 1000,2,'.','');


error_log("$uri\t$peak_memory_usage\t$escape_time\n",3,dirname(__FILE__)."/record.log");


?>