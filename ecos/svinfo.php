<?php
var_dump($_SERVER);
define('ROOT_DIR',realpath(dirname(__FILE__)));
require(ROOT_DIR.'/app/base/kernel.php');
class svinfo_kernel extends kernel{
static function boot(){
        ob_start(array('base_storager','image_storage'));
        self::$url_app_map = (array)require(ROOT_DIR.'/config/mapper.php');
        self::$url_app_map['/setup'] = 'setup';

        if(get_magic_quotes_gpc()){
            self::strip_magic_quotes($_GET);
            self::strip_magic_quotes($_POST);
        }

        $pathinfo = self::request()->get_path_info();
        var_dump($pathinfo);
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
		var_dump($jmp);
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

        date_default_timezone_set(defined('DEFAULT_TIMEZONE')?
                ('Etc/GMT'.(DEFAULT_TIMEZONE>0?('+'.DEFAULT_TIMEZONE):DEFAULT_TIMEZONE)):'UTC');
        
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
}
svinfo_kernel::boot();
