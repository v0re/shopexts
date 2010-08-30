<?php
class svhost_ctl_admin_vhostlist extends desktop_controller{
      /**
     * 构造方法
     * @params object app object
     * @return null
     */
    public function __construct($app)  {
        parent::__construct($app);

    }
    

    public function index(){
        $this->finder('svhost_mdl_vhostlist',array(
            'title'=>'虚拟空间列表',
            'allow_detail_popup'=>false,
            'actions'=>array(                           array('label'=>'生成虚拟空间','icon'=>'add.gif','href'=>'index.php?app=svhost&ctl=admin_vhostlist&act=add_page','target'=>"dialog::{title:'生成虚拟空间',width:560,height:200}"),
            ),'use_buildin_set_tag'=>true,'use_buildin_recycle'=>true,'use_buildin_filter'=>false,'use_view_tab'=>false,
            ));
    }
    
    public function add_page(){
        $this->pagedata['finder_id'] = $_GET['finder_id'];
        $model_serverlist = $this->app->model('serverlist');
        foreach((array)$model_serverlist->getList('server_id,name') as $server){
            $options[$server['server_id']] = $server['name'];
        }
        $this->pagedata['server']['name'] = 'server';
        $this->pagedata['server']['options'] = $options;
        $this->display('admin/vhostlist/new.html');
    }
    
    public function add(){
        $this->begin();
        $domain = $_POST['domain'];
        $server_id = $_POST['server'];
        $server_setting = $this->app->model('serverlist')->dump(  
            $server_id,
            '*',
            array(      
                'database'=>'*',
            )
        );        
        
        $domain_strip_dot = str_replace('.','',$domain);
        $sdf = array(
            'domain'=>$domain,
            'server_id'=>$server_id,
            'ip'=>$server_setting['server']['ip'],
            'db'=>array(
                'host'=>$server_setting['database']['host'],
                'port'=>$server_setting['database']['port'],
                'name'=>$domain_strip_dot,
                'user'=>$domain_strip_dot,
                'password'=>svhost_utils::gen_radom_string(8),
            ),
            'ftp'=>array(
                'user'=>$domain_strip_dot,
                'password'=>svhost_utils::gen_radom_string(8),
            ),
        );
        $model_vhostlist = $this->app->model('vhostlist');
        if( $model_vhostlist->save($sdf)){
            $this->end(true, __('添加成功！'));
        }else{
            $this->end(false, __('添加失败！'));
        }
    }   

}