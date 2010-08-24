<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class image_rebuild{
    function run(&$cursor_id,$params){
          //每次最多处理2个
        $limit = 2;
        $model = app::get('image')->model('image');
        $db = kernel::database();
        if($params['filter']['image_id']=='_ALL_'||$params['filter']['image_id']=='_ALL_'){
            unset($params['filter']['image_id']);
        }
        $where = $model->_filter($params['filter']);
        $where .= ' and last_modified<='.$params['queue_time'];
        $rows = $db->select('select image_id from sdb_image_image where '.$where.' order by last_modified desc limit '.$limit);
        foreach($rows as $r){

            $model->rebuild($r['image_id'],$params['size'],$params['watermark']);

        }
        $r = $db->selectrow('select count(*) as c from sdb_image_image where '.$where);
        return $r['c'];

    }
}
