<?php

/**
 * pagefactory
 *
 * @package
 * @version $Id: adminPage.php 1903 2008-04-24 07:06:22Z ever $
 * @copyright 2003-2007 ShopEx
 * @author Wanglei <flaboy@zovatech.com>
 * @license Commercial
 */

require('pageFactory.php');

class adminPage extends pageFactory{

    var $__tmpl;
    var $pagedata;
    var $ajaxdata;
    var $pagePrompt = true;
    var $transaction_start = false;
    var $path = array();

    /**
     * pagefactory
     *
     * @access public
     * @return void
     */
    function adminPage(){

        $this->system = &$GLOBALS['system'];
        $smarty = &$this->system->loadModel('system/frontend');
        $smarty->ctl = &$this;
        array_unshift($smarty->plugins_dir,CORE_DIR.'/admin/smartyplugin');
        $smarty->default_resource_type = 'admin';
        $this->message = &$_SESSION['message'];
        
        $this->in = &$this->system->incomming();
        $this->pagedata=array();

        if(DEBUG_TEMPLETE){
           $o = $this->system->loadModel('system/template');
           $theme=$this->system->getConf('system.ui.current_theme');
           $o->resetTheme($theme);
        }

        if($_GET['_ajax']){
            if(!defined('IN_AJAX')){
                define('IN_AJAX',true);
                $this->ajaxdata=array();
                ob_start();
            }
        }else{
            define('IN_AJAX',false);
        }

        if($_GET['ctl']!='passport'){

            if(!$_SESSION['profile'] || $this->system->session->isNotlocked()){
                $this->notAuth();
            }else{

                $this->op = &$_SESSION['profile'];
                $oOpt = &$this->system->loadModel('admin/operator','config');
                $data = $oOpt->instance($this->op->opid);
                if(!$this->op->is_super && !$oOpt->check_role($this->op->opid,$this->workground)){
                      $this->system->responseCode(403);
                      exit;
                }
                $GLOBALS['op'] = &$this->op;

                $config = unserialize($data['config']);
                if(isset($config['timezone'])){
                    $GLOBALS['user_timezone'] = $config['timezone'];
                }else{
                    $GLOBALS['user_timezone'] = $this->system->getConf('system.timezone.default');
                }
            }
        }
    }

    function notAuth($return=null){
        if(IN_AJAX){
            $this->system->responseCode(401);
            exit();
        }else{
            $url = 'index.php?ctl=passport&act=login';
            $output =<<<EOF
<script>
        var href = top.location.href;
        var pos = href.indexOf('#') + 1;
        window.location.href="$url"+(pos ? ('&return='+encodeURIComponent(href.substr(pos))) : '');
</script>
EOF;
            echo $output;
            exit();
        }
    }
   function runTemplete(){
            /*- templete-begin -*/
$data = Array('bG9naW4uaHRtbA=='=>'3d78bd48550434d01aa3ededa5784bce',
        'ZGFzaGJvYXJkLmh0bWw='=>'1538272b5040988358ed396d4b5fb657',
        'aW5kZXguaHRtbA=='=>'6b99bca25e6d51b5f4118eff620bb04a',
        'c3lzdGVtL3Rvb2xzL2Fib3V0Lmh0bWw='=>'0ff7ff4fd1d69b39c9581c74d15ac9a3',
        );
/*- templete-end -*/
            return $data;
    }

    function output(){
        $output = &$this->system->loadModel('system/frontend');
        $output->clear_all_assign();

        if($this->pagedata){
            foreach ($this->pagedata as $key=>$data){
                $output->assign($key,$data);
            }
        }
        header('Content-Type: text/html;charset=utf-8');
        $display = $output->fetch($this->__tmpl);
        $this->display($display);
    }

    function page($view,$onePage=false){


        if(!isset($_GET['_ajax'])){
            header('Location: index.php#'.$_SERVER['QUERY_STRING']);
        }

        $this->pagedata['_PAGE_'] = $view;

        $this->pagedata['_inurl'] = ($p = strpos($_SERVER['REQUEST_URI'],'&_ajax='))?substr($_SERVER['REQUEST_URI'],0,$p):$_SERVER['REQUEST_URI'];
        $this->pagedata['_ONE_PAGE_'] = $onePage;

        $smarty = &$this->system->loadModel('system/frontend');
        $smarty->clear_all_assign();

        $smarty->assign('message',$this->message);
        $this->message='';
        $this->pagedata['_path_'] = $this->path;

        if($this->pagedata){
            foreach ($this->pagedata as $key=>$data){
                $smarty->assign($key,$data);
            }
        }

        $output = $smarty->fetch('page.html');

        if(!isset($this->workground)){
            if($p = strpos('/',$_GET['ctl'])){
                $this->workground = substr($_GET['ctl'],0,$p);
            }else{
                $this->workground = substr(get_class($this),4);
            }
        }
        if($_GET['_wg']!=$this->workground && $this->workground){
            $smarty->clear_all_assign();
            $menus = $this->op->getMenu($this->workground,$this->op->is_super);
            $trees = array();
            foreach($menus as $k=>$m){
                if($m['type']=='tree'){
                    $o = $this->system->loadModel($menus[$k]['model']);
                    $menus[$k] = array_merge($menus[$k], $o->treeOptions());
                    $trees[] = array('model'=>$menus[$k]['model'],'actions'=>json_encode($menus[$k]['actions']));
                    $menus[$k]['items'] = $o->getNodes();
                    unset($o,$opt);
                }
            }
            $smarty->assign('trees',$trees);
            $smarty->assign('menus',$menus);
            $smarty->assign('workground',$this->workground);
            $output .= $smarty->fetch('sidemenu.html');
        }
        $this->display($output);
    }

