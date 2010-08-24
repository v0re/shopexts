<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

/* TODO: Add code here */
class desktop_finder_roles{
    var $column_control = '角色操作';
    function __construct($app){
        $this->app= $app;
    }
        
    function column_control($row){
        $render = $this->app->render();
        $render->pagedata['role_id'] = $row['role_id'];
        return $render->fetch('users/href.html');
      }
    
    function detail_indo($param_id){
        $render = $this->app->render();
        $opctl = &$this->app->model('roles');
        $menus = $this->app->model('menus');
        $sdf_roles = $opctl->dump($param_id);
        $render->pagedata['roles'] = $sdf_roles;
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
            $workgrounds[$k]['permissions'] = $this->get_permission($v['menu_id'],$workground);
            if(in_array($v['workground'],(array)$menu_workground)){
                $workgrounds[$k]['checked'] = 1;
                
            }
        }
        $render->pagedata['workgrounds'] = $workgrounds;
        $render->pagedata['adminpanels'] = $this->get_adminpanel($param_id,$workground,$flg);
        $render->pagedata['flg'] = $flg;
        echo $render->fetch('users/users_roles.html');
    }
    
     //根据工作组获得所有下面的permission
    function get_permission($menu_id,$wg){
        
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
    function get_adminpanel($role_id,$wg,&$flg){
        $menus = $this->app->model('menus');
        $aPer = $menus->getList('*',array('menu_type' => 'permission'));
        $adminpanel_per = array();
        foreach((array)$aPer as $val){
        $aData = $menus->dump(array('menu_type' => 'menu','permission' => $val['permission'])); 
            if(!$aData){
                if(in_array($val['permission'],(array)$wg)){
                     $val['checked'] = 1;
                     $flg = 1;
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
?>
