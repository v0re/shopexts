<?php

$realpath = dirname(__FILE__);
$pubcert = $realpath."/cert/user.crt";  #下面的$sigend密文是用用户的私钥文件生成的，所以这里是用户的公钥。正式环境返回的密文是工行私钥生成的，所以记得换成工行的公钥
$message = 'i am plain text';
$sigend = "zUKBqnwN5qQT6vrphJDA9IShGuqxoopbntwuQiNM6fuJiVoaYlPE+D+TxP7PQhL5FY3BVUlWpTV8vb0jsKMrKTfubTYWVVXG+AdGwuo59HS7NKsEmqX06Ali6bmTz6dObm+T1ltrioyaLZvGYKS8yAzi2IvOcLk3dxOZAQ1xtQo=";

$rst = icbc_verify($pubcert,$message,$sigend);
var_export($rst);

/*
* Comment for icbc_verify
* @access public
* @param String $pubcert	 公钥文件的绝对路径，注意这里应该是工行的公钥文件ebb2cpublic.crt 
* @param String $message	 需要验签的明文
* @param String enc_text     验签的密文
* @return int    验签成功返回1，失败返回0
* @工行验签函数
*/
function icbc_verify($pubcert,$message,$enc_text){
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
    
    $cmd = "java -classpath {$classpath} icbc_verify {$pubcert}  \"{$message}\"  \"{$enc_text}\"";

	$handle = popen($cmd, 'r');
	while(!feof($handle)){ 
		$merSignMsg .= fread($handle,1024);
	}
	pclose($handle);
	if(preg_match('/<message>(.+)<\/message>/',$merSignMsg,$match)){
		$verifycode = $match[1];
		return $verifycode;
	}
	return false;	
}



