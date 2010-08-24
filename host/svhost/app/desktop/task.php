<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class desktop_task{
    
    function install_options(){
        return array(
                'admin_uname'=>array('type'=>'text','vtype'=>'required','required'=>true,'title'=>'管理员用户名','default'=>'admin'),
                'admin_password'=>array('type'=>'password','vtype'=>'required','required'=>true,'title'=>'管理员密码'),
                'admin_password_re'=>array('type'=>'password','vtype'=>'required','required'=>true,'title'=>'再输入一次'),  
            );
    }
    
    function checkenv($options){
        if($options['admin_password']!=$options['admin_password_re']){
            echo "Error: 两次密码不一致\n";
            return false;    
        }
        if(empty($options['admin_password'])){
            echo "Error: 密码不能为空\n";
            return false;    
        }
        return true;
    }

    function post_install($options)
    {
        kernel::log('Create admin account');
        //设置用户体系，前后台互不相干
        pam_account::register_account_type('desktop','shopadmin','后台管理系统');
        
        
        //todo: 封装成更简单的函数
        $account = array(
            'pam_account'=>array(
                'login_name'=>$options['admin_uname'],
                'login_password'=>md5($options['admin_password']),
                'account_type'=>'shopadmin',
                ),
            'name'=>$options['admin_uname'],
            'super'=>1,
            'status'=>1
            );

        app::get('desktop')->model('users')->save($account);
    }

    function post_uninstall(){
        pam_account::unregister_account_type('desktop');
    }
}
