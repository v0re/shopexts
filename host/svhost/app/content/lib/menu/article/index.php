<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class content_menu_article_index implements site_interface_menu 
{
    public function inputs($config=array()){
        
        $inputs = array(
            "文章ID" => array('type'=>'input', 'size'=>5, 'title'=>'article_id', 'name'=>'article_id', 'value'=>$config['article_id']),
        );
        return $inputs;
    }

    public function handle($post){
        
        $this->params['article_id'] = $post['article_id'];

        $this->config = $this->params;
    }

    public function get_params(){
        return $this->params;
    }

    public function get_config(){
        return $this->config;
    }
}//End Class
