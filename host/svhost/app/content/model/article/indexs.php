<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class content_mdl_article_indexs extends dbeav_model
{
    var $has_tag = true;

    var $has_many = array(
        //'tag'=>'tag_rel@desktop:replace:article_id^rel_id',
    );

    public function searchOptions() 
    {
        $arr = parent::searchOptions();
        return array_merge($arr, array(
                'title' => __('文章标题'),
            ));
    }//End Function

    public function count($filter=null) 
    {
        if($filter['node_id'] > 0){
            $filter['node_id'] = app::get('content')->model('article_nodes')->get_childrens_id($filter['node_id']);
        }
        return parent::count($filter);
    }//End Function

    public function getList($cols='*', $filter=array(), $offset=0, $limit=-1, $orderType=null) 
    {
        if( $filter['node_id'] > 0 ){
            $filter['node_id'] = app::get('content')->model('article_nodes')->get_childrens_id($filter['node_id']);
        }
        return parent::getList($cols, $filter, $offset, $limit, $orderType);
    }//End Function
    
    
    public function getList_1($cols='*', $filter=array(), $offset=0, $limit=-1, $orderType=null) 
    {
        return parent::getList($cols, $filter, $offset, $limit, $orderType);
    }//End Function

    public function format_params($params) 
    {
        if(isset($params['type'])) $params['type'] = in_array($params['type'], array(1,2,3)) ? $params['type'] : 1;
        if(isset($params['title'])) $params['title'] = htmlspecialchars($params['title'], ENT_QUOTES);
        if(isset($params['author'])) $params['author'] = htmlspecialchars($params['author'], ENT_QUOTES);
        if(isset($params['ifpub'])) $params['ifpub'] = ($params['ifpub']) ? 'true' : 'false';
        return $params;
    }//End Function


    public function valid_insert($params) 
    {
        if(empty($params['title'])){
            trigger_error('文章名称不能为空', E_USER_ERROR);
            return false;
        }
        if(empty($params['node_id'])){
            trigger_error('所属节点不能为空', E_USER_ERROR);
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
        if(!$params) return false;
        $params['uptime'] = time();
        if(empty($params['pubtime']))   $params['pubtime'] = $params['uptime'];
        return parent::insert($params);
    }//End Function

    public function update($params, $filter) 
    {
        $params = $this->valid_update($params);
        if(!$params)    return false;
        if(empty($params['pubtime']))   unset($params['pubtime']);
        $params['uptime'] = time();
        return parent::update($params, $filter);
    }//End Function

    public function update_time($filter) 
    {
        $params['uptime'] = time();
        return parent::update($params, $filter);
    }//End Function


}//End Class
