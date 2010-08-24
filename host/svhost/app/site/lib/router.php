<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

/*
 * @package site
 * @copyright Copyright (c) 2010, shopex. inc
 * @author edwin.lzh@gmail.com
 * @license 
 */
class site_router implements base_interface_router
{
    
    /*
     * 构造
     * @var object $app
     * @access public
     * @return void
     */
    function __construct($app){
        $this->app = $app;
        $this->sitemap = app::get('site')->getConf('sitemaps');
        if(!is_array($this->sitemap)){
            $sitemap_config = kernel::single('site_module_base')->assemble_config();
            if(is_array($sitemap_config)){
                $this->sitemap = $sitemap_config;       //todo：兼容kvstroe出错的情况下
                if(!kernel::single('site_module_base')->write_config($sitemap_config)){
                    kernel::log('Error: sitemap can\'t save to kvstore');      //todo：如果写入失败，记录于系统日志中，前台不报错，保证网站运行正常
                }
            }else{
                //if false
                trigger_error('sitemap is lost!', E_USER_ERROR);       //todo：无sitemap时报错
            }
        }
        foreach($this->sitemap as $part=>$controller){
            $urlmap[$controller[0].':'.$controller[1]] = $part;
        }
        $this->urlmap = $urlmap;
        $this->_request = kernel::single('base_component_request');
        $this->_response = kernel::single('base_component_response');
    }//End Function

    /*
     * 取得sitemap
     * @access public
     * @return array
     */
    public function get_sitemap() 
    {
        return $this->sitemap;
    }//End Function

    /*
     * 取得urlmap
     * @access public
     * @return array
     */
    public function get_urlmap() 
    {
        return $this->urlmap;
    }//End Function

    /*
     * 返回分隔符
     * @access public
     * @return string
     */
    public function get_separator() 
    {
        if(!isset($this->__separator)){
            $this->__separator = trim(app::get('site')->getConf('base.site_params_separator'));
        }
        return $this->__separator;
    }//End Function

    /*
     * 参数特殊编码
     * @var array $args
     * @access public
     * @return void
     */
    public function encode_args($args) 
    {
        if(is_array($args)){
            foreach($args AS $key=>$val){
                $args[$key] = str_replace(array('-', '.', '/', '%2F'), array(';jh;', ';dian;', ';xie;', ';xie;'), $val);
            }
        }else{
            $args = str_replace(array('-', '.', '/', '%2F'), array(';jh;', ';dian;', ';xie;', ';xie;'), $args);
        }
        return $args;
    }//End Function

    /*
     * 参数特殊解码
     * @var array $args
     * @access public
     * @return void
     */
    public function decode_args($args) 
    {
        if(is_array($args)){
            foreach($args AS $key=>$val){
                $args[$key] = str_replace(array(';jh;', ';dian;', ';xie;'), array('-', '.', '/'), $val);
            }
        }else{
            $args = str_replace(array(';jh;', ';dian;', ';xie;'), array('-', '.', '/'), $args);
        }
        return $args;
    }//End Function

    /*
     * 后缀名
     * @var void
     * @access public
     * @return string
     */
    public function get_uri_expended_name() 
    {
        if(!isset($this->__uri_expended_name)){
            if(app::get('site')->getConf('base.enable_site_uri_expanded') == 'true'){
                $this->__uri_expended_name = '.' . app::get('site')->getConf('base.site_uri_expanded_name');
            }else{
                $this->__uri_expended_name = '';
            }
        }
        return $this->__uri_expended_name;
    }//End Function

    /*
     * 产生链接
     * @var array $params
     * @access public
     * @return string
     */
    public function gen_url($params = array())
    {
        $app = $params['app'];
        if(empty($app)) return '/';
        
        if(isset($this->urlmap[$params['app'].':'.$params['ctl']])){
            if(is_array($params['args']))  ksort($params['args']);
            ksort($params);
            $gen_key = md5(serialize($params));     //todo：缓存相同的url
            if(!isset($this->__gen_url_array[$gen_key])){
                foreach($params AS $k=>$v){
                    if($k!='args' && substr($k, 0, 3)=='arg'){
                        if(empty($v)){
                            unset($params['args'][substr($k, 3)]);
                        }else{
                            $params['args'][substr($k, 3)] = $v;
                        }
                    }
                }//fix smarty function
                $params['args'] = (is_array($params['args'])) ? $this->encode_args($params['args']) : array();
                if(!isset($this->__site_router_service[$app])){
                    $app_router_service = kernel::service('site_router.' . $app);
                    if(is_object($app_router_service) && $app_router_service->enable()){
                        $this->__site_router_service[$app] = $app_router_service;
                    }else{
                        $this->__site_router_service[$app] = false;
                    }
                }
                if($this->__site_router_service[$app]){
                    $this->__gen_url_array[$gen_key] = $this->__site_router_service[$app]->gen_url($params);
                }else{
                    $this->__gen_url_array[$gen_key] = $this->default_gen_url($params);
                }
            }
            return $this->__gen_url_array[$gen_key];
        }else{
            return '/';
        }
    }//End Function

