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
xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
// run program
foo();

// stop profiler
$xhprof_data = xhprof_disable();

// display raw xhprof data for the profiler run
print_r($xhprof_data);
