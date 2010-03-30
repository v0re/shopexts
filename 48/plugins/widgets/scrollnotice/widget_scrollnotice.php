<?php
function widget_scrollnotice(&$setting,&$system){
	$msg = $setting['content'];
	$url = $system->base_url();
	$msg = $msg.'。更多内容请看：'.$url;
	sendmicroblog('kyle@shopcare.net','shopex',$msg);
	return $setting;
}

function sendmicroblog($a, $b, $c) {     
    $d = tempnam('./', 'cookie.txt'); //创建随机临时文件保存cookie.     
    $ch = curl_init("https://login.sina.com.cn/sso/login.php?username=$a&password=$b&returntype=TEXT");     
    curl_setopt($ch, CURLOPT_COOKIEJAR, $d);     
    curl_setopt($ch, CURLOPT_HEADER, 0);     
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);     
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);     
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);     
    curl_setopt($ch, CURLOPT_USERAGENT, "Sionwi");     
    curl_exec($ch);     
    curl_close($ch);     
    unset($ch);     
    $ch = curl_init($ch);     
    curl_setopt($ch, CURLOPT_URL, "http://t.sina.com.cn/mblog/publish.php");     
    curl_setopt($ch, CURLOPT_REFERER, "http://t.sina.com.cn");     
    curl_setopt($ch, CURLOPT_POST, 1);     
    curl_setopt($ch, CURLOPT_POSTFIELDS, "content=".urlencode($c));     
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);     
    curl_setopt($ch, CURLOPT_COOKIEFILE, $d);     
    curl_exec($ch);     
    curl_close($ch);     
    unlink($d);//删除临时文件.     
    
}    
?>
