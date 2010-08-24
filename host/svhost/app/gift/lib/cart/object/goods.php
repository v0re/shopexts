<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
    
class gift_cart_object_goods extends b2c_cart_object_goods {
    
    
    function __construct($app) {
        $this->app = $app;
        $this->o_goods = $this->app->model('goods');
    }
    
    public function _get_products($id) {
        if(!$id) return false;
        
        if( !is_array($id) ) $id = array($id);
        foreach( $id as $_key => $_id ) {
            if( isset($this->arr_gift[$_id]) ) unset($id[$_key]);
        }
        if( !$id ) return $this->arr_gift;
        
        $arr_gift = $this->o_goods->getList('*', array('goods_id'=>$id));
        foreach($arr_gift as $row) {
           if($row['marketable']=='false') {  //Æ·Â¼Ü¹ï³µÊ§í£¡
               unset($row);continue;
           }

           $aResult[$row['goods_id']] = array(
                    'bn' => $row['bn'],
                    'price' => array(
                                'price' => $row['price'],
                                'cost' => $row['cost'],
                                'member_lv_price' => $row['price'],
                                'buy_price' => $row['price'],
                              ),
                    'product_id' => $row['product_id'],
                    'goods_id' => $row['goods_id'],
                    'goods_type' => $row['goods_type'],
                    'name'=> $row['name'],
                    'consume_score' => 0,
                    'gain_score' => intval($row['gain_score']),
                    'type_id' => $row['type_id'],
                    'min_buy' => $row['min_buy'],
                    'spec_info' => $row['spec_info'],
                    'weight' => $row['weight'],
                    'quantity' => 1,
                    'params' => $row['params'],
                    'floatstore' => $row['floatstore'],
                    'store'=> (empty($row['store']) ? ($row['store']===0 ? 0 : 999999999) : $row['store']),
                    'default_image' => array(
                                        'thumbnail' => $row['image_default_id'],
                                      )
           );
           $this->arr_gift[$row['goods_id']] = $aResult[$row['goods_id']];
       }
       
       
       return $this->arr_gift;
    }
    
}