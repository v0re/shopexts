<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

/*
 * @package content
 * @subpackage dbeav_model
 * @copyright Copyright (c) 2010, shopex. inc
 * @author edwin.lzh@gmail.com
 * @license 
 */
class content_mdl_article_nodes extends dbeav_model
{

    /*
     * 去除params的html_tag
     * @var array $params
     * @access public
     * @return array
     */
    public function strip_params_tags($params) 
    {
        if($params['seo_title']) $params['seo_title'] = htmlspecialchars($params['seo_title'], ENT_QUOTES);
        if($params['seo_keywords']) $params['seo_keywords'] = htmlspecialchars($params['seo_keywords'], ENT_QUOTES);
        if($params['seo_description']) $params['seo_title'] = htmlspecialchars($params['seo_description'], ENT_QUOTES);
        return $params;
    }//End Function

    /*
     * 格式化节点路径名
     * @var array $params
     * @access public
     * @return array
     */
    public function format_params_pagename($params) 
    {
        if(isset($params['node_pagename']) && empty($params['node_pagename'])){
            $params['node_pagename'] = strtolower(preg_replace('([^0-9a-zA-Z])', '', implode('', kernel::single('content_py_base')->get_array($params['node_name'], 'utf-8'))));
        }
        return $params;
    }//End Function

    /*
     * 验证插入数据
     * @var array $params
     * @access public
     * @return array
     */
    public function valid_insert($params) 
    {
        if(empty($params['node_name'])){
            trigger_error('节点名称不能为空', E_USER_ERROR);
            return false;
        }
        $params['parent_id'] = ($params['parent_id'] > 0) ? $params['parent_id'] : 0;
        $params['ordernum'] = ($params['ordernum'] > 0) ? $params['ordernum'] : 0;
        $params = $this->strip_params_tags($params);
        $params = $this->format_params_pagename($params);
        return $params;
    }//End Function

    /*
     * 验证更新数据
     * @var array $params
     * @access public
     * @return array
     */
    public function valid_update($params) 
    {
        $params = $this->strip_params_tags($params);
        $params = $this->format_params_pagename($params);
        return $params;
    }//End Function

    /*
     * 添加节点
     * @var array $params
     * @access public
     * @return boolean
     */
    public function insert($params)
    {
        $params = $this->valid_insert($params);
        if(!$params)    return false;
        $insert_id = parent::insert($params);
        if($insert_id){
            $this->upgrade_parent($insert_id);
            return $this->update_node_path($insert_id);   //更新节点路径信息
        }else{
            return false;
        }
    }

    /*
     * 更新节点
     * @var int $node_id
     * @var array $params
     * @access public
     * @return boolean
     */
    public function update($params, $filter)
    {
        $params = $this->valid_update($params);
        if(!$params)    return false;
        if(isset($params['parent_id'])){
            $node_path_arr = array();
            if($params['parent_id'] > 0){
                $node_path = $this->select()->columns('node_path')->where('node_id = ?', $params['parent_id'])->instance()->fetch_one();
                if($node_path){
                    $node_path_arr = @explode(",", $node_path);
                }
            }
            $rows = $this->getList('*', $filter);
            foreach($rows AS $row){
                if($row['node_id'] == $params['parent_id']){
                    trigger_error('节点『'.$row['node_name']."』的父节点不能为自己", E_USER_ERROR);
                    return false;   //父节点不能更新为自己，防止错误
                }
                if(in_array($row['node_id'], $node_path_arr)){
                    trigger_error('节点『'.$row['node_name']."』的父节点不能为自己的子节点", E_USER_ERROR);
                    return false;   //父节点不能移动至自己的子节点，防止错误
                }
            }
        }
        $res = parent::update($params, $filter);
        if($res){
            if(isset($params['parent_id'])){
                foreach($rows AS $row){
                    if($row['parent_id'] != $params['parent_id']){
                        $this->upgrade_parent($row['node_id']);
                        $this->update_nodes_path($row['node_id']);
                        $this->update_node_path($row['parent_id']);
                    }
                }
            }
            return true;
        }else{
            return false;
        }
    }

