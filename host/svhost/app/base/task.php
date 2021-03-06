<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class base_task{
    
    function install_options(){
        return array(
                'db_host'=>array('type'=>'text','vtype'=>'required','required'=>true,'title'=>'数据库主机','default'=>'localhost'),
                'db_user'=>array('type'=>'text','vtype'=>'required','required'=>true,'title'=>'数据库用户名','default'=>'root'),
                'db_password'=>array('type'=>'password','title'=>'数据库密码','default'=>''),
                'db_name'=>array('type'=>'text','vtype'=>'required','required'=>true,'title'=>'数据库名'),
                'db_prefix'=>array('type'=>'text','title'=>'数据库表前缀','default'=>'sdb_'),
                'default_timezone'=>array('type'=>'select','options'=>base_location::timezone_list()
                    ,'title'=>'默认时区','default'=>'8','vtype'=>'required','required'=>true),
            );
    }
    
    function checkenv($options){
        
        if(!$options['db_host']){
            echo "Error: 需要填写数据库主机\n";
            return false;
        }
        if(!$options['db_user']){
            echo "Error: 需要填写数据库用户名\n";
            return false;
        }
        if(!$options['db_name']){
            echo "Error: 数据库名\n";
            return false;
        }

        $link = @mysql_connect($options['db_host'],$options['db_user'],$options['db_password']);
        if(!$link){
            echo "Error: 数据库连接错误\n";
            return false;
        }

        if(!mysql_select_db($options['db_name'], $link)){
            echo "Error: 数据库\"" . $options['db_name'] . "\"不存在\n";
            return false;
        }
        
        if(!kernel::single('base_setup_config')->write($options)){
            echo "Error: Config文件写入错误\n";
            return false;
        }
        
        if(file_exists(ROOT_DIR.'/config/config.php')){
            require(ROOT_DIR.'/config/config.php');
        }

        date_default_timezone_set(
            defined('DEFAULT_TIMEZONE') ? ('Etc/GMT'.(DEFAULT_TIMEZONE>=0?(DEFAULT_TIMEZONE*-1):'+'.(DEFAULT_TIMEZONE*-1))):'UTC'
        );
        
        return true;
    }

    function pre_install(){
        kernel::set_online(false);
        base_certificate::active();
    }

    function post_install(){
        kernel::single('base_application_manage')->sync();
        kernel::set_online(true);
        $rpc_server = array(
                'node_id'=>'1',
                'node_url'=>MATRIX_URL, //todo 测试
                'node_name'=>'Global Matrix',
                'node_api'=>'',
                'link_status'=>'active',
            );
        app::get('base')->model('network')->replace($rpc_server,array('node_id'=>1));
    }

}
