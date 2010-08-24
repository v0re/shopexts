<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class pam_passport_uc implements pam_interface_passport{

    function get_name(){
        return 'Discuz Ucenter';
    }

    function get_login_form($auth, $appid, $view, $ext_pagedata=array()){
        $render = app::get('pam')->render();
        $render->pagedata['callback'] = $auth->get_callback_url(__CLASS__);
        return $render->fetch('basic-login.html');
    }

    function login($auth,&$usrdata){
        $usrdata['log_data'] = __('用户').$_POST['uname'].__('登录成功！');
        return 1;
    }

    function get_data(){
    }

    function get_id(){
    }

    function get_expired(){
    }
    
    function get_config(){
        if($ret = app::get('pam')->getConf('passport.'.__CLASS__)){
            return $ret;
        }else{
            $ret = $this->get_setting();
            $ret['passport_id']['value'] = __CLASS__;
            $ret['passport_name']['value'] = $this->get_name();
            $ret['passport_status']['value'] = 'false';
            $ret['passport_version']['value'] = '1.5';
            app::get('pam')->setConf('passport.'.__CLASS__,$ret);
            return $ret;        
        }
    }
    
    function set_config(&$config){
        $save = app::get('pam')->getConf('passport.'.__CLASS__);
        if(count($config))
            foreach($config as $key=>$value){
                if(!in_array($key,array_keys($save))) continue;
                $save[$key]['value'] = $value;
            }
        return app::get('pam')->setConf('passport.'.__CLASS__,$save);
    }

    function get_setting(){
        return array(
            'passport_id'=>array('label'=>'通行证id','type'=>'text','editable'=>false),
            'passport_name'=>array('label'=>'通行证','type'=>'text','editable'=>false),
            'passport_status'=>array('label'=>'开启','type'=>'bool',),
            'passport_version'=>array('label'=>'版本','type'=>'text','editable'=>false),
            'uc_url'=>array('label'=>'UCenter URL','type'=>'text',  ),   
            'uc_saltl'=>array('label'=>'UCenter 通信密钥',  'type'=>'text',  ),
            'uc_app_id'=>array('label'=>'UCenter 应用ID', 'type'=>'text', ),   
            'uc_db_host'=>array('label'=>'UCenter 数据库服务器(不带http://前缀)', 'type'=>'text', ),
            'uc_db_userl'=>array('label'=>'UCenter 数据库用户名', 'type'=>'text',    ),
            'uc_db_passwd'=>array('label'=>'UCenter 数据库密码',  'type'=>'text',  ), 
            'uc_db_dbname'=>array('label'=>'UCenter 数据库名', 'type'=>'text',    ),
            'uc_db_prefix'=>array('label'=>'UCenter 表名前缀',  'type'=>'text',  ), 
            'uc_charset'=>array('label'=>'UCenter系统编码','type'=>'select','options'=>array('utf8'=>'国际化编码(utf-8)','gbk'=>'简体中文','bgi5'=>'繁体中文','en'=>'英文')),
            'uc_db_charset'=>array( 'label'=>'UCenter数据库编码',  'type'=>'select','options'=>array('utf8'=>'UTF8','gbk'=>'GBK'), ),
        );
    }

}
