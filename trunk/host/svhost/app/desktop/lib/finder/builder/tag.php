<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class desktop_finder_builder_tag extends desktop_finder_builder_prototype{

    function main(){
        $render = app::get('desktop')->render();
        $tagctl = app::get('desktop')->model('tag');
        $tag_rel = app::get('desktop')->model('tag_rel');
        $tags = $tagctl->getList('tag_id,tag_name,tag_abbr,tag_bgcolor,tag_fgcolor',array('tag_type'=>$this->object->table_name()
//            ,'tag_mode'=>'normal'
        ));
        $rel_tag_list = $tag_rel->get_list_in($_POST[$this->dbschema['idColumn']]);
        if($rel_tag_list){
            foreach($rel_tag_list as $k=>$v){
                $tmp[$v['rel_id']][] = $v['tag_id'];
                $used_tag[$v['tag_id']] = 1;
            }
        }
        $i=0;
        if($tmp){
            foreach($tmp as $rel_id=>$rel_tags){//计算标签的交集
                if($i++==0){
                    $intersect = $rel_tags;
                }else{
                    $intersect = array_intersect($intersect,$rel_tags);
                }
                if(!$intersect) break;
            }
        }
        $filter = $_POST;
        unset($filter['_finder']);
        $count_method = $this->object_method['count'];
        $render->pagedata['count'] = $this->object-> $count_method($filter);
        $render->pagedata['tags'] = (array)$tags;
        $render->pagedata['selected_item'] = implode('|',$_POST[$this->dbschema['idColumn']]);
        $render->pagedata['object_name'] = $this->object_name;
        $render->pagedata['used_tag'] = array_keys((array)$used_tag);
        $render->pagedata['intersect'] = (array)$intersect;
        $render->pagedata['url'] = $this->url;
        echo $render->fetch('common/tagsetter.html');
    }

}
