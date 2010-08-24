<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class base_application_service extends base_application_prototype_xml{

    var $xml='services.xml';
    var $xsd='base_app';
    var $path='service';

    function set_current($current){
        $this->current = $current;
    }

    public function current() {
        $this->current = $this->iterator()->current();
        $this->key = $this->current['id'];
        return $this;
    }

    public function install(){
        kernel::log('Installing '.$this->content_typename().' '.$this->key());
        
        $data = $this->row();
        $data['content_type'] = 'service_category';
        if($this->current['optname']){
            $data['content_title'] = $this->current['optname'];
        }
        if($this->current['opttype']){
            $data['content_path'] = $this->current['opttype'];
        }
        app::get('base')->model('app_content')->insert($data);
        
        base_kvstore::instance('service')->fetch($this->key,$service_define);
        
        foreach((array)$this->current['class'] as $class){
            $row = $this->row();
            $row['content_path'] = $class;
            app::get('base')->model('app_content')->insert($row);
            $service_define['list'][$class] = $class;
            //todo: interface... check
        }
        base_kvstore::instance('service')->store($this->key,$service_define);
    }
    
    function clear_by_app($app_id){
        if(!$app_id){
            return false;
        }
        
        $to_remove = array();
        base_kvstore::instance('service')->fetch($this->key,$service_define);
        $service_list = app::get('base')->model('app_content')->getlist('content_name,content_path,app_id', array('app_id'=>$app_id, 'content_type'=>'service'));
        foreach($service_list as $service){
            $to_remove[$service['content_name']][] = $service['content_path'];
        }
        foreach($to_remove as $service_name=>$rows){
            if(base_kvstore::instance('service')->fetch($service_name,$service_define)){
                foreach($rows as $row){
                    unset($service_define['list'][$row]);
                }
                base_kvstore::instance('service')->store($service_name,$service_define);
            }
        }
        
        app::get('base')->model('app_content')->delete(array(
            'app_id'=>$app_id,'content_type'=>'service'));
            
        app::get('base')->model('app_content')->delete(array(
            'app_id'=>$app_id,'content_type'=>'service_category'));
    }

}
