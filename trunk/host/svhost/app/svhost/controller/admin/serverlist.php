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