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
            'actions'=>array(                           array('label'=>'添加服务器','icon'=>'add.gif','href'=>'index.php?app=svhost&ctl=admin_serverlist&act=add_page','target'=>'_blank'),
            array('label'=>'检查服务器','icon'=>'add.gif','submit'=>'index.php?app=svhost&ctl=admin_serverlist&act=check','target'=>"dialog::{title:'检查服务器',width:600,height:400}"),
            ),'use_buildin_set_tag'=>true,'use_buildin_recycle'=>true,'use_buildin_filter'=>false,'use_view_tab'=>false,
            ));
    }
    
    public function add_page(){
        $this->pagedata['finder_id'] = $_GET['finder_id'];
        $serverlist = $this->gen_input_from_schema('serverlist'); 
        $this->pagedata['serverlist'] = $serverlist;
        #
        $http = $this->gen_input_from_schema('http','serverlist'); 
        $this->pagedata['http'] = $http;
        #
        $database = $this->gen_input_from_schema('database','serverlist'); 
        $this->pagedata['database'] = $database;
        #
        $ftp = $this->gen_input_from_schema('ftp','serverlist'); 
        $this->pagedata['ftp'] = $ftp;
        
        $this->singlepage('admin/serverlist/new.html');
    }
    
    public function add_new(){
        $this->begin();
        foreach($_POST as $key=>$value){
            $key = str_replace('/','_',$key);
            $key_array = explode('_',$key);
            $key_string = '$'.$key_array[0];
            for($i=1;$i<count($key_array);$i++){
                $key_string .= "[".$key_array[$i]."]";
            }
            $key_string .= " = '".$value."';";
            eval($key_string);            
        }
        $model_serverlist = $this->app->model('serverlist');
        #$serverlist is same as 'gen_input_from_schema' method's $parent parameter
       if( $model_serverlist->save($serverlist)){
            $this->end(true, __('添加成功！'));
        }else{
            $this->end(false, __('添加失败！'));
        }
    }
    
    public function check(){
        $this->begin();        
        $server_id = intval($_POST['server_id'][0]);
        $server_setting = $this->app->model('serverlist')->dump(  
            $server_id,
            '*',
            array(      
                'database'=>'*',
                'http'=>'*',
                'ftp'=>'*',
            )
        );        
        $htdocs = $server_setting['http']['htdocs'];
        $obj_dir = dir($htdocs);
        $server = kernel::service('svhost_server', array('content_path'=>'svhost_server'));
        $model_vhostlist = $this->app->model('vhostlist');
        while(($domain = $obj_dir->read()) !== false){
            if(substr($domain ,0,1) == '.') continue;
            $site_root = "$htdocs/$domain"; 
            if(is_file($site_root)) continue;
            if(!strstr($domain,'.')) continue;
            if($server->is_exists($domain)) continue;
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
                    'password'=>'',
                ),
                'ftp'=>array(
                    'user'=>$domain_strip_dot,
                    'password'=>'',
                ),
            );
            $model_vhostlist->save($sdf);
        }

        $this->end(true, __('检查完成！'));     
    }
    
    
    private function gen_input_from_schema($model_name,$parent=null){
        
        $i = 0;
        $ret = array();
        foreach( $this->app->model($model_name)->schema['columns']  as $column_name=>$column){
            if( $column['editable'] != 'true'){
                continue;
            }            
            $input['label'] = $column['label'];
            if($column['sdfpath']){
                $sdf = $column['sdfpath'];
            }else{
                $sdf = $column_name;
            }
            if(!$parent){
                $input['name'] = $model_name."_".$sdf; 
            }else{
                $input['name'] = $parent."_".$model_name."_".$sdf; 
            }
            $input['type'] = 'text';
            if(substr($column['type'],0,4) == 'enum'){
                $input['type'] = 'select';
                $input['required'] = 'true';
                $text = str_replace('enum','array',$column['type']);
                eval('$tmp='.$text.';');
                $options = null;
                foreach($tmp as $v){
                    $options[$v] = $v;
                }
                $input['options'] = $options;
            }
            $ret[$i] = $input;
            unset($input);
            $i++;
        }
        return $ret;
    }
}