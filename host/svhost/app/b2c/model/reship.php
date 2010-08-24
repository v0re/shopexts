<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class b2c_mdl_reship extends dbeav_model{
    var $has_many = array(
        'reship_items'=>'reship_items',
        'orders'=>'order_delivery:contrast:reship_id^dly_id',
    );

    function save(&$sdf,$mustUpdate = null){
        if(!isset($sdf['orders'])){
            $sdf['orders'] = array(
                                array(
                                    'order_id' => $sdf['order_id'],
                                    'items' => $sdf['items'],
                                )
                            );
        }
        $tmpvar = $sdf['orders'];
        foreach($tmpvar as $k => $row){
            $sdf['orders'][$k]['dlytype'] = 'reship';
            $sdf['orders'][$k]['dly_id'] = $sdf['reship_id'];
        }
        unset($tmpvar);
        
        if(parent::save($sdf)){
            //一张发货单多个订单
            $oOrder = &$this->app->model('orders');
            foreach($sdf['orders'] as $order){
                if($sdf['order_id']){
                    $sdf_order = $oOrder->dump($order['order_id']);
                    if($sdf_order['ship_status'] == 5){
                        continue;
                    }

                    //todo 订单是否完全发货 
                    $data['ship_status'] = 4;

                    $data['order_id'] = $sdf['order_id'];
                    $filter['order_id'] = $sdf['order_id'];
                    $orders = &$this->app->model('orders');
                    $orders->update($data, $filter);
                }
            }
        }
        return true;
    }
    
    function gen_id(){
        $sign = '9'.date("Ymd");
        $sqlString = 'SELECT MAX(reship_id) AS maxno FROM sdb_b2c_reship WHERE reship_id LIKE \''.$sign.'%\'';
        $aRet = $this->db->selectrow($sqlString);
        if(is_null($aRet['maxno'])) $aRet['maxno'] = 0;
        $maxno = substr($aRet['maxno'], -6) + 1;
        if ($maxno==1000000){
            $maxno = 1;
        }
        return $sign.substr("00000".$maxno, -6);
    }

}
