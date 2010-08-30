<?php

class svhost_utils {    

    static function gen_radom_string($len){    
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz' ;
        $string = ''; 
        for(;$len>=1;$len--)   {
            $position=rand()%strlen($chars);
            $string.=substr($chars,$position,1); 
        }
        return $string; 
    }
    
    static function get_nginx_site_template(){
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