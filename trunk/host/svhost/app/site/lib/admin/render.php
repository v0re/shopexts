<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class site_admin_render extends base_render 
{
    private $__theme = null;
    private $__tmpl = null;
    
    function __construct(&$app) 
    {
        parent::__construct($app);
        if(@constant('WITHOUT_STRIP_HTML')){
            $this->enable_strip_whitespace = false;
        }
    }//End Function

    public function display_admin_widget($tpl, $fetch=false) 
    {
        $this->_vars = $this->pagedata;
        $tmpl_file = realpath($tpl);
        $compile_id = $this->compile_id($tmpl_file);
        if($this->force_compile || base_kvstore::instance('cache/theme_admin_widget')->fetch($compile_id, $compile_code, filemtime($tmpl_file)) === false){
            $compile_code = $this->_compiler()->compile_file($tmpl_file);
            if($compile_code!==false){
                base_kvstore::instance('cache/theme_admin_widget')->store($compile_id, $compile_code);
            }
        }

        ob_start();
        eval('?>'.$compile_code);
        $content = ob_get_contents();
        ob_end_clean();
        
        $this->pre_display($content);

        if($fetch === true){
            return $content;
        }else{
            echo $content;
        }
    }//End Function

    public function fetch_admin_widget($tpl) 
    {
        return $this->display_admin_widget($tpl, true);
    }//End Function

}//End Class
