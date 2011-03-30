<?php

/**
 * pagefactory
 *
 * @package
 * @version $Id: shopPage.php 2049 2008-04-29 06:53:26Z flaboy $
 * @copyright 2003-2007 ShopEx
 * @author Wanglei <flaboy@zovatech.com>
 * @license Commercial
 */

require_once('pageFactory.php');
class shopPage extends pageFactory{

    var $noCache = false;
    var $_running = true;
    var $contentType = 'text/html;charset=utf-8';
    var $member;
    var $header='';
    var $keyWords=null;
    var $metaDesc=null;
    var $title=null;
    var $type=null;
    var $transaction_start = false;
    var $__tmpl=null;

    /**
     * pagefactory
     *
     * @access public
     * @return void
     */
    function shopPage(){
        $this->system = &$GLOBALS['system'];
        $this->setError();

        if($this->system->getConf('shop.showGenerator',true))
            $this->header.="<meta name=\"generator\" content=\"ShopEx ".$this->system->_app_version."\" />\n";

        $this->system->controller = &$this;
        $this->path = array();
        if(!$this->is_login()){
            $this->system->location($this->system->mkUrl('passport','login',array(base64_encode(str_replace(array('+','/','='),array('_',',','~'),$this->system->mkUrl($this->system->request['action']['controller'],$this->system->request['action']['method'],$this->system->request['action']['args']))))));
        }
    }

    function header($header){
        $this->_header[] = $header;
    }

    function error_jump($errMsg,$tpl='default.html'){
        $this->__tmpl = 'user:'.TPL_ID.'/'.$tpl.':exception/index';
        $this->title = "error";
        $this->pagedata['errormsg'] = $errMsg;
        $this->output();
    }

    function checkSiteKey(){
        if(defined('WITH_PAGE_API') && WITH_PAGE_API){
            return true;
        }else{
            return false;
        }
    }

    function output(){
        $requestMethod = strtolower($this->action['type']);
        /* 产生路径信息，构建导航系统 */
        switch($requestMethod){

        case 'json':
            ob_clean();
            $this->checkSiteKey();
            $this->contentType = 'text/plain';
            echo json_encode($this->pagedata);
            break;

        case 'xml':
            ob_clean();
            $this->checkSiteKey();
            $this->contentType = 'text/xml';
            $xmlModel = &$this->system->loadModel('utility/xml');
            $xml ='<'.'?xml version="1.0" encoding="utf-8"?'.'>';
            $xml .= $xmlModel->array2xml($this->pagedata,'root');
            echo $xml;
            break;

        default:
            $sitemap = &$this->system->loadModel('content/sitemap');
            if(!isset($this->type))$type = 'action';
            if(!isset($this->id))$this->id = array('action'=>$this->action['controller'].':'.urldecode($this->action['method']));
            if($this->title){
                if($this->path[count($this->path)-1]['title']!=$this->title && count($this->path)){
                    $this->path[]=array('title'=>$this->title);
                }
            }
            if($this->cat_type){
                $GLOBALS['runtime']['path'] = array_merge($sitemap->getPath($this->type,$this->cat_type,$this->action['method']),$this->path);
            }else{
                $GLOBALS['runtime']['path'] = array_merge($sitemap->getPath($this->type,$this->id,$this->action['method']),$this->path);
            }
            /* 环境变量 */
            $this->pagedata['env']['thumbnail_pic_height'] = $this->system->getConf('site.thumbnail_pic_height');
            $this->pagedata['env']['thumbnail_pic_width'] = $this->system->getConf('site.thumbnail_pic_width');
            $this->pagedata['env']['small_pic_height'] = $this->system->getConf('site.small_pic_height');
            $this->pagedata['env']['small_pic_width'] = $this->system->getConf('site.small_pic_width');
            $this->pagedata['env']['big_pic_height'] = $this->system->getConf('site.big_pic_height');
            $this->pagedata['env']['big_pic_width'] = $this->system->getConf('site.big_pic_width');
            $this->pagedata['request'] = &$this->system->request;
            $this->pagedata['member'] = &$this->member;
            if(!$this->title){
                if($GLOBALS['runtime']['path']){
                    array_shift($GLOBALS['runtime']['path']);
                    $shopTitle = $GLOBALS['runtime']['path'];
                    krsort($shopTitle);
                    foreach($shopTitle as $tk => $tl){
                        if ($tk==intval(count($shopTitle)-1))
                            $this->title.=trim($tl['title'])." ";
                        else
                            $this->title.=trim($tl['title'])." ";
                    }
                }
            }
            if($titleFormat = $this->system->getConf('site.title_format')){
                $this->pagedata['title'] = $this->system->sprintf($titleFormat,$this->title);
            }else{
                $this->pagedata['title'] = $this->title;
            }

            if(DEBUG_TEMPLETE){
                $o = $this->system->loadModel('system/template');
                $theme=$this->system->getConf('system.ui.current_theme');
                $o->resetTheme($theme);
            }
            $output = &$this->system->loadModel('system/frontend');

            $output->statusString = "action={$this->action['controller']}:{$this->action['method']}&p=".(isset($this->action['args'][0])?$this->action['args'][0]:null);
            $output->clear_all_assign();

            if($keyWords = ($this->keyWords?$this->keyWords:$this->system->getConf('site.meta_key_words')))
                $this->header.="<meta name=\"Keywords\" content=\"$keyWords\" />\n";
            if($metaDesc = ($this->metaDesc?$this->metaDesc:$this->system->getConf('site.meta_desc')))
                $this->header.="<meta name=\"Description\" content=\"$metaDesc\" />\n";

            $oTemplate=$this->system->loadModel('system/template');
            $theme = $oTemplate->applyTheme(defined('TPL_ID')?TPL_ID:null);
            $output->theme = $theme['theme'];

            if(is_array($theme['config'])){

                foreach($theme['config']['config'] as $c){
                    if(isset($c['key']))$this->pagedata['theme'][$c['key']] = $c['value'];
                    echo $c['value'];
                }
            }

            if(!isset($this->pagedata['_MAIN_']))$this->pagedata['_MAIN_'] = $this->action['controller'].'/'.$this->action['method'].'.html';

            if(!isset($this->__tmpl)){

                $tmpl_file = $this->_get_view(TPL_ID,
                    $ctl=$this->system->request['action']['controller']
                    ,$act=$this->system->request['action']['method']);
                $this->__tmpl = 'user:'.$output->theme.'/'.$tmpl_file;
            }else{
                $this->__tmpl = $output->template_exists('user:'.$output->theme.'/view/'.$this->__tmpl)?'user:'.$output->theme.'/view/'.$this->__tmpl:'shop:'.$this->__tmpl;
            }
            if($this->pagedata){
                foreach ($this->pagedata as $key=>$data){
                    $output->assign($key,$data);
                }
            }

            $this->system->_debugger['log'] = ob_get_contents();
            ob_clean();
            $output->display($this->__tmpl);
        }
    }

