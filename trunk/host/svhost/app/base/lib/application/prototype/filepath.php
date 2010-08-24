<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class base_application_prototype_filepath extends base_application_prototype_content{

    var $current;
    var $path;

    function init_iterator(){
        if(is_dir($this->target_app->app_dir.'/'.$this->path)){
            return new DirectoryIterator($this->target_app->app_dir.'/'.$this->path);
        }else{
            return new ArrayIterator(array());
        }
    }

    public function getPathname(){
        return $this->iterator()->getPathname();
    }

    public function current() {
        $this->key = $this->iterator()->getFilename();
        return $this;
    }

    function prototype_filter(){
        $filename = $this->iterator()->getFilename();
        if($filename{0}=='.'){
            return false;
        }else{
            return $this->filter();
        }
    }
    
    function last_modified($app_id){
        $modified = 0;
        foreach($this->detect($app_id) as $item){
            //todo: 如果是文件而不是目录就麻烦了
            $modified = max($modified,filemtime($this->getPathname()));
        }
        return $modified;
    }

}
