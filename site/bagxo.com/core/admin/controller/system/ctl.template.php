<?php
class ctl_template extends adminPage{

    var $workground ='site';

    function index(){
        
        set_time_limit(360);
        $this->path[] = array('text'=>"模板管理");
        $o = $this->system->loadModel('system/template');
       
        $usedTpl = $o->getDefault();
        
        
        $this->pagedata['themes'] = $o->getList();
        
    
        foreach($this->pagedata['themes'] as $k=>$theme){
            if($theme['theme']==$usedTpl){
                $this->pagedata['currentTheme'] = $this->pagedata['themes'][$k];
                unset($this->pagedata['themes'][$k]);
            }
        }

        if(defined('SAAS_MODE')&&SAAS_MODE){
             $this->pagedata['saas_mode'] = true;
        } 

        $this->pagedata['allowUpload'] = $o->allowUpload($msg);
        $this->pagedata['cantUploadMsg'] = $msg;
        $this->page('system/template/list.html');
    }
    function backTemplate(){
        $o = $this->system->loadModel('system/template');
        $name='theme-bak';
        $this->begin('index.php?ctl=system/template&act=edit&p[0]='.$_GET['template']);
        if($_GET['template'] &&  $o->makeXml($_GET['template'],$name)){
            $this->end(true,'备份成功');
        }else{
            $this->end(false,'备份失败');
        }
    }
    function resetTheme($theme){
         $o = $this->system->loadModel('system/template');
         if($o->resetTheme($theme)){
            $this->splash('success','index.php?ctl=system/template&act=index');
         }else{
            $this->splash('failed','index.php?ctl=system/template&act=index');
         }

    }

    function upload(){
        header('Content-Type: text/html; charset=utf-8');
        $o = $this->system->loadModel('system/template');
        if($theme = $o->upload($_FILES['Filedata'],$msg)){
            $this->pagedata['theme'] = $theme;
            $this->setView('system/template/themeupload.html');
            $this->output();
        }else{
            echo $msg;
        }
    }

    function setDefault($theme){
        $o = $this->system->loadModel('system/template');
        if($o->setDefault($theme)){
            $this->splash('success','index.php?ctl=system/template&act=index');
        }else{
            $this->splash('failed','index.php?ctl=system/template&act=index',__('设置默认失败'));
        }
    }

    function remove($theme){
        $this->begin('index.php?ctl=system/template&act=index');
        $o = $this->system->loadModel('system/template');
        $this->end($o->remove($theme),'模板'.$theme.'已删除');
    }

    function preview($theme){
        require(CORE_DIR.'/include/shopPreview.php');
        $this->system->session->close();

        $s = new shopPreview();
        $s->view(array(
            'cache'=>null,
            'query'=>$_GET['url']?$_GET['url']:'index.html',
            'base_url'=>"index.php?ctl=system/template&act=preview&p[0]={$theme}&url=/",
            'url_prefix'=>"index.php?ctl=system/template&act=preview&p[0]={$theme}&url=/",
            'domain'=>'',
            'member'=>null,
            'cur'=>null,
            'lang'=>null
        ));
    }
    function doBak($theme){
    
        $this->begin('index.php?ctl=system/template&act=edit&p[0]='.$theme);
        $o = $this->system->loadModel('system/template');
        if($_POST['validtemplate']){
            $xml=$_POST['validtemplate'];
        }
        $o = $this->system->loadModel('system/template');
        
        if($o->reset($theme,$xml)){
            $this->end(true,'加载成功');
        }else{
            $this->end(false,'加载失败');
        }
    }
    function reset($theme,$xml=''){
        
        $o = $this->system->loadModel('system/template');
        $o->reset($theme,$xml);
        $this->message=$theme.'已经还原回安装状态。';
        $this->index();
    }

    function dlPkg($theme){
        $o = $this->system->loadModel('system/template');
        $o->outputPkg($theme);
    }

    function previewImg($theme){
        $o = $this->system->loadModel('system/template');
        $o->previewImg($theme);
    }

    function templateList(){
        $o=$this->system->loadModel('system/template');
        $this->system->output(json_encode($o->templateList()));
    }
    function templateConfig(){
        $o=$this->system->loadModel('system/template');
        $this->system->output(json_encode($o->templateConfig($this->in['template'])));
    }
    function templateConfigEdit($sTemplate,$sKey,$sValue){
        $o=$this->system->loadModel('system/template');
        $o->templateConfigEdit($sTemplate,$sKey,$sValue);
    }

