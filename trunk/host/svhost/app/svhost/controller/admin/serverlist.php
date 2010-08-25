<?php
class svhost_ctl_admin_serverlist extends desktop_controller{
      /**
     * 构造方法
     * @params object app object
     * @return null
     */
    public function __construct($app)  {
        parent::__construct($app);

    }
    

    public function index(){
        $this->finder('svhost_mdl_serverlist',array(
            'title'=>'服务器列表',
            'allow_detail_popup'=>true,
            'actions'=>array(                           array('label'=>'添加服务器','icon'=>'add.gif','href'=>'index.php?app=svhost&ctl=admin_serverlist&act=addnew','target'=>'_blank'),
            ),'use_buildin_set_tag'=>true,'use_buildin_recycle'=>true,'use_buildin_filter'=>false,'use_view_tab'=>false,
            ));
    }
    
    public function addnew(){
        $this->pagedata['finder_id'] = $_GET['finder_id'];
        $this->singlepage('admin/serverlist/new.html');
    }
    
}