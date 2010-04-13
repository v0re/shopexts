<?php
class core_logger{
    
    static $content;
    static $lf = "\r\n";
    static $__instance = array();
    
    function  __construct($prefix){
        $this->prefix = $prefix;
    }
        
    function __destruct(){
        self::display();
    }
    
    static function instance($prefix){
        if(!isset(self::$__instance[$prefix])){
            self::$__instance[$prefix] = new core_logger($prefix);
        }
        return self::$__instance[$prefix];
    }
    
    static function debug($msg){
        self::$content[] = $msg;
    }  
    
    
    static function display(){
        self::$lf = "<br>";
        $debug_info = "<div id='core_debug'>".implode(self::$lf,self::$content)."</div>";
        echo $debug_info;
    }
}
?>
