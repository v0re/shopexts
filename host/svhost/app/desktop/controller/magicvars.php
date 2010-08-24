<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class desktop_ctl_magicvars extends desktop_controller{
    
    function index(){
        $this->finder('desktop_mdl_magicvars',array(
            'title'=>'全局变量',
            'actions'=>array(
                            array('label'=>'添加','icon'=>'add.gif','href'=>'index.php?app=desktop&ctl=magicvars&act=add','target'=>'_blank'),
                        ),'use_buildin_recycle'=>true,
            ));
    }
    function add(){
        $this->singlepage('system/var_item.html');
    }
 
    function edit($var_id){

        if($var_id){
            $mdl = $this->app->model('magicvars');
            $this->pagedata['var'] = $mdl->dump($var_id);
            $this->pagedata['readOnly'] = 'readOnly';
        }

        $this->singlepage('system/var_item.html');
    }
 
    function magicvar_save(){
        $this->begin("index.php?app=desktop&ctl=magicvars&act=index");
        $magicvars = &$this->app->model('magicvars');
        $var_name = $_POST['var_name'];
        if($var_name1 = substr(substr($var_name,1),0,-1)){
           $message = str_replace('_','',$var_name1);
             if(strlen($message)<2){
               trigger_error('变量名必须为2个字母以上',E_USER_ERROR);
               return false;
             }
        }
        if(!preg_match('/^\w+$/', substr(substr($_POST['var_name'],1),0,-1))){
            trigger_error('变量名非法',E_USER_ERROR);
            return false;
        }
        $message = '{'.$message.'}';
        $_POST['var_type'] = 'custom';
        if(isset($_POST['is_editing'])){
            $this->end($magicvars->save($_POST,$message),$message?$message:__('修改成功'),'index.php?app=desktop&ctl=magicvars&act=index');
        }else{
            $this->end($magicvars->save($_POST,$message),$message?$message:__('保存成功'),'index.php?app=desktop&ctl=magicvars&act=index');
        }
    }
       
}
