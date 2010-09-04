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
     
    function run_bash($params){
        $this->server_id= $params['server_id'];
        $this->vhost_id= $params['vhost_id'];
        $this->bash_create($params['domain']);
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
            $nginx = new nginx($server_setting['http']);
            $server_ip = $server_setting['server']['ip'];
            $nginx->create($domain,$server_ip);
        }
        #生成ftp帐号
        if($server_setting['ftp']['name'] == 'proftpd'){
            $proftpd = new proftpd($server_setting['ftp']);
            $ftp['user'] = $vhost_setting['ftp']['user'];
            $ftp['password'] = $vhost_setting['ftp']['password'];
            $ftp['home'] = $htdocs;
            $proftpd->create($ftp);
        }
        #生成mysql帐号
        if($server_setting['database']['name'] == 'mysql'){
            $database = new mysql($server_setting['database']);
            $mysql['db_name'] = $vhost_setting['db']['name'];
            $mysql['db_user'] = $vhost_setting['db']['user'];
            $mysql['db_host'] = $vhost_setting['db']['host'];
            $mysql['db_password'] = $vhost_setting['db']['password'];
            $database->create($mysql);
        }
                
        return true;
    } 
    
    function bash_create($domain){
        $bash = new bash;
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
            $nginx = new nginx($server_setting['http']);
            $server_ip = $server_setting['server']['ip'];
            $nginx->bash_create($bash,$domain,$server_ip);
        }
        #生成ftp帐号
        if($server_setting['ftp']['name'] == 'proftpd'){
            $proftpd = new proftpd($server_setting['ftp']);
            $ftp['user'] = $vhost_setting['ftp']['user'];
            $ftp['password'] = $vhost_setting['ftp']['password'];
            $ftp['home'] = $htdocs;
            $proftpd->bash_create($bash,$ftp);
        }
        #生成mysql帐号
        if($server_setting['database']['name'] == 'mysql'){
            $database = new mysql($server_setting['database']);
            $mysql['db_name'] = $vhost_setting['db']['name'];
            $mysql['db_user'] = $vhost_setting['db']['user'];
            $mysql['db_host'] = $vhost_setting['db']['host'];
            $mysql['db_password'] = $vhost_setting['db']['password'];
            $database->bash_create($bash,$mysql);
        }
        
        echo $bash->get();        
        return true;
    } 
    
}

class bash{
    
    var $cmd;
    
    function __construct(){
        $this->cmd = '';
    }
    
    function put($cmd){
        $this->cmd .= $cmd."\n";
    }
    
    function get(){
        return $this->cmd;
    }
    
    function mkdir($dir){
        $this->put("mkdir $dir");
    }
    
    function chown($dir,$own){
        $this->put("chown -R {$own}:{$own} {$dir}");
    }    
    
