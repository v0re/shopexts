<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class site_ctl_admin_theme_manage extends site_admin_controller 
{
       
    /*
     * workground
     * @var string
     */
    var $workground = 'site.wrokground.theme';

    //列表
    public function index() 
    {
        //finder
        kernel::single('site_theme_install')->check_install();
        $actions = array(
                    array('label'=>'上传模板','href'=>'index.php?app=site&ctl=admin_theme_manage&act=swf_upload','target'=>'dialog::{title:\'上传模板\',width:300,height:280}'),
                    array('label'=>'在线安装模板','href'=>'http://addons.shopex.cn/templates/#'.app::get('desktop')->base_url(1) . '?app=site&ctl=admin_theme_manage&act=install_online','target'=>'_blank'),
                    array('label'=>'删除','icon'=>'del.gif','confirm'=>'确定删除选中项？删除后不可从回收站恢复','submit'=>'?app=site&ctl=admin_theme_manage&act=delete')
                );
        $this->finder('site_mdl_themes',array('title'=>'模板管理', 'actions'=>$actions,'use_buildin_recycle'=>false));
    }//End Function

    //flash上传
    public function swf_upload() 
    {
        $this->pagedata['ssid'] = kernel::single('base_session')->sess_id();
        $this->pagedata['swf_loc'] = app::get('desktop')->res_url;
        $this->display('admin/theme/manage/swf_upload.html');
    }//End Function

    public function install_online() 
    {
        $params = $this->_request->get_post();
        if(isset($params['url']) && isset($params['tpl_name']) && isset($params['fullsize'])){
            $params['name'] = ($params['tpl_name']) ? $params['tpl_name'] : basename($params['url']);       //如果没有传入文件名，则使用basename
            $downObj = kernel::single('site_utility_download');
            $ident = $downObj->set_task($params);
            $this->pagedata['ident'] = $ident;
            $this->pagedata['success_url'] = 'index.php?app=site&ctl=admin_theme_manage&act=install';
            $this->singlepage('admin/download/process.html');
        }
    }//End Function
    
    public function upload() 
    {
        $themeInstallObj = kernel::single('site_theme_install');
        $res = $themeInstallObj->install($_FILES['Filedata'],$msg);
        if($res){
            $img = kernel::base_url(1) . '/themes/' . $res['theme'] . '/preview.jpg';
            echo '<img src="'.$img.'" onload="$(this).zoomImg(50,50);" />';
        }else{
            echo $msg;
        }        
    }//End Function

    public function install() 
    {
        $ident = $this->_request->get_get('ident');
        $downObj = kernel::single('site_utility_download');
        $task_info = $downObj->get_task($ident);
        if(empty($task_info))   $this->_error();
        $file = $downObj->get_work_dir() . '/' . $ident . '/' . $task_info['name'];
        
        $msg = __('无法找到安装文件');
        
        if(is_file($file)){
            $fileInfo['tmp_name'] = $file;
            $fileInfo['name'] = time();
            $fileInfo['error'] = '0';
            $fileInfo['size'] = filesize($file);
            $themeInstallObj = kernel::single('site_theme_install');
            $res = $themeInstallObj->install($fileInfo, $msg);
        }
        if($res){
            $img = kernel::base_url(1) . '/themes/' . $res['theme'] . '/preview.jpg';
            $this->pagedata['img'] = '<img src="'.$img.'" />';
            $this->pagedata['msg'] = __('模板安装成功，您可以在模板列表中启用它。');
        }else{
            $this->pagedata['msg'] = $msg;
        }        
        $this->singlepage('admin/download/result.html');
    }//End Function

    public function set_default() 
    {
        $this->begin();
        $theme = $this->_request->get_get('theme');
        if($theme){
            if(kernel::single('site_theme_base')->set_default($theme)){
                $this->end(true, '设置成功', 'index.php?app=site&ctl=admin_theme_manage');
            }else{
                $this->end(false, '设置失败');
            }            
        }
    }//End Function

    public function set_style() 
    {
        $this->begin();
        $theme = $this->_request->get_get('theme');
        $style_id = $this->_request->get_get('style_id');
        if($theme){
            $styles = kernel::single('site_theme_base')->get_theme_styles($theme);
            if(is_array($styles) && array_key_exists($style_id, $styles)){
                if(kernel::single('site_theme_base')->set_theme_style($theme, $styles[$style_id]))
                    $this->end(true, '设置成功', 'index.php?app=site&ctl=admin_theme_manage');
            }
            $this->end(false, '设置失败');
        }
    }//End Function
    
    public function bak() {
        $this->begin();
        $theme = $this->_request->get_get('theme');
        
        $data = kernel::single('site_theme_tmpl')->make_configfile($theme);

        if(file_put_contents(THEME_DIR . '/' . $theme . '/theme_bak.xml', $data)) {
            $this->end(true, __('备份成功！'));
        } else {
            $this->end(false, __('备份失败！'));
        }
    }
    
    public function reset() {
        $this->begin();
        $theme = $this->_request->get_get('theme');
        $loadxml = $this->_request->get_get('rid');
        if(kernel::single("site_theme_install")->init_theme($theme, true, false, $loadxml)) {
            $this->end(true, __('应用成功！'));
        } else {
            $this->end(false, __('应用失败！'));
        }
    }
    
    public function delete() 
    {
        $this->begin();
        $post = $this->_request->get_post();
        if(app::get('site')->model('themes')->delete_file(array('theme'=>$post['theme']))){
            $this->end(true, __('删除成功'), 'javascript:finderGroup["'.$_GET['finder_id'].'"].unselectAll();finderGroup["'.$_GET['finder_id'].'"].refresh();');
        }else{
            $this->end(false, __('删除失败'));
        }
    }//End Function

    public function download() 
    {
        $theme = $this->_request->get_get('theme');
        kernel::single('site_theme_tmpl')->output_pkg($theme);
        exit;
    }//End Function

}//End Class
