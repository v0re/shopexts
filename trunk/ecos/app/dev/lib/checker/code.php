<?php

class dev_checker_code implements dev_interface_checker {
    
	var $find_views = array();#从php和html文件中找到的view文件
	var $find_views_dynamic = array();#动态调用view文件列表
	var $record_views = array();#扫到的全部view文件
	var $current_app_dir = '';
	
    public function worker($directory,$file){
        $file_postion = "$directory/$file";
        if(substr($file,-3,3) == 'php'){
			$this->get_views_in_php($file_postion);
		}
		$this->record_views($directory,$file);
    }
    
    public function exception($directory,$file){
        $file_postion = "$directory/$file";
        if(is_file($file_postion)){      
        	$mime=pathinfo($file_postion);
		    if(!in_array($mime["extension"],array("php","html"))){
	            return true;	     
		    }   
        }
        if(is_dir($file_postion)){
            return false;
        }
        return false;
    }
    
    private function get_views_in_php($file_postion){
    	$content = file_get_contents($file_postion);
        if(!preg_match_all('/->(display|page|fetch)\((.*)\);/',$content,$ret)) return;
     	foreach($ret[2] as $view){
     		$view = trim($view,"\'");
    		$view = trim($view,"\"");
			if(substr($view,-4,4) == 'html'){
				$view_postion = "$this->current_app_dir/view/$view";
				if(strstr($view_postion,"\$")){
					$this->find_views_dynamic[] = realpath($view_postion);
				}
				if(!file_exists($view_postion)) continue;
				$this->find_views[] = realpath($view_postion);
				$this->get_views_in_html($view_postion);
			}        	
		}
    }
    
    private function get_views_in_html($file_postion){
		$html_content = file_get_contents($file_postion);
		if(!preg_match_all('/<{include file=(.*) app=.*/',$html_content,$ret)) return;
		foreach($ret[1] as $view){
			$view = trim($view,"\'");
    		$view = trim($view,"\"");
			if(substr($view,-4,4) == 'html'){
				$view_postion = "$this->current_app_dir/view/$view";
				$this->find_views[] = realpath($view_postion);
				$this->get_views_in_html($view_postion);#递归一下				
			}
		}
	}   
	
	private function record_views($directory,$file){
		$file_postion = "$directory/$file";
		if(strstr($directory,'/view/') && substr($file,-4,4) == 'html' && is_file($file_postion)){			
			$this->record_views[] = realpath($file_postion);
		}
	}
	
	function get_unref_html(){
		kernel::log('未被调用的view文件');
		$i = 0;
		foreach($this->record_views as $view){
			if(in_array($view,$this->find_views)) continue;
			kernel::log($view);
			$i++;
		}
		kernel::log("查找结束，共找到 $i 个文件");
	}
    
}
