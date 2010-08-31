<?php
error_reporting(1); 
set_time_limit(0);    

function is_exception($directory,$file){
	//排除.开头的文件
	if(substr($file,0,1) == '.'){
	 	return true;
	} 
    if(is_file($file)){
        //排除非php
         $mime=pathinfo($file);
         if($mime["extension"] != "php"){
            return true;	     
         }
        //排除缓存文件
        if(preg_match('/home\/cache\/.*\/[0-9a-f]{32}\.php/',"$directory/$file")){
            return true;
        }
    }
    return false;
}

function check_trojan($directory){
    //定义挂马语句和webshell特征字符串的正则表达式..
    $troscript="/(iframe)|(exec\()|(system\()|(shell_exec)|(proc_open) |(wscript.shell)|(eval\(\$_)/i";//加模式修正符i表示不区分大小写..
    $check_trojan_dir = @opendir($directory);     
    while ($file = @readdir($check_trojan_dir)) {
        if(is_exception($directory,$file)) continue;
        if(is_dir("$directory/$file")){     
            check_trojan("$directory/$file"); //递归调用    
        }else{
            $handle=file(trim($directory."/".$file));
            $notes_length=count($handle);
            for($i=0; $i<$notes_length; $i++){
                $content = $handle[$i];
                //搜索$file文件中的字符是否包含正则表达式中的挂马和webshell特征字符串..
                if(preg_match($troscript,$content,$arr)){
                    //print_r ($arr);
                    $i=$i+1;//校正行数.
                    print_r ("warning trojan code ".$arr[0]." found in ".$directory."/".$file." line $i <br>");
                }
            }
        }        
    }    
    closedir($check_trojan_dir);     
}
    
check_trojan('.'); //调用上面定义好的函数..
date_default_timezone_set('Asia/shanghai');
echo "scan success. time:".date('Y年m月d日H时i分s秒')."<br>";
    
