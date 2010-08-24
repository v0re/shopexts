<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class b2c_mdl_delivery extends dbeav_model{
    var $has_many = array(
        'delivery_items'=>'delivery_items',
        'orders'=>'order_delivery:contrast:delivery_id^dly_id',
    );

    function save(&$sdf,$mustUpdate = null){
        if(!isset($sdf['orders'])){
            $sdf['orders'] = array(
                                array(
                                    'order_id' => $sdf['order_id'],
                                    'items' => $sdf['delivery_items'],
                                )
                            );
        }
        $tmpvar = $sdf['orders'];
        foreach($tmpvar as $k => $row){
            $sdf['orders'][$k]['dlytype'] = 'delivery';
            $sdf['orders'][$k]['dly_id'] = $sdf['delivery_id'];
        }
        unset($tmpvar);
        if(parent::save($sdf)){
            //一张发货单多个订单
            /*$oOrder = &$this->app->model('orders');
            foreach($sdf['orders'] as $order){
                if($sdf['order_id']){
                    $sdf_order = $oOrder->dump($order['order_id'],'*',array('order_items'=>'*'));
                    if($sdf_order['ship_status'] == 1){
                        continue;
                    }
                    //todo 订单是否完全退货 
                    $data['ship_status'] = 1;
                    
                    $data['order_id'] = $sdf['order_id'];
                    $filter['order_id'] = $sdf['order_id'];
                    $orders = &$this->app->model('orders');
                    $orders->update($data, $filter);
                }
            }*/
        }
        return true;
    }

    function gen_id(){
        $sign = '1'.date("Ymd");
        $sqlString = 'SELECT MAX(delivery_id) AS maxno FROM sdb_b2c_delivery WHERE delivery_id LIKE \''.$sign.'%\'';
        $aRet = $this->db->selectrow($sqlString);
        if(is_null($aRet['maxno'])) $aRet['maxno'] = 0;
        $maxno = substr($aRet['maxno'], -6) + 1;
        if ($maxno==1000000){
            $maxno = 1;
        }
        return $sign.substr("00000".$maxno, -6);
    }
    
    /**
     * 得到最新的发货单
     * @params int 最新的数量，条数
     * @return array 数据数组
     */
    public function getLatestDelivery($number)
    {
        return $this->getList('*', array(), 0, $number, 't_begin DESC');
    }
    
    public function modifier_member_id($row)
    {
        $obj_members = $this->app->model('members');
        $arr_member = $obj_members->dump($row, '*', array(':account@pam'=>array('*')));
        
        return $arr_member['pam_account']['login_name'] ? $arr_member['pam_account']['login_name'] : '顾客';
    }
}