    function fwrite($filename,$content){
        $this->put( "
        cat >$filename<<'EOF'
        $content
        EOF
        ");
    }
    
    function mysql_query($host,$user,$password,$sql){
        $this->put("
        mysql -h{$host} -u{$user} -p{$password} <<'EOF'
        $sql;
        EOF
        ");
    }
    
    function mysql_query_on_db($host,$user,$password,$db_name,$sql){
        $this->put("
        mysql -h{$host} -u{$user} -p{$password} <<'EOF'
        USE {$db_name};
        $sql;
        EOF
        "); 
    }
    
    
}

class nginx{
    function __construct($config){
        $this->config = $config;
    }
    
    function create($domain,$ip){
        $conf_dir = dirname($this->config['conf']);
        $conf_name = $domain.".conf";
        $htdocs = $this->config['htdocs'];        
        $conf_template =$this->site_conf_template();
        $conf = str_replace(
            array('SERVERIP','#DOMAIN#','#HTDOCS#'),
            array($ip,$domain,$htdocs),
            $conf_template
        );
        $save_file = "$conf_dir/site/$conf_name";
        file_put_contents($save_file,$conf);
        
        return true;
    }
    
    function bash_create(&$bash,$domain,$ip){
        $conf_dir = dirname($this->config['conf']);
        $conf_name = $domain.".conf";
        $htdocs = $this->config['htdocs'];        
        $conf_template =$this->site_conf_template();
        $conf = str_replace(
            array('SERVERIP','#DOMAIN#','#HTDOCS#'),
            array($ip,$domain,$htdocs),
            $conf_template
        );
        $save_file = "$conf_dir/site/$conf_name";
        $bash->fwrite($save_file,$conf);
        
        return true;
    }
    
       function site_conf_template(){
        return <<<EOF
server
{
    listen       #SERVERIP#:80;
    server_name  #DOMAIN# www.#DOMAIN#;
    index index.html index.htm index.php;
    root  #HTDOCS#;
        
    location / {
        if (!-e \$request_filename) {
            rewrite ^/(.+\.(html|xml|json|htm|php|jsp|asp|shtml))$ /index.php?$1 last;
        }
    }
    
    location ~ /(home|themes|images)/
    {
        access_log  off;
    }
    
    location ~ .*\.php?$
    {
        include php_fcgi.conf;
    }
    
    location ~ .*\.(gif|jpg|jpeg|png|bmp|swf)$
    {
        expires      30d;
    }
        
    location ~ .*\.(js|css)?$
    {
        expires      1h;
    }
    
    access_log off;
}
EOF;
 
    }
}

class mysql{
        function __construct($config){
            $this->config = $config;
        }
        
        function create($mysql){            
            $db_host = $this->config['host'];
            $db_user = $this->config['root'];
            $db_password = $this->config['password'];
            $link = mysql_connect($db_host,$db_user,$db_password);
             $sql = "CREATE DATABASE $db_name";
            mysql_query($sql,$link);        
            $db_name = $mysql['db_name'];
            $db_user = $mysql['db_user'];
            $db_host = $mysql['db_host'];
            $db_password = $mysql['db_password'];
            $sql = "GRANT ALL ON $db_name TO $db_user@$db_host IDENTIFIED BY $db_password";
            mysql_query($sql,$link);
            mysql_close($link);
            
            return true;
    }
    
    function bash_create(&$bash,$mysql){            
            $db_host = $this->config['host'];
            $db_user = $this->config['root'];
            $db_password = $this->config['password'];
             $sql = "CREATE DATABASE $db_name";
            $bash->mysql_query($db_host,$db_user,$db_password,$sql);        
            $db_name = $mysql['db_name'];
            $db_user = $mysql['db_user'];
            $db_host = $mysql['db_host'];
            $db_password = $mysql['db_password'];
            $sql = "GRANT ALL ON $db_name TO $db_user@$db_host IDENTIFIED BY $db_password";
            $bash->mysql_query($db_host,$db_user,$db_password,$sql);  
                
            return true;
    }
}

class proftpd{
    
    function __construct($config){
        $this->config = $config;
    }
        
    function create($ftp){
        $ftp_user = $ftp['user'];
        $ftp_password = $ftp['password'];
        $ftp_homedir = $ftp['home'];
        #
        $sql = "INSERT INTO ftpusers (`userid`,`passwd`,`homedir`) 
                    VALUES  ('{$ftp_user}','{$ftp_password}','{$ftp_homedir}')";
        $db_host = $this->config['db']['host'];
        $db_name = $this->config['db']['name'];
        $db_user = $this->config['db']['user'];
        $db_password = $this->config['db']['password'];
        $link = mysql_connect($db_host,$db_user,$db_password);
        mysql_select_db($db_name,$link);
        mysql_query($sql,$link);
        mysql_close($link);
         
        return true;
    }
    
    function bash_create(&$bash,$ftp){
        $ftp_user = $ftp['user'];
        $ftp_password = $ftp['password'];
        $ftp_homedir = $ftp['home'];
        #
        $sql = "INSERT INTO ftpusers (`userid`,`passwd`,`homedir`) 
                    VALUES  ('{$ftp_user}','{$ftp_password}','{$ftp_homedir}')";
        $db_host = $this->config['db']['host'];
        $db_name = $this->config['db']['name'];
        $db_user = $this->config['db']['user'];
        $db_password = $this->config['db']['password'];        
        $bash->mysql_query_on_db($db_host,$db_user,$db_password,$db_name,$sql);
         
        return true;
    }
}