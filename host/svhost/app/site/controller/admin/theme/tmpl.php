<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class site_ctl_admin_theme_tmpl extends site_admin_controller 
{

    /*
     * workground
     * @var string
     */
    var $workground = 'site.wrokground.theme';

    private function check($theme) 
    {
        if(empty($theme))   return false;
        $dir = THEME_DIR . '/' . $theme;
        return is_dir($dir);
    }//End Function

    private function get_theme_dir($theme, $open_path='') 
    {
        return realpath(THEME_DIR . '/' . $theme . '/' . str_replace(array('-','.'), array('/','/'), $open_path));  
    }//End Function

    public function index() 
    {
        $theme = $this->_request->get_get('theme');
        $this->pagedata['list'] = kernel::single('site_theme_tmpl')->get_edit_list($theme);
        $this->pagedata['types'] = kernel::single('site_theme_tmpl')->get_name();
        $this->pagedata['theme'] = $theme;
        $this->display('admin/theme/tmpl/index.html');
    }//End Function

    public function add() 
    {
        $theme = $this->_request->get_get('theme');
        if(!$this->check($theme))   $this->_error();
        
        $this->pagedata['theme'] = $theme;
        $this->pagedata['type'] = $this->_request->get_get('type');
        $this->pagedata['types'] = kernel::single('site_theme_tmpl')->get_name();
        $this->pagedata['content'] = '<{require file="block/header.html"}>
<div class="AllWrapInside clearfix">

  <div class="mainColumn pageMain"><{widgets id="nav"}>  <{main}> </div>
  <div class="sideColumn pageSide"> <{widgets id="sideritems"}> </div>
</div>
<{require file="block/footer.html"}>';
        $this->display('admin/theme/tmpl/add.html');
    }//End Function

    public function set_default() 
    {
        $id = $this->_request->get_get('id');
        if($id > 0 && is_numeric($id)){
            $data = $this->app->model('themes_tmpl')->getList('*', array('id'=>$id));
            $data = $data[0];
            if($data['id']){
                kernel::single('site_theme_tmpl')->set_default($data['tmpl_type'], $data['theme'], $data['tmpl_path']);
                die('success');
            }
        }
        die('failure');
    }//End Function

    /*
     * 添加模版
     */
    public function insert_tmpl() 
    {
        $this->begin();
        $data['theme'] = $this->_request->get_post('theme');
        if(!$this->check($data['theme']))   $this->_error();

        $data['tmpl_type'] = $this->_request->get_post('tmpl_type');
        $data['tmpl_name'] = $this->_request->get_post('tmpl_name');
        $data['tmpl_path'] = $this->_request->get_post('tmpl_path');
        $data['content'] = $this->_request->get_post('content');
        
        if(kernel::single('site_theme_tmpl')->insert_tmpl($data)){
            $this->end(true, '添加成功');
        }else{
            $this->end(false, '添加失败');
        }
    }//End Function

    /*
     * 添加相似
     */
    public function copy_tmpl() 
    {
        $this->begin();
        $theme = $this->_request->get_get('theme');
        $file_name = $this->_request->get_get('tmpl');
        
        if(!$this->check($theme))   $this->end(false, '缺少参数');

        if(kernel::single('site_theme_tmpl')->copy_tmpl($file_name, $theme)){

            $this->end(true, '添加成功');
        }else{
            $this->end(false, '添加失败');
        }

    }//End Function

    /*
     * 删除模版文件
     */
    public function delete_tmpl() 
    {
        $this->begin();
        $theme = $this->_request->get_get('theme');
        $file_name = $this->_request->get_get('tmpl');

        if(!$this->check($theme))   $this->end(false, '缺少参数');

        //数据库
        if(kernel::single('site_theme_tmpl')->delete_tmpl($file_name, $theme)){
        
            //物理
            $dir = $this->get_theme_dir($theme, '/');
            $fileObj = kernel::single('site_explorer_file');
            $fileObj->delete_file($dir . '/' . $file_name);

            //配置
            kernel::single('site_theme_tmpl')->del_default(basename($file_name, '.index'), $theme);
            $this->end(true, '删除成功');
        }else{
            $this->end(false, '删除失败');
        }
    }//End Function

}//End Class
