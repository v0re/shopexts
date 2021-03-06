<?php
require(CORE_DIR.'/lib/smarty/Smarty.class.php');
class mdl_frontend extends smarty{

    function mdl_frontend(){
        parent::smarty();
        $this->system = &$GLOBALS['system'];

        $this->files = array();
        $this->lang = &$this->system->lang;
        $this->left_delimiter='<{';
        $this->right_delimiter='}>';
        $this->plugins_dir[] = CORE_DIR.'/shop/smartyplugin';
        $this->compile_dir = HOME_DIR.'/cache/front_tmpl';
        $this->memcache=&$this->system->memcache;
        $db = &$this->system->database();
        $version=$this->system->getConf('site.version');

        $this->versionTimeStamp=filemtime(CORE_DIR.'/version.txt');
        $this->langtools = &$this->system->loadModel('utility/language');

        $this->register_compiler_function('t',array(&$this,'_txt_block_begin'));
        $this->register_compiler_function('/t',array(&$this,'_txt_block_end'));

        $this->register_resource("widgets", array(array(&$this,"_get_widgets_template"), 
        array(&$this,"_get_widgets_timestamp"), 
        array(&$this,"_get_secure"), 
        array(&$this,"_get_trusted"))); 

        $this->register_resource('systmpl',array(
            array($this,"_get_systmpl_template"),
            array($this,"_get_systmpl_timestamp"),
            array($this,"_get_secure"),
            array($this,"_get_trusted")));

        $this->register_resource("admin", array(array(&$this,"_get_admin_template"), 
        array(&$this,"_get_admin_timestamp"), 
        array(&$this,"_get_secure"), 
        array(&$this,"_get_trusted"))); 

        $this->register_resource("border", array(array(&$this,"_get_border_template"), 
        array(&$this,"_get_border_timestamp"), 
        array(&$this,"_get_secure"), 
        array(&$this,"_get_trusted"))); 

        $this->register_resource("user", array(array(&$this,"_get_user_template"), 
        array(&$this,"_get_user_timestamp"), 
        array(&$this,"_get_secure"), 
        array(&$this,"_get_trusted")));

        $this->register_resource("shop", array(array(&$this,"_get_shop_template"),
            array(&$this,"_get_shop_timestamp"),
            array(&$this,"_get_secure"),
            array(&$this,"_get_trusted")));

        $this->register_resource("page", array(array(&$this,"_get_page_template"),
            array(&$this,"_get_page_timestamp"),
            array(&$this,"_get_secure"),
            array(&$this,"_get_trusted")));

        $this->register_function('respath',array(&$this,'_respath'));

    }

    function new_dom_id(){
        return 'el_'.(++$this->dom_id);
    }

