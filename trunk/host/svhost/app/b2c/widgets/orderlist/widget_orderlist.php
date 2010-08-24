<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
function widget_orderlist(&$setting,&$smarty){

    $order = app::get('b2c')->model('orders');
    $number=intval($setting['rowNum'])?intval($setting['rowNum']):5;

    //$result=$order->getList('*',$where,0,$number);
    
    $setting['smallPic'] and $setting['smallPic'] = $app->base_url() . 'statics/icons/' . $setting['smallPic'];

    $result=$order->getLastestOrder($number);
    if ($result)
        foreach($result as $key=>$val){
            $aTmp[$key]['order_id'] = $val['order_id'];
            $aTmp[$key]['ship_name'] = $val['ship_name'];
            $aTmp[$key]['sex'] = $val['sex'];
            $aTmp[$key]['date'] = date("Y-m-d",$val['createtime']);
            $aTmp[$key]['total_amount'] = $val['total_amount'];
        }
    return $aTmp;
}
?>
