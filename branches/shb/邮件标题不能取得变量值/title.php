<?php

/*

*/
//�ڸ��û������ʼ���ʱ���ʼ�����������е���ȡ���еĲ���ȡ��������ȡ�������Ǳ���������<{$delivery.ship_name}> �м��'.'����ַ��ģ���Ϊ��������û��//delivery.ship_name����������Ӧ�ļ���[delivery][ship_name] ,��/core/model/system/��mdl.messenger.php�ļ���loadTitle�����ĳ����´��룬���ɽ�������⡣
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