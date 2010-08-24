<?php
class base_render{

    var $pagedata = array();
    var $force_compile = 0;
    var $_tag_stack = array();
    private $_compiler;
    static $_vars = array();
    var $_files = array();
    var $_tpl_key_prefix = array();

    function __construct(&$app){
        $this->app = $app;
        $this->pagedata = &base_render::$_vars;
        $this->tmpl_cachekey('lang', app::get_lang());  //设置模版所属语言包
    }

    function display($tmpl_file,$app_id=null){
        array_unshift($this->_files,$tmpl_file);
        $this->_vars = $this->pagedata;

        if($p = strpos($tmpl_file,':')){
            $object = kernel::service('tpl_source.'.substr($tmpl_file,0,$p));
            if($object){
                $tmpl_file_path = substr($tmpl_file,$p+1);
                $last_modified = $object->last_modified($tmpl_file_path);
            }
        }else{
            $tmpl_file = realpath(APP_DIR.'/'.($app_id?$app_id:$this->app->app_id).'/view/'.$tmpl_file);
            $last_modified = filemtime($tmpl_file);
        }

        if(!$last_modified){
            //无文件
        }

        $compile_id = $this->compile_id($tmpl_file);

        if($this->force_compile || base_kvstore::instance('cache/template')->fetch($compile_id, $compile_code, $last_modified) === false){
            if($object){
                $compile_code = $this->_compiler()->compile($object->get_file_contents($tmpl_file_path));
            }else{
                $compile_code = $this->_compiler()->compile_file($tmpl_file);
            }
            if($compile_code!==false){
                base_kvstore::instance('cache/template')->store($compile_id,$compile_code);
            }
        }
        eval('?>'.$compile_code);
        array_shift($this->_files);
    }

    public function _compiler(){
        return $this->single('base_component_compiler');
    }
    
    private function single($classname){
        if(!isset($this->_object[$classname])){
            $this->_object[$classname] = new $classname($this);
        }
        return $this->_object[$classname];
    }

    function fetch($tmpl_file,$app_id=null){
        ob_start();
        $this->display($tmpl_file,$app_id);
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    public function tmpl_cachekey($key,$value){
        $this->_tpl_key_prefix[$key] = $value;
    }

    function &ui(){
        return $this->single('base_component_ui');
    }

    function _fetch_compile_include($app_id,$tmpl_file, $vars=null){
        if(count($vars)==0){
             $this->pagedata = $this->_vars;
        }else{              
             $this->pagedata = $vars;
        }
        return $this->fetch($tmpl_file,$app_id);
    }

    function _fetch_compile_include_goods($app_id,$tmpl_file, $vars=null){
        $this->pagedata = $this->_vars;
        return $this->fetch($tmpl_file,$app_id);
    }


    function compile_id($path){
        return md5($path.print_r($this->_tpl_key_prefix,1));
    }

}
