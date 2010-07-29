<?php
if(!defined('APP_DIR')){
    define('APP_DIR',ROOT_DIR.'/app');    
}
error_reporting(E_ALL ^ E_NOTICE);

class kernel{

    static $base_url = '';
    static $singleton_instance = array();
    static $_online = null;
    static $url_app_map = array();
    static $__router = null;
    static $console_output = false;
    static $__db_instance = null;
    static $__request_instance = null;
    static $__single_apps = array();
    static $__service_list = array();

    static function boot(){
        ob_start(array('base_storager','image_storage'));
        self::$url_app_map = (array)require(ROOT_DIR.'/config/mapper.php');
        self::$url_app_map['/setup'] = 'setup';

        if(get_magic_quotes_gpc()){
            self::strip_magic_quotes($_GET);
            self::strip_magic_quotes($_POST);
        }

        $pathinfo = self::request()->get_path_info();
        $jump = false;
        if(isset($pathinfo{1})){
            if($p = strpos($pathinfo,'/',2)){
                $part = substr($pathinfo,0,$p);
            }else{
                $part = $pathinfo;
                $jump = true;
            }
        }else{
            $part = '/';
        }

        if($part=='/api'){
            cachemgr::init();
            return kernel::single('base_rpc_service')->process($pathinfo);
        }elseif($part=='/app-doc'){
            cachemgr::init();
            return kernel::single('base_misc_doc')->display($pathinfo);
        }

        if(isset(self::$url_app_map[$part])){
            if($jump){
                $request_uri = self::request()->get_request_uri();
                $urlinfo = parse_url($request_uri);
                $query = $urlinfo['query']?'?'.$urlinfo['query']:'';
                header('Location: '.$urlinfo['path'].'/'.$query);
                exit;
            }else{
                $app = self::$url_app_map[$part];
                $prefix_len = strlen($part)+1;
            }
        }else{
            $app = self::$url_app_map['/'];
            $prefix_len = 1;
        }

        if(!$app){
            readfile(ROOT_DIR.'/app/base/readme.html');
            exit;
        }

        if(!self::is_online()){
            if(file_exists(APP_DIR.'/setup/app.xml')){
                if($app!='setup'){
                    header('Location: '. app::get('setup')->base_url());
                    exit;
                }    
            }else{
                echo '<h1>System is Offline, install please.</h1>';
                exit;
            }
        }else{
            require(ROOT_DIR.'/config/config.php');
        }

        date_default_timezone_set(
            defined('DEFAULT_TIMEZONE') ? ('Etc/GMT'.(DEFAULT_TIMEZONE>=0?(DEFAULT_TIMEZONE*-1):'+'.(DEFAULT_TIMEZONE*-1))):'UTC'
        );
        
        @include(APP_DIR.'/base/defined.php');

        if(isset($pathinfo{$prefix_len})){
            $path = substr($pathinfo,$prefix_len);
        }else{
            $path = '';
        }
        
        //init cachemgr
        if($app=='setup'){
            cachemgr::init(false);
        }else{
            cachemgr::init();
        }

        //get app router
        self::$__router = app::get($app)->router();
        self::$__router->dispatch($path);
    }

    static function router(){
        return self::$__router;
    }

    static function api_url($api_service_name,$method='access',$params=null){
        if(substr($api_service_name,0,4)!='api.'){
            trigger_error('$api_service_name must start with: api.');
            return false;
        }
        $arg = array();
        foreach((array)$params as $k=>$v){
            $arg[] = urlencode($k);
            $arg[] = urlencode(str_replace('/','%2F',$v));
        }
        return kernel::base_url(1).kernel::url_prefix().'/api/'.substr($api_service_name,4).'/'.$method.'/'.implode('/',$arg);
    }

    static function request(){
        if(!isset(self::$__request_instance)){
            self::$__request_instance = kernel::single('base_request',1);
        }
        return self::$__request_instance;
    }

    static function url_prefix(){
        return (defined('WITH_REWRITE') && WITH_REWRITE === true)?'':'/index.php';
    }
    
    static function this_url($full=false){
    	return self::base_url($full).self::url_prefix().self::request()->get_path_info();
    }

