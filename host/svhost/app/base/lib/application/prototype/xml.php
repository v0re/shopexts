<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class base_application_prototype_xml extends base_application_prototype_content{

    var $current;
    var $xml;
    var $xsd;
    var $path;
    static $__appinfo;

    public function init_iterator() {
        if(file_exists($this->target_app->app_dir.'/'.$this->xml)){
            $ident = $this->target_app->app_id.'-'.$this->xml;
            if(!isset(self::$__appinfo[$ident])){
                self::$__appinfo[$ident] = kernel::single('base_xml')->xml2array(
                    file_get_contents($this->target_app->app_dir.'/'.$this->xml),$this->xsd);
            }
            
            eval('$array = &self::$__appinfo[$ident]['.str_replace('/','][',$this->path).'];');
        }else{
            $array = array();
        }
        return new ArrayIterator((array)$array);
    }
    
    function last_modified($app_id){
        $file = app::get($app_id)->app_dir.'/'.$this->xml;
        if(file_exists($file)){
            return filemtime($file);
        }else{
            return false;
        }
    }

}
