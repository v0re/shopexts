<?php    

class browser{

    var $cookie_file;
    var $user_agent;
    
    function __construct(){
        $this->cookie_file = dirname(__FILE__)."/cookie_".md5(basename(__FILE__)).".txt"; 
        $this->user_agent = "User-Agent: Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1) ; )";
    }
    
    function __destruct(){
        unlink($this->cookie_file);
    }
       
    function login($url,$data){ 
        $curl = curl_init(); 
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); 
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); 
        curl_setopt($curl, CURLOPT_USERAGENT, $this->user_agent); 
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); 
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); 
        curl_setopt($curl, CURLOPT_POST, 1); 
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data); 
        curl_setopt($curl, CURLOPT_COOKIEJAR, $this->cookie_file); 
        curl_setopt($curl, CURLOPT_COOKIEFILE, $this->cookie_file); 
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); 
        curl_setopt($curl, CURLOPT_HEADER, 0); 
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
        $tmpInfo = curl_exec($curl); 
        if (curl_errno($curl)) {    
           echo 'Errno'.curl_error($curl);    
        }    
        curl_close($curl); 
        return $tmpInfo; 
    }    
       
    function get($url){ 
        $curl = curl_init(); 
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); 
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); 
        curl_setopt($curl, CURLOPT_USERAGENT, $this->user_agent); 
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); 
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); 
        curl_setopt($curl, CURLOPT_HTTPGET, 1); 
        curl_setopt($curl, CURLOPT_COOKIEFILE, $this->cookie_file); 
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); 
        curl_setopt($curl, CURLOPT_HEADER, 0); 
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $tmpInfo = curl_exec($curl); 
        if (curl_errno($curl)) {    
           echo 'Errno'.curl_error($curl);    
        }    
        curl_close($curl); 
        return $tmpInfo;
    }    
       
    function post($url,$data){ 
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url); 
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); 
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); 
        curl_setopt($curl, CURLOPT_USERAGENT, $this->user_agent); 
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        curl_setopt($curl, CURLOPT_POST, 1); 
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data); 
        curl_setopt($curl, CURLOPT_COOKIEFILE, $this->cookie_file); 
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); 
        curl_setopt($curl, CURLOPT_HEADER, 0); 
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
        $tmpInfo = curl_exec($curl); 
        if (curl_errno($curl)) {    
            echo 'Errno'.curl_error($curl);    
        }    
        curl_close($curl); 
        return $tmpInfo; 
    }    
    
    function is_login(){
        return file_exists($this->cookie_file);
    }
       
}





$b = new browser;
if(!$b->is_login()){
    /*
    $post_url = "http://www.kaixin001.com/login/login_api.php";
    $post_data = "ver=1&email=xu.qinyong%40msn.com&rpasswd=xxxxxxxx&encypt=bbbbbbbbb&url=%2Fhome%2F&remember=1"; 
    */
    $ret = $b->login($post_url,$post_data);  
}
//$ret = $b->get('http://www.kaixin001.com/photo/album.php?uid=1803114&albumid=26377065&passwd=1111');
file_put_contents("ret.html",$ret);
echo "done!";