    function _txt_block_begin($tag_arg, &$smarty){
        $this->_translate_block_id = $tag_arg;
    }

    
    function fetch($resource_name, $cache_id = null, $compile_id = null, $display = false)
    {
       
        static $_cache_info = array();

        $_smarty_old_error_level = $this->debugging ? error_reporting() : error_reporting(isset($this->error_reporting)
            ? $this->error_reporting : error_reporting() & ~E_NOTICE);

        if (!$this->debugging && $this->debugging_ctrl == 'URL') {
            $_query_string = $this->request_use_auto_globals ? $_SERVER['QUERY_STRING'] : $GLOBALS['HTTP_SERVER_VARS']['QUERY_STRING'];
            if (@strstr($_query_string, $this->_smarty_debug_id)) {
                if (@strstr($_query_string, $this->_smarty_debug_id . '=on')) {
                    // enable debugging for this browser session
                    @setcookie('SMARTY_DEBUG', true);
                    $this->debugging = true;
                } elseif (@strstr($_query_string, $this->_smarty_debug_id . '=off')) {
                    // disable debugging for this browser session
                    @setcookie('SMARTY_DEBUG', false);
                    $this->debugging = false;
                } else {
                    // enable debugging for this page
                    $this->debugging = true;
                }
            } else {
                $this->debugging = (bool)($this->request_use_auto_globals ? @$_COOKIE['SMARTY_DEBUG'] : @$GLOBALS['HTTP_COOKIE_VARS']['SMARTY_DEBUG']);
            }
        }

        if ($this->debugging) {
            // capture time for debugging info
            $_params = array();
            require_once(SMARTY_CORE_DIR . 'core.get_microtime.php');
            $_debug_start_time = smarty_core_get_microtime($_params, $this);
            $this->_smarty_debug_info[] = array('type'      => 'template',
                'filename'  => $resource_name,
                'depth'     => 0);
            $_included_tpls_idx = count($this->_smarty_debug_info) - 1;
        }

        if (!isset($compile_id)) {
            $compile_id = $this->compile_id;
        }

        $this->_compile_id = $compile_id;
        $this->_inclusion_depth = 0;

        if ($this->caching) {
            // save old cache_info, initialize cache_info
            array_push($_cache_info, $this->_cache_info);
            $this->_cache_info = array();
            $_params = array(
                'tpl_file' => $resource_name,
                'cache_id' => $cache_id,
                'compile_id' => $compile_id,
                'results' => null
            );
            require_once(SMARTY_CORE_DIR . 'core.read_cache_file.php');
            if (smarty_core_read_cache_file($_params, $this)) {
                $_smarty_results = $_params['results'];
                if (!empty($this->_cache_info['insert_tags'])) {
                    $_params = array('plugins' => $this->_cache_info['insert_tags']);
                    require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
                    smarty_core_load_plugins($_params, $this);
                    $_params = array('results' => $_smarty_results);
                    require_once(SMARTY_CORE_DIR . 'core.process_cached_inserts.php');
                    $_smarty_results = smarty_core_process_cached_inserts($_params, $this);
                }
                if (!empty($this->_cache_info['cache_serials'])) {
                    $_params = array('results' => $_smarty_results);
                    require_once(SMARTY_CORE_DIR . 'core.process_compiled_include.php');
                    $_smarty_results = smarty_core_process_compiled_include($_params, $this);
                }


                if ($display) {
                    if ($this->debugging)
                    {
                        // capture time for debugging info
                        $_params = array();
                        require_once(SMARTY_CORE_DIR . 'core.get_microtime.php');
                        $this->_smarty_debug_info[$_included_tpls_idx]['exec_time'] = smarty_core_get_microtime($_params, $this) - $_debug_start_time;
                        require_once(SMARTY_CORE_DIR . 'core.display_debug_console.php');
                        $_smarty_results .= smarty_core_display_debug_console($_params, $this);
                    }
                    if ($this->cache_modified_check) {
                        $_server_vars = ($this->request_use_auto_globals) ? $_SERVER : $GLOBALS['HTTP_SERVER_VARS'];
                        $_last_modified_date = @substr($_server_vars['HTTP_IF_MODIFIED_SINCE'], 0, strpos($_server_vars['HTTP_IF_MODIFIED_SINCE'], 'GMT') + 3);
                        $_gmt_mtime = gmdate('D, d M Y H:i:s', $this->_cache_info['timestamp']).' GMT';
                        if (@count($this->_cache_info['insert_tags']) == 0
                            && !$this->_cache_serials
                            && $_gmt_mtime == $_last_modified_date) {
                                if (php_sapi_name()=='cgi')
                                    header('Status: 304 Not Modified');
                                else
                                    header('HTTP/1.1 304 Not Modified');

                            } else {
                                header('Last-Modified: '.$_gmt_mtime);
                                echo $_smarty_results;
                            }
                    } else {
                        echo $_smarty_results;
                    }
                    error_reporting($_smarty_old_error_level);
                    // restore initial cache_info
                    $this->_cache_info = array_pop($_cache_info);
                    return true;
                } else {
                    error_reporting($_smarty_old_error_level);
                    // restore initial cache_info
                    $this->_cache_info = array_pop($_cache_info);
                    return $_smarty_results;
                }
            } else {
                $this->_cache_info['template'][$resource_name] = true;
                if ($this->cache_modified_check && $display) {
                    header('Last-Modified: '.gmdate('D, d M Y H:i:s', time()).' GMT');
                }
            }
        }

        // load filters that are marked as autoload
        if (count($this->autoload_filters)) {
            foreach ($this->autoload_filters as $_filter_type => $_filters) {
                foreach ($_filters as $_filter) {
                    $this->load_filter($_filter_type, $_filter);
                }
            }
        }

        $_smarty_compile_path = $this->_get_compile_path($resource_name);

        // if we just need to display the results, don't perform output
        // buffering - for speed
        $_cache_including = $this->_cache_including;
        $this->_cache_including = false;
        if ($display && !$this->caching && count($this->_plugins['outputfilter']) == 0) {
            if ($this->_is_compiled($resource_name, $_smarty_compile_path)
                || $this->_compile_resource($resource_name, $_smarty_compile_path))
            {
                $this->getMemoryContents($_smarty_compile_path);
                //include($_smarty_compile_path);
            }
        } else {
            ob_start();
            if ($this->_is_compiled($resource_name, $_smarty_compile_path)
                || $this->_compile_resource($resource_name, $_smarty_compile_path))
            {
                $this->getMemoryContents($_smarty_compile_path,false);

                //include($_smarty_compile_path);
            }
            $_smarty_results = ob_get_contents();
            ob_end_clean();

            foreach ((array)$this->_plugins['outputfilter'] as $_output_filter) {
                $_smarty_results = call_user_func_array($_output_filter[0], array($_smarty_results, &$this));
            }
        }

        if ($this->caching) {
            $_params = array('tpl_file' => $resource_name,
                'cache_id' => $cache_id,
                'compile_id' => $compile_id,
                'results' => $_smarty_results);
            require_once(SMARTY_CORE_DIR . 'core.write_cache_file.php');
            smarty_core_write_cache_file($_params, $this);

            require_once(SMARTY_CORE_DIR . 'core.process_cached_inserts.php');
            $_smarty_results = smarty_core_process_cached_inserts($_params, $this);

            if ($this->_cache_serials) {
                // strip nocache-tags from output
                $_smarty_results = preg_replace('!(\{/?nocache\:[0-9a-f]{32}#\d+\})!s'
                ,''
                ,$_smarty_results);
            }
            // restore initial cache_info
            $this->_cache_info = array_pop($_cache_info);
        }
        $this->_cache_including = $_cache_including;

        if ($display) {
            if (isset($_smarty_results)) { echo $_smarty_results; }
            if ($this->debugging) {
                // capture time for debugging info
                $_params = array();
                require_once(SMARTY_CORE_DIR . 'core.get_microtime.php');
                $this->_smarty_debug_info[$_included_tpls_idx]['exec_time'] = (smarty_core_get_microtime($_params, $this) - $_debug_start_time);
                require_once(SMARTY_CORE_DIR . 'core.display_debug_console.php');
                echo smarty_core_display_debug_console($_params, $this);
            }
            error_reporting($_smarty_old_error_level);
            return;
        } else {
            error_reporting($_smarty_old_error_level);
            if (isset($_smarty_results)) { return $_smarty_results; }
        }
    }
    function _compile_resource($resource_name, $compile_path)
    {

        $_params = array('resource_name' => $resource_name);
        if (!$this->_fetch_resource_info($_params)) {
            return false;
        }

        $_source_content = $_params['source_content'];
        $_cache_include    = substr($compile_path, 0, -4).'.inc';

        if ($this->_compile_source($resource_name, $_source_content, $_compiled_content, $_cache_include)) {
            // if a _cache_serial was set, we also have to write an include-file:
            if ($this->_cache_include_info) {
                require_once(SMARTY_CORE_DIR . 'core.write_compiled_include.php');
                smarty_core_write_compiled_include(array_merge($this->_cache_include_info, array('compiled_content'=>$_compiled_content, 'resource_name'=>$resource_name)),  $this);
            }

            $_params = array('compile_path'=>$compile_path, 'compiled_content' => $_compiled_content);
            if($this->memcache){
                $this->smarty_core_write_compiled_cache_resource($_params, $this);
            }else{
                require_once(SMARTY_CORE_DIR . 'core.write_compiled_resource.php');
                smarty_core_write_compiled_resource($_params, $this);
            }
            return true;
        } else {
            return false;
        }

    }
    function smarty_core_write_compiled_cache_resource($params, &$smarty)
    {   
        $_params = array('filename' => $params['compile_path'], 'contents' => $params['compiled_content'], 'create_dirs' => true);
        if($this->memcache){
            if($smarty->memcache->set(basename($_params['filename']),$_params['contents'])&& $smarty->memcache->set('modifier_'.basename($_params['filename']),time())){
                return true;  
            }
        }else{
            if(!@is_writable($smarty->compile_dir)) {
                // compile_dir not writable, see if it exists
                if(!@is_dir($smarty->compile_dir)) {
                    $smarty->trigger_error('the $compile_dir \'' . $smarty->compile_dir . '\' does not exist, or is not a directory.', E_USER_ERROR);
                    return false;
                }
                $smarty->trigger_error('unable to write to $compile_dir \'' . realpath($smarty->compile_dir) . '\'. Be sure $compile_dir is writable by the web server user.', E_USER_ERROR);
                return false;
            }
        }
        return true;
    }
    function _smarty_include($params)
    {
        if ($this->debugging) {
            $_params = array();
            require_once(SMARTY_CORE_DIR . 'core.get_microtime.php');
            $debug_start_time = smarty_core_get_microtime($_params, $this);
            $this->_smarty_debug_info[] = array('type'      => 'template',
                'filename'  => $params['smarty_include_tpl_file'],
                'depth'     => ++$this->_inclusion_depth);
            $included_tpls_idx = count($this->_smarty_debug_info) - 1;
        }

        $this->_tpl_vars = array_merge($this->_tpl_vars, $params['smarty_include_vars']);

        // config vars are treated as local, so push a copy of the
        // current ones onto the front of the stack
        array_unshift($this->_config, $this->_config[0]);

        $_smarty_compile_path = $this->_get_compile_path($params['smarty_include_tpl_file']);


        if ($this->_is_compiled($params['smarty_include_tpl_file'], $_smarty_compile_path)
            || $this->_compile_resource($params['smarty_include_tpl_file'], $_smarty_compile_path))
        {
            $this->getMemoryContents($_smarty_compile_path);
            //include($_smarty_compile_path);
        }

        // pop the local vars off the front of the stack
        array_shift($this->_config);

        $this->_inclusion_depth--;

        if ($this->debugging) {
            // capture time for debugging info
            $_params = array();
            require_once(SMARTY_CORE_DIR . 'core.get_microtime.php');
            $this->_smarty_debug_info[$included_tpls_idx]['exec_time'] = smarty_core_get_microtime($_params, $this) - $debug_start_time;
        }

        if ($this->caching) {
            $this->_cache_info['template'][$params['smarty_include_tpl_file']] = true;
        }
    }