    function htmEditPage(){
        $o=$this->system->loadModel('content/page');
        $data=$o->htmEdit($this->in['src']);
        $this->pagedata['title']=$this->in['src'];
        $this->pagedata['name']=empty($data['page_name'])?$this->in['name']:$data['page_name'];
        $this->pagedata['html']=$data['page_content'];
        $this->page('system/template/htmEdit.html');
    }

    function editHtml(){
        $o=$this->system->loadModel('system/template');
        $o->editHtml($this->in['title'],array('page_name'=>$this->in['name'],'page_content'=>$this->in['html'],'page_time'=>time()));
    }

    function edit($theme){

        $this->path[] = array('text'=>'模板编辑');
        $o = $this->system->loadModel('system/template');
        $xmlTheme=$o->getThemes($theme);
       
        $info = $o->getThemeInfo($theme);
        //        $this->pagedata['viewSets'] = $info['views'];
        $this->pagedata['theme'] = $theme;
        $this->pagedata['themeslist'] = $xmlTheme;
        $this->pagedata['templetename'] = $info['name'];
        $this->pagedata['config']=$info['config']['config'];
        $this->pagedata['template']=$o->templateList($theme);
        unset($info);
        $this->page('system/template/edit.html');
    }

    function saveViewSet($theme){
        $this->splash('success','index.php?ctl=system/template&act=edit&p[0]='.$theme);
    }
    function saveConfig($theme){
        
        $o=$this->system->loadModel('system/template');
        
        $info=$o->getThemeInfo($theme);
        foreach($info['config']['config'] as $k=>$v){
            $key=$v['key'];
            $info['config']['config'][$k]['value']=$this->in['config'][$key];
            
        }
        
        $info['config']=array(
            'config'=>$info['config']['config'],
            'borders'=>$info['config']['borders'],
            'views'=>$info['config']['views']
        );
        
        unset($info['borders'],$info['views']);
        if($o->updateThemes($info)){
            $this->splash('success','index.php?ctl=system/template&act=edit&p[0]='.$theme);
        }else{
            $this->splash('failed','index.php?ctl=system/template&act=edit&p[0]='.$theme,__('设置失败'));
        }
    }
    function widgetsSet($theme,$view){        
        header('Content-Type: text/html; charset=utf-8');
        $this->path[] = array('text'=>'模板可视化编辑');
        $widgets = $this->system->loadModel('content/widgets');
        $o = $this->system->loadModel('system/template');
        $this->pagedata['views'] = $o->getViews($theme);
        $this->pagedata['widgetsLib'] = $widgets->getLibs();
        $this->pagedata['theme'] = $theme;
        $this->pagedata['view'] =$view;
        $this->pagedata['viewname'] = $o->getListName($view);
        $this->setView('system/template/templateEdit.html');
        $this->output();
    }
    
    function _headerOfWidget(){
        $return='<script src="js/2.DropMenu.js"></script>';
        $return.='<script src="js/2.jstools.js" type="text/javascript"></script>';
        $return.= '<script src="js/3.ajaks.js" type="text/javascript"></script>';
        $return.= '<script src="js/coms/Filter.js" type="text/javascript"></script>';
        $return.= '<script src="js/coms/editor.js" type="text/javascript"></script>';
        $return.= '<script src="js/coms/Dialog.js" type="text/javascript"></script>';
        $return.='<script src="js/3.HistoryManager.js" type="text/javascript"></script>';
        $return.='<link media="screen, projection" type="text/css" href="css/reset.css" rel="stylesheet"></link>';
        $return.='<link media="screen, projection" type="text/css" href="css/grid.css" rel="stylesheet"></link>';
        $return.='<link media="screen, projection" type="text/css" href="css/forms.css" rel="stylesheet"></link>';
        $return.='<link media="screen, projection" type="text/css" href="css/struct.css" rel="stylesheet"></link>';
        $return.='<link media="screen, projection" type="text/css" href="css/style.css" rel="stylesheet"></link>';
        $return.='<link media="screen, projection" type="text/css" href="css/mooRainbow.css" rel="stylesheet"></link>';
        $return.='<link media="screen, projection" type="text/css" href="css/typography.css" rel="stylesheet"></link>';
        $return.='<link media="screen, projection" type="text/css" href="css/eidtor.css" rel="stylesheet"></link>';
        return $return;
    }
    function widgetsToolbar(){
        $widgets = $this->system->loadModel('content/widgets');
        $this->pagedata['widgetsLib'] = $widgets->getLibs(0);
        $this->pagedata['theme'] = $this->in['theme'];
        $this->setView('system/template/widgetsToolbar.html');
        $this->output();
    }
    
