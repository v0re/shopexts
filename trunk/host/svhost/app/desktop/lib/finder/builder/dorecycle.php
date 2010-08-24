<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class desktop_finder_builder_dorecycle extends desktop_finder_builder_prototype{

    function main(){
        $this->controller->begin();
        $oRecycle = app::get('desktop')->model('recycle');
        $recycle_item = array();
        $recycle_item['drop_time'] = time();
        $recycle_item['item_type'] = $this->object->table_name();
        $o = $this->app->model($this->object->table_name());
        $this->dbschema = $this->object->get_schema();
        $textColumn = $this->dbschema['textColumn'];
        foreach($this->dbschema['columns'] as $k=>$col){
            if($col['is_title']&&$col['sdfpath']){
                $textColumn = $col['sdfpath'];
                break;
            }
        }
        $pkey = $this->dbschema['idColumn'];
        
        $pkey_value = $_POST[$pkey];//explode('|',$_POST['_PKEY_']);

        $rows = $o->getList('*',array($pkey=>$pkey_value),0,-1);


        if(method_exists($o, 'pre_recycle')){
            if(!$o->pre_recycle($rows)){
               $this->controller->end(false,$o->recycle_msg?$o->recycle_msg:'删除失败');
             return false;
            }
        }
        foreach($rows as $k=>$v){
            $pkey_value = $v[$pkey];
            $v = $o->dump($v[$pkey],'*','delete');
            $recycle_item['item_sdf'] = $v;
            $recycle_item['app_key'] = $this->app->app_id;
            $recycle_item['item_title'] = $v[$textColumn];
            $tmp = $recycle_item;
            $return = $oRecycle->save($tmp);
            unset($tmp[$pkey]);
            $o->delete(array($pkey=>$pkey_value));
        }
        
        if(method_exists($o, 'suf_recycle')){
            if(!$o->suf_recycle($_POST)){
                $this->controller->end(false,$o->recycle_msg?$o->recycle_msg:'删除失败');
              return false;
            }
        }

        $services = kernel::serviceList('desktop_finder_callback.' . get_class($o));
        foreach($services AS $service){
            if(method_exists($service, 'recycle')){
                $service->recycle($_POST);
            }
        }
        $this->controller->end(true,'删除成功','javascript:finderGroup["'.$_GET['finder_id'].'"].unselectAll();finderGroup["'.$_GET['finder_id'].'"].refresh();');
    }

}
