<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
/**
 * dbeav_meta
 * metaֵ
 *
 * @uses modelFactory
 * @package
 * @version $Id$
 * @copyright 2003-2007 ShopEx
 * @author Ever <ever@shopex.cn>
 * @license Commercial
 */
class dbeav_meta {    
    
    function __construct($table,$column,$has_pk=true){
        $sql = "SELECT mr_id,col_type,pk_name FROM sdb_dbeav_meta_register WHERE tbl_name='".$table."' AND col_name='".$column."'";
        $rows =  kernel::database()->select($sql);  
        $this->table = $table;
        $this->column = $column;
        $this->mr_id = $rows[0]['mr_id'];
        $this->value_type = "meta_value_".$rows[0]['col_type'];
        $this->value =  app::get('dbeav')->model($this->value_type);
        $this->pk_name = $rows[0]['pk_name'];
        $this->has_pk = $has_pk;       
    }
    
    function insert($data){
        $data['mr_id'] = $this->mr_id;
        return $this->value->insert($data);
    }
    
    function select(&$data){
        foreach($data as $row){
            $pk[] = $row[$this->pk_name];
        }
        $metarows = $this->value->select($this->mr_id,$pk);
        #кϲ
        foreach($data as $dkey=>$drow){
            $pk_id = $drow[$this->pk_name];
            if(!$metarows[$pk_id]){
                $metarows[$pk_id] = array($this->column=>NULL);
            }
            $drow = array_merge($drow,$metarows[$pk_id]);
            #ҪеĲѯҪȥеֵ
            if(!$this->has_pk){
                unset($drow[$this->pk_name]);
            }
            $data[$dkey] = $drow;
        }      
         return true;
    }
    
    function update($value,$pk){
        $this->value->update($value,$pk,$this->mr_id);
    }

    function delete($pk){
        $this->value->delete($pk);
    }
    
    function filter($filter){
        $value = $filter[$this->column];
        $pk = $this->value->get_pk($value);
        if(!is_array($pk)) return " AND 0 ";
        $ret = " AND ".$this->pk_name." IN (".implode(',',$pk).")";
        return $ret;
    }
    
    static function get_meta_column($tbl_name){
        $sql = "select col_name,col_desc from sdb_dbeav_meta_register where tbl_name='{$tbl_name}'";
        $rows = kernel::database()->select($sql);
        foreach($rows as $row){
            $meta['metaColumn'][] = $row['col_name'];
            $meta['columns'][$row['col_name']] = unserialize($row['col_desc']);
        }
        
        return $meta;
    }
    


}
