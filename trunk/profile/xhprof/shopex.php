<?php
	xhprof_enable(
        XHPROF_FLAGS_MEMORY + XHPROF_FLAGS_NO_BUILTINS,
        array('ignored_functions' => array(
                                        'call_user_func',
                                        'call_user_func_array')
        )
    );
    include("../shopex484/index.php");
    $xhprof_data = xhprof_disable();
    include_once "../xhprof/xhprof_lib/utils/xhprof_lib.php";
    include_once "../xhprof/xhprof_lib/utils/xhprof_runs.php";
    $xhprof_runs = new XHProfRuns_Default();
    $run_id = $xhprof_runs->save_run($xhprof_data, "xhprof_shopex");
    $report = "http://192.168.0.12/xhprof/xhprof_html/index.php?run=$run_id&source=xhprof_shopex";
    error_log($report,3,"xhprof.txt");

?>
