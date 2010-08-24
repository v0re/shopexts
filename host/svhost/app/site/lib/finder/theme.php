<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class site_finder_theme
{
    public $addon_cols='theme,stime,author,site,version';

    public $column_preview='预览';
    public $column_preview_width='140';
    public function column_preview($row){
        $current_sytle = kernel::single('site_theme_base')->get_theme_style($row[$this->col_prefix.'theme']);
        $preview = ($current_sytle['preview']) ? $current_sytle['preview'] : 'preview.jpg';
        return sprintf('<img src="%s" id="%s" />', kernel::base_url(1) . '/themes/' . $row[$this->col_prefix.'theme'] . '/' . $preview . '?' . time(), $row[$this->col_prefix.'theme'].'_img');
    }

    public $column_fullstyles = '样式';
    public $column_fullstyles_width = '40';
    public function column_fullstyles($row) 
    {
        $styles = kernel::single('site_theme_base')->get_theme_styles($row[$this->col_prefix.'theme']);
        $render = app::get('site')->render();
        $render->pagedata['styles'] = $styles;
        $render->pagedata['theme'] = $row[$this->col_prefix.'theme'];
        $render->pagedata['preview_prefix'] = kernel::base_url(1) . '/themes/' . $row[$this->col_prefix.'theme'];
        $render->pagedata['current'] = kernel::single('site_theme_base')->get_theme_style($row[$this->col_prefix.'theme']);
        return $render->fetch('admin/theme/manage/style.html');
    }//End Function

    public $column_use = '使用';
    public $column_use_width = '140';
    public function column_use($row) 
    {
        $current_theme = kernel::single('site_theme_base')->get_default();
        if($row[$this->col_prefix.'theme'] == $current_theme){
            return '使用中';
        }else{
            return '<a href="javascript:;" onClick="javascript:W.page(\'index.php?app=site&ctl=admin_theme_manage&act=set_default&theme='.$row[$this->col_prefix.'theme'].'\')" >启用</a>';
        }
    }//End Function

    public $detail_tmpl = '模版文件';
    public function detail_tmpl($id){
        $data = app::get('site')->model('themes')->getList('*', array('theme'=>$id));
        $render = app::get('site')->render();
        $theme = $data[0]['theme'];
        $render->pagedata['list'] = kernel::single('site_theme_tmpl')->get_edit_list($theme);
        $render->pagedata['types'] = kernel::single('site_theme_tmpl')->get_name();
        $render->pagedata['theme'] = $theme;
        return $render->fetch('admin/theme/tmpl/frame.html');
    }

    public $detail_info = '基本信息';
    public function detail_info($id) 
    {
        $data = app::get('site')->model('themes')->getList('*', array('theme'=>$id));
        $render = app::get('site')->render();
        $row = $data[0];
        $row['config'] = $row['config'];
        $render->pagedata['row'] = $row;
        return $render->fetch('admin/theme/detail/info.html');
    }//End Function

    public $detail_files = '文件结构';
    public function detail_files($id) 
    {
        $data = app::get('site')->model('themes')->getList('*', array('theme'=>$id));
        $render = app::get('site')->render();
        $theme = $data[0]['theme'];
        $render->pagedata['init_url'] = 'index.php?app=site&ctl=admin_explorer_theme&act=directory&theme=' . $theme;
        return $render->fetch('admin/explorer/theme/index.html');
    }//End Function
    
    
    public $detail_back = '备份与还原';
    public function detail_back($theme) 
    {
        $render = app::get('site')->render();
        $render->pagedata['theme'] = $theme;
        $option = '';
        if(file_exists(THEME_DIR . '/' . $theme . '/theme.xml')) {
            $option .= '<option value="theme.xml">默认</option>';
        }
        if(file_exists(THEME_DIR . '/' . $theme . '/theme_bak.xml')) {
            $option .= '<option value="theme_bak.xml">备份</option>';
        }
        $render->pagedata['option'] = $option;
        return $render->fetch('admin/theme/tmpl/backup.html');
    }//End Function
    
    public $detail_download = '下载模板';
    public function detail_download($theme) 
    {
        $render = app::get('site')->render();
        $render->pagedata['theme'] = $theme;
        return $render->fetch('admin/theme/tmpl/download.html');
    }//End Function
   
    
}//End Class
