<?php

function isallowed(){
	$ip = getip();
	$aList = array(
		'127.0.0.1',
		'116.228.220.98',
	);
	if(in_array($ip,$aList)){
		return true;
	}elseif(substr($ip,0,7) == '192.168'){
		return true;
	}else{
		return false;
	}
}

function getip(){
	if(getenv('HTTP_CLIENT_IP')) { 
		$onlineip = getenv('HTTP_CLIENT_IP');
	} elseif(getenv('HTTP_X_FORWARDED_FOR')) { 
		$onlineip = getenv('HTTP_X_FORWARDED_FOR');
	} elseif(getenv('REMOTE_ADDR')) { 
		$onlineip = getenv('REMOTE_ADDR');
	} else { 
		$onlineip = $HTTP_SERVER_VARS['REMOTE_ADDR'];
	}

	return $onlineip;
}

function msg($str){
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

function cnSubStr($string,$sublen) { 
    if($sublen >= strlen($string)) { 
    return $string; 
    } 
    $s=""; 
    for($i=0;$i<$sublen;$i++) { 
			if(ord($string{$i}) > 127) { 
					$s.= $string{$i}.$string{++$i}; 
					continue; 
			}else{ 
					$s .= $string{$i}; 
					continue; 
			} 
    } 
    return $s.'...'; 
}


?>