<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
/**
 * dbeav_mdl_meta_value_text
 * meta textʹȡ
 *
 * @version $Id$
 * @copyright 2003-2007 ShopEx
 * @author Ken <ken@shopex.cn>
 * @license Commercial
 */
class dbeav_mdl_meta_value_text extends dbeav_metavalue{
     
    function insert($data){
        $rs = $this->db->exec('select * from '.$this->table.' where 0=1');
        foreach( $data as $k => $v ){
            if( is_array($v) )
                $data[$k] = serialize( $v );
        }
        $sql = base_db_tools::getInsertSQL($rs,$data);
        $this->db->exec($sql);            
    }
    
        /**
     * select 
     * עidֵmetaֵе
     * @param int $mr_id 
     * @param array $pk 
     * @access public
     * @return array
     */    
    function select($mr_id,$pk){
        $sql = "
        SELECT r.tbl_name,r.col_name,v.pk,v.value ,r.col_desc
        FROM ".$this->table." v 
        LEFT JOIN sdb_dbeav_meta_register r 
        ON v.mr_id=r.mr_id  
        WHERE v.mr_id='".$mr_id."' 
        AND v.pk in (".implode(',',$pk).")
        ";
        $rows = $this->db->select($sql);
        foreach($rows as $row){
            $colDesc = unserialize( $row['col_desc'] );
            $ret[$row['pk']] = array($row['col_name']=>(($row['col_desc']['type']=='serialize')?unserialize($row['value']):$row['value'])); 
        }
        return $ret;
    }
   
    function update($value,$pk,$mr_id){
        $pk_id = $pk[0];
        $aSql = "SELECT * FROM ".$this->table." WHERE pk = ".$pk_id ." AND mr_id = ".$mr_id;
        $result = $this->db->select($aSql);
        if($result){
        $sql = "
        UPDATE ".$this->table."
        SET value='".(is_array($value)?serialize($value):$value)."'
        WHERE pk
        IN (".implode(',',$pk).") AND mr_id = ".$mr_id;
        }
        else{
            $sql = "INSERT INTO ".$this->table."(mr_id,pk,value) VALUES('$mr_id','$pk_id','".(is_array($value)?serialize($value):$value)."')";
        }
        $this->db->exec($sql);
    }

}
