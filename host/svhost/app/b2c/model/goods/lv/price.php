<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class b2c_mdl_goods_lv_price extends dbeav_model{

    function dump($filter,$field = '*',$subSdf = null){
        $rs = parent::dump($filter,$field,$subSdf);
        $oMlv = &$this->app->model('member_lv');
        $oPro = &$this->app->model('products');
        $memLv = $oMlv->dump( $filter['level_id'] );
        $price =  $oPro->dump($filter['product_id'],'price');
        $price = $price['price']['price']['price'];
        if($rs){
            $rs['title'] = $memLv['name'];
            $rs['custom'] = 'true';
        }else{
            $rs = array(
                'level_id' => $filter['level_id'],
                'price' => ($memLv['dis_count']>0?$memLv['dis_count'] * $price:$price),
                'title' => $memLv['name'],
                'custom' => 'false'
            );
        }
        return $rs;
    }

    function save(&$data,$mustUpdate = null){
        if( $data['custom'] == 'false' )
            return true;
        parent::save($data,$mustUpdate);
    }
    
}