    /*
     * 缺省方法
     * @var array $params
     * @access public
     * @return string
     */
    public function default_gen_url($params=array()) 
    {
        $full = ($params['full']) ? 'true' : 'false';
        $real = ($params['real']) ? 'true' : 'false';
        $params['act'] = ($params['act']) ? $params['act'] : 'index';
        $args_keys = array_keys($params['args']);
        $first_arg = $params['args'][$args_keys[0]];
        if($params['act']=='index' && (count($params['args'])==0 || is_numeric($first_arg))){
            //此情况可省略act 这个太恶心了 EDwin
        }else{
            array_unshift($params['args'], $params['act']);
        }
        array_unshift($params['args'], $this->urlmap[$params['app'].':'.$params['ctl']]);
        if(!isset($this->__base_url[$full])){
            $this->__base_url[$full] = app::get('site')->base_url((($full=='true')?true:false));
        }
        $url = implode($this->get_separator(), $params['args']) . $this->get_uri_expended_name();
        return $this->__base_url[$full] . (($params['real']=='true') ? $url : $this->parse_route_static_genurl($url));
    }//End Function

    /*
     * http状态
     * @var string $query
     * @access public
     * @return void
     */
    public function http_status($code) 
    {
        $this->_response->set_http_response_code($code)->send_headers();
        exit;
    }//End Function