    function getMemoryContents($_smarty_compile_path,$debug=false){
        if($this->memcache){
            $key = basename($_smarty_compile_path);
            if($content=$this->memcache->get($key)){
                eval('?>'.$content);
                unset($content);
            }
        }else{
            require($_smarty_compile_path);
        }
    }

    function _is_compiled($resource_name, $compile_path)
    {

        if (!$this->force_compile && $this->checkFile($compile_path)) {

            if (!$this->compile_check) {
                // no need to check compiled file
                return true;
            } else {
                // get file source and timestamp
                $_params = array('resource_name' => $resource_name, 'get_source'=>false);

                if (!$this->_fetch_resource_info($_params)) {

                    return false;
                }

                if ($_params['resource_timestamp'] <= $this->getCachFileTime($compile_path)) {
                    // template not expired, no recompile
                    return true;
                } else {
                    // compile template
                    return false;
                }
            }
        } else {
            // compiled template does not exist, or forced compile
            return false;
        }
    }

    function checkFile($file){
        if($this->memcache){
            if($this->memcache->get(basename($file))){
                return true;
            }else{
                return false;
            }
        }else{
            return file_exists($file);
        }
    }

    function getCachFileTime($file){
        if($this->memcache && $modifyTime=$this->memcache->get('modifier_'.basename($file))){
            return $modifyTime;
        }else{
            return filemtime($file);
        }
    }