    static function log($message,$keepline=false){
        if(self::$console_output){
            if($keepline){
                echo $message;
            }else{
                echo $message = $message."\n";
            }
        }else{
            //modify by edwin.lzh@gmail.com 2010/6/10
            $message = sprintf("%s\t%s\n", date("Y-m-d H:i:s"), $message);
            switch(LOG_TYPE)
            {
                case 3:
                    if(defined('LOG_FILE')){
                        $logfile = str_replace('{date}', date("Ymd"), LOG_FILE);
                        $ip = ($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
                        $ip = str_replace(array('.', ':'), array('_', '_'), $ip);
                        $logfile = str_replace('{ip}', $ip, $logfile);
                    }else{
                        $logfile = DATA_DIR . '/logs/all.php';
                    }
                    if(!is_file($logfile)){
                        if(!is_dir(dirname($logfile)))  utils::mkdir_p(dirname($logfile));
                        file_put_contents($logfile, LOG_HEAD_TEXT);
                    }
                    error_log($message, 3, $logfile);
                break;
                case 0:
                default:
                    error_log($message, 0);
            }//End Switch
        }
    }
    
    static function base_url($full=false){
        if(defined('BASE_URL')){
            if($full){
                return constant('BASE_URL');
            }else{
                $url = parse_url(constant('BASE_URL'));
                return $url['path'];
            }
        }
        if(!self::$base_url){
             self::$base_url = self::request()->get_base_url();
        }

 		if(self::$base_url == '/'){
 			self::$base_url = '';
 		}

        if($full){
            return strtolower(self::request()->get_schema()).'://'.self::request()->get_host().self::$base_url;
        }else{
            return self::$base_url;
        }
    }

    static function set_online($mode){
        self::$_online = $mode;
    }

    static function is_online(){
        if(self::$_online===null){
            self::$_online = file_exists(ROOT_DIR.'/config/config.php');
        }
        return self::$_online;
    }

    static function single($class_name,$arg=null){
        if(!$arg){
            $p = strpos($class_name,'_');
            if($p){
                $app_id = substr($class_name,0,$p);
                if(!isset(self::$__single_apps[$app_id])){
                    self::$__single_apps[$app_id] = app::get($app_id);
                }
                $arg = self::$__single_apps[$app_id];
            }
        }
        if(is_object($arg)){
            $key = get_class($arg);
            if($key=='app'){
                $key .= $app->app_id;
            }
        }else{
            $key = md5('key_'.serialize($arg));
        }
        
        if(!isset(self::$singleton_instance[$class_name][$key])){
            self::$singleton_instance[$class_name][$key] = new $class_name($arg);
        }
        return self::$singleton_instance[$class_name][$key];
    }

    static function database(){
        if(!isset(self::$__db_instance)){
            self::$__db_instance = self::single('base_db_connections');
        }
        return self::$__db_instance;
    }

    static function service($srv_name,$filter=null){
        $defined_service = app::get('base')->getConf('server.'.$srv_name);
        if($defined_service && $defined_service = kernel::single($defined_service)){
            return $defined_service;
        }
        return self::servicelist($srv_name,$filter)->current();
    }

    static function servicelist($srv_name,$filter=null){
        if(self::is_online()){
            if(base_kvstore::instance('service')->fetch($srv_name,$service_define)){
                return new service($service_define,$filter);
            }
        }
        return new ArrayIterator(array());
    }

    static function strip_magic_quotes(&$var){
        foreach($var as $k=>$v){
            if(is_array($v)){
                self::strip_magic_quotes($var[$k]);
            }else{
                $var[$k] = stripcslashes($v);
            }
        }
    }
    
}

//{{{
function __autoload($class_name)
{
    $p = strpos($class_name,'_');

    if($p){
        $owner = substr($class_name,0,$p);
        $class_name = substr($class_name,$p+1);
        $tick = substr($class_name,0,4);
        switch($tick){
        case 'ctl_':
            return require_once APP_DIR.'/'.$owner.'/controller/'.str_replace('_','/',substr($class_name,4)).'.php';
        case 'mdl_':
            $path = APP_DIR.'/'.$owner.'/model/'.str_replace('_','/',substr($class_name,4)).'.php';
            if(file_exists($path)){
                return require_once $path;
            }elseif(file_exists(APP_DIR.'/'.$owner.'/dbschema/'.substr($class_name,4).'.php')){
                $parent_model_class = app::get($owner)->get_parent_model_class();
                eval ("class {$owner}_{$class_name} extends {$parent_model_class}{ }");
                return true;
            }else{
                return false;
            }
        default:
            return require_once APP_DIR.'/'.$owner.'/lib/'.str_replace('_','/',$class_name).'.php';
        }
    }elseif(file_exists($path = APP_DIR.'/base/lib/static/'.$class_name.'.php')){
        return require_once $path;
    }else{
        return false;
    }
}

function __($str){
    return $str;
}
//}}}

