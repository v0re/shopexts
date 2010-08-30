<?php
if (test_time()) {
	register_shutdown_function('xhprofclose');
	xhprof_enable(
	    XHPROF_FLAGS_MEMORY + XHPROF_FLAGS_NO_BUILTINS
	);
}
#每个小时的前10秒
function test_time(){
	$min = date("i");
	$sec = date("s");
	if($min == '01' && $sec < "10"){
		return true;
	}
	return false;
}
function xhprofclose(){
   $xhprof_data = xhprof_disable();
   $xhprof_location = "/data/htdocs/xhprof";
   include_once "$xhprof_location/xhprof_lib/utils/xhprof_lib.php";
   include_once "$xhprof_location/xhprof_lib/utils/xhprof_runs.php";
   $xhprof_runs = new XHProfRuns_Default();
   $identifer = str_replace(array('/','?','.','=','&'),'_',$_SERVER['REQUEST_URI']);
   $xhprof_runs->save_run($xhprof_data, $identifer);
}

define('TOM_EXEC', true);
require(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config/config.php');
Tom_Core_Autoload::addPath(TOM_MODULES_DIR);
Tom_Core_Autoload::addPath(TOM_PLUGINS_DIR);
Tom_Core_Context::createInstance()->dispatch();