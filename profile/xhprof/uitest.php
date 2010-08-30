<?php

function bar($x) {
      if ($x > 0) {
              bar($x - 1);
                }
}

function foo() {
      for ($idx = 0; $idx < 2; $idx++) {
              bar($idx);
                  $x = strlen("abc");
                    }
}

// start profiling
//xhprof_enable();
//xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
xhprof_enable(XHPROF_FLAGS_MEMORY + XHPROF_FLAGS_NO_BUILTINS,
array('ignored_functions' => array('call_user_func',
'call_user_func_array')));
// run program
foo();

// stop profiler
$xhprof_data = xhprof_disable();

include_once "xhprof_lib/utils/xhprof_lib.php";
include_once "xhprof_lib/utils/xhprof_runs.php";

$xhprof_runs = new XHProfRuns_Default();

$run_id = $xhprof_runs->save_run($xhprof_data, "xhprof_foo");

echo "---------------<br>".
     "Assuming you have set up the http based UI for <br>".
     "XHProf at some address, you can view run at <br>".
     "http://192.168.0.12/xhprof/xhprof_html/index.php?run=$run_id&source=xhprof_foo<br>".
     "---------------<br>";