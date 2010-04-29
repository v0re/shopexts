<?php


if($path_info = svinfo::get_path_info()){
    echo $path_info;
}else{
    $run = new tester;
    $run->test_path_info();
}




class tester{
	static $result = array();
	
	function __destruct(){
		echo "<table border=1>";
		echo "<tr><td>Item</td><td>Expected</td><td>Result</td><td>Test</td></tr>";
		if(count(self::$result))
			foreach(self::$result as $name=>$item){
				$test = $item['expected'] == $item['result'] ? 'pass' : 'fail';
				echo "<tr><td>".$name."</td><td>".$item['expected']."</td><td>".$item['result']."</td><td>".$test."</td></tr>";
			}
		echo "</table>";
	}
    
    function test_path_info(){
        $url  = $_SERVER['HTTP_HOST'].'/'.$_SERVER['PHP_SELF'];
        $path = '/test/path/info'; 
        $url = $url.$path;
        $ret = $this->http_get($url);
        self::$result['test_path_info']['expected'] = $path;
        self::$result['test_path_info']['result'] = $ret;
    }
    
    function http_get($url){
        try{
            $ch = curl_init($url) ; 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ; 
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, true) ; 
            $ret = curl_exec($ch) ;  
            return $ret;
        }catch(Exception $e){
            echo $e->getMessage();            
        }
    }
}

class svinfo{
    static function get_path_info(){
        $path_info = '';
        if(isset($_SERVER['PATH_INFO'])){
            $path_info = $_SERVER['PATH_INFO'];
            if(isset($_SERVER['DOCUMENT_URI'])){
                $diff = strlen($_SERVER['SCRIPT_NAME']) - strlen($_SERVER['DOCUMENT_URI']);
                if($diff){
                    $path_info = substr($path_info,0,-$diff);
                }
            }
        
        } else {
            $script_name = isset($_SERVER['ORIG_SCRIPT_NAME']) ? $_SERVER['ORIG_SCRIPT_NAME'] : (isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '');
            $script_dir =dirname($script_name);
            $request_uri = self::get_request_uri();
            $urlinfo = parse_url($request_uri);
        
            if ( strpos($urlinfo['path'], $script_name) === 0) {
             $path_info = substr($urlinfo['path'], strlen($script_name));
            } elseif ( strpos($urlinfo['path'], $script_dir) === 0 ) {
                $path_info = substr($urlinfo['path'], strlen($script_dir));
            }
        }
        return $path_info;
    }  
    
    static function get_request_uri() {
        if (isset($_SERVER['HTTP_X_REWRITE_URL'])) {
            return $_SERVER['HTTP_X_REWRITE_URL'];
        } elseif (isset($_SERVER['REQUEST_URI'])) {
            return $_SERVER['REQUEST_URI'];
        } elseif (isset($_SERVER['ORIG_PATH_INFO'])) {
            return $_SERVER['ORIG_PATH_INFO'] . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '');
        }
    }  
}

