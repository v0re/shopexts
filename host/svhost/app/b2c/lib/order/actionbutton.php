<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class b2c_order_actionbutton{

    function __construct($app){
        $this->app = $app;
        $this->app_ectools = app::get('ectools');
    }

    function get_button_html($order_id)
    {
        $buttons = array('pay'=>'支付',
                        'delivery'=>'发货',
                        'finish'=>'完成',
                        'refund'=>'退款',
                        'reship'=>'退货',
                        'cancel'=>'作废');
        
        $order = $this->app->model('orders');
        $payments = app::get('ectools')->model('payments');
        
        $sdf_order = $order->dump($order_id, 'pay_status,ship_status,status');
        //todo button disabled
        $html_group_start = "<ul>";
        $html_group_end = "</ul>";
        
        $html .= $html_group_start;
        
        $disable_style = 'disabled="disabled" class="inactive"';
        if($sdf_order['pay_status']==1
            ||$sdf_order['pay_status']==2
            ||$sdf_order['pay_status']==4
            ||$sdf_order['pay_status']==5
            ||$sdf_order['status']!='active')
        {
            $pay_style = $disable_style;
        }

        if($sdf_order['status']!='active'
            ||$sdf_order['ship_status']==1)
        {
        
            $delivery_style = $disable_style;
        
        }

        if($sdf_order['status']!='active')
        {
        
            $finish_style = $disable_style;
        
        }

        if($sdf_order['status']!='active'
            ||$sdf_order['pay_status']==0
            ||$sdf_order['pay_status']==5)
        {
        
            $refund_style = $disable_style;
        
        }

        if($sdf_order['status']!='active'
            ||$sdf_order['ship_status']==0
            ||$sdf_order['ship_status']==4)
        {
        
            $reship_style = $disable_style;
        
        }
        if($sdf_order['status']!='active'
            ||$sdf_order['ship_status']>0
            ||$sdf_order['pay_status']>0)
        {
        
            $cancel_style = $disable_style;
        
        }




        if($this->app->getConf('order.flow.payed')||1){
            $html .= '<li><input type="button" onclick="new Dialog(\'index.php?app=b2c&ctl=admin_order&act=gopay&p[0]='.$order_id.'&_finder[finder_id]='.$_GET['_finder']['finder_id'].'\',{title:\'订单付款\'})" value="'.$buttons['pay'].'..." '.$pay_style.'/></li>';
        }else{
            $html .= '<li><input type="button" onclick="location.href=\'index.php?app=b2c&ctl=admin_order&act=dopay&p[0]='.$order_id.'\'" value="'.$buttons['pay'].'" '.$pay_style.'/></li>';
        }
        
        if($this->app->getConf('order.flow.consign')||1){
            $html .= '<li><input type="button" onclick="new Dialog(\'index.php?app=b2c&ctl=admin_order&act=godelivery&p[0]='.$order_id.'&_finder[finder_id]='.$_GET['_finder']['finder_id'].'\',{title:\'订单发货\'})" value="'.$buttons['delivery'].'..." '.$delivery_style.'/></li>';
        }else{
            $html .= '<li><input type="button" onclick="location.href=\'index.php?app=b2c&ctl=admin_order&act=dodelivery&p[0]='.$order_id.'\'" value="'.$buttons['delivery'].'" '.$delivery_style.'/></li>';
        }
        
        $html .= $html_group_end;
        
        $html .= $html_group_start;
        
        $html .= '<li><input type="button" onclick="W.page(\'index.php?app=b2c&ctl=admin_order&act=dofinish&p[0]='.$order_id.'\',{onComplete:function(){window.finderGroup[\''.$_GET['_finder']['finder_id'].'\'].refresh().delay(400);}})" value="'.$buttons['finish'].'" '.$finish_style.'/></li>';
        
        $html .= $html_group_end;
        
        $html .= $html_group_start;
        
        if($this->app->getConf('order.flow.refund')||1){
            $html .= '<li><input type="button" onclick="new Dialog(\'index.php?app=b2c&ctl=admin_order&act=gorefund&p[0]='.$order_id.'&_finder[finder_id]='.$_GET['_finder']['finder_id'].'\',{title:\'订单退款\'})" value="'.$buttons['refund'].'..." '.$refund_style.'/></li>';
        }else{
            $html .= '<li><input type="button" onclick="location.href=\'index.php?app=b2c&ctl=admin_order&act=dorefund&p[0]='.$order_id.'\'" value="'.$buttons['refund'].'" '.$refund_style.'/></li>';
        }
        
        if($this->app->getConf('order.flow.reship')||1){
            $html .= '<li><input type="button" onclick="new Dialog(\'index.php?app=b2c&ctl=admin_order&act=goreship&p[0]='.$order_id.'&_finder[finder_id]='.$_GET['_finder']['finder_id'].'\',{title:\'订单退货\'})" value="'.$buttons['reship'].'..." '.$reship_style.'/></li>';
        }else{
            $html .= '<li><input type="button" onclick="location.href=\'index.php?app=b2c&ctl=admin_order&act=doreship&p[0]='.$order_id.'\'" value="'.$buttons['reship'].'" '.$reship_style.'/></li>';
        }
            
        $html .= $html_group_end;
    
        $html .= $html_group_start;
        
        $html .= '<li><input type="button" onclick="W.page(\'index.php?app=b2c&ctl=admin_order&act=docancel&p[0]='.$order_id.'\', {onComplete:function(){window.finderGroup[\''.$_GET['_finder']['finder_id'].'\'].refresh().delay(400);}})" value="'.$buttons['cancel'].'" '.$cancel_style.'/></li>';
        
        $html .= $html_group_end;
        
        return $html;
    }
}
