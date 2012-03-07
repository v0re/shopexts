<?php

$realpath = dirname(__FILE__);
$prikey = $realpath."/cert/user.key";
$password = "123456";

$message = 'i am plain text';
$merSignMsg = icbc_sign($message);
print $merSignMsg;

echo "<hr>all done";

function icbc_sign($message){
        global $prikey,$password;
    	$libpath = realpath(dirname(__FILE__)."/lib");
		$self_classpath = $libpath.":";
		$self_classpath .= $libpath."/icbc.jar:";
		$self_classpath .= $libpath."/InfosecCrypto_Java1_02_JDK14+.jar:";
		$glob_classpath = getenv('CLASSPATH');
		$classpath = $self_classpath.':'.$glob_classpath; 
		
       	if (strtoupper(substr(PHP_OS,0,3))=="WIN"){
            $classpath = str_replace(array('/',':'),array('\\',';'),$classpath);
            $classpath = str_replace(";\\",":\\",$classpath);
            $prikey = str_replace('/','\\',$prikey);
        }

	    $cmd = "java -classpath {$classpath} icbc_sign {$prikey} {$password} \"{$message}\"";
	
	    $handle = popen($cmd, 'r');
	    $merSignMsg = '';
        while(!feof($handle)){ 
            $merSignMsg .= fread($handle,1024);
        }
        pclose($handle);
        $merSignMsg = str_replace("\n","",$merSignMsg);
        if(preg_match('/<message>(.+)<\/message>/',$merSignMsg,$match)){
            $merSignMsg = $match[1];
        }
    	return $merSignMsg;
}

