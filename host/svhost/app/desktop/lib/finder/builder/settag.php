<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class desktop_finder_builder_settag extends desktop_finder_builder_prototype{
    function main(){
        $tagctl = app::get('desktop')->model('tag');
        $tag_rel = app::get('desktop')->model('tag_rel');
        $tag_name = $_POST['tag']['name'];
        $tag_stat = $_POST['tag']['stat'];
        if($_POST['_PKEY_']=='_ALL_' || $_POST['_PKEY_']==array('_ALL_')){
            $obj = $this->object;
            $schema = $obj->get_schema();
            $idColumn = $schema['idColumn'];
            $rows = $obj->getList($idColumn,null,0,-1);
            foreach($rows as $value){
                $pkey[] = $value[$idColumn];
            }
        }else{
            $pkey = explode('|',$_POST['_PKEY_']);    
        }
        $pkey = (array)$pkey;
        foreach($tag_stat as $key=>$value){
            if($value===2) continue;
            if($value==1){//取消标签
                $tag_item = $tagctl -> getList('tag_id',array('tag_name'=>$tag_name[$key],'tag_type'=>$this->object->table_name()));
                foreach($pkey as $id){
                    $tag_rel->delete(array('tag_id'=>$tag_item[0]['tag_id'],'rel_id'=>$id));
                }
            }else{//设置标签
                $data['tag_type'] = $this->object->table_name();
                $data['tag_name'] = $tag_name[$key];
                $data['app_id'] = $this->app->app_id;
                $tagctl->save($data);
                if($data['tag_id']){
                    $data2['tag']['tag_id'] = $data['tag_id'];
                    unset($data['tag_id']);
                    foreach($pkey as $id){
                        $data2['tag_type'] = $this->object->table_name();
                        $data2['app_id'] = $this->app->app_id;
                        $data2['rel_id'] = $id;
                        $tag_rel->save($data2);
                    }
                }
            }
        }

                header('Content-Type:text/jcmd; charset=utf-8');
                echo '{success:"标签设置成功."}';    
    }
}
