<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class desktop_ctl_users extends desktop_controller{
    
    var $workground = 'desktop_ctl_system';

    function index(){
        
        $this->finder('desktop_mdl_users',array(
            'title'=>'用户',
            'actions'=>array(
                array('label'=>'新建用户','href'=>'index.php?ctl=users&act=addnew','target'=>'dialog::{title:\'添加管理员\'}'),
            ),'use_buildin_export'=>false
            ));
    }
    
    function addnew(){
        $roles = $this->app->model('roles');
        $users = $this->app->model('users');
        if($_POST){
        $this->begin('index.php?app=desktop&ctl=users&act=index');
        if($users->validate($_POST,$msg)){
        if($_POST['super']==0 && (!$_POST['role'])){
            $this->end(false,__('请至少选择一个工作组'));
            /*
        foreach($workgroup as $roles)
        $_POST['roles'][]=array('role_id'=>$roles['role_id']);
        }
        elseif($_POST['role']){
        foreach($_POST['role'] as $roles)
        $_POST['roles'][]=array('role_id'=>$roles);
        
        }*/
        //else{
           
        }
        elseif($_POST['super'] == 0 && ($_POST['role'])){
            foreach($_POST['role'] as $roles)
            $_POST['roles'][]=array('role_id'=>$roles);
        }
        $_POST['pam_account']['login_password'] = md5($_POST['pam_account']['login_password']);
        $_POST['pam_account']['account_type'] = pam_account::get_account_type($this->app->app_id);
        if($users->save($_POST)){
        if($_POST['super'] == 0){   //是超管就不保存
        $this->save_ground($_POST);
            }
        $this->end(true,__('保存成功'));
        }else{
        $this->end(false,__('保存失败'));
        }
        
        }
        else{
        $this->end(false,__($msg));
        }   
        }   
        else{
        $workgroup=$roles->getList('*');
        $this->pagedata['workgroup']=$workgroup; 
        $this->display('users/users_add.html');
        }     
    }

      
    ####修改密码
    function chkpassword(){
        $this->begin('index.php?app=desktop&ctl=users&act=index');
        $users = $this->app->model('users');
        if($_POST){
            $sdf = $users->dump($_POST['user_id'],'*',array( ':account@pam'=>array('*'),'roles'=>array('*') ));
            $old_password = $sdf['account']['login_password'];
            if(md5(trim($_POST['old_login_password']))!= $old_password){
                $this->end(false,__('旧密码不正确'));         
            }
            elseif($_POST['new_login_password']!=$_POST['pam_account']['login_password']){
                $this->end(false,__('两次密码不一致')); 
            }
            else{
                $_POST['pam_account']['account_id'] = $_POST['user_id'];
                $_POST['pam_account']['login_password'] = md5(trim($_POST['new_login_password']));
                $users->save($_POST);
                $this->end(true,__('密码修改成功'));
            }
        }
        $this->pagedata['user_id'] = $_GET['id'];
        $this->page('users/chkpass.html');         

    }
        /**
         * This is method edit
         * 添加编辑
         * @return mixed This is the return value description
         *
         */
    function edit($account_id){
            //$account_id=$_GET['account_id'];
            $users = &$this->app->model('users');
            $this->begin();
            $_POST['pam_account']['account_id'] = $_POST['user_id'];
            $result = $users->save($_POST,$account_id);
            $this->end($result);
            
    }
        
    //获取工作组细分
    function detail_ground(){
        $role_id = $_POST['name'];
        $roles = $this->app->model('roles');
        $menus =$this->app->model('menus');
        $check_id = json_decode($_POST['checkedName']);
        $aPermission =array();
        #print_r($check_id);
        if(!$check_id) {
            echo '';exit;
        }
        foreach($check_id as $val){
            $result = $roles->dump($val);
            $data = unserialize($result['workground']);
            foreach((array)$data as $row){
                $aPermission[] = $row;
            } 
        }
        $aPermission = array_unique($aPermission);
        if(!$aPermission){
            echo '';exit;
        } 
        foreach((array)$aPermission as $val){
        $sdf = $menus->dump(array('menu_type' => 'permission','permission' => $val));
        $addon = unserialize($sdf['addon']);
        if($addon['show']&&$addon['save']){  //如果存在控制  
        if($addon['show']!=$addonmethod){
        $access = explode(':',$addon['show']);
        $classname = $access[0];
        $method = $access[1];
        $obj = kernel::single($classname);
        $html.=$obj->$method();
        }
        $addonmethod = $addon['show']; 
        }  
        else{
            echo '';
        } 
        }
        echo $html;
    }
   
    //保存工作组细分
    function save_ground($aData){
        $workgrounds = $aData['role'];
        $menus = $this->app->model('menus');
        $roles =  $this->app->model('roles');
        foreach($workgrounds as $val){
            $result = $roles->dump($val);
            $data = unserialize($result['workground']);
            foreach((array)$data as $row){
                $aPermission[] = $row;
            } 
        }
        $aPermission = array_unique($aPermission);
        if($aPermission){
        $addonmethod = null;
        foreach((array)$aPermission as $key=>$val){
        $sdf = $menus->dump(array('menu_type' => 'permission','permission' => $val));
        $addon = unserialize($sdf['addon']);
        if($addon['show']&&$addon['save']){  //如果存在控制 
        if($addon['save']!=$addonmethod){
        $access = explode(':',$addon['save']);
        $classname = $access[0];
        $method = $access[1];
        $obj = kernel::single($classname);
        $obj->$method($aData['user_id'],$aData);
        }  
        $addonmethod = $addon['save'];
        }   
        }
    }
     
        }
        
        
    
      

}
