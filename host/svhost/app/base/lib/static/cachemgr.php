<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

/*
 * @package base
 * @copyright Copyright (c) 2010, shopex. inc
 * @author edwin.lzh@gmail.com
 * @license 
 */
class cachemgr 
{
    /*
     * @var string $_co_depth
     * @access static private
     */
    static private $_co_depth = 0;

    /*
     * @var string $_cache_objects
     * @access static private
     */
    static private $_cache_objects = array();

    /*
     * @var string $_instance
     * @access static private
     */
    static private $_instance = null;

    /*
     * @var string $_instance_name
     * @access static private
     */
    static private $_instance_name = null;

    /*
     * @var string $_cache_objects_exists
     * @access static private
     */
    static private $_cache_objects_exists = array();

    /*
     * @var string $_cache_check_version_key
     * @access static private
     */
    static private $_cache_check_version_key = '__ECOS_CACHEMGR_CACHE_CHECK_VERSION_KEY__';

    /*
     * @var string $_cache_check_version
     * @access static private
     */
    static private $_cache_check_version = null;

    /*
     * @var string $_cache_key_global_varys
     * @access static private
     */
    static private $_cache_key_global_varys = null;

    /*
     * @var string $_vary_list_froce_mysql
     * @access static private
     */
    static private $_vary_list_froce_mysql = false;

    /*
     * 初始化
     * @var boolean $with_cache
     * @access static public
     * @return void
     */
    static public function init($with_cache=true) 
    {
        if(!WITHOUT_CACHE && $with_cache && defined('CACHE_STORAGE') && constant('CACHE_STORAGE')){
            self::$_instance_name = CACHE_STORAGE;
        }else{
            self::$_instance_name = 'base_cache_nocache';    //todo：增加无cache类，提高无cache情况下程序的整体性能
        }
        self::$_instance = null;
    }//End Function

    /*
     * 获取cache_storage实例
     * @access static public
     * @return object
     */
    static public function instance() 
    {
        if(is_null(self::$_instance)){
            self::$_instance = kernel::single(self::$_instance_name);
        }//使用实例时再构造实例
        return self::$_instance;
    }//End Function
    
    /*
     * 获取modified
     * @var string $type
     * @var string $vary_key
     * @access static public
     * @return mixed
     */
    static public function get_modified($type, $vary_key) 
    {
        return self::instance()->get_modified($type, $vary_key);
    }//End Function

    /*
     * 设置modified
     * @var string $type
     * @var string $vary_key
     * @var int $time
     * @access static public
     * @return boolean
     */
    static public function set_modified($type, $vary_key, $time=0) 
    {
        self::store_vary_list();
        return self::instance()->set_modified($type, $vary_key, $time);
    }//End Function

    /*
     * 获取缓存
     * @var string $key
     * @var mixed &$return
     * @access static public
     * @return boolean
     */
    static public function get($key, &$return) 
    {
        if(self::instance()->fetch(self::get_key($key), $data)){
            if(count($data['varys']) > 0){
                foreach($data['varys'] AS $type=>$vary){
                    foreach($vary AS $o){
                        if(!isset($data['cotime'][$type][$o]) || $data['cotime'][$type][$o] != self::get_modified($type, $o)){
                            return false;
                        }else{
                            $checks[$type][] = $o;
                        }
                    }
                }
            }
            $return = $data['content'];
            if(isset($checks)){
                foreach($checks AS $type=>$check){
                    foreach($check AS $o){
                        self::check_expries($type, $o);
                    }
                }
            }//设置上级cache的check_expries
            return true;
        }else{
            return false;
        }
    }//End Function