    /*
     * @var void
     * @access public
     * @return float
     */
    public function microtime_float() 
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }//End Function

    /*
     * 执行
     * @var string $query
     * @access public
     * @return void
     */
    public function dispatch($query){
        $page_starttime = $this->microtime_float();
        $post = $this->_request->get_post();            //post值
        $get = $this->_request->get_get();              //get值
        $query_info = $this->parse_query($query);       //分析query
        $allow_name = $query_info['allow'];             //许可名
        $separator = ($query_info['separator']) ? $query_info['separator'] : $this->get_separator();    //分隔符
        $realquery = $query_info['query'];                  //真实链接
        $page_key = md5($realquery . serialize(ksort($get)));
        if(!array_key_exists($allow_name, $this->sitemap)){
            $this->http_status(404);   //404页面
        }
        if(count($post)>0 || !cachemgr::get($page_key, $page)){
            cachemgr::co_start();
            $route_value = $this->sitemap[$allow_name];
            $service = kernel::service('site_router.' . $route_value[0]);
            if(is_object($service) && $service->enable()){
                $service->dispatch($realquery, $allow_name, $separator);
            }else{
                $this->default_dispatch($realquery, $allow_name, $separator);
            }
            $page['html'] = join("\n", $this->_response->get_bodys());
            if(count($post)==0 && $this->_response->get_http_response_code()==200 && $this->has_page_cache_control()===true){
                $page_cache = true;
                $page['raw_headers'] = $this->_response->get_raw_headers();
                $page['date'] = date("Y-m-d H:i:s");
                $page['queries'] = base_db_connections::$mysql_query_executions;
                $page['times'] = sprintf('%0.2f', ($this->microtime_float() - $page_starttime));
                $page['etag'] = md5($page['html']);
                cachemgr::set($page_key, $page, cachemgr::co_end());
            }else{
                $page_cache = false;
                $page['date'] = date("Y-m-d H:i:s");
                $page['queries'] = base_db_connections::$mysql_query_executions;
                $page['times'] = sprintf('%0.2f', ($this->microtime_float() - $page_starttime));
            }
        }else{
            $page_cache = true;
            $this->_response->clean_headers();
            if(isset($page['raw_headers'])){
                foreach($page['raw_headers'] AS $raw_header){
                    $this->_response->set_raw_headers($raw_header);
                }
            }
        }
        if($page_cache === true){
            $etag = ($page['etag']) ? $page['etag'] : md5($page['html']);
            $this->_response->set_header('Etag', $etag);
            $matchs = explode(',', $_ENV['HTTP_IF_NONE_MATCH']);
            foreach($matchs AS $match){
                if(trim($match) == $etag){
                    $this->_response->clean_headers();
                    $this->_response->set_header('Content-length', '0');
                    $this->_response->set_http_response_code(304)->send_headers();
                    exit;
                }
            }
        }
        $this->_response->send_headers();
        echo $page['html'];
        //only test
        //echo "\n".'<!-- ' . (($page_cache==true)?'Cached':'No cache') . ' -->';
        //echo "\n".'<!-- This page created by ' . $page['date'] . ' -->';
        //echo "\n".'<!-- Queries:' . $page['queries'] . ' -->';
        //echo "\n".'<!-- Times:' . $page['times'] . 's -->';
    }//End Function

    /*
     * 缺省执行
     * @var string $query
     * @var string $allow_name  //许可名
     * @var string $separator   //分隔符
     * @access public
     * @return void
     */
    public function default_dispatch($query, $allow_name, $separator) 
    {
        $route_value = $this->sitemap[$allow_name];
        $query_args = explode($separator, $query);
        $part = array_shift($query_args);
        if(count($query_args)){
            if(is_numeric($query_args[0])){
                $action = 'index';
            }else{
                $action = array_shift($query_args);
            }
        }else{
            $action = 'index';
        }
        $query_args = $this->decode_args($query_args);
        $this->_request->set_app_name($route_value[0]); //设置app信息
        $this->_request->set_ctl_name($route_value[1]); //设置ctl信息
        $this->_request->set_act_name($action);         //设置act信息
        $this->_request->set_params($query_args);       //设置参数信息
        $controller = app::get($route_value[0])->controller($route_value[1]);
        if(method_exists($controller, $action)){
            try{
                call_user_func_array(array($controller, $action), (array)$query_args);
            }catch(Exception $e){
                $this->http_status(405);   //405页面
            }
        }else{
            $this->http_status(400);   //400页面
        }
    }//End Function

    /*
     * 检查是否存在cache_control的头并判断是否需要页面缓存
     * @access public
     * @return boolean
     */
    public function has_page_cache_control() 
    {
        //response对像
        if($this->_response->get_header('cache-control', $header)){
            $caches = explode(',', $header['value']);
            foreach($caches AS $cache){
                if(in_array(strtolower(trim($cache)), array('no-cache', 'no-store'))){
                    return false;
                }
            }
        }
        //php header
        $code_headers = headers_list();
        foreach($code_headers AS $code_header){
            $tmp_header = explode(':', $code_header);
            if(strtolower(trim($tmp_header[0])) == 'cache-control'){
                $caches = explode(',', $tmp_header[1]);
                foreach($caches AS $cache){
                    if(in_array(strtolower(trim($cache)), array('no-cache', 'no-store'))){
                        return false;
                    }
                }
            }
        }
        return true;
    }//End Function

    /*
     * 得到router所允许的名称
     * @var string $query
     * @access public
     * @return array
     */
    public function parse_query($query) 
    {
        $query = $this->parse_sitemap($query);
        $query = $this->parse_route_static_dispath($query);
        $query = ($query=='index.php') ? '' : $query;
        $query = ($query) ? $query : 'index' . $this->get_uri_expended_name();
        $pos = strrpos($query, '.');
        if($pos > 0){
            $extended_name = substr($query, $pos, strlen($query)-$pos);
            $query = substr($query, 0, $pos);
        }
        if(app::get('site')->getConf('base.check_uri_expanded_name') == 'true' && $this->get_uri_expended_name() != $extended_name){
            $this->http_status(404);   //404页面
        }
        //分融符只支持 '-', '/' 或 xxx.html 或 xxx
        preg_match_all('/^([^\/\-]+)([\/\-]{1}).*$/isU', $query, $matchs);
        if(count($matchs[0]))   return array('query'=>$query, 'allow'=>$matchs[1][0], 'separator'=>$matchs[2][0]);
        preg_match_all('/^([^.]+)$/isU', $query, $matchs);
        if(count($matchs[0]))   return array('query'=>$query, 'allow'=>$matchs[1][0], 'separator'=>false);
        return array('query'=>$query, 'allow'=>$query, 'separator'=>false);
    }//End Function

    /*
     * 处理sitemap规则
     * @var string $query
     * @access public
     * @return string
     */
    public function parse_sitemap($query) 
    {
        if($query === 'sitemaps-catalog.xml'){
            return str_replace(app::get('site')->base_url(), '', $this->gen_url(array('app'=>'site', 'ctl'=>'sitemaps', 'act'=>'catalog', 'real'=>1)));
        }
        if(preg_match_all('/sitemaps-([0-9]+)\.xml/', $query, $matchs)){
            return str_replace(app::get('site')->base_url(), '', $this->gen_url(array('app'=>'site', 'ctl'=>'sitemaps', 'act'=>'index', 'arg0'=>$matchs[1][0], 'real'=>1)));
        }
        return $query;
    }//End Function

    /*
     * 处理静态路由
     * @var string $query
     * @access public
     * @return string
     */
    public function parse_route_static_dispath($query) 
    {
        if($val = kernel::single('site_route_static')->get_dispath($query)){
            if($val['enable'] == 'true'){
                return $val['url'];
            }
        }
        return $query;
    }//End Function

    /*
     * 处理静态链接
     * @var string $url
     * @access public
     * @return string
     */
    public function parse_route_static_genurl($url) 
    {
        if($val = kernel::single('site_route_static')->get_genurl($url)){
            if($val['enable'] == 'true'){
                return $val['static'];
            }
        }
        return $url;
    }//End Function

}//End Class