    function widgetsSave(){

        error_reporting( E_ERROR | E_WARNING | E_PARSE );//todo
        $widgets = $this->system->loadModel('content/widgets');
        if(is_array($_POST['widgets'])){
            
                        ////exit();
            foreach($_POST['widgets'] as $widgets_id=>$base){
                //$pos = strrpos($base,':');
                //$widgetsSet[$widgets_id] = array('base_file'=>substr($base,0,$pos),'base_slot'=>substr($base,$pos+1));
                $aTmp=explode(':',$base);
                $base_id=array_pop($aTmp);
                $base_slot=array_pop($aTmp);
                $base_file=implode(':',$aTmp);
                if($_POST['html'][$widgets_id]){
                    $widgetsSet[$widgets_id] = array('base_file'=>$base_file,'base_slot'=>$base_slot,'base_id'=>$base_id,'border'=>'__none__','params'=>array('html'=>stripslashes($_POST['html'][$widgets_id])));
                }else{
                    $widgetsSet[$widgets_id] = array('base_file'=>$base_file,'base_slot'=>$base_slot,'base_id'=>$base_id);
                }
            }
        }
        
        if(false !== ($map = $widgets->saveSlots($widgetsSet,$_POST['files']))){
            echo json_encode($map);
        }else{
            echo json_encode(false);
        }
    }

    function saveWg($widgets_type,$widgets_id,$theme,$domid){
        header('Content-Type: text/html;charset=utf-8');
        unSafeVar($_POST);
        error_reporting( E_ERROR | E_WARNING | E_PARSE );//todo
        $widgets = &$this->system->loadModel('content/widgets');
    
        $set = array(
            'widgets_type'=>$widgets_type,
            'title'=>$_POST['__wg']['title'],
            'border'=>$_POST['__wg']['border'],
            'tpl'=>$_POST['__wg']['tpl'],
            'domid'=>$_POST['__wg']['domid'],
            'classname'=>$_POST['__wg']['classname'],
        );
        unset($_POST['__wg']);
        $set['params'] = $_POST;
        $set['_domid'] = $domid;

        if(is_numeric($widgets_id)){
            $widgets->saveEntry($widgets_id,$set);
        }elseif(preg_match('/^tmp_([0-9]+)$/i',$widgets_id,$match)){
            $_SESSION['_tmp_wg'][$match[1]] = $set;
        }
       
        //echo $widgets->fetch($set,true);
        echo $widgets->adminWgBorder(array('title'=>$set['title'],'widgets_id'=>$widgets_id,'domid'=>$set['domid'],'border'=>$set['border'],'widgets_type'=>$set['widgets_type'],'html'=>$widgets->fetch($set,true),'border'=>$set['border']),$theme,true);
    }

    function editWidgets($widgets_id,$theme){
        
        $widgets = $this->system->loadModel('content/widgets');
        if(is_numeric($widgets_id)){
            $widgetObj = $widgets->getWidget($widgets_id);
        }elseif(preg_match('/^tmp_([0-9]+)$/i',$widgets_id,$match)){
            $widgetObj = $_SESSION['_tmp_wg'][$match[1]];
        }
        //    $this->pagedata['widgetsType'] = $widgets_type;
        $this->pagedata['widgetEditor'] = $widgets->editor($widgetObj['widgets_type'],$theme,$widgetObj['params']);
        $this->pagedata['widgets_type'] = $widgetObj['widgets_type'];
        
        $this->pagedata['widgets_id'] = $widgets_id;
        
        //$this->pagedata['widgets_id'] =1209984198305;
        

        $this->pagedata['widgets_title'] = $widgetObj['title'];
        $this->pagedata['widgets_border']=$widgetObj['border'];
        $this->pagedata['widgets_classname']=$widgetObj['classname'];
        $this->pagedata['widgets_domid']=$widgetObj['domid'];

        //$this->pagedata['widgets_domid']=1209982722434;
        $this->pagedata['widgets_tpl']=$widgetObj['tpl'];

        //echo '####'.$widgetObj['classname'].'####';

        $this->pagedata['widgetsTpl'] = str_replace('\'','\\\'',$widgets->adminWgBorder(array('title'=>$widgetObj['title'],'html'=>'loading...'),$theme));

        $this->pagedata['theme']=$theme;
        $this->setView('system/template/saveWidgets.html');
        $this->output();
    }