    /*
     * 设置缓存
     * @var string $key
     * @var mixed $content
     * @var array $varys
     * @access static public
     * @return boolean
     */
    static public function set($key, $content, $varys=array()) 
    {
        $data = array('content' => $content);
        if(is_array($varys)){
            $data['varys'] = array();
            foreach($varys AS $type=>$vary){
                $type = strtoupper($type);
                foreach($vary AS $o=>$val){
                    $o = strtoupper($o);
                    $data['cotime'][$type][$o] = self::get_modified($type, $o);
                    $data['varys'][$type][] = $o;
                }
            }
        }
        return self::instance()->store(self::get_key($key), $data);
    }//End Function

    /*
     * 方法缓存
     * @var mixed $func
     * @var array $args
     * @var int $ttl
     * @access static public
     * @return mixed
     */
    static public function exec($func, $args, $ttl=3600) 
    {
        if(is_array($func)){
            $key = self::get_key(get_class($func[0]) . serialize($func[1]) . serialize($args));
        }else{
            $key = self::get_key($func . serialize($args));
        }

        if(self::instance()->fetch($key, $data) === false || (time() - $data['time'] > $ttl)){
            $data['return'] = call_user_func_array($func, $args);
            $data['time'] = time();
            self::instance()->store($key, $data);
        }
        return $data['return'];
    }//End Function

    /*
     * 获取缓存key
     * @var string $key
     * @access static public
     * @return string
     */
    static public function get_key($key) 
    {
        $key_array['key'] = $key;
        if(!isset(self::$_cache_check_version)){
            self::$_cache_check_version = self::ask_cache_check_version();
        }//只取一次
        $key_array['version'] = &self::$_cache_check_version;
        //引响全局的vary
        //todo：一般数据来源为get、post、cookie、session、server中取值或从http_refer等信息来判断取值
        //保证global_varys的值不受程序改变而改变
        if(!isset(self::$_cache_key_global_varys)){
            self::$_cache_key_global_varys = self::get_global_varys();
        }//只取一次
        $key_array['global_varys'] = &self::$_cache_key_global_varys;
        if(method_exists(self::instance(), "get_key")){
            return self::instance()->get_key($key_array);
        }else{
            return md5(serialize($key_array));
        }
    }//End Function

    /*
     * 获取全局key_varys属性，将影响全局key的生成
     * @access static public
     * @return array
     */
    static public function get_global_varys() 
    {
        $app_varys = array();
        $serviceList = kernel::serviceList('cachemgr_global_vary');
        foreach($serviceList AS $service){
            $class_name = get_class($service);
            $p = strpos($class_name,'_');
            if(method_exists($service, 'get_varys')){
                $varys = $service->get_varys();
            }
            if(is_array($varys) && $p){
                $app_id = substr($class_name,0,$p);
                if(isset($app_varys[$app_id])){
                    $app_varys[$app_id] = array_merge($app_varys[$app_id], $varys);
                }else{
                    $app_varys[$app_id] = $varys;
                }
                if(is_array($app_varys[$app_id])){
                    ksort($app_varys[$app_id]);
                }
            }
        }
        ksort($app_varys);
        return $app_varys;
    }//End Function

    /*
     * 询问缓存版本号
     * @var boolean $force
     * @access static public
     * @return string
     */
    static public function ask_cache_check_version($force=false) 
    {
        $kvprefix = (defined('KV_PREFIX')) ? KV_PREFIX : '';
        $key = md5($kvprefix . self::$_cache_check_version_key);
        if($force || self::instance()->fetch($key, $val) === false){
            $val = md5($kvprefix . time());
            self::instance()->store($key, $val);
            self::$_cache_check_version = $val; //todo：强制更新
        }
        return $val;
    }//End Function

    /*
     * 缓存检查开始
     * @access static public
     * @return void
     */
    static public function co_start() 
    {
        unset(self::$_cache_objects[++self::$_co_depth]);
        unset(self::$_cache_objects_exists[self::$_co_depth]);
    }//End Function

    /*
     * 缓存检查结果
     * @access static public
     * @return array
     */
    static public function co_end() 
    {
        return self::$_cache_objects[self::$_co_depth--];
    }//End Function

