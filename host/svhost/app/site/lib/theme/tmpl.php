<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class site_theme_tmpl 
{

    public function get_default($type, $theme) 
    {
        return app::get('site')->getConf('custom_template_'.$theme.'_'.$type);
    }//End Function

    public function set_default($type, $theme, $value) 
    {
        return app::get('site')->setConf('custom_template_'.$theme.'_'.$type, $value);
    }//End Function

    public function del_default($type, $theme) 
    {
        return app::get('site')->setConf('custom_template_'.$theme.'_'.$type, '');
    }//End Function

    public function set_all_tmpl_file($theme) 
    {
        $row = app::get('site')->model('themes_tmpl')->select()->columns('tmpl_path')->where('theme = ?', $theme)->instance()->fetch_col();
        return app::get('site')->setConf('custom_template_'.$theme.'_all_tmpl', $row['tmpl_path']);
    }//End Function

    public function get_all_tmpl_file($theme) 
    {
        return app::get('site')->getConf('custom_template_'.$theme.'_all_tmpl');
    }//End Function

    public function tmpl_file_exists($tmpl_file, $theme) 
    {
        $all = $this->get_all_tmpl_file($theme);
        $all[] = 'block/header.html';
        $all[] = 'block/footer.html';   //头尾文件
        if(is_array($all)){
            return in_array($tmpl_file, $all);
        }else{
            return false;
        }
    }//End Function

    public function get_edit_list($theme) 
    {
        $data = app::get('site')->model('themes_tmpl')->getList('*', array("theme"=>$theme));
        if(is_array($data)){
            foreach($data AS $value){
                if($this->get_default($value['tmpl_type'], $theme) == $value['tmpl_path'])
                    $value['default'] = 1;

                $ret[$value['tmpl_type']][] = $value;
            }
        }
        return $ret;
    }//End Function
    
    public function install($theme) 
    {
        $list = array();
        $this->__get_all_files(THEME_DIR . '/' . $theme, $list, false);
        if(file_exists(THEME_DIR.'/'.$theme.'/cart.html')){
            if(!file_exists(THEME_DIR.'/'.$theme.'/order_detail.html')){
                copy(THEME_DIR.'/'.$theme.'/cart.html',THEME_DIR.'/'.$theme.'/order_detail.html');
            }
            if(!file_exists(THEME_DIR.'/'.$theme.'/order_index.html')){
                copy(THEME_DIR.'/'.$theme.'/cart.html',THEME_DIR.'/'.$theme.'/order_index.html');
            }
        }
        $ctl = $this->get_name();
        foreach($list AS $key=>$value){
            $file_name = basename($value, '.html');
            if(!strpos($file_name,'.')){
                if(($pos=strpos($file_name,'-'))){
                    $type=substr($file_name,0,$pos);
                    $file[$type][$key]['name']=$ctl[substr($file_name,0,$pos)];
                    $file[$type][$key]['file']=$file_name.'.html';
                }else{
                    $type=$file_name;
                    $file[$file_name][$key]['name']=$ctl[$file_name];
                    $file[$file_name][$key]['file']=$file_name.'.html';
                    //$file[$key]['name']=$ctl[$file_name];
                }
                
                touch(THEME_DIR . '/' . $theme . '/' . $file_name . '.html');
                
                if($type && array_key_exists($type, $ctl)){
                    $array = array(
                        'theme'=>$theme,
                        'tmpl_type'=>$type,
                        'tmpl_name'=>$file_name.'.html',
                        'tmpl_path'=>$file_name.'.html',
                        'version'=>filemtime(THEME_DIR . '/' . $theme . '/' . $file_name . '.html'),
                        'content'=>file_get_contents(THEME_DIR . '/' . $theme . '/' . $file_name . '.html')
                    );
                    $this->insert($array);
                    if(!$this->get_default($type, $theme)){
                        $this->set_default($type, $theme, $file_name.'.html');
                    }
                }
            }
        }
    }//End Function

    public function insert($data) 
    {
        if(app::get('site')->model('themes_tmpl')->insert($data)){
            $this->set_all_tmpl_file($data['theme']);
            return true;
        }else{
            return false;
        }
    }//End Function

    public function insert_tmpl($data) 
    {
        $dir = THEME_DIR . '/' . $data['theme'];
        if(!is_dir($dir))   return false;
        if(empty($data['tmpl_type']) || empty($data['content']))    return false;
        $data['tmpl_path'] = strtolower(preg_replace('/[^a-z0-9]/', '', $data['tmpl_path']));
        if($data['tmpl_path']){
            $target = $dir . '/' . $data['tmpl_type'] . '-' . $data['tmpl_path'] . '.html';
            if(is_file($target)){
                $target = '';
            }
        }
        if(empty($target)){
            $flag = true;
            for($i=1; $flag; $i++){
                $target = sprintf('%s/%s(%s).html', $dir, $data['tmpl_type'], $i);
                if(file_exists($target))    continue;
                $flag = false;
            }
        }
        if(file_put_contents($target, $data['content'])){
            $data['tmpl_path'] = basename($target);
            $data['tmpl_name'] = ($data['tmpl_name']) ? $data['tmpl_name'] : basename($target);
            $data['version'] = filemtime($target);
            return $this->insert($data);
        }
        return false;
    }//End Function

    public function copy_tmpl($tmpl, $theme) 
    {
        $source = THEME_DIR . '/' . $theme . '/' . $tmpl;
        if(!is_file($source))   return false;
        $data = app::get('site')->model('themes_tmpl')->getList('*', array('theme'=>$theme, 'tmpl_path'=>$tmpl));
        $data = $data[0];
        if(empty($data))    return false;
        $flag = true;
        for($i=1; $flag; $i++){
            $target = sprintf('%s/%s/%s(%s).html', THEME_DIR, $theme, $data['tmpl_type'], $i);
            if(file_exists($target))    continue;
            copy($source, $target);
            $flag = false;
        }
        unset($data['id']);
        $data['tmpl_path'] = basename($target);
        $data['tmpl_name'] = basename($target);
        return $this->insert($data);
    }//End Function

    public function delete_tmpl($tmpl, $theme) 
    {
        $source = THEME_DIR . '/' . $theme . '/' . $tmpl;
        if(!is_file($source))   return false;
        $data = app::get('site')->model('themes_tmpl')->getList('*', array('theme'=>$theme, 'tmpl_path'=>$tmpl));
        if($data[0]['id'] > 0){
            if(app::get('site')->model('themes_tmpl')->delete(array('id'=>$data[0]['id']))){
                app::get('site')->model('widgets_instance')->delete(array('core_file'=>$theme.'/'.$tmpl));
                $this->set_all_tmpl_file($data[0]['theme']);
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }//End Function

    private function __get_all_files($sDir, &$aFile, $loop=true){
        if($rHandle=opendir($sDir)){
            while(false!==($sItem=readdir($rHandle))){
                if ($sItem!='.' && $sItem!='..' && $sItem!=''){
                    if(is_dir($sDir.'/'.$sItem)){
                        if($loop){
                            $this->__get_all_files($sDir.'/'.$sItem,$aFile);
                        }
                    }else{
                        $aFile[]=$sDir.'/'.$sItem;
                    }
                }
            }
            closedir($rHandle);
        }
    }

    public function get_name(){
        $ctl = array(
            'index'=>__('首页'),
            'gallery'=>__('商品列表页'),
            'product'=>__('商品详细页'),
            'comment'=>__('商品评论/咨询页'),
            'article'=>__('文章页'),
            'gift'=>__('赠品页'),
            'package'=>__('捆绑商品页'),
            'brandlist'=>__('品牌专区页'),
            'brand'=>__('品牌商品展示页'),
            'cart'=>__('购物车页'),
            'search'=>__('高级搜索页'),
            'passport'=>__('注册/登录页'),
            'member'=>__('会员中心页'),
            'page'=>__('站点栏目单独页'),
            'order_detail'=>__('订单详细页'),
            'order_index'=>__('订单确认页'),
            'default'=>__('默认页'),
        );
        return $ctl;
    }

    public function get_list_name() 
    {
        $ctl = array(
            'default.html'=>__('其它页面'),
            'index.html'=>__('首页'),
            'article.html'=>__('文章页'),
            'product.html'=>__('商品详细页'),
            'comment.html'=>__('商品评论/咨询页'),
            'gallery.html'=>__('商品列表页'),
            'cart.html'=>__('购物车页'),
            'gift.html'=>__('赠品页'),
            'member.html'=>__('会员中心页'),
            'page.html'=>__('站点栏目单独页'),
            'passport.html'=>__('注册/登录页'),
            'search.html'=>__('高级搜索页')
        );
        return $ctl[$name];
    }//End Function

    public function touch_tmpl_file($tmpl, $time=null) 
    {
        if(empty($time))    $time = time();
        $source = THEME_DIR . '/' . $tmpl;
        if(is_file($source)){
            return touch($source, $time);
        }else{
            return false;
        }
    }//End Function

    function output_pkg($theme){
        $tar = kernel::single('base_tar');
        $workdir = getcwd();

        if(chdir(THEME_DIR.'/'.$theme)){
            $this->__get_all_files('.',$aFile);
            for($i=0;$i<count($aFile);$i++){
                if($f = substr($aFile[$i],2)){
                    if($f!='theme.xml'){
                        $tar->addFile($f);
                    }
                }
            }
            if(is_file('info.xml')){
                $tar->addFile('info.xml',file_get_contents('info.xml'));
            }
            $tar->addFile('theme.xml',$this->make_configfile($theme));

            $aTheme = kernel::single('site_theme_base')->get_theme_info($theme);

            kernel::single('base_session')->close();

            $name = kernel::single('base_charset')->utf2local(preg_replace('/\s/','-',$aTheme['name'].'-'.$aTheme['version']),'zh');
            @set_time_limit(0);

            header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
            header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
            header('Content-type: application/octet-stream');
            header('Content-type: application/force-download');
            header('Content-Disposition: attachment; filename="'.$name.'.tgz"');
            $tar->getTar('output');
            chdir($workdir);
        }else{
            chdir($workdir);
            return false;
        }
    }

    public function make_configfile($theme){
        $aTheme = kernel::single('site_theme_base')->get_theme_info($theme);

        $aWidget['widgets'] = app::get('site')->model('widgets_instance')->getList('*', array('core_file|head'=>$theme.'/'));
        foreach($aWidget['widgets'] as $i => &$widget){
            $widget['core_file'] = str_replace($theme.'/', '', $widget['core_file']);
            $widget['params'] = serialize($widget['params']);
        }
        $aTheme['config']['config'] = $aTheme['config']['config'];
        $aTheme['config']['views'] = $aTheme['views'];
        $aTheme['id'] = $aTheme['theme'];
        $aTheme=array_merge($aTheme, $aWidget);

        $render = kernel::single('base_render');
        $render->pagedata = $aTheme;
        $sXML = $render->fetch('admin/theme/theme.xml', 'site');

        return $sXML;
    }

}//End Class
