<?php
class svhost_server_mysql{
        function __construct($config){
            $this->config = $config;
        }
        
        function create($mysql){            
            $db_host = $this->config['host'];
            $db_user = $this->config['root'];
            $db_password = $this->config['password'];
            $db_name = $mysql['db_name'];
            $db_user = $mysql['db_user'];
            $db_host = $mysql['db_host'];
            $db_password = $mysql['db_password'];
            $link = mysql_connect($db_host,$db_user,$db_password);
             $sql = "CREATE DATABASE $db_name";
            mysql_query($sql,$link);        

            $sql = "GRANT ALL ON $db_name TO $db_user@$db_host IDENTIFIED BY $db_password";
            mysql_query($sql,$link);
            mysql_close($link);
            
            return true;
    }
    
    function bash_create(&$bash,$mysql){            
            $root_db_host = $this->config['host'];
            $root_db_user = $this->config['root'];
            $root_db_password = $this->config['password'];
            $db_name = $mysql['db_name'];
            $db_user = $mysql['db_user'];
            $db_host = $mysql['db_host'];
            $db_password = $mysql['db_password'];
             $sql = "CREATE DATABASE $db_name";
            $bash->mysql_query($root_db_host,$root_db_user,$root_db_password,$sql);        
  
            $sql = "GRANT ALL ON {$db_name}.* TO {$db_user}@{$db_host} IDENTIFIED BY '{$db_password}'";
            $bash->mysql_query($root_db_host,$root_db_user,$root_db_password,$sql);  
                
            return true;
    }
    
     function bash_delete(&$bash,$mysql){            
            $root_db_host = $this->config['host'];
            $root_db_user = $this->config['root'];
            $root_db_password = $this->config['password'];
            $db_name = $mysql['db_name'];
            $db_user = $mysql['db_user'];
            $db_host = $mysql['db_host'];
             $sql = "DROP DATABASE $db_name";
            $bash->mysql_query($root_db_host,$root_db_user,$root_db_password,$sql);        
  
            $sql = "DROP USER {$db_user}@{$db_host}";
            $bash->mysql_query($root_db_host,$root_db_user,$root_db_password,$sql);  
                
            return true;
    }
    
    
}