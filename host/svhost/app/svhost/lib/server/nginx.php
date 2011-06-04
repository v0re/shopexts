<?php

class svhost_server_nginx{
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
            array('#SERVERIP#','#DOMAIN#','#HTDOCS#'),
            array($ip,$domain,$htdocs),
            $conf_template
        );
        $save_file = "$conf_dir/vhosts/$conf_name";
        $bash->fwrite($save_file,$conf);
        
        return true;
    }
    
    function bash_delete(&$bash,$domain){
        $conf_dir = dirname($this->config['conf']);
        $conf_name = $domain.".conf";
        $save_file = "$conf_dir/vhosts/$conf_name";
        $bash->del($save_file);
        
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