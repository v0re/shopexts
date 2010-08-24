<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class app{

    static private $__instance = array();
    static private $__language = null;
    private $__render = null;
    private $__router = null;
    private $__define = null;
    private $__taskrunner = null;
    private $__appConf = array();
    private $__checkVaryArr = array();
    private $__appSetting = array();
    private $__langPack = array();

    function __construct($app_id){
        $this->app_id = $app_id;
        $this->app_dir = APP_DIR.'/'.$app_id;
        $this->res_url = kernel::base_url().'/app/'.$app_id.'/statics';
        $this->res_dir = APP_DIR.'/'.$app_id.'/statics';
        $this->app_lang = app::get_lang();
        $this->init_lang_pack(); //todo：初始化语言包
    }

    static function get($app_id){
        if(!isset(self::$__instance[$app_id])){
            self::$__instance[$app_id] = new app($app_id);
        }
        return self::$__instance[$app_id];
    }

    static public function set_lang($language) 
    {
        self::$__language = trim(strtolower($language));
    }//End Function

    static public function get_lang() 
    {
        return  self::$__language ? self::$__language : ((false ? LANG : 'zh-cn'));
    }//End Function

    public function init_lang_pack() 
    {
        if(file_exists($this->app_dir . '/lang/' . $this->app_lang . '/config.php')){
            $this->__langPack = (array)require($this->app_dir . '/lang/' . $this->app_lang . '/config.php');
        }elseif(file_exists($this->app_dir . '/lang/zh-cn/config.php')){
            $this->__langPack = (array)require($this->app_dir . '/lang/zh-cn/config.php');
        }else{
            //trigger_error('language pack is lost in '.$this->app_id, E_USER_ERROR);
            $this->__langPack = array();
        }
    }//End Function

    public function _($key) 
    {
        return (isset($this->__langPack['language'][$key])) ? $this->__langPack['language'][$key] : $key;
    }//End Function

    public function lang($res=null) 
    {
        return (is_null($res)) ? $this->__langPack : $this->__langPack[$res];
    }//End Function

    public function render(){
        if(!$this->__render){
            $this->__render = new base_render($this);
        }
        return $this->__render;
    }

    public function controller($controller){
        return kernel::single($this->app_id.'_ctl_'.$controller,$this);
    }

    public function model($model){
        return kernel::single($this->app_id.'_mdl_'.$model,$this);
    }

    public function router(){
        if(!$this->__router){
            if(file_exists($this->app_dir.'/lib/router.php')){
                $class_name = $this->app_id.'_router';
                $this->__router = new $class_name($this);
            }else{
                $this->__router = new base_router($this);
            }
        }
        return $this->__router;
    }

    public function base_url($full=false){
        $c = $full?'full':'part';
        if(!$this->base_url[$c]){
            $app_url_map = array_flip(kernel::$url_app_map);
            $this->base_url[$c] = kernel::base_url($full).kernel::url_prefix().$app_url_map[$this->app_id].($app_url_map[$this->app_id]=='/' ? '':'/');
        }
        return $this->base_url[$c];
    }

    public function get_parent_model_class(){
        $parent_model_class = $this->define('parent_model_class');
        return $parent_model_class?$parent_model_class:'base_db_model';
    }

    public function define($path=null){
        if(!$this->__define){
            if(is_dir($this->app_dir) && file_exists($this->app_dir.'/app.xml')){
				$tags = array();
                $this->__define = kernel::single('base_xml')->xml2array(
                    file_get_contents($this->app_dir.'/app.xml'),'base_app');
            }else{
                $row = app::get('base')->model('apps')->getList('remote_config',array('app_id'=>$this->app_id));
                $this->__define = $row[0]['remote_config'];
            }
        }
        if($path){
            return eval('return $this->__define['.str_replace('/','][',$path).'];');
        }else{
            return $this->__define;
        }
    }

    public function getConf($key){
        if(!isset($this->__appConf[$key])){
            if(base_kvstore::instance('setting/'.$this->app_id)->fetch($key, $val) === false){
                if(!isset($this->__appSetting[$this->app_dir])){
                    if(@include($this->app_dir.'/setting.php')){
                        $this->__appSetting[$this->app_dir] = $setting;
                    }else{
                        $this->__appSetting[$this->app_dir] = false;
                    }
                }
                if($this->__appSetting[$this->app_dir] && isset($this->__appSetting[$this->app_dir][$key]['default'])){
                    $val = $this->__appSetting[$this->app_dir][$key]['default'];
                    $this->setConf($key, $val);
                }else{
                    return null;
                }
            }
            $this->__appConf[$key] = $val;
        }//todo: 缓存已经取到的conf，当前PHP进程有效
        if(cachemgr::check_current_co_depth()>0){
            $this->check_expries($key, true);
        }//todo：如果存在缓存检查，进行conf检查
        return $this->__appConf[$key];
    }

    public function setConf($key, $value){
        if(base_kvstore::instance('setting/'.$this->app_id)->store($key, $value)){
            $this->__appConf[$key] = $value;    //todo：更新当前进程缓存
            $this->set_modified($key);
            return true;
        }else{
            return false;
        }
    }

    public function set_modified($key) 
    {
        $vary_name = strtoupper(md5($this->app_id . $key));
        $now = time();
        $db = kernel::database();
        $db->exec('REPLACE INTO sdb_base_cache_expires (`type`, `name`, `expire`) VALUES ("CONF", "'.$vary_name.'",' .$now. ')', true);
        if($db->affect_row()){
            cachemgr::set_modified('CONF', $vary_name, $now);
        }
    }//End Function

    public function check_expries($key, $force=false) 
    {
        if($force || cachemgr::check_current_co_depth()>0){
            if(!isset($this->__checkVaryArr[$key])){
                $this->__checkVaryArr[$key] = strtoupper(md5($this->app_id . $key));
            }
            if(!cachemgr::check_current_co_objects_exists('CONF', $this->__checkVaryArr[$key])){
                cachemgr::check_expries('CONF', $this->__checkVaryArr[$key]);
            }
        }
    }//End Function

    function runtask($method,$option=null){
        if($this->__taskrunner===null){
            $this->__taskrunner = false;
            if(file_exists($this->app_dir.'/task.php')){
                require($this->app_dir.'/task.php');
                $class_name = $this->app_id.'_task';
                if(class_exists($class_name)){
                    $this->__taskrunner = new $class_name($this);
                }
            }
        }
        if(is_object($this->__taskrunner) && method_exists($this->__taskrunner,$method)){
            return $this->__taskrunner->$method($option);
        }else{
            return true;
        }
    }

    function status(){
        if(kernel::is_online()){
            if($this->app_id=='base'){
               if(!kernel::database()->select('SHOW TABLES LIKE "'.kernel::database()->prefix.'base_apps"')){
                   return 'uninstalled';
               }
            }
            $row = @kernel::database()->selectrow('select status from sdb_base_apps where app_id="'.$this->app_id.'"');
            return $row?$row['status']:'uninstalled';
        }else{
            return 'uninstalled';
        }
    }

    function is_installed() 
    {
        if(!isset($this->installed)){
            $this->installed = ($this->status()!='uninstalled') ? true : false;
        }
        return $this->installed;
    }//End Function

    function remote($node_id){
        return new base_rpc_caller($this,$node_id);
    }

    function matrix(){
        return new base_rpc_caller($this,1);
    }

}
