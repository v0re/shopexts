<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class site_ctl_admin_theme_widget extends site_admin_controller 
{

    /*
     * workground
     * @var string
     */
    var $workground = 'site.wrokground.theme';

    public function editor() 
    {
        $theme = $this->_request->get_get('theme');
        $file = $this->_request->get_get('file');
        
        header('Content-Type: text/html; charset=utf-8');
        $this->path[] = array('text'=>__('模板可视化编辑'));
        $this->pagedata['views'] = kernel::single('site_theme_base')->get_view($theme);
        $this->pagedata['widgetsLib'] = kernel::single('site_theme_widget')->get_libs();
        $this->pagedata['theme'] = $theme;
        $this->pagedata['view'] = $file;
        $this->pagedata['viewname'] = kernel::single('site_theme_tmpl')->get_list_name($file);

        $this->pagedata['shopadmin'] = app::get('desktop')->base_url(1);

        $this->pagedata['site_url'] = kernel::base_url(1);

        return $this->singlepage('admin/theme/widget/editor.html');
    }//End Function

    public function preview() 
    {
        $theme = $this->_request->get_get('theme');
        $file = $this->_request->get_get('file');

        header('Content-Type: text/html; charset=utf-8');
        kernel::single('base_session')->close();
        $smarty = kernel::single('site_controller');
        $smarty->tmpl_cachekey('widgets_modifty_'.$theme , true);
        
        $smarty->pagedata['theme_dir'] = kernel::base_url() . '/themes/' . $theme . '/';
        
        $smarty->_compiler()->set_compile_helper('compile_main', kernel::single('site_theme_complier'));
        $smarty->_compiler()->set_view_helper('function_header', 'site_theme_helper');
        $smarty->_compiler()->set_view_helper('function_footer', 'site_theme_helper');
        $smarty->_compiler()->set_compile_helper('compile_widgets', kernel::single('site_theme_complier'));
        $smarty->set_theme($theme);
        $smarty->display_tmpl(urldecode($file));
    }//End Function

    public function add_widgets_page()
    {
        $theme = $this->_request->get_get('theme');
        $this->pagedata['theme'] = $theme;
        $this->pagedata['widgetsLib'] = kernel::single('site_theme_widget')->get_libs();
        $this->display('admin/theme/widget/add_widgets_page.html');
    }//End Function

    public function add_widgets_page_extend()
    {
        $theme = $this->_request->get_get('theme');
        $type = $this->_request->get_get('type');
        $catalog = $this->_request->get_post('catalog');

        $this->pagedata['theme'] = $theme;
        $this->pagedata['widgetsLib'] = kernel::single('site_theme_widget')->get_libs($catalog);
        $this->display('admin/theme/widget/add_widgets_page_extend.html');
    }//End Function

    public function get_widgets_info()
    {
        $type = $this->_request->get_get('type');
        $widgets = $this->_request->get_get('widgets');
        $app = $this->_request->get_get('widgets_app');
        if($widgets){
            $this->pagedata['widgetsInfo'] = kernel::single('site_theme_widget')->get_this_widgets_info($widgets, $app);
            $this->pagedata['widgets'] = $widgets;

        }
        $this->pagedata['theme'] = app::get('site')->getConf('current_theme');
        $this->display('admin/theme/widget/get_widgets_info.html');
    }//End Function

    public function do_add_widgets(){

        $widgets = $this->_request->get_get('widgets');
        $app = $this->_request->get_get('widgets_app');
        $theme = $this->_request->get_get('theme');

        $this->pagedata['widgetsType'] = $widgets;
        $this->pagedata['widgetsApp'] = $app;
        $this->pagedata['widgetEditor'] = kernel::single('site_theme_widget')->editor($widgets, $app, $theme);

        $this->pagedata['theme'] = $theme;

        $this->pagedata['i']=is_array($_SESSION['_tmp_wg_insert'])?count($_SESSION['_tmp_wg_insert']):0;

        $this->display('admin/theme/widget/do_add_widgets.html');
    }

    public function do_edit_widgets(){

        $widgets_id = $this->_request->get_get('widgets_id');
        $theme = $this->_request->get_get('theme');

        if(is_numeric($widgets_id)){
            $widgetObj = app::get('site')->model('widgets_instance')->getList('*', array('widgets_id'=>$widgets_id));
            $widgetObj = $widgetObj[0];
        }elseif(preg_match('/^tmp_([0-9]+)$/i',$widgets_id,$match)){
            $widgetObj = $_SESSION['_tmp_wg_insert'][$match[1]];
        }

        $this->pagedata['widgetEditor'] = kernel::single('site_theme_widget')->editor($widgetObj['widgets_type'],$widgetObj['app'],$theme,$widgetObj['params']);
        $this->pagedata['widgets_type'] = $widgetObj['widgets_type'];

        $this->pagedata['widgets_id'] = $widgets_id;

        $this->pagedata['widgets_title'] = $widgetObj['title'];
        $this->pagedata['widgets_border']=$widgetObj['border'];
        $this->pagedata['widgets_classname']=$widgetObj['classname'];
        $this->pagedata['widgets_domid']=$widgetObj['domid'];
        $this->pagedata['widgets_app'] = $widgetObj['app'];

        $this->pagedata['widgets_tpl']=$widgetObj['tpl'];

        $this->pagedata['widgetsTpl'] = str_replace('\'','\\\'',kernel::single('site_theme_widget')->admin_wg_border(array('title'=>$widgetObj['title'],'html'=>'loading...'),$theme));

        $this->pagedata['theme'] = $theme;
        header("Cache-Control:no-store, no-cache, must-revalidate"); //强制刷新IE缓存
        $this->display('admin/theme/widget/do_edit_widgets.html');
    }

    public function insert_widget(){
        
        header('Content-Type: text/html;charset=utf-8');
       
        $widgets = $this->_request->get_get('widgets');
        $app = $this->_request->get_get('widgets_app');
        $theme = $this->_request->get_get('theme');
        $domid = $this->_request->get_get('domid');

        $wg = $this->_request->get_post('__wg');
    
        $set = array(
            'widgets_type' => $widgets,
            'app' => $app,
            'title' => $wg['title'],
            'border' => $wg['border'],
            'tpl' => $wg['tpl'],
            'domid' => $wg['domid']?$wg['domid']:$domid,
            'classname' => $wg['classname'],
        );

        $post = $this->_request->get_post();
        unset($post['__wg']);

        $set['params'] = $post;
        $set['_domid'] = $set['domid'];

        $i=is_array($_SESSION['_tmp_wg_insert'])?count($_SESSION['_tmp_wg_insert']):0;
        $_SESSION['_tmp_wg_insert'][$i] = $set;
        $data = kernel::single('site_theme_widget')->admin_wg_border(
            array(  'title'=>$set['title'],
                    'domid'=>$set['domid'],
                    'border'=>$set['border'],
                    'widgets_type'=>$set['widgets_type'],
                    'html'=> kernel::single('site_theme_widget')->fetch($set, true),
                    'border'=>$set['border']
            ),
            $theme,true);

        echo $data;
    }

    public function save_widget() 
    {
        header('Content-Type: text/html;charset=utf-8');
        
        $widgets_id = $this->_request->get_get('widgets_id');
        $widgets = $this->_request->get_get('widgets');
        $app = $this->_request->get_get('widgets_app');
        $theme = $this->_request->get_get('theme');
        $domid = $this->_request->get_get('domid');

        $wg = $this->_request->get_post('__wg');

        if($widgets_type=='html')   $widgets_type='usercustom';
        $set = array(
            'widgets_type'=>$widgets,
            'app' => $app,
            'title' => $wg['title'],
            'border' => $wg['border'],
            'tpl' => $wg['tpl'],
            'domid' => $wg['domid']?$wg['domid']:$domid,
            'classname' => $wg['classname'],
        );

        $post = $this->_request->get_post();
        unset($post['__wg']);

        $set['params'] = $post;
        $set['_domid'] = $set['domid'];

        if(is_numeric($widgets_id)){
            $sdata = $set;
            kernel::single('site_theme_widget')->save_widgets($widgets_id, $sdata);
            $set['widgets_id'] = $widgets_id;
        }elseif(preg_match('/^tmp_([0-9]+)$/i',$widgets_id,$match)){
            $_SESSION['_tmp_wg_insert'][$match[1]] = $set;
        }

        $data = kernel::single('site_theme_widget')->admin_wg_border(
            array(  'widgets_id'=>$widgets_id,
                    'title'=>$set['title'],
                    'domid'=>$set['domid'],
                    'border'=>$set['border'],
                    'widgets_type'=>$set['widgets_type'],
                    'html'=> kernel::single('site_theme_widget')->fetch($set, true),
                    'border'=>$set['border']
            ),
            $theme,true);
        echo $data;
    }//End Function

    public function save_all() 
    {
        $widgets = $this->_request->get_post('widgets');
        $html = $this->_request->get_post('html');
        $files = $this->_request->get_post('files');
        
        if(is_array($widgets)){

            foreach($widgets as $widgets_id=>$base){
                $aTmp=explode(':',$base);
                $base_id=array_pop($aTmp);
                $base_slot=array_pop($aTmp);
                $base_file=implode(':',$aTmp);
                if($html[$widgets_id]){
                    $widgetsSet[$widgets_id] = array(
                        'core_file'=>$base_file,
                        'core_slot'=>$base_slot,
                        'core_id'=>$base_id,
                        'border'=>'__none__',
                        'params'=>array('html'=>stripslashes($html[$widgets_id]))
                    );
                }else{
                    $widgetsSet[$widgets_id] = array('core_file'=>$base_file,'core_slot'=>$base_slot,'core_id'=>$base_id);
                }
            }
        }

        if(false !== ($map = kernel::single('site_theme_widget')->save_all($widgetsSet,$files))){
            echo json_encode($map);
        }else{
            echo json_encode(false);
        }
    }//End Function

}//End Class
