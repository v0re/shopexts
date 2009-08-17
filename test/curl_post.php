<?php

if(!empty($_POST)){
	print_r($_POST);
}else{

	//
	// test HTTP POST submitter, using libcurl
	//

	// the target url which contains scripts that accepts post request

	$url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];


	// we are spoofing Yahoo Seeker bot >:)
	$useragent="YahooSeeker-Testing/v3.9 (compatible; Mozilla 4.0; MSIE 5.5; http://search.yahoo.com/)";

	$ch = curl_init();
	// set user agent
	curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
	// set the target url
	curl_setopt($ch, CURLOPT_URL,$url);
	// howmany parameter to post
	curl_setopt($ch, CURLOPT_POST, 1);
	// the parameter 'username' with its value 'johndoe'
	curl_setopt($ch, CURLOPT_POSTFIELDS,"username=johndoe");
	// execute curl,fetch the result and close curl connection
	$result= curl_exec ($ch);
	curl_close ($ch); 

	// display result
	print $result;
}

?>