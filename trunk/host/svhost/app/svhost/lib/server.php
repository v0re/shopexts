<?php

class svhost_server {
    
    function __construct(){
            
    }
    
    function create($domain,&$message){
        $this->config = $this->gen_site_conf($domain); 
        $this->new_htdocs_space();
        $this->new_nginx_site();
        $this->new_mysql_account();
        $this->new_ftp_account();        
        
        $message = "create $domain ok! ";   
        
        return true;
    }
    
    function  new_htdocs_space(){
        $htdocs = $this->config['htdocs'];
        $ftpd_user = $this->config['ftpd_user'];
        mkdir($htdocs);
        chown($htdocs,$ftpd_user);    
        
        return true;
    }
    
    function new_nginx_site(){
        $conf_dir = $this->config['nginx_conf_dir'];
        $conf_name = $this->config['domain'].".conf";
        $conf_template =$this->get_nginx_site_template();
        $domain = $this->config['domain'];
        $htdocs = $this->config['htdocs'];        
        $conf = str_replace(array('#DOMAIN#','#HTDOCS#'),array($domain,$htdocs),$conf_template);
        file_put_contents($conf_name,$conf);
        
        return true;
    }
    
    function new_mysql_account(){
        $db_host = $this->config['db_host'];
        $db_user = $this->config['db_user'];
        $db_password = $this->config['db_password'];
        $db_name = $this->config['db_name'];
        $link = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
        $sql = "create database $db_name";
        mysql_query($sql,$link);
        $sql = "grant all on $db_name to $db_user@$db_host identified by $db_password";
        mysql_query($sql,$link);
        mysql_close($link);
        
        return true;
    }
    
    function new_ftp_account(){
        $ftpd_db = $this->config['ftpd_db'];
        $ftp_user = $this->config['ftp_user'];
        $ftp_password = $this->config['ftp_password'];
        $ftp_homedir = $this->config['ftp_homedir'];
        $link = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
        mysql_select_db($this->ftpd_db,$link);
        $sql = "insert into ftpusers (`userid`,`passwd`,`homedir`) values('{$ftp_user}','{$ftp_password}','{$ftp_homedir}')";
        mysql_query($sql,$link);
        mysql_close($link);
         
        return true;
    }
    
    function gen_site_conf($domain){
        $config['htdocs'] = "/var/www/html/".$domain;
        $config['nginx_conf_dir'] = '/srv/nginx/conf';
        $config['domain'] = $domain;
        $config['db_host'] = '127.0.0.1';
        #
        $domain_strip_dot = str_replace('.','',$domain);
        $config['db_user'] = $domain_strip_dot;
        $config['db_name'] = $domain_strip_dot;
        $config['db_password'] = $this->gen_radom_string(8);
        #
        $config['ftpd_user'] = 'ftpd';
        $config['ftpd_group'] = 'ftpd';
        $config['ftpd_db'] = 'proftpd';
        #
        $config['ftp_user'] = substr($domain,0,strpos($domain,"."));
        $config['ftp_password'] = $this->gen_radom_string(8);
        $config['ftp_homedir'] = "/var/www/html/".$domain;
    }
    
    function gen_radom_string($len){    
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz' ;
        $string = ''; 
        for(;$len>=1;$len--)   {
            $position=rand()%strlen($chars);
            $string.=substr($chars,$position,1); 
        }
        return $string; 
    }
    
    function get_nginx_site_template(){
        return <<<EOF
server
{
    listen       174.133.40.226:80;
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