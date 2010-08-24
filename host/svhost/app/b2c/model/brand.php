<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

/**
 * brand 模板
 */
class b2c_mdl_brand extends dbeav_model{

    var $has_many = array(
        'gtype' => 'type_brand:replace',
    );

    function getBrandTypes($brandid){
        return $this->db->select('SELECT t.* FROM sdb_b2c_goods_type t LEFT JOIN sdb_b2c_type_brand b ON t.type_id = b.type_id
                WHERE brand_id = '.$brandid);
    }

    function getBidByType($typeid){
        return $this->db->select('SELECT brand_id FROM sdb_b2c_type_brand  WHERE type_id = '.$typeid);
    }

    function getDefinedType(){
        $oType = &$this->app->model('goods_type');
        $aType = $oType->getList('type_id,name,setting,is_def',null,-1,-1);
        foreach($aType as $row){
            if($row['is_def'] == 'true'){
                $brandType['default'] = $row;
            }else{
//                $row['setting'] = unserialize($row['setting']);
                if($row['setting']['use_brand']){
                    $brandType['custom'][] = $row;
                }
            }
        }
        return $brandType;
    }

    function save( &$data,$mustUpdate = null ){
        $rs = parent::save($data,$mustUpdate);
        $this->brand2json();
        return $rs;
    }

    function brand2json($return=false){
        @set_time_limit(600);
        $contents=$this->db->select('SELECT brand_id,brand_name,brand_url,ordernum,brand_logo FROM sdb_b2c_brand WHERE disabled = \'false\' order by ordernum desc');
        if($return){
            base_kvstore::instance('b2c_goods')->store('goods_brand.data',$contents);
            return $contents;
        }else{
            return base_kvstore::instance('b2c_goods')->store('goods_brand.data',$contents);
        }
    }

    function getAll(){
        if(base_kvstore::instance('b2c_goods')->fetch('goods_brand.data', $contents) !== false){

            if(!is_array($contents)){
                if(($result=json_decode($contents,true))){
                    return json_decode($contents,true);
                }else{
                    return $this->brand2json(true);
                }
            }else{
                    return $contents;
            }
        }else{
            return $this->brand2json(true);
        }
    }


}