    function doAddWidgets($widgets_type,$theme){
        error_reporting( E_ERROR | E_WARNING | E_PARSE );//todo
        $widgets = $this->system->loadModel('content/widgets');
        $this->pagedata['widgetsType'] = $widgets_type;
        $this->pagedata['widgetEditor'] = $widgets->editor($widgets_type,$theme);
        
        $this->pagedata['theme'] = $theme;

        $this->pagedata['i']=is_array($_SESSION['_tmp_wg'])?count($_SESSION['_tmp_wg']):0;
        //$this->pagedata['widgetsTpl'] = str_replace('\'','\\\'',$widgets->adminWgBorder(array('title'=>'title','html'=>'loading...')));

        $this->setView('system/template/doAddWidgets.html');
        $this->output();
    }
    function addWidgetsPage($themes){
    
        $widgets = $this->system->loadModel('content/widgets');
        //$o = $this->system->loadModel('system/template');
        //$this->pagedata['views'] = $o->getViews($theme);
        $this->pagedata['themes'] = $themes;
        $this->pagedata['widgetsLib'] = $widgets->getLibs(null);
        $this->setView('system/template/widgetsCenter.html');
        $this->output();
    }

    function getWidgetsInfo($type=''){
        if($_GET['widgets']){
            $widgets = $this->system->loadModel('content/widgets');
            $this->pagedata['widgetsInfo'] = $widgets->getThisWidgetsInfo($_GET['widgets']);
            $this->pagedata['widgets'] =$_GET['widgets'];
            
        }
    
        $this->pagedata['themes'] = $this->system->getConf('system.ui.current_theme');
        if($type=='1'){
            $this->setView('content/widgets/widgetsDetailRight.html');
        }
        else{
            $this->setView('system/template/widgetsDetailRight.html');
        }
        $this->output();
    }

    function addWidgetsPageExtend($themes,$type=''){
        
        $widgets = $this->system->loadModel('content/widgets');
        $this->pagedata['themes'] = $themes;
        $this->pagedata['widgetsLib'] = $widgets->getLibs($_POST['catalog']);
        if($type=='1'){
            $this->setView('content/widgets/widgetsLeftDetail.html');
        }else{
            $this->setView('system/template/widgetsLeftDetail.html');
        }
        $this->output();

    }
    function insertWg($widgets_type,$domid,$theme){
        header('Content-Type: text/html;charset=utf-8');
        error_reporting( E_ERROR | E_WARNING | E_PARSE );//todo
        unSafeVar($_POST);
        $widgets = $this->system->loadModel('content/widgets');
        $set = array(
            'widgets_type'=>$widgets_type,
            'title'=>$_POST['__wg']['title'],
            'border'=>$_POST['__wg']['border'],
            'tpl'=>$_POST['__wg']['tpl'],
            'domid'=>$_POST['__wg']['domid'],
            'classname'=>$_POST['__wg']['classname'],
        );
       
        unset($_POST['__wg']);
        $set['params'] = $_POST;
        $set['_domid'] = $domid;
        $i=is_array($_SESSION['_tmp_wg'])?count($_SESSION['_tmp_wg']):0;
        $_SESSION['_tmp_wg'][$i] = $set;
        $data=$widgets->adminWgBorder(array('title'=>$set['title'],'domid'=>$set['domid'],'border'=>$set['border'],'widgets_type'=>$set['widgets_type'],'html'=>$widgets->fetch($set,true),'border'=>$set['border']),$theme,true);
        echo $data;
    }

    function copyWg($domid,$widgetid){
        $widgets = $this->system->loadModel('content/widgets');
        if(strstr($widgetid,'tmp_')){
            $widgetid=str_replace('tmp_','',$widgetid);
            $set=$_SESSION['_tmp_wg'][$widgetid];
        }else{
            $set=$widgets->getWidget($widgetid);
            unset($set['widgets_id']);
        }
        $set['_domid'] = $domid;
        $i=is_array($_SESSION['_tmp_wg'])?count($_SESSION['_tmp_wg']):0;
        $_SESSION['_tmp_wg'][$i] = $set;
        echo json_encode(array('widgetid'=>'tmp_'.$i));
    }

