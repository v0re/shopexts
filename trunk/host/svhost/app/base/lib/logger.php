<?php
class base_logger{

    static $path = array();
    static $finish = false;
            
    static function begin($key){
        register_shutdown_function(array('base_logger','shutdown'));
        array_push(self::$path,$key);
        ob_start();
    }

    static function end($shutdown=false){
        if(self::$path){
            self::$finish = true;
            $content = ob_get_contents();
            ob_end_clean();
            $name = array_pop(self::$path);            
            if(defined('SHOP_DEVELOPER')){
                error_log("\n\n".str_pad(@date(DATE_RFC822).' ',60,'-')."\n".$content
                    ,3,ROOT_DIR.'/data/trace.'.$name.'.log');
            }
            if($shutdown){
                echo json_encode(array(
                    'rsp'=>'fail',
                    'res'=>$content,
                    'data'=>null,
                ));
            }
            return $content;
        }
    }
    
    static function shutdown(){
        self::end(1);
    }

}