    function splash($status='success',$jumpto=null,$msg="Finished!",$links=array(),$wait=false,$js=null){
        //        if($this->transaction_start) $this->end();
        $this->system->_succ = true;

        $this->pagedata['_MAIN_'] = 'splash/'.$status.'.html';
        $this->pagedata['msg'] = $msg;
        $this->pagedata['jumpto'] = $jumpto;
        $this->pagedata['links'] = $links;
        $this->pagedata['js'] = $js;

        if($wait){
            $this->pagedata['wait'] = $wait;
        }elseif($status='success'){
            $this->pagedata['wait'] = 1;
        }else{
            $this->pagedata['wait'] = 10;
        }

        $this->pagedata['debug_code'] = defined('DEBUG_CODE')?DEBUG_CODE:false;
        $err_valve = (defined('DEBUG_CODE')?DEBUG_CODE:false)?0:1;
        if(count($this->system->_err)>$err_valve){

            $this->pagedata['error_info'] = $this->system->_err;
        }

        header('Content-type: '.$this->contentType);
        $this->title = $status=='success'?'Finished!':'Fail!';
        $this->output();
        exit;
    }

    function nowredirect($status='success',$url,$msg=""){
        if($status=='success'){
            header('Location: '.$url);
        }else{
            $url = $this->system->mkUrl('passport','login',array($url,base64_encode(str_replace(array('+','/','='),array('_',',','~'),$msg))));
            header('Location: '.$url);
            exit;
        }

    }

    function redirect($ctl=null,$act='index',$args=null,$jsJump=false){
        if(!$ctl)$ctl=$this->system->request['action']['controller'];
        $url = $this->system->mkUrl($ctl,$act,$args);
        $this->system->_succ=true;
        if($jsJump){
            echo "<header><meta http-equiv=\"refresh\" content=\"0; url={$url}\"></header>";
        }else{
            header('Location: '.$url);
        }
        exit();
    }

