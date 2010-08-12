<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml">
<head> 
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"> 
</head>

<?php    

class browser{

    var $cookie_file;
    var $user_agent;
    
    function __construct(){
        $this->cookie_file = dirname(__FILE__)."/cookie_".md5(basename(__FILE__)).".txt"; 
        $this->user_agent = "User-Agent: Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1) ; )";
    }
    
    function __destruct(){
        if(file_exists($this->cookie_file))
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
        curl_setopt($curl, CURLOPT_HEADER, 1); 
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
    
    function m_post($url,$data,$m=10){
        // 创建cURL批处理句柄
        $mh = curl_multi_init();
        for($i=0;$i<$m;$i++){
                $i = curl_init();
                $this->set_post_ch($i,$url,$data);
                curl_multi_add_handle($mh,$i);
        }         
        // 预定义一个状态变量
        $active = null;        
        // 执行批处理
        do {
            $mrc = curl_multi_exec($mh, $active);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);
        
        while ($active && $mrc == CURLM_OK) {
            if (curl_multi_select($mh) != -1) {
                do {
                    $mrc = curl_multi_exec($mh, $active);
                } while ($mrc == CURLM_CALL_MULTI_PERFORM);
            }
        }
    }
    
    function set_post_ch(&$curl,$url,$data){
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
    }
       
}

if($_POST){
    switch($_POST['action']){
        case "bbs":
            set_time_limit(0);
            $post_url = rtrim($_POST['e_site'],'/')."/?message-sendMsgToOpt.html";
            $post_data = "subject=".urlencode($_POST[e_subject])."&message=".urlencode($_POST[e_content])."&email=";

            $b = new browser;
            //$post_url = "http://localhost/485/?message-sendMsgToOpt.html";
            //$post_data = "subject=%E6%9C%89%E4%B8%AA%E7%96%91%E9%97%AE&message=%E4%BD%A0%E4%B8%8D%E6%98%AF%E5%A5%BD%E4%BA%BA&email=";
            $num = intval($_POST['e_num']);
            $num = $num ? $num : 100;
            $step = floor($num / 100);
            for($i=0;$i<$step;$i++){
                $b->m_post($post_url,$post_data,100);
            }
            
        break;
    }
}

?>


<form method=post>
<div style="width:50%">
<fieldset>
<legend>留言板</legend>
<table>
<tr><td>用户名：</td><td><input type='text' name='e_name' size=32 value='郭海藻'></td></tr>
<tr><td>主题：</td><td><input type='text' name='e_subject' size=32 value='我前天买的衣服有点问题'></td></tr>
<tr><td >留言:</td><td><textarea name='e_content' cols=50 rows=5 >是这样的，衣服的颜色太淡了，我觉得问题很严重，请尽快解决，我的电话是13800138000！</textarea></td></tr>
<tr><td>发送条数:</td><td><input type='text' name='e_num' value=1000></td></tr>
<tr><td>网站地址:</td><td><input type='text' name='e_site' value=http://localhost/485/></td></tr>
<input type='hidden' name='action' value='bbs'>
</table>
</fieldset>
</div>
<input type='submit' value='start'>

</form>


