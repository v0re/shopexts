<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class desktop_ctl_roles extends desktop_controller{
    
    var $workground = 'desktop_ctl_system';

    function index(){
        $this->finder('desktop_mdl_roles',array(
            'title'=>'角色',
            'actions'=>array(
                            array('label'=>'新建角色','href'=>'index.php?ctl=roles&act=addnew','target'=>'dialog::{title:\'新建角色\'}'),
                        )
            ));
    }

    function addnew(){
        $workgrounds = app::get('desktop')->model('menus')->getList('*',array('menu_type'=>'workground','disabled'=>'false','display'=>'true'));
        $this->pagedata['workgrounds'] = $workgrounds;
        foreach($workgrounds as $k => $v){
                $workgrounds[$k]['permissions'] = $this->get_permission_per($v['menu_id'],array());
                //if(in_array($v['workground'],(array)$menu_workground)){
                    //$workgrounds[$k]['checked'] = 1;
                    
                //}
            }
            $this->pagedata['workgrounds'] = $workgrounds;
            $this->pagedata['adminpanels'] = $this->get_adminpanel(null,array());#print_r($workgrounds);exit;
            $this->page('users/add_roles.html');
    }
    
    function save(){
        $this->begin('index.php?app=desktop&ctl=roles&act=index');
        #$this->begin();
        $roles = $this->app->model('roles');
        //if($_POST){
        if($roles->validate($_POST,$msg)){
        if($roles->save($_POST)){
        $this->end(true,__('保存成功'));
        }else{
        $this->end(false,__('保存失败')); 
        }
        }
        else{
        $this->end(false,$msg);
        }
    }
    
    
    function edit($roles_id){
        $param_id = $roles_id;
        $this->begin('index.php?app=desktop&ctl=roles&act=index');
        if($_POST){
            if($_POST['role_name']==''){
                 $this->end(false,__('工作组名称不能为空'));
            }
            if(!$_POST['workground']){
                //$_POST['workground'] = '';
                $this->end(false,__('请至少选择一个权限'));
            }
            $opctl = &$this->app->model('roles');
            $result = $opctl->check_gname($_POST['role_name']);
            if($result && ($result!=$_POST['role_id'])) {$this->end(false,__('该工作组名称已存在'));}
            if($opctl->save($_POST)){
                 $this->end(true,__('保存成功'));
            }else{
               $this->end(false,__('保存失败'));
            }
        
            }
        else{
            $opctl = &$this->app->model('roles');
            $menus = $this->app->model('menus');
            $sdf_roles = $opctl->dump($param_id);
            $this->pagedata['roles'] = $sdf_roles;
            $workground = unserialize($sdf_roles['workground']);
            foreach((array)$workground as $v){
                #$sdf = $menus->dump($v);
                $menuname = $menus->getList('*',array('menu_type' =>'menu','permission' => $v));
                foreach($menuname as $val){
                    $menu_workground[] = $val['workground'];
                }
            }
            $menu_workground = array_unique((array)$menu_workground);
            #print_r($menu_workground);exit;
            $workgrounds = app::get('desktop')->model('menus')->getList('*',array('menu_type'=>'workground','disabled'=>'false','display'=>'true'));
            foreach($workgrounds as $k => $v){
                $workgrounds[$k]['permissions'] = $this->get_permission_per($v['menu_id'],$workground);
                if(in_array($v['workground'],(array)$menu_workground)){
                    $workgrounds[$k]['checked'] = 1;
                    
                }
            }
            $this->pagedata['workgrounds'] = $workgrounds;
            $this->pagedata['adminpanels'] = $this->get_adminpanel($param_id,$workground);#print_r($workgrounds);exit;
            $this->page('users/edit_roles.html');
            }
    }
    
    //根据工作组获得所有下面的permission
    function get_permission(){
        $menu_id = $_POST['name'];
        $role_id = $_POST['role_id'];
        $opctl = &$this->app->model('roles');
        $sdf_roles = $opctl->dump($role_id);
        $work = unserialize($sdf_roles['workground']);
        $menus = $this->app->model('menus');
        $sdf = $menus->dump($menu_id);
        $workground = $sdf['workground'];
        $aMenus = $menus->getList('*',array('menu_type' => 'menu','workground' => $workground));
        $aTmp = array();
        $menu_group = array();
        foreach($aMenus as $val ){
            $aTmp['menu_group'][] = $val['menu_group'];
            $aTmp['permission'][] = $val['permission'];
        }
        $aMenus = array_unique($aTmp['permission']); //所有的permissions
        $permissions = array();
        foreach($aMenus as $val){
        $sdf = $menus->dump(array('menu_type' => 'permission','permission' => $val));
        #print_r($sdf);exit;
        if(in_array($sdf['menu_id'],(array)$work)){
        echo "<input style='margin-left:50px;'type=checkbox  checked=checked name=workground[]"." value=".$sdf['menu_id'].">";
        }
        else{
         echo "<input style='margin-left:50px;'type=checkbox  name=workground[]"." value=".$sdf['menu_id'].">";
        }
        echo "<label>".$sdf['menu_title']."</label>";
        echo "<br>";
        }
       // print_r($permissions);
        
    }
    
    //获取控制面板的permissions
    function get_adminpanel_per(){
        $menus = $this->app->model('menus');
        $aPer = $menus->getList('*',array('menu_type' => 'permission'));
        #print_r($aPer);exit;
        foreach($aPer as $val){
        $aData = $menus->dump(array('menu_type' => 'menu','permission' => $val['permission'])); 
            if(!$aData){
                echo "<input style='margin-left:50px;'type=checkbox  name=workground[]"." value=".$val['menu_id'].">";
                echo "<label>".$val['menu_title']."</label>";
                echo "<br>";
    }
        }
    }
    
     //根据工作组获得所有下面的permission
    function get_permission_per($menu_id,$wg){
        
        $menus = $this->app->model('menus');
        $sdf = $menus->dump($menu_id);
        $workground = $sdf['workground'];
        $aMenus = $menus->getList('*',array('menu_type' => 'menu','workground' => $workground));
        $aTmp = array();
        $menu_group = array();
        foreach($aMenus as $val ){
            $aTmp['menu_group'][] = $val['menu_group'];
            $aTmp['permission'][] = $val['permission'];
        }
        $aMenus = array_unique($aTmp['permission']); //所有的permissions
        $permissions = array();
        foreach($aMenus as $val){
        $sdf = $menus->dump(array('menu_type' => 'permission','permission' => $val));
        if(in_array($sdf['permission'],$wg)){
            $sdf['checked'] = 1;
        }
        else{
            $sdf['checked'] = 0;
        }
        $permissions[] = $sdf;
        }
        return $permissions;
        
    }
    //获取控制面板的permissions
    function get_adminpanel($role_id,$wg){
        $menus = $this->app->model('menus');
        $aPer = $menus->getList('*',array('menu_type' => 'permission','disabled' => 'false'));
        $adminpanel_per = array();
        foreach((array)$aPer as $val){
        $aData = $menus->dump(array('menu_type' => 'menu','permission' => $val['permission'])); 
            if(!$aData){
                if(in_array($val['permission'],(array)$wg)){
                     $val['checked'] = 1;
                 }
                else{
                    $val['checked'] = 0;
                }
               $adminpanel_per[] = $val;
    }
        }
    return $adminpanel_per;
    }
    
}
