<?php
function widget_orderlist(&$setting,&$system){
    $order =&$system->loadModel('trading/order');
    $smarty = &$system->loadModel('system/frontend');
    $number=intval($setting['rowNum'])?intval($setting['rowNum']):5;

    //$result=$order->getList('*',$where,0,$number);
    
    $result=$order->getLastestOrder($number);
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