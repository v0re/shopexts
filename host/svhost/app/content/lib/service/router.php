<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class content_service_router
{
    private $_enable = false;

    function __construct() 
    {
        $this->_enable = (app::get('content')->getConf('base.use_node_path_url') == 'true') ? true : false;
    }//End Function

    function enable() 
    {
        return $this->_enable;
    }//End Function

    public function gen_url($params = array()) 
    {
        $full = ($params['full']) ? 'true' : 'false';
        $real = ($params['real']) ? 'true' : 'false';
        $act = ($params['act']) ? $params['act'] : 'index';
        $args_keys = array_keys($params['args']);
        $first_arg = $params['args'][$args_keys[0]];
        if($params['ctl'] == 'site_article' && $first_arg > 0){
            switch($act){
                case 'index':
                    $article_indexs = kernel::single('content_article_detail')->get_index($first_arg);
                    $node_id = $article_indexs['node_id'];
                    break;
                case 'lists':
                case 'nodeindex':
                    $node_id = $first_arg;
                    break;
                default:
                    $node_id = 0;
            }
            if($node_id > 0){
                $prefix = kernel::single('content_article_node')->get_node_path_url($node_id);
            }
        }
        if($params['act']=='index' && (count($params['args'])==0 || is_numeric($first_arg))){
            //此情况可省略act 这个太恶心了 EDwin
        }else{
            array_unshift($params['args'], $params['act']);
        }
        if(isset($prefix)) array_unshift($params['args'], $prefix);
        $urlmap = kernel::single('site_router')->get_urlmap();
        array_unshift($params['args'], $urlmap[$params['app'].':'.$params['ctl']]);
        $app_url_map = array_flip(kernel::$url_app_map);
        $base_url = app::get('site')->base_url($full);
        $url =  implode(kernel::single('site_router')->get_separator(), $params['args']) . kernel::single('site_router')->get_uri_expended_name();
        return $base_url . (($params['real']=='true') ? $url : kernel::single('site_router')->parse_route_static_genurl($url));
    }//End Function

    public function dispatch($query, $allow_name, $separator) 
    {
        $args = explode($separator, $query);
        if(isset($args)){
            $ctl_flag = array_shift($args);
            $urlmap = kernel::single('site_router')->get_urlmap();
            $urlmap = array_flip($urlmap);
            if($urlmap[$ctl_flag] == 'content:site_article'){
                array_shift($args);
            }
            array_unshift($args, $ctl_flag);
            $query = @join(kernel::single('site_router')->get_separator(), $args);
        }
        kernel::single('site_router')->default_dispatch($query, $allow_name, $separator);
    }//End Function

}//End Class
