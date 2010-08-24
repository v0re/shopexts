<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class content_menu_article_lists implements site_interface_menu 
{
    public function inputs($config=array()){
        
        $selectmaps = kernel::single('content_article_node')->get_selectmaps();
        if(is_array($selectmaps)){
            foreach($selectmaps AS $select){
                $options[$select['node_id']] = str_repeat(' ', $select['step']-1) . $select['node_name'];
            }
        }

        $inputs = array(
            "文章节点" => array('type'=>'select', 'title'=>'node_id', 'required'=>true, 'name'=>'node_id', 'value'=>$config['node_id'], 'options'=>$options),
        );
        return $inputs;
    }

    public function handle($post){
        
        $this->params['node_id'] = $post['node_id'];

        $this->config = $this->params;
    }

    public function get_params(){
        return $this->params;
    }

    public function get_config(){
        return $this->config;
    }
}//End Class
