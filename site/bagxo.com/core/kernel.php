<?php
/**
 * kernel
 *
 * @package
 * @version $Id: kernel.php 1948 2008-04-25 09:36:32Z flaboy $
 * @copyright 2003-2007 ShopEx
 * @license Commercial
 */
class kernel{

    var $__setting;
    var $_funcPkg;
    var $models;
    var $_app_version = '4.8.4';
    var $__call_libs;
    var $_co_depth=0;
    var $memcache = false;

    function kernel(){
        set_error_handler(array(&$this,"errorHandler"));
        $this->_halt_err_level = E_ERROR | E_USER_ERROR | E_CORE_ERROR | E_PARSE;
        $this->_start = $this->microtime();
        $GLOBALS['system'] = &$this;

        if (!get_magic_quotes_gpc())
        {
            safeVar($_GET);
            safeVar($_POST);
            safeVar($_COOKIE);
        }

        set_include_path(CORE_DIR.'/include'.PATH_SEPARATOR.'.');
        require('defined.php');
        error_reporting(E_ALL);

        if(defined('WITH_MEMCACHE') && WITH_MEMCACHE){
            $this->init_memcache();
        }

        if(!defined('HOME_DIR') || (defined('WITHOUT_CACHE') && WITHOUT_CACHE)){
            $this->cache = new nocache();
        }else{
            require('cachemgr.php');
            if(defined('WITH_MEMCACHE') && WITH_MEMCACHE){
                require(PLUGIN_DIR.'/functions/cache_memcache.php');
                $this->cache = new cache_memcache;
            }elseif(defined('CACHE_METHOD')){
                require(PLUGIN_DIR.'/functions/'.CACHE_METHOD.'.php');
                $cache_method = CACHE_METHOD;
                $this->cache = new $cache_method;
            }elseif(php_sapi_name()=='isapi'){
                require('secache.php');
                require('secache_no_flock.php');
                $this->cache = new secache_no_flock;
            }else{
                require('secache.php');
                $this->cache = new secache;
            }
        }

        require('setmgr.php');
        $this->__setting = new setmgr;
        $this->set_timezone(SERVER_TIMEZONE);
    }

    function init_memcache(){
        if(!$this->memcache){
            $this->memcache=new Memcache;
            $ports = explode(',',MEMCACHED_PORT);
            foreach(explode(',',MEMCACHED_HOST) as $i=>$h){
                $this->memcache->addServer($h,$ports[$i]);
            }
            $this->memcache->pconnect();
        }
    }

    function set_timezone($tz){
        if(function_exists('date_default_timezone_set')){
            $tz = 0-$tz;
            if($tz>12 || $tz<-12){
                $tz = 0;
            }
            date_default_timezone_set('Etc/GMT'.($tz>0?('+'.$tz):$tz));
        }
    }
    
    function base_url(){
        if(!isset($this->_base_url)){
             $this->_base_url='http://'.$_SERVER['HTTP_HOST'].substr(PHP_SELF, 0, strrpos(PHP_SELF, '/') + 1);
        }
        return $this->_base_url;
    }

    function call($method,$args){
        if(!$method){return false;}

        if(!$this->__call_libs[$method]){
            if($s = strpos($method,'.')){
                $class = substr($method,0,$s);
                $func = substr($method,$s+1);
                require_once(PLUGIN_DIR.'/functions/'.$class.'.php');
                if(!$this->__call_obj[$class]){
                    $this->__call_obj[$class] = new $class;
                }
                $this->__call_libs[$method] = array($this->__call_obj[$class],$func);
            }else{
                 require_once(PLUGIN_DIR.'/functions/'.$method.'.php');
                 $this->__call_libs[$method] = $method;
            }
        }
        return call_user_func_array($this->__call_libs[$method],$args);
    }