    /*
     * 移除节点
     * @var int $node_id
     * @access public
     */
    public function delete($filter)
    {
        $rows = $this->getList('*', $filter);
        foreach($rows AS $row){
            if($this->has_chilren($row['node_id'])){
                trigger_error("节点『".$row['node_name']."』下存在子节点，不能删除", E_USER_ERROR);
                return false;   //存在子节点
            }
        }
        $res = parent::delete($filter);
        if($res){
            foreach($rows AS $row){
                $this->upgrade_parent($row['node_id']);
            }
            return true;
        }else{
            return false;
        }
    }

    /*
     * 节点发布
     * @var boolean $pub
     * @var array $filter
     * @access public
     */
    public function publish($pub=true, $filter)
    {
        
        $params = array('ifpub' => ($pub)? "true":"false");
        $oCAN =  kernel::single("content_article_node");
        if(!$pub) { //取消发布:同时取消子节点
            $aNodes = $oCAN->get_nodes($filter['node_id']);
            foreach ($aNodes as $row) {
                if($row['has_children']) 
                    $this->publish($pub, array('node_id'=>$row['node_id']));
            }
        } else {  //父节点未发布时禁止当前节点发布
            $aInfo = $oCAN->get_node($filter['node_id']);
            if($aInfo['parent_id']) {
                $aParInfo = $oCAN->get_node($aInfo['parent_id']);
                if($aParInfo['ifpub']=='false') {
                    return false;
                }
            }
        }
        
        return $this->update($params, $filter);
    }

    /*
     * 更新节点下所有节点的节点信息包括自身
     * @var int $node_id
     * @access public
     * @return void
     */
    public function update_nodes_path($node_id) 
    {
        if(empty($node_id))    return false;
        $this->update_node_path($node_id);
        $rows = $this->select()->columns('node_id')->where('parent_id = ?', $node_id)->instance()->fetch_all();
        foreach($rows AS $data){
            $this->update_nodes_path($data['node_id']);
        }
    }//End Function


    /*
     * 更新节点path信息
     * @var int $node_id
     * @access public
     * @return boolean
     */
    public function update_node_path($node_id) 
    {
        if(empty($node_id)) return false;
        $params = $this->get_node_path($node_id);
        $params['has_children'] = ($this->has_chilren($node_id)) ? 'true' : 'false';
        return $this->update($params, array('node_id'=>intval($node_id)));
    }//End Function

    /*
     * 取得节点path信息
     * @var int $node_id
     * @access public
     * @return array
     */
    public function get_node_path($node_id) 
    {
        if(empty($node_id)) false;
        $row = $this->select()->where('node_id = ?', $node_id)->instance()->fetch_row();
        if($row['parent_id'] == 0)  return array('node_depth'=>1, 'node_path'=>$row['node_id']);
        $parentRow = $this->select()->where('node_id = ?', $row['parent_id'])->instance()->fetch_row();
        $path = $parentRow['node_path'] . ',' . $row['node_id'];
        return array('node_depth'=>count(explode(',', $path)), 'node_path'=>$path);
    }//End Function

    /*
     * 强制检测是否有子节点
     * @var int $node_id
     * @access public
     * @return boolean
     */
    public function has_chilren($node_id) 
    {
        if(empty($node_id)) return false;
        $count = $this->select()->columns('count(*)')->where('parent_id = ?', $node_id)->instance()->fetch_one();
        if($count){
            return $count;
        }else{
            return false;
        }
    }//End Function

    /*
     * 更新节点父类
     * @var int $node_id
     * @access public
     * @return boolean
     */
    public function upgrade_parent($node_id) 
    {
        $parent_id = $this->select()->columns('parent_id')->where('node_id = ?', $node_id)->instance()->fetch_one();
        if($parent_id > 0){
            return $this->update_node_path($parent_id);
        }
        return true;
    }//End Function

    /*
     * 取得节点信息
     * @var int $node_id
     * @access public
     * @return boolean
     */
    public function get_by_id($node_id) 
    {
        return $this->select()->where('node_id = ?', $node_id)->instance()->fetch_row();
    }//End Function

    public function get_childrens_id($parent_id) 
    {
        if($parent_id > 0){
            $data =  $this->select()->columns('node_id')->where('FIND_IN_SET("'.$parent_id.'", node_path)')->instance()->fetch_col();
        }else{
            $data = $this->select()->columns('node_id')->instance()->fetch_col();
        }
        return $data['node_id'];
    }//End Function
}//End Class
