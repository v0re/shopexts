<?php
function widget_nav($setting,&$system,$env){
	//error_log(var_export($env,true),3,__FILE__.".log");
	$env['path'][0]['title'] = 'BagXO';
    return $env['path'];
}
?>