    /*
     * 检查过期
     * @var string $type
     * @var mixed $cache_name
     * @access static public
     * @return void
     */
    static public function check_expries($type, $cache_name) 
    {
        $upper_type = strtoupper($type);
        for($i=self::$_co_depth; $i>0; $i--){
            if(is_array($cache_name)){
                foreach($cache_name AS $name){
                    $upper_cache_name = strtoupper($name);
                    if($upper_type!='DB' || self::get_modified($type, $name)>0){
                        self::$_cache_objects[$i][$upper_type][$upper_cache_name] = 1;
                    }
                }
            }else{
                $upper_cache_name = strtoupper($cache_name);
                if($upper_type!='DB' || self::get_modified($type, $cache_name)>0){
                    self::$_cache_objects[$i][$upper_type][$upper_cache_name] = 1;
                }
            }
            self::$_cache_objects_exists[$i][$upper_type][strtoupper(md5(serialize($cache_name)))] = 1;
        }
    }//End Function

    /*
     * 检查当前缓存深度
     * @access static public
     * @return int
     */
    static public function check_current_co_depth() 
    {
        return self::$_co_depth;
    }//End Function

    /*
     * 检查当前缓存层中是否已经check_expries todo：优化缓存性能
     * @var string $type
     * @var mixed $cache_name
     * @access static public
     * @return boolean
     */
    static public function check_current_co_objects_exists($type, $cache_name) 
    {
        return isset(self::$_cache_objects_exists[self::$_co_depth][strtoupper($type)][strtoupper(md5(serialize($cache_name)))]);
    }//End Function

    /*
     * 保存vary_list
     * @access static public
     * @return void
     */
    static public function store_vary_list() 
    {
        $vary_list = self::fetch_vary_list(true);
        base_kvstore::instance('cache/expires')->store('vary_list', $vary_list);
        unset($vary_list);
    }//End Function

    /*
     * 读取vary_list
     * @access static public
     * @return mixed
     */
    static public function fetch_vary_list($force=false) 
    {
        if(self::$_vary_list_froce_mysql===true || $force===true){
            $rs = kernel::database()->exec('SELECT UPPER(`type`) AS `type`, UPPER(`name`) AS `name`, `expire` FROM sdb_base_cache_expires', true);
            while($row = mysql_fetch_assoc($rs['rs'])){
                $vary_list[$row['type']][$row['name']] = $row['expire'];
            }
            mysql_free_result($rs['rs']);
            unset($rs);
        }else{
            base_kvstore::instance('cache/expires')->fetch('vary_list', $vary_list);
        }
        return $vary_list;
    }//End Function

    /*
     * 查看缓存状态
     * @var array &$msg
     * @access static public
     * @return boolean
     */
    static public function status(&$msg) 
    {
        if(method_exists(self::instance(), "status")){
            $msg = self::instance()->status();
            return true;
        }else{
            $msg = app::get('base')->_('当前缓存控制器无法显示状态');
            return false;
        }
    }//End Function

    /*
     * 优化缓存
     * @var array &$msg
     * @access static public
     * @return boolean
     */
    static public function optimize(&$msg) 
    {
        if(method_exists(self::instance(), "optimize")){
            return self::instance()->optimize();
        }else{
            $msg = app::get('base')->_('当前缓存控制器无法优化');
            return false;
        }
    }//End Function

    /*
     * 清空缓存 
     * todo：不是真正删除
     * 只是迭代新的缓存版本号
     * 如果使用的cache_storage不会自动释放空间，则需要人工干预
     * 也可以重截cache_storage的clean方法，实现物理删除
     * @var array &$msg
     * @access static public
     * @return boolean
     */
    static public function clean(&$msg) 
    {
        if(method_exists(self::instance(), "clean")){
            return self::instance()->clean();
        }else{
            if(self::ask_cache_check_version(true)){
                return true;
            }else{
                return false;
            }
        }
    }//End Function

}//End Class
