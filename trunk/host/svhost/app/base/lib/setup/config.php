<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class base_setup_config{

    function __construct(){
        if(file_exists(ROOT_DIR.'/config/config.php')){
            $this->set_sample_file(ROOT_DIR.'/config/config.php');
        }else{
            $this->set_sample_file(ROOT_DIR.'/app/base/examples/config.php');
        }
    }

    function set_sample_file($file){
        $this->sample_file = $file;
    }

    function write($config){

        $this->sample_file = realpath($this->sample_file);

        $sample = file_get_contents($this->sample_file);

        foreach($config as $k=>$v){
            $arr['#(define\\s*\\(\\s*[\'"]'.strtoupper($k).'[\'"]\\s*,\\s*)[^;]+;#i'] = '\\1\''.str_replace('\'','\\\'',$v).'\');';
        }

        kernel::log('Using sample :'.$this->sample_file);
        kernel::log('Writing config file... ok.');
        return file_put_contents(ROOT_DIR.'/config/config.php',preg_replace(array_keys($arr),array_values($arr),$sample));
    }
    
    static function deploy_info(){
        return kernel::single('base_xml')->xml2array(
            file_get_contents(ROOT_DIR.'/config/deploy.xml'),'base_deploy');
    }
}