    function editor($theme,$file){
        $this->path[] = array('text'=>'模板源码编辑');
        $o = $this->system->loadModel('system/template');
        $usedTpl = $o->getDefault();
        $this->pagedata['theme'] = $theme;
        $this->pagedata['file'] = $file;
        if(!($this->pagedata['content'] = $o->getContent($theme,$file))){
            $this->pagedata['content'] = $o->getContent($theme,'default.html');
        }
        $this->page('system/template/editor.html');
    }
    function removePage($theme,$file){
        $o = $this->system->loadModel('system/template');
        $this->begin('index.php?ctl=system/template&act=edit&p[0]='.$theme);
        if($o->delFile($theme,$file)){
            $this->end(true,'删除成功');
        }else{
            $this->end(false,'删除失败');
        }
        
    }
    function saveContent(){
        //$this->begin('index.php?ctl=system/template&act=index&p[0]='.$_POST['theme'].'&p[1]='.$_POST['file']);
        $this->begin('index.php?ctl=system/template&act=edit&p[0]='.$_POST['theme']);
        $o = $this->system->loadModel('system/template');
        $ret = $o->setContent($_POST['theme'],$_POST['file'],$_POST['content']);
        if($ret){
           $this->end(true,'模板修改成功');
        }else{
           $this->end(false,'模板修改失败');
        }
    }

    function templetePreview($tpl,$file){
        
        header('Content-Type: text/html; charset=utf-8');        
        $this->system->session->close();
        $smarty = &$this->system->loadModel('system/frontend');
        $smarty->compile_dir = HOME_DIR.'/cache/admin_tmpl';
        $smarty->theme = $tpl;
        $this->theme = $tpl;

        $smarty->register_prefilter(array(&$this,'_prefix_tpl'));
        $smarty->register_compiler_function('require',array(&$this,'_require'));
        $smarty->register_compiler_function('main',array(&$this,'_main'));

        $smarty->register_function('link',array(&$this->system,'mkUrl'));
        $smarty->register_function('footer',array(&$this,'_footer'));
        $smarty->register_function('header',array(&$this,'_header'));

        $smarty->register_resource("user", array(array(&$this,"_get_template"), 
        array(&$this,"_get_timestamp"), 
        array(&$this,"_get_secure"), 
        array(&$this,"_get_trusted"))); 
        $smarty->register_compiler_function('widgets',array(&$this,'_widgets_bar'));
        $smarty->display('user:'.$tpl.'/'.urldecode($file));
    }
    
