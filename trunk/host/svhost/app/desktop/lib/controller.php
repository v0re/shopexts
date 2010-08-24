<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class desktop_controller extends base_controller{

    var $defaultwg;
    function __construct($app){
        $this->defaultwg = $this->defaultWorkground;
        parent::__construct($app);
        kernel::single('base_session')->start();
        $auth = pam_auth::instance(pam_account::get_account_type('desktop'));
        $account = $auth->account();
        if(get_class($this)!='desktop_ctl_passport' && !$account->is_valid()){
            $url = $this->app->router()->gen_url(array(),1);
            $url = base64_encode($url);
            echo "<script>top.location='index.php?ctl=passport&url=".$url."'</script>";
            exit;
        }
        $this->user = kernel::single('desktop_user');
        if($_GET['ctl']!="passport"&&$_GET['ctl']!=""){
            $this->status = $this->user->get_status();
            if(!$this->status&&$this->status==0){
                #echo "未启用";exit;
                //echo "<script>alert('管理员未启用')</script>";
                echo "<script>window.location='index.php?ctl=passport&act=logout'</script>";
                exit;
            }
        }
        ###如果不是超级管理员就查询操作权限
        if(!$this->user->is_super()){
            if(!$this->user->chkground($this->workground)){
                echo "您无权操作";exit;
            }    
        }

        $this->_finish_modifier = array();
        foreach(kernel::servicelist(sprintf('desktop_controller_content.%s.%s.%s', $_GET['app'],$_GET['ctl'],$_GET['act']))
                as $class_name=>$service){
            if($service instanceof desktop_interface_controller_content){
                if(method_exists($service,'modify')){
                    $this->_finish_modifier[$class_name] = $service;
                }
                if(method_exists($service,'boot')){
                    $service->boot($this);
                }
            }
        }
        if($this->_finish_modifier){
            ob_start();
            register_shutdown_function(array(&$this,'finish_modifier'));
        }

        $this->url = 'index.php?app='.$this->app->app_id.'&ctl='.$_GET['ctl'];
    }
    
    /*
    * 有modifier的处理程序
    */
    function finish_modifier(){
        $content = ob_get_contents();
        ob_end_clean();
        foreach($this->_finish_modifier as $modifier){
            $modifier->modify($content,$this);
        }
        echo $content;
    }
    
    function redirect($url){
        $arr_url = parse_url($url);
        if($arr_url['scheme'] && $arr_url['host']){
            header('Location: '.$url);
        }else{
            header('Location: '.app::get('desktop')->base_url(1).$url);
        }
        // 
    }

    function finder($object_name,$params=array()){
        $_GET['action'] = $_GET['action']?$_GET['action']:'view';
        $finder = kernel::single('desktop_finder_builder_'.$_GET['action'],$this);

        foreach($params as $k=>$v){
            $finder->$k = $v;
        }
        $app_id = substr($object_name,0,strpos($object_name,'_'));
        $app = app::get($app_id);
        $finder->app = $app;
        $finder->work($object_name);
    }

    function singlepage($view, $app_id=''){
        

        $page = $this->fetch($view, $app_id);

        $re = '/<script([^>]*)>(.*?)<\/script>/is';
        $this->__scripts = '';
        $page = preg_replace_callback($re,array(&$this,'_singlepage_prepare'),$page)
            .'<script type="text/plain" id="__eval_scripts__" >'.$this->__scripts.'</script>';
 
        $this->pagedata['statusId'] = $this->app->getConf('b2c.wss.enable');
        $this->pagedata['session_id'] = kernel::single('base_session')->sess_id();
        $this->pagedata['desktop_path'] = app::get('desktop')->res_url;
        $this->pagedata['shopadmin_dir'] = dirname($_SERVER['PHP_SELF']).'/';
        $this->pagedata['shop_base'] = $this->app->base_url();
        $this->pagedata['desktopresurl'] = app::get('desktop')->res_url;

        $this->pagedata['_PAGE_'] = &$page;
        $this->display('singlepage.html','desktop');
    }

    function _singlepage_prepare($match){
        if($match[2] && !strpos($match[1],'src') && !strpos($match[1],'hold')){
            $this->__scripts.="\n".$match[2];
            return '';
        }else{
            return $match[0];
        }
    }
    
    function _outSplitBegin($key){
       return "<!-----$key-----";
    }
    
    function _outSplitEnd($key){
       return "-----$key----->";
    }
    


    

    function url_frame($url){
        $this->sidePanel();
        echo '<iframe width="100%" scrolling="auto" allowtransparency="true" frameborder="0" height="100%" src="'.$url.'" ></iframe>';
    }

    function page($view='', $app_id=''){
        if(!isset($_SERVER['HTTP_REFERER'])){
            header('Location: index.php#'.$_SERVER['QUERY_STRING']);
        }
        $_SESSION['message'] = '';
        

        $service = kernel::service(sprintf('desktop_controller_display.%s.%s.%s', $_GET['app'],$_GET['ctl'],$_GET['act']));
        if($service){
            if(method_exists($service, 'get_file'))  $view = $service->get_file();
            if(method_exists($service, 'get_app_id'))   $app_id = $service->get_app_id();
        }


        if(!$view){
            $view = 'common/default.html';
            $app_id = 'desktop';
        }

        ob_start();
        parent::display($view, $app_id);
        $output = ob_get_contents();
        ob_end_clean();        
                
        $output=$this->sidePanel().$output;

        $this->output($output);
    }
    
    

    function sidePanel(){
         $menuObj = app::get('desktop')->model('menus');
         $bcdata = $menuObj->get_allid($_GET);
         $output = '';
         if(!$this->workground){
            $this->workground = get_class($this);
         }
         $output.="<script>window.BREADCRUMBS ='".($bcdata['workground_id']?$bcdata['workground_id']:0)
                                                .":"
                                                .($bcdata['menu_id']?$bcdata['menu_id']:0)
                                                ."'</script>";
                                            
         if('desktop_ctl_dashboard'==$this->workground){

             $output .="<script>fixSideLeft('add');</script>";
             return $output;
         }else{
             
             $output .="<script>fixSideLeft('remove');</script>";
         }

        if($_SERVER['HTTP_WORKGROUND'] == $this->workground){
            return $output;
        }

            
        $output.= $this->_outSplitBegin('.side-content');
        $output .= $this->get_sidepanel($menuObj);
        $output .= $this->_outSplitEnd('.side-content');
            
        $output .= '<script>window.currentWorkground=\''.$this->workground.'\';</script>';
 
        return $output;
    }

    public function output(&$output) 
    {
       echo $output;
    }//End Function

    function splash($status='success',$url=null,$msg=null,$method='redirect',$params=array()){
        header("Cache-Control:no-store, no-cache, must-revalidate"); // HTTP/1.1
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");// 强制查询etag
        header('Progma: no-cache');
        $default = array(
                $status=>$msg?$msg:__('操作成功'),
                $method=>$url,
            );
        $json = json_encode(array_merge($default, $params));
            
        if($_FILES){
            header('Content-Type: text/html; charset=utf-8');
            echo '<script>top.W.onComplete.call(top.W,'.$json.');</script>';
        }else{
            header('Content-Type:text/jcmd; charset=utf-8');
            echo $json;
        }
        
        exit;
    }

    /**
     * jump_to
     *
     * @param string $act
     * @param string $ctl
     * @param array $args
     * @access public
     * @return void
     */
    function jumpTo($act='index',$ctl=null,$args=null){

        $_GET['act'] = $act;
        if($ctl) $_GET['ctl'] = $ctl;
        if($args) $_GET['p'] = $args;

        if(!is_null($ctl)){

            if($pos=strpos($_GET['ctl'],'/')){
                $domain = substr($_GET['ctl'],0,$pos);
            }else{
                $domain = $_GET['ctl'];
            }
            $ctl = $this->app->single(str_replace('/', '-', $ctl));
            $ctl->message = $this->message;
            $ctl->pagedata = &$this->pagedata;
            $ctl->ajaxdata = &$this->ajaxdata;
            call_user_func(array(str_replace('/', '_', $ctl), $act), $args);
        }else{
            call_user_func(array(get_class($this), $act), $args);
        }
    }
   function get_sidepanel($menuObj){
        $obj = $menuObj;
        $workground_menus = ($obj->menu($_GET,$this->defaultwg));
        if($workground_menus['nogroup']){
            $nogroup = $workground_menus['nogroup'];            
            unset($workground_menus['nogroup']);

        }
        if(!$workground_menus){
            $dashboard_menu = new desktop_sidepanel_dashboard(app::get('desktop'));
            return $dashboard_menu->get_output();
            
        }
        $workground = array();
        $render = app::get('desktop')->render();
        if($_GET['app']&&$_GET['ctl']){
            $workground = $obj->get_current_workground($_GET);
            $render->pagedata['workground'] = $workground;
        };
        $data_id = $obj->get_allid($_GET);
        //$render->pagedata['dataid'] = $data_id['workground_id'].":".$data_id['menu_id'];
        $render->pagedata['side'] = "leftpanel";
        $render->pagedata['menus_data'] = $workground_menus;
        $render->pagedata['nogroup'] = $nogroup;
        return $render->fetch('sidepanel.html');

    }
    function tags(){
        $ex_p = '&wg='.urlencode($_GET['wg']).'&type='.urlencode($_GET['type']);
        $params = array(
            'title'=>'标签管理',
            'actions'=>array(
                array('label'=>'新建普通标签','icon'=>'add.gif','href'=>$this->url.'&act=new_mormal_tag'.$ex_p,'target'=>'dialog::{title:\'新建普通标签\'}'),
               // array('label'=>'新建条件标签','href'=>$this->url.'&act=new_filter_tag'.$ex_p,'target'=>'dialog::{title:\'新建条件标签\'}'),
            ),
            'base_filter'=>array(
                'tag_type'=>$_GET['type']
            ),'use_buildin_new_dialog'=>false,'use_buildin_set_tag'=>false,'use_buildin_export'=>false);
        $this->finder('desktop_mdl_tag',$params);
    }

    function new_mormal_tag(){
        $ex_p = '&wg='.urlencode($_GET['wg']).'&type='.urlencode($_GET['type']);
       if($_POST){
            $this->begin();
            $tagmgr = app::get('desktop')->model('tag');
            $data = array(
                    'tag_name'=>$_POST['tag_name'],
                    'tag_abbr'=>$_POST['tag_abbr'],
                    'tag_type'=>$_REQUEST['type'],
                    'app_id'=>$this->app->app_id,
                    'tag_mode'=>'normal',
                    'tag_bgcolor'=>$_POST['tag_bgcolor'],
                    'tag_fgcolor'=>$_POST['tag_fgcolor'],
                );
            if($_POST['tag_id']){
                $data['tag_id'] = $_POST['tag_id'];
            }//print_r($data);exit;
            $tagmgr->save($data);
            $this->end();
        }else{
            $html = $this->ui()->form_start(array(
                'action'=>$this->url.'&act=new_mormal_tag'.$ex_p,
                'id'=>'form_settag'
                ));
            $html .= $this->ui()->form_input(array('title'=>'标签名','name'=>'tag_name'));
            $html .= $this->ui()->form_input(array('title'=>'标签缩写','maxlength'=>'2','name'=>'tag_abbr'));
            $html .= $this->ui()->form_input(array('title'=>'标签背景色','type'=>'color','name'=>'tag_bgcolor'));
            $html .= $this->ui()->form_input(array('title'=>'标签字体景色','type'=>'color','name'=>'tag_fgcolor'));
            $html.=$this->ui()->form_end();
            echo $html;
echo <<<EOF
<script>
   \$('form_settag').store('target',{
        
     
        onComplete:function(){

            window.finderGroup['{$_GET['finder_id']}'].refresh();
                         
            $('form_settag').getParent('.dialog').retrieve('instance').close();
             
        }
   
   });

</script>
EOF;
        }
    }

    function tag_edit($id){
        $this->url = 'index.php?app='.$_GET['app'].'&ctl='.$_GET['ctl'];
       $render =  app::get('desktop')->render();
        //return $render->fetch('admin/tag/detail.html',$this->app->app_id);
        $mdl_tag = app::get('desktop')->model('tag');
        $tag = $mdl_tag->dump($id,'*');
        $ui = new base_component_ui(null,app::get('desktop'));
        $html = $ui->form_start(array(
                        'action'=>$this->url.'&act=new_mormal_tag'.$ex_p,
                        'id'=>'tag_form_add',
                        ));
            $html .= $ui->form_input(array('title'=>'标签名','name'=>'tag_name','value'=>$tag['tag_name']));
            $html .= $ui->form_input(array('title'=>'标签缩写','maxlength'=>'2','name'=>'tag_abbr','value'=>$tag['tag_abbr']));
            $html .= $ui->form_input(array('title'=>'标签背景色','type'=>'color','name'=>'tag_bgcolor','value'=>$tag['tag_bgcolor']));
            $html .= $ui->form_input(array('title'=>'标签字体色','type'=>'color','name'=>'tag_fgcolor','value'=>$tag['tag_fgcolor']));
            $html .= '<input type="hidden" name="tag_id" value="'.$id.'"/>';
            $html .= '<input type="hidden" name="app_id" value="'.$tag['app_id'].'"/>';
            $html .= '<input type="hidden" name="type" value="'.$tag['tag_type'].'"/>';
            $html.=$ui->form_end();
            echo $html;
echo <<<EOF
<script>
window.addEvent('domready', function(){
    $('tag_form_add').store('target',{
        onComplete:function(){
            
           
            window.finderGroup['{$_GET['finder_id']}'].refresh();
            
            if($('tag_form_add').getParent('.dialog'))
            $('tag_form_add').getParent('.dialog').retrieve('instance').close();
        }
    });
});
</script>
EOF;
    }

}