    function microtime(){
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    function realUrl($ctl,$act='index',$args=null,$extName = 'html',$base_url=null){
        $extName =$extName?$extName:'html';

        if(!isset($this->__emu_static)){
            $this->__emu_static = (!$this->getConf('system.seo.emuStatic') || $this->getConf('system.seo.emuStatic') == 'false');
            $this->__link_builder = $this->getConf('system.seo.mklink');
        }
        if(!isset($this->_base_link)){
            $this->_base_link =$base_url;//echo $this->_base_link;exit;
            if($this->__emu_static){
                $this->_base_link.=APP_ROOT_PHP.'?';
            }
        }
        if($ctl=='page' && $act=='index'){
            return $this->_base_link;
        }else{
             return $this->_base_link.$this->getLink($ctl?$ctl:$this->request['action']['controller'],$act?$act:$this->request['action']['method'],$args,$extName);
        }

    }



    function &getLink($controller,$method,$args=null,$extname=null){
        if($controller=='index') return '';
        $array = array($controller);
        $use_arg = 0;
        if(is_array($args) && (isset($args[1]) || (isset($args[0]) && $args[0]))){
            $use_arg = 1;
            foreach($args as $k=>$v){
                $args[$k] = str_replace(array('-','.','/','%2F'),array(';jh;',';dian;',';xie;',';xie;'),$v);
            }
            $array = array_merge(array($controller),$args);
            }
        if($method!='index' || ($use_arg && !is_numeric(array_pop($args)))){
            $array[] = urlencode($method);
        }
        return implode('-',$array).'.'.($extname?$extname:$this->seoEmuFile);
    }


    function shutdown(){ }

    function errorHandler($errno, $errstr, $errfile, $errline){
        //        $errstr = '['.str_replace(realpath(CORE_DIR),'(CORE_DIR)',$errfile).':'.$errline.'> '.$errstr.']';
        //        $this->log($errstr,$errno,1000+log($errno,2));
        return ($errno== ($this->_halt_err_level & $errno))?false:true;
    }

    function log($message,$level=E_NOTICE,$code=0){
        if(defined('LOG_LEVEL') && is_int(LOG_LEVEL)){
            if($level == (LOG_LEVEL&$level)){
                if(!isset($this->_log)){
                    include('dazuiLog.php');
                    $this->_log = new dazuiLog;
                }
                $this->_log->log($code,$message);
            }
        }
    }

    function co_start(){
        $this->_co_depth++;
    }

    function co_end(){
        return array_keys($this->_cacheObjects[$this->_co_depth--]);
    }

    function checkExpries($cname){
        if(is_array($cname)){
            for($i=$this->_co_depth;$i>0;$i--){
                foreach($cname as $obj){
                    $this->_cacheObjects[$i][strtoupper($obj)]=1;
                }
            }
        }else{
            for($i=$this->_co_depth;$i>0;$i--){
                $this->_cacheObjects[$i][strtoupper($cname)]=1;
            }
        }
    }

    /**
     * &template
     *
     * @access public
     * @return void
     */
    function &template($clone=false){
        if(!isset($this->_smarty) || $clone){
            include_once(CORE_DIR.'/lib/smarty/Smarty.class.php');
            $smarty = new smarty();
            //      $smarty->force_compile=true;//WZP
            //      $smarty->compile_check = true;
            $smarty->debug = false;
            $smarty->system = &$this;

            if($clone){
                return $smarty;
            }else{
                $this->_smarty = &$smarty;
            }
        }
        return $this->_smarty;
    }

    function &network(){
        if(!isset($this->_network)){
            include_once(CORE_DIR.'/lib/Snoopy.class.php');
            $this->_network = new Snoopy();
        }
        return $this->_network;
    }

    function &incomming(){
        if(!isset($this->_in)){
            ini_get('magic_quotes_gpc');
            $this->_in = ini_get('magic_quotes_gpc')?$_REQUEST:$this->_safe_var($_REQUEST);
        }
        return $this->_in;
    }

    function _safe_var($var){
        foreach($var as $k=>$v){
            if(is_array($v)){
                $var[$k]=$this->_safe_var($v);
            }else{
                $var[$k]=addslashes($v);
            }
        }
        return $var;
    }

    /**
     * &database
     *
     * @access public
     * @return void
     */
    function &database(){
        if(!isset($this->__db)){
            require_once CORE_DIR.'/lib/adodb_lite/adodb.inc.php';
            require_once 'AloneDB.php';
            $GLOBALS['ADODB_FETCH_MODE'] = ADODB_FETCH_ASSOC;
            $this->__db = new AloneDB();
            $this->__db->prefix = DB_PREFIX;
        }
        return $this->__db;
    }

    /**
     * &cache
     *
     * @access public
     * @return void
     */
    function &cache(){
        if(!isset($this->__cache)){
            require_once('smartCache.php');
            $this->__cache = new smartCache($this,HOME_DIR.'/cache/data');
        }
        return $this->__cache;
    }

    /**
     * event
     *
     * @param mixed $hookevent ,arg0,1,2,3....
     * @access public
     * @return bool
     */
    function event($hookevent, &$arg0, &$arg1, &$arg2){
        if(!isset($this->__hookslist)){
            /*$objHooker = $this->loadModel('hooker');
            $this->__hookslist = &$objHooker->getHookList($hookevent);*/
        }

        if(is_array($this->__hookslist[$hookevent])) {
            foreach($this->__hookslist[$hookevent] as $i=>$hook){
                if($hook['pkg']){
                    $hookfile = $this->pkgPath($hook['pkg'])."/hooks/{$hook['pkg']}.{$hook['method']}.php";
                    $hookObjName = "hook_{$hook['pkg']}_{$hook['method']}";
                }else{
                    $hookfile = CORE_DIR."/admin/hooks/hook.{$hook['object']}.php";
                    $hookObjName = "hook_{$hook['object']}";
                }
                if(file_exists($hookfile)){
                    require($hookfile);
                    $hookObj = new $hookObjName();
                    $hookObj->shopId = $_SESSION['SHOP_ID'];
                    $hookObj->db = &$this->database();
                    if(!$hookObj->$hook['method']($arg0,$arg1,$arg2)){
                        return false;
                    }
                }else{
                    unset($this->__hookslist[$i]);
                }
            }
        }
        return true;
    }

    /**
     * error
     *
     * @param int $errcode
     * @access public
     * @return void
     */
    function error($errcode=404,$errmsg=null){
        if($errcode==404){
            $this->responseCode(404);
        }
        header('X-JSON: '.json_encode(array('code'=>$errcode,'id'=>time())));
        die('<h1>Error:'.$errcode.'</h1><p>'.$errmsg.'</p>');
    }

    /**
     * pkgPath
     *
     * @param mixed $pkg_name
     * @access public
     * @return void
     */
    function pkgPath($pkg_name){
        if(is_dir(PLUGIN_DIR.'/packages/'.$pkg_name)){
            return PLUGIN_DIR.'/packages/'.$pkg_name;
        }else{
            return false;
        }
    }

    /**
     * throwit 抛出错误
     *
     * @param mixed $errtype 错误类型 E_USER_ERROR,E_USER_WARNING,E_USER_NOTICE
     * @param mixed $errmsg
     * @param mixed $errcode
     * @access public
     * @return void
     */
    function throwit($errtype,$errmsg,$errcode){
        $this->errors[]=array('type'=>$errtype,'msg'=>$errmsg,'code'=>$errcode);
    }

    function popErrors($num=1){
        if($num==1){
            return array_pop($this->errors);
        }elseif($num>1){
            $ret = array_slice($this->errors,-$num);
            $this->errors=array_slice($this->errors,$num);
            return $ret;
        }
    }
    function api_call($instance,$host,$file,$port=80,$tolken){
        require_once(API_DIR.'/include/api_utility.php');
        if(!$this->intance_api[$instance]){
            $this->intance_api[$instance]=new api_utility($host,$file,$port,$tolken);
        }
        return $this->intance_api[$instance];
    }
    /**
     * loadModel
     *
     * @param mixed $className
     * @param mixed $single
     * @access public
     * @return void
     */
    function &loadModel($modelName,$single=true){

        if(isset($this->models[strtolower($modelName)]))
            return $this->models[strtolower($modelName)];

        require_once('modelFactory.php');
        require_once(CORE_DIR.'/model/'.dirname($modelName).'/mdl.'.basename($modelName).'.php');
        $className='mdl_'.basename($modelName);

        if (defined('CUSTOM_CORE_DIR')){
            $cusinc = CUSTOM_CORE_DIR.'/model/'.dirname($modelName).'/cmd.'.basename($modelName).'.php';
            if(file_exists($cusinc)){
                require_once($cusinc);
                $className='cmd_'.basename($modelName);
            }
        }

        $object= new $className();
        $object->modelName = $modelName;
        $this->models[strtolower($modelName)] = &$object;
        return $object;
    }

    function &loadSchema($schema_id){
        if(include_once(PLUGIN_DIR.'/schema/'.$schema_id.'/schema.php')){
            $className = 'schema_'.$schema_id;
            $obj= new $className($this);
            return $obj;
        }else{
            return false;
        }
    }

    /**
     * callAction
     *
     * @param mixed $objMod
     * @param mixed $act_method
     * @access public
     * @return void
     */
    function callAction(&$objCtl,$act_method,$args=null){
        if(isset($objCtl->_call)){
            array_unshift($args,$act_method);
            $act_method = $objCtl->_call;
        }
        if($act_method{0}!=='_' && method_exists($objCtl,$act_method)){
            if(count($args)>0)
                call_user_func_array(array(&$objCtl,$act_method),$args);
            else
                call_user_func_array(array(&$objCtl,$act_method),array());
            return true;
        }else{
            return false;
        }
    }

    /**
     * output
     *
     * @param mixed $content
     * @param int $expired_time
     * @param mixed $mime_type
     * @param mixed $headers
     * @param mixed $filename
     * @access public
     * @return void
     */
    function output($content,$expired_time=0,$mime_type=MIME_HTML,$headers=false,$filename=null){
        $lastmodified = gmdate("D, d M Y H:i:s");
        $expires = gmdate ("D, d M Y H:i:s", time() + 20);

        header("Last-Modified: " . $lastmodified . " GMT");
        header("Expires: " .$expires. " GMT");

        if(is_array($headers)){
            foreach($headers as $theheader){
                header($theheader);
            }
        }

        if($mime_type==MIME_HTML){
            header('Content-Type: text/html; charset=utf-8');
            echo($content);
        }else{
            header('Content-Type: '.$mime_type.'; charset=utf-8');
            if($filename){
                header('Content-Disposition: inline; filename="'.$filename.'"');
            }
            flush();
            echo($content);
        }
    }

    function getConf($key){
        return $this->__setting->get($key,$var);
    }

    function setConf($key,$data,$immediately=false){
        return $this->__setting->set($key,$data,$immediately);
    }

    function sprintf(){
        $args = func_get_args();
        $str = $args[0];
        unset($args[0]);
        $str =    preg_replace_callback('/\\$([a-z\\.\\_0-9]+)\\$/is',array(&$this,'_rep_conf'),$str);
        foreach($args as $k=>$v){
            $str = str_replace('%'.$k,$v,$str);
        }
        return $str;
    }

    function _rep_conf($matches){
        return $this->getConf($matches[1]);
    }

    /**
     * sfile
     *
     * @param mixed $file
     * @access public
     * @return void
     */
    function sfile($file,$file_bak=null,$head_redect=false){
        if(!file_exists($file)){
            $file = $file_bak;
        }

        $etag = md5_file($file);
        header('Etag: '.$etag);

        if(isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $etag){
            header('HTTP/1.1 304 Not Modified',true,304);
            exit(0);
        }else{
            set_time_limit(0);
            header("Expires: " .$expires. " GMT");
            header("Cache-Control: public");
            session_cache_limiter('public');
            sendfile($file);
        }
    }

    function responseCode($code){
        $codeArr = array(
            100=>'Continue',
            101=>'Switching Protocols',
            200=>'OK',
            201=>'Created',
            202=>'Accepted',
            203=>'Non-Authoritative Information',
            204=>'No Content',
            205=>'Reset Content',
            206=>'Partial Content',
            300=>'Multiple Choices',
            301=>'Moved Permanently',
            302=>'Found',
            303=>'See Other',
            304=>'Not Modified',
            305=>'Use Proxy',
            307=>'Temporary Redirect',
            400=>'Bad Request',
            401=>'Unauthorized',
            402=>'Payment Required',
            403=>'Forbidden',
            404=>'Not Found',
            405=>'Method Not Allowed',
            406=>'Not Acceptable',
            407=>'Proxy Authentication Required',
            408=>'Request Timeout',
            409=>'Conflict',
            410=>'Gone',
            411=>'Length Required',
            412=>'Precondition Failed',
            413=>'Request Entity Too Large',
            414=>'Request-URI Too Long',
            415=>'Unsupported Media Type',
            416=>'Requested Range Not Satisfiable',
            417=>'Expectation Failed',
            500=>'Internal Server Error',
            501=>'Not Implemented',
            502=>'Bad Gateway',
            503=>'Service Unavailable',
            504=>'Gateway Timeout',
            505=>'HTTP Version Not Supported',
        );
        header('HTTP/1.1 '.$code.' '.$codeArr[$code],true,$code);
    }

    function version(){
        if(!file_exists(CORE_DIR.'/version.txt')){
            $return = array();
        }else{
            $return = parse_ini_file(CORE_DIR.'/version.txt');
        }
        $return['app'] = $this->_app_version;
        return $return;
    }

}

class nocache{
    function set($key,$value){return true;}
    function get($key,$value){return false;}
    function setModified(){;}
    function status(){;}
    function clear(){;}
    function exec(){;}
}

function safeVar(&$data)
{
    if (is_array($data))
    {
        foreach ($data as $key => $value)
        {
            safeVar($data[$key]);
        }
    }else{
        $data = addslashes($data);
    }
}
