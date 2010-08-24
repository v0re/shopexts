<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class site_module_base
{      
    final public function create_site_config() 
    {
        $conf = $this->assemble_config();
        return $this->write_config($conf);
    }//End Function

    final public function assemble_config() 
    {
        $rows = app::get('site')->model('modules')->select()->where('enable = ?', 'true')->instance()->fetch_all();
        if(is_array($rows)){
            $conf = array();
            foreach($rows AS $row){
                $conf[$row['path']] = array($row['app'], $row['ctl'], $row['title']);
            }
            return $conf;
        }
        return false;
    }//End Function

    final public function write_config($conf) 
    {
        if(is_array($conf)){
            return app::get('site')->setConf('sitemaps', $conf);
        }else{
            return false;
        }
    }//End Function

}//End Class
