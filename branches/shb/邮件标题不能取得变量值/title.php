<?php

/*

*/
//在给用户发送邮件的时候，邮件标题变量名有的能取到有的不能取到，不能取到到的是变量名形如<{$delivery.ship_name}> 中间带'.'这个字符的，因为在数组中没有//delivery.ship_name键，而它对应的键是[delivery][ship_name] ,把/core/model/system/中mdl.messenger.php文件的loadTitle方法改成以下代码，即可解决此问题。
function loadTitle($action,$msg,$lang='',$data=''){
      $title = $this->system->getConf('messenger.title.'.$action.'.'.$msg);
 
         if($data!=""){
            preg_match_all('/<\{\$(.*?)\}>/', $title, $result);
   
   
   foreach($result[1] as $k => $v){
   
                if(in_array($v,array_keys($data))){
                    $title = str_replace($result[0][$k],$data[$v],$title);
     
    }
  
    
    if(strpos($v,'.')){
    
    $aRow=explode('.',$v); 
    
       $a=$aRow[0];$b=$aRow[1];
    
    $title = str_replace($result[0][$k],$data[$a][$b],$title);
   
    }
     
            }
   
        }
  
  
        return $title;
    }
?>