    function _txt_block_end($tag_arg,&$smarty){
        $smarty->_current_block = $this->langtools->translate($this->_translate_block_id?$this->_translate_block_id:$smarty->_current_block);
        unset($this->_translate_block_id);
    }

    function _respath($params){
        if($params['type']=='user'){
            if(function_exists('template_files')){
                return template_files($this,$this->system).'/'.$params['name'].'/';
            }else{
                return $this->system->base_url().'themes/'.$params['name'].'/';
            }
        }elseif($params['type']=='widgets'){
            return $this->system->base_url().'plugins/widgets/'.$params['name'].'/';
        }
    }

    function _get_shop_template($tpl_name, &$tpl_source, &$smarty) {

        if (defined('CUSTOM_CORE_DIR')){
            if (file_exists(CUSTOM_CORE_DIR.'/shop/view/'.$tpl_name))
                $tpl_source = file_get_contents(CUSTOM_CORE_DIR.'/shop/view/'.$tpl_name);
            else
                $tpl_source = file_get_contents(CORE_DIR.'/shop/view/'.$tpl_name);
        }
        else
            $tpl_source = file_get_contents(CORE_DIR.'/shop/view/'.$tpl_name);
        if (!is_bool($tpl_source)) { 
            $this->_fix_tpl($tpl_source,'shop',$tpl_name);
            return true; 
        } else { 
            return false; 
        } 
    } 

