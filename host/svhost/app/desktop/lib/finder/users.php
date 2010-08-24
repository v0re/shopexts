<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

/* TODO: Add code here */
class desktop_finder_users{
    function __construct($app){
        $this->app=$app;
    }
    function detail_info($param_id){
    
        //获取该项记录集合
        $users = $this->app->model('users');
        $roles=$this->app->model('roles');
        $workgroup=$roles->getList('*');
        $sdf_users = $users->dump($param_id); 
        if($_POST){
            $_POST['pam_account']['account_id'] = $param_id;
            if($sdf_users['super']==1){
            $users->editUser($_POST);
            //echo "修改成功";
            }
            elseif($_POST['super'] == 0 && $_POST['role']){
            foreach($_POST['role'] as $roles){
            $_POST['roles'][]=array('role_id'=>$roles);
                }
            $users->editUser($_POST);
            $users->save_per($_POST);
                }
            else{
            echo "<script>alert('请至少选择一个工作组')</script>";
            }
                }
            //返回无内容信息
            if(empty($sdf_users)) return '无内容';   
            $hasrole=$this->app->model('hasrole');
            foreach($workgroup as $key=>$group){
            $rolesData=$hasrole->getList('*',array('user_id'=>$param_id,'role_id'=>$group['role_id']));
            if($rolesData){
            $check_id[] = $group['role_id'];
            $workgroup[$key]['checked']="true";
            }
            else{
            $workgroup[$key]['checked']="false";
            }
            
            }
            $ui= new base_component_ui($this);
            $html .= $ui->form_start();
            //foreach($arrGroup as  $arrVal){  $html .= $ui->form_input($arrVal); }
            $render = $this->app->render();
            $render->pagedata['workgroup'] = $workgroup; 
            $render->pagedata['account_id'] = $param_id;
            $render->pagedata['name'] = $sdf_users['name'];
            $render->pagedata['super'] = $sdf_users['super'];
            $render->pagedata['status'] = $sdf_users['status'];
            if(!$sdf_users['super']){
            $render->pagedata['per'] = $users->detail_per($check_id,$param_id);
            
           }
            $html.= $render->fetch('users/users_detail.html');
            $html .= $ui->form_end();
            return $html;
   }
      
}

?>
