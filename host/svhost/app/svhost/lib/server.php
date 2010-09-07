<?php

class svhost_server {
    
    function __construct(){
        $this->server_id = 1;    
        $this->vhost_id = 1;
    }
    
    function load_server_setting(){
        return app::get('svhost')->model('serverlist')->dump(  
            $this->server_id,
            '*',
            array(
                'http'=>'*',             
                'ftp'=>'*',              
                'database'=>'*',
            )
        );         
    }
    
    function load_vhost_setting(){
        return app::get('svhost')->model('vhostlist')->dump(  $this->vhost_id );        
    }
    
     function run_queue(&$cursor_id,$params){
        $this->server_id= $params['server_id'];
        $this->vhost_id= $params['vhost_id'];
        $this->create($params['domain']);
     }
     
    function get_bash($params){
        $this->server_id= $params['server_id'];
        $this->vhost_id= $params['vhost_id'];
        return $this->bash_create($params['domain']);
     }
     
     function get_delete_bash($params){
        $this->server_id= $params['server_id'];
        $this->vhost_id= $params['vhost_id'];
        return $this->bash_delete($params['domain']);
     }
     
     function get_update_bash($params){
        $this->server_id= $params['server_id'];
        $this->vhost_id= $params['vhost_id'];
        return $this->bash_update($params['domain']);
     }
     

    function create($domain){
        $server_setting = $this->load_server_setting();        
        $vhost_setting = $this->load_vhost_setting();
        #生成web空间
        $htdocs = $server_setting['http']['htdocs'].'/'.$vhost_setting['domain'];
        $server_setting['http']['htdocs'] = $htdocs;
        $htdocs_owner = $server_setting['ftp']['user'];
        mkdir($htdocs);
        chown($htdocs,$htdocs_owner);    
        #生成http服务配置
        if($server_setting['http']['name'] == 'nginx'){
            $nginx = new svhost_server_nginx($server_setting['http']);
            $server_ip = $server_setting['server']['ip'];
            $nginx->create($domain,$server_ip);
        }
        #生成ftp帐号
        if($server_setting['ftp']['name'] == 'proftpd'){
            $proftpd = new svhost_server_proftpd($server_setting['ftp']);
            $ftp['user'] = $vhost_setting['ftp']['user'];
            $ftp['password'] = $vhost_setting['ftp']['password'];
            $ftp['home'] = $htdocs;
            $proftpd->create($ftp);
        }
        #生成mysql帐号
        if($server_setting['database']['name'] == 'mysql'){
            $database = new svhost_server_mysql($server_setting['database']);
            $mysql['db_name'] = $vhost_setting['db']['name'];
            $mysql['db_user'] = $vhost_setting['db']['user'];
            $mysql['db_host'] = $vhost_setting['db']['host'];
            $mysql['db_password'] = $vhost_setting['db']['password'];
            $database->create($mysql);
        }
                
        return true;
    } 
    
    function bash_create($domain){
        $bash = new svhost_bash;
        $server_setting = $this->load_server_setting();        
        $vhost_setting = $this->load_vhost_setting();
        #生成web空间
        $htdocs = $server_setting['http']['htdocs'].'/'.$vhost_setting['domain'];
        $server_setting['http']['htdocs'] = $htdocs;
        $htdocs_owner = $server_setting['ftp']['user'];
        $bash->mkdir($htdocs);
        $bash->chown($htdocs,$htdocs_owner);    
        #生成http服务配置
        if($server_setting['http']['name'] == 'nginx'){
            $nginx = new svhost_server_nginx($server_setting['http']);
            $server_ip = $server_setting['server']['ip'];
            $nginx->bash_create($bash,$domain,$server_ip);
        }
        #生成ftp帐号
        if($server_setting['ftp']['name'] == 'proftpd'){
            $proftpd = new svhost_server_proftpd($server_setting['ftp']);
            $ftp['user'] = $vhost_setting['ftp']['user'];
            $ftp['password'] = $vhost_setting['ftp']['password'];
            $ftp['home'] = $htdocs;
            $proftpd->bash_create($bash,$ftp);
        }
        #生成mysql帐号
        if($server_setting['database']['name'] == 'mysql'){
            $database = new svhost_server_mysql($server_setting['database']);
            $mysql['db_name'] = $vhost_setting['db']['name'];
            $mysql['db_user'] = $vhost_setting['db']['user'];
            $mysql['db_host'] = $vhost_setting['db']['host'];
            $mysql['db_password'] = $vhost_setting['db']['password'];
            $database->bash_create($bash,$mysql);
        }
        
        return $bash->get();        
    } 
    
    
    function bash_delete($domain){
        $bash = new svhost_bash;
        $server_setting = $this->load_server_setting();        
        $vhost_setting = $this->load_vhost_setting();
        #删除web空间
        $htdocs = $server_setting['http']['htdocs'].'/'.$vhost_setting['domain'];
        $bash->deldir($htdocs);
        #删除http服务配置文件
        if($server_setting['http']['name'] == 'nginx'){
            $nginx = new svhost_server_nginx($server_setting['http']);
            $nginx->bash_delete($bash,$domain);
        }
        #删除ftp帐号
        if($server_setting['ftp']['name'] == 'proftpd'){
            $proftpd = new svhost_server_proftpd($server_setting['ftp']);
            $ftp['user'] = $vhost_setting['ftp']['user'];
            $ftp['home'] = $htdocs;
            $proftpd->bash_delete($bash,$ftp);
        }
        #删除mysql帐号
        if($server_setting['database']['name'] == 'mysql'){
            $database = new svhost_server_mysql($server_setting['database']);
            $mysql['db_name'] = $vhost_setting['db']['name'];
            $mysql['db_user'] = $vhost_setting['db']['user'];
            $mysql['db_host'] = $vhost_setting['db']['host'];
            $database->bash_delete($bash,$mysql);
        }
        
        return $bash->get();        
    } 
    
    function bash_update($domain){
        $bash = new svhost_bash;
        $server_setting = $this->load_server_setting();        
        $vhost_setting = $this->load_vhost_setting();

        #更新ftp密码
        if($server_setting['ftp']['name'] == 'proftpd'){
            $proftpd = new svhost_server_proftpd($server_setting['ftp']);
            $ftp['user'] = $vhost_setting['ftp']['user'];
            $ftp['password'] = $vhost_setting['ftp']['password'];
            $proftpd->bash_update($bash,$ftp);
        }
        #更新mysql密码
        if($server_setting['database']['name'] == 'mysql'){
            $database = new svhost_server_mysql($server_setting['database']);
            $mysql['db_name'] = $vhost_setting['db']['name'];
            $mysql['db_user'] = $vhost_setting['db']['user'];
            $mysql['db_host'] = $vhost_setting['db']['host'];
            $mysql['db_password'] = $vhost_setting['db']['password'];
            $database->bash_update($bash,$mysql);
        }
        
        return $bash->get();        
    } 
    
    function is_exists($domain){
        $filter['domain'] = $domain;
        if(app::get('svhost')->model('vhostlist')->getList($filter)){
            return true;
        }
        return false;
    }

    
}