    function _get_shop_timestamp($tpl_name, &$tpl_timestamp, &$smarty) { 
        if (defined('CUSTOM_CORE_DIR')){
            if (file_exists(CUSTOM_CORE_DIR.'/shop/view/'.$tpl_name))
                $tpl_timestamp = filemtime(CUSTOM_CORE_DIR.'/shop/view/'.$tpl_name);
            else
                $tpl_timestamp = filemtime(CORE_DIR.'/shop/view/'.$tpl_name);
        }
        else
            $tpl_timestamp = filemtime(CORE_DIR.'/shop/view/'.$tpl_name);
        if (!is_bool($tpl_timestamp)) { 

            $tpl_timestamp = max($tpl_timestamp,$this->versionTimeStamp);
            return true; 
        } else { 
            return false; 
        } 
    }

    function _get_admin_template($tpl_name, &$tpl_source, &$smarty) {
        if (defined('CUSTOM_CORE_DIR')){
            if (file_exists(CUSTOM_CORE_DIR.'/admin/view/'.$tpl_name))
                $tpl_source = file_get_contents(CUSTOM_CORE_DIR.'/admin/view/'.$tpl_name);
            else
                $tpl_source = file_get_contents(CORE_DIR.'/admin/view/'.$tpl_name);
        }
        else
            $tpl_source = file_get_contents(CORE_DIR.'/admin/view/'.$tpl_name);
        if (!is_bool($tpl_source)) { 
            $this->_fix_tpl($tpl_source,'admin',$tpl_name);
            return true; 
        } else { 
            return false; 
        } 
    } 

    function _get_admin_timestamp($tpl_name, &$tpl_timestamp, &$smarty) { 
        if (defined('CUSTOM_CORE_DIR')){
            if (file_exists(CUSTOM_CORE_DIR.'/admin/view/'.$tpl_name))
                $tpl_timestamp = filemtime(CUSTOM_CORE_DIR.'/admin/view/'.$tpl_name);
            else
                $tpl_timestamp = filemtime(CORE_DIR.'/admin/view/'.$tpl_name);
        }
        else
            $tpl_timestamp = filemtime(CORE_DIR.'/admin/view/'.$tpl_name);//.substr($tpl_name,0,strpos($tpl_name,':')));

        if (!is_bool($tpl_timestamp)) { 
            $tpl_timestamp = max($tpl_timestamp,$this->versionTimeStamp);
            return true; 
        } else { 
            return false; 
        }
    }


