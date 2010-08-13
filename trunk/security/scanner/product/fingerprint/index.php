<?php

$here = dirname(__FILE__);
/*
$sign = new signmaker;
$sign->set_source("$here/../../shopex-single-4.8.5.45144");
$sign->set_output("$here/signature/shopex-single-4.8.5.45144.sig");
$sign->init();
$sign->run('.');
echo "done!";




class signmaker{
    
    function set_source($dir){
        $this->source_dir = $dir;    
    }
    
    function set_output($file){
        $this->output = $file;
    }

    function init(){
        set_time_limit();
        $this->output_fp = fopen($this->output,'wb+');
        if(!is_resource($this->output_fp)) die('init fail');
        chdir($this->source_dir);
    }
    
    function run($dir){
        $dir_model = dir($dir);
        while(($file = $dir_model->read()) !== false){
            if(substr($file,0,1) == '.') continue;
            $daf = "$dir/$file";
            if(is_dir($daf)){
                $this->run($daf);    
            }else{
                $item = $daf.":".md5_file($daf);
                $item .= "\n";
                fwrite($this->output_fp ,$item);
            }
        }        
    }
}
*/

$scanner = new scanner;
$scanner->set_target_dir("$here/../../shopex-single-4.8.5.45144");
$scanner->set_signature('shopex-single-4.8.5.45144.sig');
$scanner->init();
$scanner->scan_system();
$scanner->report();

class scanner{
    function set_target_dir($dir){
        $this->target_dir = $dir;
    }
    
    function set_target_version($version){
        $this->target_version =$version;
    }
    
    function set_signature($name){
        $this->signature = $name;
    }
    
    function init(){
        set_time_limit(0);
        foreach( (array)file("signature/$this->signature") as $item){
            $a_temp = explode(":",$item);
            $this->signature_array[trim($a_temp[0])] = trim($a_temp[1]);
        }
        chdir($this->target_dir);
        if(!isset($this->target_version)){
            $this->target_version = $this->get_version();
        }
        $this->report['system']['changed'] = array();
    }
    
    function get_version(){
        $version_file = "core/version.txt";
        if(!file_exists($version_file)) die('version file not found!');
        $arr = file($version_file);
        preg_match('/rev=  (\d+)/',$arr[1],$ret);
        return trim($ret[1]);
    }
    
    function scan_system(){
        /*
        $dir_model = dir($dir);
        while(($file = $dir_model->read()) !== false){
            if(substr($file,0,1) == '.') continue;
            $daf = "$dir/$file";
            if(is_dir($daf)){
                $this->scan_system($daf);    
            }else{
                $md5_signature = md5_file($daf);
                if($this->signature_array[$daf] != $md5_signature){
                    $this->report['system']['changed'][]  = $daf;
                }     
            }
        }     */
        foreach((array)$this->signature_array as $daf=>$md5_signature){
                if(!file_exists($daf)){
                    $this->report['system']['deleted'][] = $daf;
                    continue; 
                }
                if($md5_signature != md5_file($daf)){
                    $this->report['system']['changed'][] = $daf;
                }   
        }
    }
    
    function report(){
        foreach((array)$this->report as $cate=>$data){
            echo "<dl>";
            echo "<dt>$cate</dt><ol>";
            $type = current(array_keys($data));
            foreach($data[$type] as  $item){
                echo "<dd><li>$type => $item</dd>";
            }
            echo "</ol></dl>";
        }
    }

}



