<?php
class ctl_pages extends adminPage{

    var $workground ='site';

    //  function index_bak(){
    //    $this->pagedata['frames'] = array('1-column','2-columns-left','2-columns-right','3-columns');
    //    $this->pagedata['base'] = $this->system->getConf('site.homepage.tmpl_name');
    //    $this->page('content/frameset.html');
    //  }

    function index($ident,$node_id){
    
        $ident=urldecode($ident);
        $sitemap = $this->system->loadModel('content/sitemap');
        $this->path[] = array('text'=>'编辑单独页面-['.$ident.']');
        $this->pagedata['ident'] = $ident;
        $this->pagedata['node_id'] = $node_id;
        $this->pagedata['path'] = $sitemap->getPathById($node_id);
        $this->pagedata['themes'] = $this->system->getConf('system.ui.current_theme');
        if($this->pagedata['path'][count($this->pagedata['path'])-1]['title']){    
            $this->path[]=array('text'=>$this->pagedata['path'][count($this->pagedata['path'])-1]['title']);
        }
        $this->page('content/page_edit.html');
    }

    function editor($ident,$layout=null){
        $ident=urldecode($ident);
        header('Content-type: text/html;charset=utf-8');
        $page = &$this->system->loadModel('content/page');
        $page->editor($ident,$layout,$_GET['theme']);
    }

    function save(){
        
        if($_POST['ident']  && $_POST['node_id']){
            $systmpl = &$this->system->loadModel('content/systmpl');
            $systmpl->set('pages/'.$_POST['ident'],$_POST['body']);
            $sitemap = &$this->system->loadModel('content/sitemap');
            $setTitle = $sitemap->setTitle($_POST['node_id'],$_POST['title']);
            $this->splash('success','index.php?ctl=content/sitemaps',__('页面成功保存'));
        }else{
            $this->splash('failed','index.php?ctl=content/sitemaps',__('页面保存失败'));
        }
    }
    
    function view($style){
        $this->pagedata['page'] = 'systmpl:frames/'.$style;
        $this->system->loadModel('content/systmpl');
        $this->setView('content/page.html');
        $this->output();
    }

    function layout($ident){
        $page = &$this->system->loadModel('content/page');
        $this->pagedata['layouts'] = &$page->getList();
        $this->pagedata['ident'] = $ident;
        $this->setView('content/layout.html');
        $this->output();
    }

    function widgets(){
        /*$widgets = $this->system->loadModel('content/widgets');
        $this->pagedata['themes'] = $this->system->getConf('system.ui.current_theme');
        $this->pagedata['widgetsLib'] = $widgets->getLibs(null);
        $this->setView('content/widgets/widgetsCenter.html');
        $this->output();*/
    }
    
    function editHtml(){
       $this->pagedata['htmls']=stripslashes($_POST['htmls']);
       if(file_exists(CORE_DIR.'/admin/view/content/editHtml.html')){
           $this->setView('content/editHtml.html');
       }else{
           $this->setView('content/editHTML.html');
       }
       $this->output();
    }

    function widgetinfo($type){
        $widgets = &$this->system->loadModel('content/widgets');
        $this->pagedata['widgets'] = $widgets->getWidgetsInfo($type);
        $this->setView('content/widgets/info.html');
        $this->output();
    }

}
?>