    function _get_user_template($tpl_name, &$tpl_source, &$smarty) {
        $tpl_source = file_get_contents(THEME_DIR.'/'.$tpl_name);
        if (!is_bool($tpl_source)) { 
            $this->_fix_tpl($tpl_source,'user',$tpl_name);
            return true; 
        } else { 
            return false; 
        } 
    } 

    function _get_user_timestamp($tpl_name, &$tpl_timestamp, &$smarty) {
        $tpl_timestamp = is_file(THEME_DIR.'/'.$tpl_name)?filemtime(THEME_DIR.'/'.$tpl_name):false;//.substr($tpl_name,0,strpos($tpl_name,':')));
        if (!is_bool($tpl_timestamp)) { 
            $tpl_timestamp = max($tpl_timestamp,$this->versionTimeStamp);
            return true; 
        } else { 
            return false; 
        } 
    }

    function _get_page_template ($tpl_name, &$tpl_source, &$smarty_obj) {

        $db = &$this->system->database();
        $row = $db->selectrow('select page_content from sdb_pages where page_name="'.$tpl_name.'" or page_name="' . urlencode($tpl_name) . '" ');
        if ($row) { 
            $tpl_source = $row['page_content']; 
            $this->_fix_tpl($tpl_source,'page',$tpl_name);
            return true; 
        } else { 
            $file = CORE_DIR.'/html/pages/'.$tpl_name.'.html';
            if(file_exists($file)){
                $tpl_source = file_get_contents($file);
                return true;
            }else{
                return false; 
            }
        }
    } 

