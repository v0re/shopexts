<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

/*
 * @package content
 * @subpackage article
 * @copyright Copyright (c) 2010, shopex. inc
 * @author edwin.lzh@gmail.com
 * @license 
 */
class content_article_node 
{
    const VERSION = '0.1';

    function __construct() 
    {
        $this->model = app::get('content')->model('article_nodes');
        $this->layout_res_dir = app::get('content')->res_dir.'/layout';
    }//End Function
    
    public function get_node_path_url($node_id) 
    {
        $node = $this->get_node($node_id);
        if(empty($node))    return '';
        $nodeArr = explode(',', $node['node_path']);
        foreach($nodeArr AS $id){
            $node = $this->get_node($id);
            $node_url[] = $node['node_pagename'];
        }
        return @join('_', $node_url);
    }//End Function
    
    
    public function get_node_path($node_id) 
    {
        $node = $this->get_node($node_id);
        if(empty($node))    return '';
        $obj = app::get('site')->router();

        $nodeArr = explode(',', $node['node_path']);
        foreach($nodeArr AS $id){
            $node = $this->get_node($id);
            $path[] = array(
                        'link'  =>  (   ($node['homepage']=='true') 
                                        ? $obj->gen_url(array('app'=>'content', 'ctl'=>'site_article', 'act'=>'nodeindex', 'arg0'=>$id)) 
                                        : $obj->gen_url(array('app'=>'content', 'ctl'=>'site_article', 'act'=>'lists', 'arg0'=>$id)) 
                                    ), 
                        'title' =>  $node['node_name'],
                    );
        }
        return $path;
    }//End Function
    

    public function get_node($node_id) 
    {
        return $this->model->select()->where('node_id = ?', $node_id)->instance()->fetch_row();
    }//End Function    
    
    public function get_nodes($parent_id=0) 
    {
        return $this->model->select()->where('parent_id = ?', $parent_id)->order('ordernum ASC')->instance()->fetch_all();
    }//End Function


    public function get_maps($node_id=0, $step=null) 
    {
        $rows = $this->get_nodes($node_id);
        $step = ($step==null) ? $step : $step-1;
        foreach($rows AS $k=>$v){
            if($v['has_children']=='true' && ($step==null || $step>=0)){
                $rows[$k]['childrens'] = $this->get_maps($v['node_id'], $step);
            }
        }
        return $rows;
    }//End Function

    public function get_selectmaps($node_id=0, $step=null) 
    {
        $rows = $this->get_maps($node_id, $step);
        return $this->parse_selectmaps($rows);
    }//End Function

    public function get_listmaps($node_id=0, $step=null) 
    {
        $rows = $this->get_maps($node_id, $step);
        return $this->parse_listmaps($rows);
    }//End Function

    private function parse_selectmaps(array $rows) 
    {
        $data = array();
        foreach($rows AS $k=>$v){
            $data[] = array('node_id'=>$v['node_id'], 'step'=>$v['node_depth'], 'node_name'=>$v['node_name']);
            if($v['childrens']){
                $data = array_merge($data, $this->parse_selectmaps($v['childrens']));
            }
        }
        return $data;
    }//End Function

    private function parse_listmaps($rows) 
    {
        $data = array();
        foreach($rows AS $k=>$v){
            $children = $v['childrens'];
            if(isset($v['childrens']))  unset($v['childrens']);
            $data[] = $v;           
            if($children){
                $data = array_merge($data, $this->parse_listmaps($children));
            }
        }
        return $data;
    }//End Function
    
    public function editor($id, $layout) 
    {
        $bodys = kernel::single('content_article_node')->get_node($id);
        if(empty($bodys['content'])){
            $data['content'] = file_get_contents($this->layout_res_dir . '/1-column/layout.html');
            app::get('content')->model('article_nodes')->update($data, array('node_id'=>$id));
        }else{
            if($layout){
                $data['content'] = file_get_contents($this->layout_res_dir . '/' . $layout . '/layout.html');
                if(app::get('content')->model('article_nodes')->update($data, array('node_id'=>$id))){
                    //app::get('content')->model('article_indexs')->update(array('uptime'=>time()), array('article_id'=>$article_id));
                    $setting = $this->get_layout($layout);
                    $setting['slotsNum'] = intval($setting['slotsNum']);
                    if($setting['slotsNum']>0){
                        $setting['slotsNum']--;
                        $db = kernel::database();
                        $db->exec("update sdb_site_widgets_instance set core_slot=".$db->quote($setting['slotsNum'])." where core_slot>".intval($setting['slotsNum'])." and core_file='content_node:".$id."'");
                    }
                }
            }
        }
        return true;
    }//End Function

    public function get_layout_list(){
        $handle = opendir($this->layout_res_dir);
        $t = array();

        while(false!==($file=readdir($handle))){
            if(in_array($file, array('.', '..', '.svn')))   continue;

            $layouts[$file] = require($this->layout_res_dir . '/' . $file . '/layout_' . $file . '.php');
        }
        closedir($handle);
        return $layouts;
    }//End Function

    public function get_layout($layout) 
    {
        $layouts = $this->get_layout_list();
        return $layouts[$layout];
    }//End Function
    
    
    
}//End Class
