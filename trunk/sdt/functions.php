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



?>