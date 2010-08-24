<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class content_mdl_article_bodys extends dbeav_model
{
    public function format_params($params) 
    {
        if(isset($params['seo_title'])) $params['seo_title'] = htmlspecialchars($params['seo_title'], ENT_QUOTES);
        if(isset($params['seo_keywords'])) $params['seo_keywords'] = htmlspecialchars($params['seo_keywords'], ENT_QUOTES);
        if(isset($params['seo_description'])) $params['seo_description'] = htmlspecialchars($params['seo_description'], ENT_QUOTES);
        if(isset($params['content'])) $params['content'] = $params['content'];
        $params['length'] = strlen($params['content']);
        return $params;
    }//End Function
    
    public function valid_insert($params) 
    {
        if(empty($params['article_id'])){
            trigger_error('文章ID不能为空', E_USER_ERROR);
            return false;
        }
        $params = $this->format_params($params);
        return $params;
    }//End Function

    public function valid_update($params) 
    {
        $params = $this->format_params($params);
        return $params;
    }//End Function
    
    public function insert($params) 
    {
        $params = $this->valid_insert($params);
        if(!$params)    return false;
        return parent::insert($params);
    }//End Function

    public function update($params, $filter) 
    {
        $params = $this->valid_update($params);
        if(!$params)    return false;
        return parent::update($params, $filter);
    }//End Function

}//End Class
