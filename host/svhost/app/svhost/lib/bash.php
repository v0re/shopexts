<?php

class svhost_bash{
    
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
    
    function deldir($dir){
        $this->put("rm -rf $dir");
    }
    
    function del($file){
        $this->put("rm -f $file");
    }
    
    function chown($dir,$own){
        $this->put("chown -R {$own}:{$own} {$dir}");
    }    
    
    function fwrite($filename,$content){
        $this->put( "cat >$filename<<'EOF'\n$content\nEOF");
    }
    
    function mysql_query($host,$user,$password,$sql){
        $this->put("mysql -h{$host} -u{$user} -p{$password} <<'EOF'\n$sql;\nEOF");
    }
    
    function mysql_query_on_db($host,$user,$password,$db_name,$sql){
        $this->put("mysql -h{$host} -u{$user} -p{$password} <<'EOF'\nUSE {$db_name};\n$sql;\nEOF"); 
    }
}