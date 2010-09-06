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
            array('label'=>'删除虚拟空间','icon'=>'add.gif','submit'=>'index.php?app=svhost&ctl=admin_vhostlist&act=delete_page','target'=>'_blank'),
            ),'use_buildin_set_tag'=>true,'use_buildin_recycle'=>true,'use_buildin_filter'=>true,'use_view_tab'=>true,
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
    
    public function delete_page(){
        if(is_array($_POST['vhost_id'])){
            $obj_vhostlist = $this->app->model('vhostlist');
            $server = kernel::service('svhost_server', array('content_path'=>'svhost_server'));
            foreach($_POST['vhost_id'] as $vhost_id){
                $sdf = $obj_vhostlist->dump($vhost_id);
                $bash = $server->get_delete_bash($sdf);
                echo "<pre>$bash</pre>";
            }
        }
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
            $this->insert_queue($sdf['vhost_id']);
            $this->end(true, __('添加成功！'));
        }else{
            $this->end(false, __('添加失败！'));
        }
    }   
    
    function insert_queue($vhost_id){
        $sdf = $this->app->model('vhostlist')->dump($vhost_id);
        $server = kernel::service('svhost_server', array('content_path'=>'svhost_server'));
        $bash = $server->get_bash($sdf);
        $httpsqs = kernel::single('svhost_httpsqs'); 
        $queue_name = $sdf['ip'];
        $message = $bash;
        $result = $httpsqs->put("127.0.0.1", 1218, "utf-8", $queue_name, $message);
        return $result;
    }

}