    function _get_page_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj) 
    {
        $db = &$this->system->database();
        $row = $db->selectrow('select page_time,page_title from sdb_pages where page_name="'.$tpl_name.'" or page_name="' . urlencode($tpl_name) . '"');
        if ($row) { 
            $tpl_timestamp = $row['page_time']; 
            $tpl_timestamp = max($tpl_timestamp,$this->versionTimeStamp);
            return true; 
        } else { 
            $file = CORE_DIR.'/html/pages/'.$tpl_name.'.html';
            if(file_exists($file)){
                $tpl_timestamp = filemtime($file);
                return true;
            }else{
                return false; 
            }
        } 
    } 



    function _fix_tpl(&$tpl,$type,$name){
        $tpl = '<{php}>array_unshift($this->files,\''.$type.':'.$name.'\')<{/php}>'.$tpl.'<{php}>array_shift($this->files);<{/php}>';

        if($type!='admin'){

            switch($type){
            case 'block':
            case 'user':
                $pos = strpos($name,'/');
                $name = substr($name,0,$pos);
                $type = 'user';
                break;

            case 'shop':
                break;
            case 'widgets':
                $pos = strpos($name,'/');
                $name = substr($name,0,$pos);

                $tpl = preg_replace('/(["|\'])(images\/.*?["|\'])/','\1<{respath type="widgets" name="'.$name.'" }>\2',$tpl);
                break;
            }

            $from = array(
                '/((?:background|src|href)\s*=\s*["|\'])(?:\.\/|\.\.\/)?(images\/.*?["|\'])/is',
                '/((?:background|background-image):\s*?url\()(?:\.\/|\.\.\/)?(images\/)/is',
                '/<!--[^<|>|{|\n]*?-->/'
            );
            //    $tpl_res = $this->system->base_url().'themes/'.TPL_ID.'/';
            $to = array(
                '\1<{respath type="'.$type.'" name="'.$name.'" }>\2',
                '\1<{respath type="'.$type.'" name="'.$name.'" }>\2',
                //      '\1'.$tpl_res.'\2',
                ''
            );

            $tpl = preg_replace($from,$to,$tpl);

            if(substr($tpl,0,3)=="\xEF\xBB\xBF") $tpl = substr($tpl,3);

            if(!defined('WITHOUT_STRIP_HTML') || !WITHOUT_STRIP_HTML){
                $tpl = '<{strip}>'.$tpl.'<{/strip}>';
            }
        }else{
            if(substr($tpl,0,3)=="\xEF\xBB\xBF") $tpl = substr($tpl,3);
        }
        return $tpl;
    }

    function _get_widgets_template($tpl_name, &$tpl_source, &$smarty) { 

        $this->_in_widgets = dirname($tpl_name);
        if($p = strpos($tpl_name,':')) $tpl_name = substr($tpl_name,0,$p);
        $tpl_source = file_get_contents(PLUGIN_DIR.'/widgets/'.$tpl_name);

        if (!is_bool($tpl_source)) { 
            $this->_fix_tpl($tpl_source,'widgets',$tpl_name);
            return true; 
        } else { 
            return false; 
        } 
    } 

    function _get_widgets_timestamp($tpl_name, &$tpl_timestamp, &$smarty) { 
        if($p = strpos($tpl_name,':')) $tpl_name = substr($tpl_name,0,$p);
        $tpl_timestamp = filemtime(PLUGIN_DIR.'/widgets/'.$tpl_name);
        if (!is_bool($tpl_timestamp)) {

            $tpl_timestamp = max($tpl_timestamp,$this->versionTimeStamp);
            return true; 
        } else { 
            return false; 
        } 
    } 

    function _get_border_template($tpl_name, &$tpl_source, &$smarty) {
        $tplname=explode("#",$tpl_name);
        $tpl_source=file_get_contents(THEME_DIR.'/'.$smarty->theme.'/'.$tplname[0]);
        //$tpl_source = file_get_contents(PLUGIN_DIR.'/borders/'.$tpl_name);

        if (!is_bool($tpl_source)) { 
            $this->_fix_tpl($tpl_source,'border',$tplname[0]);
            return true; 
        } else { 
            return false; 
        } 
    } 

    function _get_border_timestamp($tpl_name, &$tpl_timestamp, &$smarty) { 
        $tplname=explode("#",$tpl_name);
        $tpl_timestamp = filemtime(THEME_DIR.'/'.$smarty->theme.'/'.$tplname[0]);
        //$tpl_timestamp = filemtime(PLUGIN_DIR.'/borders/'.$tpl_name);
        if (!is_bool($tpl_timestamp)) { 
            $tpl_timestamp = max($tpl_timestamp,$this->versionTimeStamp);
            return true; 
        } else { 
            return false; 
        } 
    }

    function _widgets_bar($params, &$smarty){
        if(!$this->widgets_mdl){
            $this->widgets_mdl = &$this->system->loadModel('content/widgets');
        }
        return $this->widgets_mdl->load($smarty->_current_file,intval($smarty->_wgbar[$smarty->_current_file]++),$params['id']);
    }


    function _get_systmpl_template ($tpl_name, &$tpl_source, &$smarty_obj){
        $systmpl = $this->system->loadModel('content/systmpl');
        $tpl_source = $systmpl->get($tpl_name);
        return $tpl_source!==false;
    }

    function _get_systmpl_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj){
        $db = &$this->system->database();
        if ($aRet = $db->selectrow("SELECT edittime FROM sdb_systmpl WHERE tmpl_name = '$tpl_name'")) {
            $tpl_timestamp = $aRet['edittime'];
            $tpl_timestamp = max($tpl_timestamp,$this->versionTimeStamp);
            return true;
        } else {
            $systmpl = $this->system->loadModel('content/systmpl');
            $tpl_timestamp = filemtime($systmpl->_file($tpl_name));
            if(!is_bool($tpl_timestamp)){
                $tpl_timestamp = max($tpl_timestamp,$this->versionTimeStamp);
                return true;
            }else{
                return false;
            }
        }
    }

    function _get_auto_filename($auto_base, $auto_source = null, $auto_id = null) {
        $fname = parent::_get_auto_filename($auto_base, $auto_source , $auto_id);
        return $this->lang?$fname.'-'.$this->lang:$fname;
    }
}
