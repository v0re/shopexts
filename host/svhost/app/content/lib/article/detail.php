<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

/*
 * @package cmsex
 * @subpackage article
 * @copyright Copyright (c) 2010, shopex. inc
 * @author edwin.lzh@gmail.com
 * @license 
 */
class content_article_detail
{

    const VERSION = '0.1';

    function __construct() 
    {
        $this->indexs_model = app::get('content')->model('article_indexs');
        $this->bodys_model = app::get('content')->model('article_bodys');
    }//End Function

    public function get_index($article_id) 
    {
        return $this->indexs_model->dump($article_id, '*');
    }//End Function

    public function get_body($article_id) 
    {
        $data = $this->bodys_model->dump($article_id, '*');
        return $data;
    }//End Function

    /*
     * 取得文件
     * @var int $article_id
     * @access public
     * @return mixed
     */
    public function get_detail($article_id)
    {
        $data['indexs'] = $this->get_index($article_id);
        $data['bodys'] = $this->get_body($article_id);
        return $data;
    }

    public function parse_hot_link($bodys) 
    {
        if(is_array($bodys['hot_link'])){
            foreach($bodys['hot_link']['linkwords'] AS $k=>$v){
                $links[$k] = sprintf('<a href="%s" target="_blank">%s</a>', $bodys['hot_link']['linkurl'][$k], $v);
            }
            return str_replace($bodys['hot_link']['linkwords'], $links, $bodys['content']);
        }else{
            return $bodys['content'];
        }
    }//End Function
    
}//End Class
