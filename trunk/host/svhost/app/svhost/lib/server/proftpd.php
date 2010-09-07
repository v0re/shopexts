<?php

class svhost_server_proftpd{
    
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
    
    function bash_delete(&$bash,$ftp){
        $ftp_user = $ftp['user'];
        $sql = "DELETE FROM ftpusers WHERE userid='{$ftp_user}'";
        $db_host = $this->config['db']['host'];
        $db_name = $this->config['db']['name'];
        $db_user = $this->config['db']['user'];
        $db_password = $this->config['db']['password'];        
        $bash->mysql_query_on_db($db_host,$db_user,$db_password,$db_name,$sql);
         
        return true;
    }
    
    function bash_update(&$bash,$ftp){
        $ftp_user = $ftp['user'];
        $password = $ftp['password'];
        $sql = "UPDATE  ftpusers SET passwd='{$password}' WHERE userid='{$ftp_user}'";
        $db_host = $this->config['db']['host'];
        $db_name = $this->config['db']['name'];
        $db_user = $this->config['db']['user'];
        $db_password = $this->config['db']['password'];        
        $bash->mysql_query_on_db($db_host,$db_user,$db_password,$db_name,$sql);
         
        return true;
    }
}