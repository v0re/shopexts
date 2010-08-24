<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class desktop_ctl_dashboard extends desktop_controller{

    var $workground = 'desktop_ctl_dashboard';

    function index(){    
        
        $this->pagedata['tip'] = base_application_tips::tip(); 
        
        foreach(kernel::servicelist('desktop.widgets') as $obj){ 
            
            
            
            $this->pagedata['widgets'][]= array(
                'title'=>$obj->get_title(),
                'html'=>$obj->get_html(),
                'width'=>$obj->get_width(),
                'className'=>$obj->get_className()
                );  
                
                
        }
        $deploy = kernel::single('base_xml')->xml2array(file_get_contents(ROOT_DIR.'/config/deploy.xml'),'base_deploy');
        $this->pagedata['deploy'] = $deploy;
        $this->page('dashboard.html');
    }
    
    function advertisement(){
        
        
        $this->display('advertisement.html');
    }
    
    function appmgr() {
        $arr = app::get('base')->model('apps')->getList('*', array('status'=>'active'));
        foreach( $arr as $k => $row ) {
            if( $row['remote_ver'] <= $row['local_ver'] ) unset($arr[$k]);
        }
        $this->pagedata['apps'] = $arr;
       
        $this->display('appmgr/default_msg.html');
        
        
    }
    
    
    
    function fetch_tip(){
        echo $this->pagedata['tip'] = base_application_tips::tip();
    }

    function profile(){

        //获取该项记录集合
        $users = $this->app->model('users');
        $roles=$this->app->model('roles');
        $workgroup=$roles->getList('*');
        $sdf_users = $users->dump($this->user->get_id());
        
        if($_POST){
            $this->user->set_conf('desktop_theme',$_POST['theme']);
            $this->user->set_conf('timezone',$_POST['timezone']);
        }

        $themes = array();
        foreach(app::get('base')->model('app_content')
            ->getList('app_id,content_name,content_path'
        ,array('content_type'=>'desktop theme')) as $theme){
            $themes[$theme['app_id'].'/'.$theme['content_name']] = $theme['content_name'];
        }

        //返回无内容信息
        $this->pagedata['themes'] = $themes;
        
        $this->pagedata['current_theme'] = $this->user->get_theme();
        $this->pagedata['name'] = $sdf_users['name'];
        $this->pagedata['super'] = $sdf_users['super'];
        $this->display('users/profile.html');
    }

    ##非超级管理员修改密码
    function chkpassword(){

        $account_id = $this->user->get_id();
        
        $users = $this->app->model('users');
        $sdf = $users->dump($account_id,'*',array( ':account@pam'=>array('*'),'roles'=>array('*') ));
        $old_password = $sdf['account']['login_password'];

        if($_POST){
            $this->begin();
            if(md5(trim($_POST['old_login_password']))!= $old_password){
                //echo "旧密码不正确";
                $this->end(false, '旧密码不正确');
            }
            elseif($_POST['new_login_password']!=$_POST[':account@pam']['login_password']){
                //echo "两次密码不一致";
                $this->end(false, '两次密码不一致');
            }
            else{
                $_POST['pam_account']['account_id'] = $account_id;
                $_POST['pam_account']['login_password'] = md5(trim($_POST['new_login_password']));
                $users->save($_POST);
                //echo "密码修改成功";
                $this->end(true, '密码修改成功');
            }
        }
        $ui= new base_component_ui($this);                

        $arrGroup=array(                                        
            array( 'title'=>'旧密码','type'=>'password', 'name'=>'old_login_password', 'required'=>true,),
            array( 'title'=>'新密码','type'=>'password', 'name'=>'new_login_password', 'required'=>true,),
            array( 'title'=>'再次输入新密码','type'=>'password', 'name'=>':account@pam[login_password]', 'required'=>true,),
            ); 
        $html .= $ui->form_start();
        foreach($arrGroup as  $arrVal){  $html .= $ui->form_input($arrVal); }
        $html .= $ui->form_end();
        echo $html;
        //return $html;
    }
    
     function redit(){
        $desktop_user = kernel::single('desktop_user');
        if($desktop_user->is_super()){
            $this->redirect('index.php?ctl=adminpanel');
        }
        else{
            $aData = $desktop_user->get_work_menu();
            $aMenu = $aData['menu'];
            foreach($aMenu as $val){
                foreach($val as $value){
                    foreach($value as $v){
                        if($v['display']==='true'){
                            $url = $v['menu_path'];break;
                        }  
                    }
                    break;
                }
                break;
            }
            if(!$url) $url = "ctl=adminpanel";
            $this->redirect('index.php?'.$url);
        }
    }

}