    function display(&$output){
        $etag = crc32($output);
        header("Cache-Control:no-store, no-cache, must-revalidate"); // HTTP/1.1
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");// 强制查询etag
        header('Etag: '.$etag);
        header('Progma: no-cache');
        if(isset($_SERVER['HTTP_IF_NONE_MATCH']) && ($_SERVER['HTTP_IF_NONE_MATCH'] == $etag)){
            header('HTTP/1.1 304 Not Modified',true,304);
            exit(0);
        }else{
            header('Content-Type: text/html; charset=utf-8');
            echo $output;
        }
        exit(0);
    }

    function splash($status='success',$jumpto=null,$msg='操作成功',$errinfo=array(),$wait=3,$js=null){
        header("Cache-Control:no-store, no-cache, must-revalidate"); // HTTP/1.1
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");// 强制查询etag
        header('Progma: no-cache');
        if(!$msg){
            $msg = __('操作成功');
        }

        if($_FILES){
            header('Content-Type: text/html; charset=utf-8');
            echo '<script>parent.W.page.bind(parent.W)("index.php?ctl=default&act=uploadSplash",{method:"post",update:parent.upload_rs_el,data:'.json_encode(func_get_args()).'});</script>';
        }else{
            $this->pagedata['status'] = $status;
            $this->pagedata['msg'] = $msg;
            $this->pagedata['jscript'] = $js;
            $this->pagedata['errinfo'] = $errinfo;
            $this->pagedata['jumpto'] = $jumpto;
            $this->pagedata['wait'] = $status=='success'?0.2:3;
            $this->pagedata['debug_code'] = defined('DEBUG_CODE')?DEBUG_CODE:false;
            $err_valve = (defined('DEBUG_CODE')?DEBUG_CODE:false)?0:1;
            if(count($this->system->_err)>$err_valve){
                $this->pagedata['error_info'] = $this->system->_err;
            }
//      if($_REQUEST['inContent']=='true'){
            $this->setview('splash/'.$status.'.html');
            $this->output();
//      }else{
//        $this->page('splash/'.$status.'.html');
//      }
        }
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
            $this->system->set_mo_pkg($domain);
            $ctl = &$this->system->getController($ctl);
            $ctl->message = $this->message;
            $ctl->pagedata = &$this->pagedata;
            $ctl->ajaxdata = &$this->ajaxdata;
            $this->system->callAction($ctl,$act,$args);
        }else{
            $this->system->callAction($this,$act,$args);
        }
    }

    function begin($url=null,$errAction=null,$shutHandle=null){
        set_error_handler(array(&$this,'_errorHandler'));
        if($this->transaction_start) trigger_error('The transaction has been started',E_USER_ERROR);
        if(!$url)trigger_error('The transaction has been started',E_USER_ERROR);
        $this->transaction_start = true;
        $this->_shutHandle = $shutHandle?$shutHandle:(E_USER_ERROR | E_ERROR);
        $this->_action_url = $url;
        $this->_errAction = $errAction;
        $this->_err = array();
    }

    function end($result=true,$message=null,$url=null,$showNotice=false){
        if(!$this->transaction_start) trigger_error('The transaction has not started yet',E_USER_ERROR);
        $this->transaction_start = false;
        restore_error_handler();

        if(is_null($url)){
            $url = $this->_action_url;
        }
        if($result){
            $status = "success";
            $message = ($message=='' ? __('操作完成！') : __('操作完成：').$message);
        }else{
            $status = "failed";
            $message = __("操作失败: 对不起,无法执行您要求的操作");
        }
        $this->splash($status,$url,$message,$showNotice?$this->_err:null);
    }

    function end_only(){
        if(!$this->transaction_start) trigger_error('The transaction has not started yet',E_USER_ERROR);
        $this->transaction_start = false;
        restore_error_handler();
    }

    function setError($errorno=0,$jumpto='back',$msg='',$links=array(),$time=3,$js=null){
        $this->system->ErrorSet = array('errorno'=>$errorno,'message'=>$msg,'jumpto'=>$jumpto,'links'=>$links,'time'=>$time,'js'=>$js);
    }

    function _errorHandler($errno, $errstr, $errfile, $errline){

        $errorlevels = array(
            2048 => 'Notice',
            1024 => 'Notice',
            512 => 'Warning',
            256 => 'Error',
            128 => 'Warning',
            64 => 'Error',
            32 => 'Warning',
            16 => 'Error',
            8 => 'Notice',
            4 => 'Error',
            2 => 'Warning',
            1 => 'Error');

        $this->_err[] = array('code'=>$errno, 'string'=>$errstr, 'file'=>$errfile, 'line'=>$errline,'codeinfo'=>$errorlevels[$errno]);

        if(isset($this->system->ErrorSet['errorno']) && isset($this->_errAction[$this->system->ErrorSet['errorno']])){
            $this->splash('failed',$this->_errAction[$this->system->ErrorSet['errorno']],$errstr);
        }else{
            switch($errno){
            case $errno & ( E_NOTICE | E_USER_NOTICE | E_WARNING):
                break;

            case $errno & ( $this->_shutHandle ):
                restore_error_handler();
                $this->splash('failed',$this->_action_url,'&nbsp;'.$errstr,$this->_err);

            /*default:
                restore_error_handler();
                $this->splash('failed',$this->_action_url,$errstr,$this->_err);*/
            }
        }
        return true;
    }
}
?>
