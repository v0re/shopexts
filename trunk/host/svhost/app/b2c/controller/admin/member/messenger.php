<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
define('MANUAL_SEND','MANUAL_SEND');
class b2c_ctl_admin_member_messenger extends desktop_controller {

    var $workground = 'b2c_ctl_admin_member';

    function index(){
        $this->path[] = array('text'=>__('邮件短信配置'));
        $messenger = &$this->app->model('member_messenger');
        $action = $messenger->actions();
        foreach($action as $act=>$info){
            $list = $messenger->getSenders($act);
            foreach($list as $msg){
                $this->pagedata['call'][$act][$msg] = true;
            }
        }

        $this->pagedata['actions'] = $action;
        $this->_show('admin/messenger/index.html');
    }

    function edtmpl($action,$msg){
        $messenger = &$this->app->model('member_messenger');
        $info = $messenger->getParams($msg);
        if($this->pagedata['hasTitle'] = $info['hasTitle']){
            $this->pagedata['title'] = $messenger->loadTitle($action,$msg);
        }

        $this->pagedata['body'] = $messenger->loadTmpl($action,$msg);
        $this->pagedata['type'] = $info['isHtml']?'html':'textarea';
        $this->pagedata['messenger'] = $msg;
        $this->pagedata['action'] = $action;

        $actions = $messenger->actions();
        $this->pagedata['varmap'] = $actions[$action]['varmap'];
        $this->pagedata['action_desc'] = $actions[$action]['label'];
        $this->pagedata['msg_desc'] = $info['name'];
        $this->page('admin/messenger/edtmpl.html');
    }
    
    function saveTmpl(){
        $messenger = &$this->app->model('member_messenger');
        $ret = $messenger->saveContent($_POST['actdo'],$_POST['messenger'],array(
            'content'=>htmlspecialchars_decode($_POST['content']),
            'title'=>$_POST['title']
        ));
       
        if($ret){
            $this->splash('success','index.php?app=b2c&ctl=admin_member_messenger&act=index');
        }else{
            $this->splash('failed','index.php?app=b2c&ctl=admin_member_messenger&act=index');
        }
    }

    function save(){
        $messenger = &$this->app->model('member_messenger');
        if ($messenger->saveActions($_POST['actdo'])) {
            $this->splash('success', 'index.php?app=b2c&ctl=admin_member_messenger&act=index');
        }else{
            $this->splash('failed','index.php?app=b2c&ctl=admin_member_messenger&act=index');
        }
    }

    function outbox($sender){
        $this->path[] = array('text'=>__('发件箱'));
        $messenger = &$this->app->model('member_messenger');
        $this->pagedata['oubox'] = $messenger->outbox($sender);
        $this->pagedata['sender']=$sender;
        $this->_show('messenger/outbox.html');
    }

    function _show($tmpl){
        $messenger = &$this->app->model('member_messenger');
        $this->pagedata['messenger'] = $messenger->getList();
        $this->pagedata['__show_page__'] = $tmpl;
        $this->page('admin/messenger/page.html');
    }

    

   
    
}
?>
