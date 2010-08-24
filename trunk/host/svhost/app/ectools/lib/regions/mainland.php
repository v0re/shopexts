<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class ectools_regions_mainland
{
    var $name = '中国地区';
    var $key = 'mainland';
    var $setting = array('desc' => '系统默认为中国地区设置，包括港、澳、台地区。',
                       'maxdepth' => 3,
                       'source' => 'region-mainland.txt');
    
    function __construct($app){
        $this->app = $app;
        $this->db = kernel::database();
    }
    
    function install(){
        $file = $this->app->app_dir.'/'.$this->setting['source'];
        $basename = basename($file,'.txt');
        if($handle = fopen($file,"r")){
            $i = 0;
            $sql = "INSERT INTO `sdb_ectools_regions` (`region_id`, `package`, `p_region_id`,`region_path`,`region_grade`, `local_name`, `p_1`, `p_2`) VALUES ";
            while ($data = fgets($handle, 1000)){
                $data = trim($data);
                if(substr($data, -2) == '::'){
                    if($aSql){
                        $sqlInsert = $sql.implode(',', $aSql).";";
                        if(!$this->db->exec($sqlInsert)){
                            trigger_error($this->db->errorinfo(),E_USER_ERROR);
                            return false;
                        }
                        unset($path);
                    }
                    $i++;
                    $path[]=$i;
                    $regionPath=",".implode(",",$path).",";
                    $aSql = array();
                    $aTmp = explode('::', $data);
                    $aSql[] = "(".$i.", '{$this->key}', NULL, '".$regionPath."', '".count($path)."', '".$aTmp[0]."', NULL, NULL)";
                    $f_pid = $i;
                }else{
                    if(strstr($data, ':')){
                        $i++;
                        $aTmp = explode(':', $data);
                        unset($sPath);
                        $sPath[]=$f_pid;
                        $sPath[]=$i;
                        $regionPath=",".implode(",",$sPath).",";
                        $aSql[] = "(".$i.", '{$this->key}', ".intval($f_pid).", '".$regionPath."', '".count($sPath)."', '".$aTmp[0]."', NULL, NULL)";
                        if(trim($aTmp[1])){
                            $pid = $i;
                            $aTmp = explode(',', trim($aTmp[1]));
                            foreach($aTmp as $v){
                                $i++;
                                $tmpPath=$regionPath.$i.",";
                                $grade = count(explode(",",$tmpPath))-2;
                                $aSql[] = "(".$i.", '{$this->key}', ".intval($pid).", '".$tmpPath."', '".$grade."', '".$v."', NULL, NULL)";
                            }
                        }
                    }elseif($data){
                        $i++;
                        $tmpPath=$regionPath.$i.",";
                        $grade = count(explode(",",$tmpPath))-2;
                        $aSql[] = "(".$i.", '{$this->key}', ".intval($f_pid).", '".$tmpPath."','".$grade."','".$data."', NULL, NULL)";
                    }
                }
            }
            fclose($handle);
            return true;
        }else{
            return false;
        }
    }
}
