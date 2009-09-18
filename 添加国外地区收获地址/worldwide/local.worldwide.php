<?php
class local_worldwide{

    var $name = '国外地区';
    var $desc = '国外地区由你所需要的国家依次进行，生成不同的area.txt文件';
    var $maxdepth = 3;

    function install(){
        if($handle = fopen(dirname(__FILE__)."/area.txt","r")){
            $i = 3266;
            $sql = "INSERT INTO `sdb_regions` (`region_id`, `package`, `p_region_id`,`region_path`,`region_grade`, `local_name`, `en_name`, `p_1`, `p_2`) VALUES ";
            while ($data = fgets($handle)){
                $data = trim($data);
                if(substr($data, -2) == '::'){
                    if($aSql){
                        $sqlInsert = $sql.implode(',', $aSql).";";
                        $this->db->exec($sqlInsert);
                        unset($path);
                    }
                    $i++;
                    $path[]=$i;
                    $regionPath=",".implode(",",$path).",";
                    $aSql = array();
                    $aTmp = explode('::', $data);
                    $aSql[] = "(".$i.", 'worldwide', NULL, '".$regionPath."', '".count($path)."', '".$aTmp[0]."', NULL, NULL, NULL)";
                    $f_pid = $i;
                }else{
                    if(strstr($data, ':')){
                        $i++;
                        $aTmp = explode(':', $data);
                        unset($sPath);
                        $sPath[]=$f_pid;
                        $sPath[]=$i;
                        $regionPath=",".implode(",",$sPath).",";
                        $aSql[] = "(".$i.", 'worldwide', ".intval($f_pid).", '".$regionPath."', '".count($sPath)."', '".$aTmp[0]."', NULL, NULL, NULL)";
                        if(trim($aTmp[1])){
                            $pid = $i;
                            $aTmp = explode(',', trim($aTmp[1]));
                            foreach($aTmp as $v){
                                $i++;
                                $tmpPath=$regionPath.$i.",";
                                $grade = count(explode(",",$tmpPath))-2;
                                $aSql[] = "(".$i.", 'worldwide', ".intval($pid).", '".$tmpPath."', '".$grade."', '".$v."', NULL, NULL, NULL)";
                            }
                        }
                    }elseif($data){
                        $i++;
                        $tmpPath=$regionPath.$i.",";
                        $grade = count(explode(",",$tmpPath))-2;
                        $aSql[] = "(".$i.", 'worldwide', ".intval($f_pid).", '".$tmpPath."','".$grade."','".$data."', NULL, NULL, NULL)";
                    }
                }
            }
            fclose($handle);
			error_log(print_r($regionPath,true),3,'SHU.log');
            return true;
        }else{
			//error_log(print_r('AAAAA',true),3,'HAN.log');
            return false;
        }
    }
}
