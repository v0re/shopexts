<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class b2c_goods_goodsfilter extends dbeav_filter{

    function goods_goodsfilter($type_id,$app){
        $modTag = app::get('desktop')->model('tag');
        $brand = $app->model('brand');
        $object = $app->model('goods_cat');
        $obj_type = $app->model('goods_type');

        if(!$object->catMap){
            $object->catMap = $object->getMapTree(0,'');
        }

        $return['cats'] = $object->catMap;


        $return['brands'] = $brand->getList('*',null,0,-1);


            $row = $obj_type->dump($type_id,'*');

            if($row['props']) $row['props'] = ($row['props']);

            if($row['type_id']){
                $row['brand'] = $object->db->select('SELECT b.brand_id,b.brand_name,brand_url,brand_logo FROM sdb_b2c_type_brand t
                        LEFT JOIN sdb_b2c_brand b ON b.brand_id=t.brand_id
                        WHERE disabled="false" AND t.type_id='.$row['type_id'].' ORDER BY brand_order');
            }else{
                $row['brand'] = $brand->getList('*', null, 0, -1);
            }

        if($row){
            $return['props'] = $row['props'];
            $row = $object->db->selectrow('SELECT max(price) as max,min(price) as min FROM sdb_b2c_goods where type_id='.intval($type_id));
        }else{

            $row = $object->db->selectrow('SELECT max(price) as max,min(price) as min FROM sdb_b2c_products ');
        }
        
        $return['type_id'] = $type_id;
        $return['tags'] = $modTag->getList('*',array('tag_type'=>'goods'),0,-1);
        
        $return['prices'] = utils::steprange($row['min'],$row['max'],5);
        return $return;
    }
}