    /**
     * _verifyMember
     *
     * @param mixed $required  强制必须为会员身份。否则只验证有效性
     * @access protected
     * @return void
     */
    function _verifyMember($member_id=true){
        if($_COOKIE['MEMBER']){    //会员关闭浏览器时，该Cookie还是存在的。editor:Ever 2008-07-03
            $member = explode('-',$_COOKIE['MEMBER']);
        }else{
            $member = array(0);
        }
        $memberObj = &$this->system->loadModel('member/account');
        $memberInfo = $memberObj->verify($member[0],$member[2]);
        if($member_id!==false && (!$member[0] || !$memberInfo)){
            $this->system->setCookie('MEMBER', '', time()-1000);
            $this->system->setCookie('MLV', '', time()-1000);
            $this->system->setCookie('UNAME', '', time()-1000);
            $this->system->_succ = true;
            $this->system->location($this->system->mkUrl('passport','login',array(base64_encode(str_replace(array('+','/','='),array('_',',','~'),$this->system->mkUrl($this->system->request['action']['controller'],$this->system->request['action']['method'],$this->system->request['action']['args']))))));
        }else{
            $this->member = &$memberInfo;
            if($member_id!==true && $memberInfo['member_id']!=$member_id && is_numeric($member_id)){
                $this->system->error(404);
                return false;
            }
            $GLOBALS['runtime']['member_lv']=$this->member['member_lv_id'];
        }
    }

    function _mkform($arr,&$result,$depth){
        foreach($arr as $k=>$v){
            $newDepth = array_merge($depth,array($k));
            if(is_array($v)){
                $this->_mkform($v,$result,$newDepth);
            }else{
                if(count($newDepth)>1)
                    $result[array_shift($newDepth).'['.implode('][',$newDepth).']'] = $v;
                else
                    $result[$k] = $v;
            }
        }
    }

    function _restoreAction(){

        if(isset($_REQUEST['url']))
            $query = $this->system->request['base_url'].'m/'.$_REQUEST['url'];
        else{
            $query = $this->system->request['base_url'].'m/';
        }
        //将登录前的url改为登录后的url，todo:抽象出系统方法，目前情况需要验证 /zh_CN/时是否正确
        //      $query = $actmapper->appendUrl('m/'.substr($query,max(array(strrpos($query,'?'), strrpos($query,'/')))+1));

        if(!isset($_POST['form'])){
            echo "<header><meta http-equiv=\"refresh\" content=\"0; url={$query}\"></header>";
            exit();
        }else{
            $this->_mkform(unserialize(get_magic_quotes_gpc()?stripcslashes($_POST['form']):$_POST['form']),$form,array());
            foreach($form as $k=>$v){
                $post .= '<input type="hidden" name="'.$k.'" value="'.$v.'" />';
            }

            $html=<<<EOF
<html><head><title>Redirecting...</title><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/><meta name=”robots” content=”noindex,noarchive,follow” /></head>
<body>
    <form method="post" action="{$query}" id="redirect" >
    {$post}
    </form>
    <script>document.getElementById('redirect').submit();</script>
</body>
</html>
EOF;
            echo $html;
            exit();
        }
    }

    function _get_view($theme,$ctl,$act='index'){
        if($ctl=='page' && $act=='index'){
            if(file_exists(THEME_DIR.'/'.$theme.'/index.html')){
                return 'index.html';
            }else{
                return 'default.html';
            }
        }
        if(file_exists(THEME_DIR.'/'.$theme.'/'.$ctl.'-'.$act.'.html')){
            return $ctl.'-'.$act.'.html';
        }elseif(file_exists(THEME_DIR.'/'.$theme.'/'.$ctl.'.html')){
            return $ctl.'.html';
        }else{
            return 'default.html';
        }
    }

    function _fix_url(){
    }

    function is_login(){
        return ($this->member['member_id']>0);
    }

    function setError($errorno=0,$jumpto='back',$msg='',$links=array(),$time=3,$js=null){
        $this->system->ErrorSet = array('errorno'=>$errorno,'message'=>$msg,'jumpto'=>$jumpto,'links'=>$links,'time'=>$time,'js'=>$js);
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
        $this->splash($result?'success':'failed',$url,$result?$message:__('Fail'),$showNotice?$this->_err:null);
    }

    function end_only(){
        if(!$this->transaction_start) trigger_error('The transaction has not started yet',E_USER_ERROR);
        $this->transaction_start = false;
        restore_error_handler();
    }

    function _errorHandler($errno, $errstr, $errfile, $errline){
        $errorlevels = array(
            2048 => 'Warning',
            2048 => 'Notice',
            1024 => 'Warning',
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
/*            case $errno & ( E_NOTICE | E_USER_NOTICE | E_WARNING):
break;*/

            case $errno & ( $this->_shutHandle ):
                restore_error_handler();
                $this->splash('failed',$this->_action_url,$errstr,$this->_err);

            /*default:
                restore_error_handler();
            $this->splash('failed',$this->_action_url,$errstr,$this->_err);*/
            }
        }
        return true;
    }

}
?>
