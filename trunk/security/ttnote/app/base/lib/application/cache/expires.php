<?php

class base_application_cache_expires extends base_application_prototype_filepath  
{
    var $path = 'dbschema';

    public function install() 
    {
        $widgets_name = $this->getPathname();
        if(is_file($widgets_name)){
            require($widgets_name);
            foreach($db AS $key=>$val){
                if($val['ignore_cache'] === true)   break;
                $data['type'] = 'DB';
                $data['name'] = strtoupper(DB_PREFIX . $this->target_app->app_id . "_" . $key);
                $data['expire'] = time();
                kernel::log('Installing Cache_Expires DB:'. $data['name']);
                app::get('base')->model('cache_expires')->replace($data,
                    array('type'=>$data['type'],'name'=>$data['name'])
                    );
                break;
            }
        }
    }//End Function

}//End Class
