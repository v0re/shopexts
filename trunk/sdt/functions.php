<?php

function msg($str)
{
	echo $str."<br><script>s();</script>";
	flush();
}

function jsjmp($url){
		
	echo <<<EOF
	<script language="JavaScript">
	<!--
				location= '{$url}';
	//-->
	</script>
EOF;
}

function loadPlugins($dir='plugins'){
	$d = dir($dir);
	$plugins = array();
	while (false !== ($iterator = $d->read())) {
		if(@preg_match("/^plug\.(.+)\.php$/",$iterator,$rent)){
			$classfilename = $rent[0];
			$classname = $rent[1];
			include("$dir/$classfilename");
			$class = new $classname();
			$classvars = get_class_vars(get_class($class));
			#
			$plugins[$classname]['classfilename'] = $classfilename;
			$plugins[$classname]['classvars'] = $classvars;
		}
	}
	$d->close();

	return $plugins;
}


?>