    function _widgets_bar($tag_args, &$smarty){
        $s = $smarty->_current_file;
//    if(($pos = strpos($s,':')) && $part = substr($s,0,$pos)){
//      if($part=='test'){
//        $s = 'user:'.substr($s,$pos+1);
//      }
//    }

        $i = intval($smarty->_wgbar[$s]++);
        $args = $smarty->_parse_attrs($tag_args);
        
        
        
        
        return 'echo \'<div class="shopWidgets_panel" base_file="'.$s.'" base_slot="'.$i.'" base_id="'.substr($args['id'],1,-1).'"  >\';$system = &$GLOBALS[\'system\'];
        if(!$GLOBALS[\'_widgets_mdl\'])$GLOBALS[\'_widgets_mdl\'] = $system->loadModel(\'content/widgets\');
        $widgets = &$GLOBALS[\'_widgets_mdl\'];
        $widgets->adminLoad("'.$s.'",'.($args['id']?($i.','.$args['id']):$i).');echo \'</div>\'';
        
    }

    function _require($tag_args, &$smarty) { 
        $attrs = $smarty->_parse_attrs($tag_args);
        $output = '';

        if (isset($assign_var)) {
            $output .= "ob_start();\n";
        }

        $output .=
            "\$_smarty_tpl_vars = \$this->_tpl_vars;\n";

        $_params = "array('smarty_include_tpl_file' => 'user:'.\$this->theme.'/'.{$attrs['file']}, 'smarty_include_vars' => array())";

        $output .= "\$this->_smarty_include($_params);\n" .
            "\$this->_tpl_vars = \$_smarty_tpl_vars;\n" .
            "unset(\$_smarty_tpl_vars);\n";

        if (isset($assign_var)) {
            $output .= "\$this->assign(" . $assign_var . ", ob_get_contents()); ob_end_clean();\n";
        }

        return $output;
    }

    function _get_secure(){return true;}
    function _get_trusted(){return true;}

    function _get_template($tpl_name, &$tpl_source, &$smarty) { 
        $tpl_source = file_get_contents(THEME_DIR.'/'.$tpl_name);
        if (!is_bool($tpl_source)) { 
            return true; 
        } else { 
            return false; 
        } 
    } 

    function _get_timestamp($tpl_name, &$tpl_timestamp, &$smarty) { 
        $tpl_timestamp = filemtime(THEME_DIR.'/'.$tpl_name);
        if (!is_bool($tpl_timestamp)) { 
            return true; 
        } else { 
            return false; 
        } 
    }

    function _main($tag_args, &$smarty){
        return '?><div class="system-widgets-box">&nbsp;</div><?php';
    }

    function _prefix_tpl($tpl,&$smarty){

        if(isset($this->_in_widgets)){
            $tpl_res = $this->system->base_url().'plugins/widgets/'.$this->_in_widgets.'/';
            unset($this->_in_widgets);
        }else{
            $tpl_res = $this->system->base_url().'themes/'.$this->theme.'/';
        }

        $from = array(
            '/((?:background|src|href)\s*=\s*["|\'])(?:\.\/|\.\.\/)?(images\/.*?["|\'])/is',
            '/((?:background|background-image):\s*?url\()(?:\.\/|\.\.\/)?(images\/)/is',
            '/<!--[^<|>|{|\n]*?-->/'
        );
        $to = array(
            '\1'.$tpl_res.'\2',
            '\1'.$tpl_res.'\2',
            ''
        );

        $tpl = preg_replace($from,$to,$tpl);
        if(substr($tpl,0,3)=="\xEF\xBB\xBF")
            $tpl = substr($tpl,3);

        //      if($this->system->getConf('system.stripHtml',true)){
        $tpl = $tpl;
        //      }
        return $tpl;
    }

    function _header(){
            $ret='<base href="'.$this->system->base_url().'"/>';
        if( defined('DEBUG_CSS') && DEBUG_CSS){
            $ret.= '<link rel="stylesheet" href="statics/framework.css" type="text/css" />';
            $ret.='<link rel="stylesheet" href="statics/shop.css" type="text/css" />';
            $ret.='<link rel="stylesheet" href="statics/widgets.css" type="text/css" />';
            $ret.='<link rel="stylesheet" href="statics/widgets_edit.css" type="text/css" />';
        }elseif( defined('GZIP_CSS') && GZIP_CSS){
            $ret.= '<link rel="stylesheet" href="statics/style.zcss" type="text/css" />';
            $ret.='<link rel="stylesheet" href="statics/widgets_edit.css" type="text/css" />';
        }else{
            $ret.= '<link rel="stylesheet" href="statics/style.css" type="text/css" />';
            $ret.='<link rel="stylesheet" href="statics/widgets_edit.css" type="text/css" />';
        }
        if( defined('DEBUG_JS') && DEBUG_JS){
            $ret.= '<script src="'.dirname($_SERVER['PHP_SELF']).'/js/0.mootools.js"></script>
                    <script src="'.dirname($_SERVER['PHP_SELF']).'/DragDropPlus.js"></script>
                    <script src="'.dirname($_SERVER['PHP_SELF']).'/shopWidgets.js"></script>';
        }elseif( defined('GZIP_JS') && GZIP_JS){
            $ret.= '<script src="'.dirname($_SERVER['PHP_SELF']).'/widgets.jgz"></script>';
        }else{
            $ret.= '<script  src="'.dirname($_SERVER['PHP_SELF']).'/widgets.js"></script>';
        }
        return $ret;
    }

    function _footer(){
       return "<div id='drag_operate_box' class='drag_operate_box' style='visibility:hidden;'>
       <div class='drag_handle_box'>
             <table cellpadding='0' cellspacing='0' width='100%'>
                                           <tr>
                                           <td><span class='dhb_title'>标题</span></td>
                                           <td width='40'><span class='dhb_edit'>编辑</span></td>
                                           <td width='40'><span class='dhb_del'>删除</span></td>
                                           </tr>
              </table>
              </div>
          </div>
          
          <div id='drag_ghost_box' class='drag_ghost_box' style='visibility:hidden'>
              
          </div>";
    }
}

?>
