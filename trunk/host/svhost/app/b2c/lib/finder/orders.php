<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_finder_orders{

    var $detail_basic = '基本信息';
    var $detail_items = '商品';
    var $detail_bills = '收退款记录';
    var $detail_delivery = '收发货记录';
    var $detail_pmt = '优惠方案';
    var $detail_mark = '订单备注';
    var $detail_logs = '订单日志';
    var $detail_msg = '顾客留言';
    var $column_editbutton = '操作';
    
    public function __construct($app)
    {
        $this->app = $app;
        $this->app_ectools = app::get('ectools');
    }
    
    public function detail_basic($order_id)
    {
        $render = $this->app->render();
        $order = $this->app->model('orders');
        $payments = app::get('ectools')->model('payments');
        
        if (substr($_POST['orderact'], 0, 3) == 'pay')
        {
            $sdf['order_id'] = $_POST['order_id'];
            $sdf['payment_id'] = $payments->gen_id();
            $sdf['bill_type'] = 'pay';
            $payments->save($sdf);
        }
        elseif (substr($_POST['orderact'], 0, 6) == 'refund')
        {
            $sdf['order_id'] = $_POST['order_id'];
            $sdf['payment_id'] = $payments->gen_id();
            $sdf['bill_type'] = 'refund';
            $payments->save($sdf);
        }
        elseif (substr($_POST['orderact'], 0, 7) == 'consign')
        {
            $delivery = $this->app->model('delivery');
            $sdf['delivery_id'] = $delivery->gen_id('delivery');
            $sdf['order_id'] = $_POST['order_id'];
            $sdf['bill_type'] = 'delivery';
            $delivery->save($sdf);
        }
        elseif (substr($_POST['orderact'], 0, 6) == 'return')
        {
            $delivery = $this->app->model('delivery');
            $sdf['delivery_id'] = $delivery->gen_id('return');
            $sdf['order_id'] = $_POST['order_id'];
            $sdf['bill_type'] = 'return';
            $delivery->save($sdf);
        }
        
        $subsdf = array('order_pmt'=>array('*'),'order_objects'=>array('*',array('order_items'=>array('*',array(':products'=>'*')))));
        $aOrder = $order->dump($order_id, '*', $subsdf);

        $oCur = $this->app_ectools->model('currency');
        $aCur = $oCur->getSysCur();
        $aOrder['cur_name'] = $aCur[$aOrder['currency']];
    
        if (intval($aOrder['payinfo']['pay_app_id']) < 0)
            $aOrder['payinfo']['pay_app_id'] = '货到付款';
        else
        {  
            $payid = $aOrder['payinfo']['pay_app_id'];
            $obj_paymentsfg = app::get('ectools')->model('payment_cfgs');
            $arr_payments = $obj_paymentsfg->getPaymentInfo($payid);
            $aOrder['payinfo']['pay_app_id'] = $arr_payments['app_name'] ? $arr_payments['app_name'] : $payid;
        }
    
        if ($aOrder['member_id'])
        {
            $member = $this->app->model('members');
            $aOrder['member'] = $member->dump($aOrder['member_id'], '*', array(':account@pam'=>'*'));
            
            // 得到meta的信息
            $arrTree = array();
            $index = 0;
            if ($aOrder['member']['contact'])
            {
                if ($aOrder['member']['contact']['qq'])
                    $arrTree[$index++] = array(
                        'attr_name' => '腾讯QQ',
                        'attr_tyname' => 'QQ',
                        'value' => $aOrder['member']['contact']['qq'],
                    );
                
                if ($aOrder['member']['contact']['msn'])
                    $arrTree[$index++] = array(
                        'attr_name' => 'windows live',
                        'attr_tyname' => 'MSN',
                        'value' => $aOrder['member']['contact']['msn'],
                    );
                
                if ($aOrder['member']['contact']['wangwang'])
                    $arrTree[$index++] = array(
                        'attr_name' => 'WangWang',
                        'attr_tyname' => '旺旺',
                        'value' => $aOrder['member']['contact']['wangwang'],
                    );
                
                if ($aOrder['member']['contact']['skype'])
                    $arrTree[$index++] = array(
                        'attr_name' => 'Skype',
                        'attr_tyname' => 'Skype',
                        'value' => $aOrder['member']['contact']['skype'],
                    );
                
                $render->pagedata['tree'] = $arrTree;
            }
        }
    
        foreach ((array)$aItems as $k => $rows)
        {
            $aItems[$k]['addon'] = unserialize($rows['addon']);
            if ($rows['minfo'] && unserialize($rows['minfo']))
            {
                $aItems[$k]['minfo'] = unserialize($rows['minfo']);
            }
            else 
            {
                $aItems[$k]['minfo'] = array();
            }
            if($aItems[$k]['addon']['adjname']) $aItems[$k]['name'] .= __('<br>配件：').$aItems[$k]['addon']['adjname'];
        }
        $render->pagedata['goodsItems'] = $aItems;
        $render->pagedata['giftItems'] = $gItems;
    
        $aOrder['discount'] = 0 - $aOrder['discount'];
        $render->pagedata['order'] = $aOrder;
        //+todo license权限----------
    //    $_is_all_ship = 1;
    //    $_is_all_return_ship = 1;
    
        foreach ((array)$aItems as $_item)
        {
            if((!$_item['supplier_id']) && ($_item['sendnum'] < $_item['nums'] )){
                $_is_all_ship = 0;
            }
            if((!$_item['supplier_id']) && ($_item['sendnum'] > 0 )){
                $_is_all_return_ship = 0;
            }
        }
        
        foreach((array)$gItems as $g_item){
            if($g_item['sendnum'] < $g_item['nums'] ){
                $_is_all_ship = 0;
            }
            if($g_item['sendnum'] > 0 ){
                $_is_all_return_ship = 0;
            }
        }
        $render->pagedata['order']['_is_all_ship'] = $_is_all_ship;
        $render->pagedata['order']['_is_all_return_ship'] = $_is_all_return_ship;
        $render->pagedata['order']['flow']= array('refund' => $this->app->getConf('order.flow.refund'),
            'consign' => $this->app->getConf('order.flow.consign'),
            'reship' => $this->app->getConf('order.flow.reship'),
            'payed' => $this->app->getConf('order.flow.payed'));
            
        if (!$render->pagedata['order']['member']['contact']['area'])
        {
            $render->pagedata['order']['member']['contact']['area'] = '';
        }
        else
        {
            if (strpos($render->pagedata['order']['member']['contact']['area'], ':') !== false)
            {
                $arr_areas = explode(':', $render->pagedata['order']['member']['contact']['area']);
                $render->pagedata['order']['member']['contact']['area'] = $arr_areas[1];
            }
        }
        
        if (strpos($render->pagedata['order']['consignee']['area'], ':') !== false)
        {
            $arr_areas = explode(':', $render->pagedata['order']['consignee']['area']);
            $render->pagedata['order']['consignee']['area'] = $arr_areas[1];
        }
        
        $objMath = kernel::single('ectools_math');
        $render->pagedata['order']['pmt_amount'] = $objMath->number_plus(array($render->pagedata['order']['pmt_goods'],$render->pagedata['order']['pmt_order']));
        if ($render->pagedata['order']['pmt_amount'] > 0)
        {
            if (isset($aOrder['order_pmt']) && $aOrder['order_pmt'])
            {
                foreach ($aOrder['order_pmt'] as $arr_pmts)
                {
                    if ($arr_pmts['pmt_type'])
                    {
                        switch ($arr_pmts['pmt_type'])
                        {
                            case 'order':
                            case 'coupon':
                                $obj_save_rules = $this->app->model('sales_rule_order');
                                break;
                            case 'goods':
                                $obj_save_rules = $this->app->model('sales_rule_goods');
                                break;
                            default:
                                break;
                        }
                    }
                    
                    $arr_save_rules = $obj_save_rules->dump($arr_pmts['pmt_id']);
                    $render->pagedata['order']['use_pmt'] .= $arr_save_rules['name'] . ', ';
                }
                
                if (strpos($render->pagedata['order']['use_pmt'], ', ') !== false)
                    $render->pagedata['order']['use_pmt'] = substr($render->pagedata['order']['use_pmt'], 0, strlen($render->pagedata['order']['use_pmt']) - 2);
            }
        }
        
        // 判断是否使用了推广服务
        $is_bklinks = 'false';
        $obj_input_helpers = kernel::servicelist("html_input");
        if (isset($obj_input_helpers) && $obj_input_helpers)
        {
            foreach ($obj_input_helpers as $obj_bdlink_input_helper)
            {
                if (get_class($obj_bdlink_input_helper) == 'bdlink_input_helper')
                {
                    $is_bklinks = 'true';
                }
            }
        }
        $render->pagedata['is_bklinks'] = $is_bklinks;
        
        // 得到订单的优惠方案
        $arr_pmt_lists = array();
        $arr_order_items = array();
        $arr_gift_items = array();
        
        $this->get_pmt_lists($aOrder, $arr_pmt_lists);
        $this->get_goods_detail($aOrder, $arr_order_items, $arr_gift_items);
        
        $render->pagedata['goodsItems'] = $arr_order_items;
        $render->pagedata['giftItems'] = $arr_gift_items;
        $render->pagedata['order']['pmt_list'] = $arr_pmt_lists;        
        $render->pagedata['html_button'] = kernel::service('b2c_order.b2c_finder_orders')->get_button_html($order_id);
        return $render->fetch('admin/order/order_detail.html');
    }
    
    public function detail_items($order_id)
    {
        $render = app::get('base')->render();
        $order = $this->app->model('orders');
        $render->pagedata['orderid'] = $orderid;        
        
        $subSdf = array('order_objects'=>array('*',array('order_items'=>array('*',array(':products'=>'*')))));
        $aItems = $order->dump($order_id,'*',$subSdf);
        
        $order_items = array();
        $gift_items = array();
        $this->get_goods_detail($aItems, $order_items, $gift_items);
        
        //$order_items = array_merge($order_items,$gift_items);
        $render->pagedata['goodsItems'] = $order_items;
        $render->pagedata['giftItems'] = $gift_items;
        $render->pagedata['site_base_url'] = kernel::base_url();
        
        return $render->fetch('admin/order/order_items.html',$this->app->app_id);
    }
    
    private function get_goods_detail(&$aItems, &$order_items, &$gift_items)
    {
        $order_items = array();
        $objMath = kernel::single("ectools_math");
        
        if ($aItems['order_objects'])
        {
            $app_gift = app::get('gift');
            $gift_is_installed = false;
            if ($app_gift->is_installed())
            {
                $gift_is_installed = true;
                $objGiftGoods = $app_gift->model('goods');
            }
            $objGoods = $this->app->model('goods');
                    
            foreach ($aItems['order_objects'] as $k=>$v)
            {
                $index = 0;
                $index_adj = 0;
                $index_gift = 0;
                if ($v['obj_type'] == 'goods')
                {                    
                    foreach($v['order_items'] as $key => $item)
                    {  
                        $arrGoods = $objGoods->dump($item['goods_id'], 'goods_id,cat_id,score,price,name,udfimg,thumbnail_pic,small_pic,big_pic,image_default_id');
                        $objGoodsCat = $this->app->model('goods_cat');
                        $arrGoodsCat = $objGoodsCat->dump($arrGoods['category']['cat_id'], 'cat_name');
                           
                        if ($item['item_type'] != 'gift')
                        {
                            if($item['addon'] && unserialize($item['addon'])){
                                $gItems[$k]['minfo'] = unserialize($item['addon']);
                            }else{
                                $gItems[$k]['minfo'] = array();
                            }
                            
                            if ($item['item_type'] == 'product')
                            {  
                                $order_items[$k] = $item;
                                $order_items[$k]['small_pic'] = $arrGoods['image_default_id'];
                                $order_items[$k]['is_type'] = $v['obj_type'];
                                $order_items[$k]['item_type'] = $arrGoodsCat['cat_name'];
                                $order_items[$k]['nums'] = $item['quantity'];
                                $order_items[$k]['minfo'] = $gItems[$k]['minfo'];
                                
                                if (isset($item['products']['spec_info']) && $item['products']['spec_info'])
                                {
                                    $order_items[$k]['name'] = $item['products']['name'] . '(' . $item['products']['spec_info'] . ')';
                                }
                                else
                                {
                                    $order_items[$k]['name'] = $item['products']['name'];
                                }
                                
                                $order_items[$k]['link'] = app::get('site')->router()->gen_url(array('app' => 'b2c', 'ctl' => 'site_product', 'act' => 'index', 'arg0' => $item['goods_id']));
                            }
                            else
                            {
                                $order_items[$k]['adjunct'][$index_adj] = $item;
                                $order_items[$k]['adjunct'][$index_adj]['small_pic'] = $arrGoods['image_default_id'];
                                $order_items[$k]['adjunct'][$index_adj]['is_type'] = $v['obj_type'];
                                $order_items[$k]['adjunct'][$index_adj]['item_type'] = $arrGoodsCat['cat_name'];
                                $order_items[$k]['adjunct'][$index_adj]['nums'] = $item['quantity'];
                                
                                if (isset($item['products']['spec_info']) && $item['products']['spec_info'])
                                {
                                    $order_items[$k]['adjunct'][$index_adj]['name'] = $item['products']['name'] . '(' . $item['products']['spec_info'] . ')';
                                }
                                else
                                    $order_items[$k]['adjunct'][$index_adj]['name'] = $item['products']['name'];
                                $order_items[$k]['adjunct'][$index_adj]['link'] = app::get('site')->router()->gen_url(array('app' => 'b2c', 'ctl' => 'site_product', 'act' => 'index', 'arg0' => $item['goods_id']));
                                
                                $index_adj++;
                            }
                        }
                        else
                        {
                            $arrGoods = $objGiftGoods->dump($item['goods_id'], '*');
                            
                            $order_items[$k]['gifts'][$index_gift] = $item;
                            $order_items[$k]['gifts'][$index_gift]['small_pic'] = $arrGoods['image_default_id'];
                            $order_items[$k]['gifts'][$index_gift]['is_type'] = $v['obj_type'];
                            $order_items[$k]['gifts'][$index_gift]['item_type'] = $arrGoods['category']['cat_name'];
                            $order_items[$k]['gifts'][$index_gift]['nums'] = $item['quantity'];
                            
                            if (isset($item['products']['spec_info']) && $item['products']['spec_info'])
                            {
                                $order_items[$k]['gifts'][$index_gift]['name'] = $item['products']['name'] . '(' . $item['products']['spec_info'] . ')';
                            }
                            else
                                $order_items[$k]['gifts'][$index_gift]['name'] = $item['name'];
                            $order_items[$k]['gifts'][$index_gift]['link'] = app::get('site')->router()->gen_url(array('app' => 'gift', 'ctl' => 'site_gift', 'act' => 'index', 'arg0' => $item['goods_id']));
                            
                            $index_gift++;
                        }
                    }
                }
                else
                {
                    foreach ($v['order_items'] as $gift_key => $gift_item)
                    {
                        if (isset($gift_items[$gift_item['goods_id']]) && $gift_items[$gift_item['goods_id']])
                            $gift_items[$gift_item['goods_id']]['nums'] = $objMath->number_plus(array($gift_items[$gift_item['goods_id']]['nums'], $item['quantity']));
                        else
                        {
                            $arrGoods = $objGiftGoods->dump($gift_item['goods_id'], '*');
                            
                            $gift_items[$gift_item['goods_id']] = array(
                                'goods_id' => $gift_item['goods_id'],
                                'bn' => $gift_item['bn'],
                                'nums' => $gift_item['quantity'],
                                'name' => $gift_item['name'],
                                'item_type' => $arrGoods['category']['cat_name'],
                                'price' => $gift_item['price'],
                                'quantity' => $gift_item['quantity'],
                                'sendnum' => $gift_item['sendnum'],
                                'small_pic' => $arrGoods['image_default_id'],
                                'is_type' => $v['obj_type'],
                                'link' => app::get('site')->router()->gen_url(array('app' => 'gift', 'ctl' => 'site_gift', 'act' => 'index', 'arg0' => $gift_item['goods_id'])),
                            );
                        }
                    }
                }
            }
        }
        
        return true;
    }
    
    public function detail_bills($order_id)
    {
        $render = $this->app->render();
        $payments = app::get('ectools')->model('payments');
        $refunds = app::get('ectools')->model('refunds');
        $order_bills = app::get('ectools')->model('order_bills');
        $aBill = $order_bills->getList('*',array('rel_id'=>$order_id, 'bill_type'=>'payments', 'pay_object'=>'order'));
        $aRefund = $order_bills->getList('*',array('rel_id'=>$order_id, 'bill_type'=>'refunds', 'pay_object'=>'order'));
        
        if ($aBill)
        {
            foreach ($aBill as &$str_bill)
            {
                $arr_payments = $payments->dump($str_bill['bill_id'], 'pay_name,status');
                $str_bill['pay_name'] = $arr_payments['pay_name'];
                $str_bill['status'] = $arr_payments['status'];
            }
        }
        
        if ($aRefund)
        {
            foreach ($aRefund as &$str_refund)
            {
                $arr_refunds = $refunds->dump($str_refund['bill_id'], 'pay_name,status');
                $str_refund['pay_name'] = $arr_refunds['pay_name'];
                $str_refund['status'] = $arr_refunds['status'];
            }
        }
        
        $render->pagedata['bills'] = $aBill;
        $render->pagedata['refunds'] = $aRefund;
        $render->pagedata['orderid'] = $order_id;

        return $render->fetch('admin/order/od_bill.html',$this->app->app_id);
    }
    
    public function detail_delivery($order_id){
        $render = $this->app->render();

        $objDelivery = $this->app->model('delivery');
        $objReship = $this->app->model('reship');
        $filter['order_id'] = $order_id;
        $render->pagedata['consign'] = $objDelivery->getList('*',$filter);
        if ($render->pagedata['consign'])
        {
            foreach ($render->pagedata['consign'] as &$arr_delivery)
            {
                $obj_dlytype = $this->app->model('dlytype');
                $arr = $obj_dlytype->dump($arr_delivery['delivery'], 'dt_name');
                $arr_delivery['delivery'] = $arr['dt_name'];
            }
        }
        $render->pagedata['reship'] =  $objReship->getList('*',$filter);
        $render->pagedata['orderid'] = $order_id;
        
        return $render->fetch('admin/order/od_delivery.html',$this->app->app_id);
    }
    
    public function detail_pmt($order_id)
    {
        $render = $this->app->render();
        
        $order = $this->app->model('orders');
        $subsdf = array('order_pmt'=>array('*'));
        $sdf_order = $order->dump($order_id, '*', $subsdf);
        $arr_pmt_lists = array();
        
        $this->get_pmt_lists($sdf_order, $arr_pmt_lists);
        
        $render->pagedata['pmtlist'] = $arr_pmt_lists;
        
        return $render->fetch('admin/order/od_pmts.html',$this->app->app_id);
    }
    
    private function get_pmt_lists(&$sdf_order, &$arr_pmt_lists)
    {
        $arr_pmt_lists = array();
        
        if (isset($sdf_order['order_pmt']) && $sdf_order['order_pmt'])
        {
            foreach ($sdf_order['order_pmt'] as $arr_pmt_items)
            {
                $arr_pmt_lists[] = array(
                    'pmt_describe' => $arr_pmt_items['pmt_describe'],
                    'pmt_amount' => $arr_pmt_items['pmt_amount'],
                );
            }
        }
        
        return true;
    }
    
    public function detail_mark($order_id)
    {
        $render = $this->app->render();
        $order = $this->app->model('orders');

        $aOrder = $order->dump($order_id,'mark_text,mark_type');
        $render->pagedata['mark_text'] = $aOrder['mark_text'];
        $render->pagedata['mark_type'] = $aOrder['mark_type'];
        $render->pagedata['orderid'] = $order_id;
        $render->pagedata['res_url'] = $this->app->res_url;

        return $render->fetch('admin/order/od_mark.html',$this->app->app_id);
    }
    
    public function detail_logs($order_id){
        $render = app::get('base')->render();
        $order = $this->app->model('orders');
        $aOrder = $order->dump($order_id);
    
        $order = &$this->app->model('orders');
        
        $page = ($_GET['page']) ? $_GET['page'] : 1;
        $pageLimit = 30;
        $aLog = $order->getOrderLogList($order_id, $page-1, $pageLimit);
        $render->pagedata['logs'] = $aLog;
        $render->pagedata['result'] = array('SUCCESS'=>__('成功'),'FAILURE'=>__('失败'));
        $pager = array(
            'current'=> $page,
            'total'=> ceil($aLog['page']),
            'link'=> 'javascript:W.page(\'index.php?app=b2cctl=admin_order&act=detail_logs&p[0]='.$orderid.'&p[1]=_PPP_\', {update:$E(\'.tableform\').parentNode, method:\'post\'});',
            'token'=> '_PPP_'
        );
        $render->pagedata['pager'] = $pager;
        $render->pagedata['pagestart'] = ($page-1)*$pageLimit;

        return $render->fetch('admin/order/order_logs.html',$this->app->app_id);
    }
    
    /**
     * 顾客留言
     * @params string order id
     * @return null
     */
    public function detail_msg($order_id)
    {
        $render = $this->app->render();
        $order = $this->app->model('orders');
        $objMath = kernel::single("ectools_math");

        $oMsg = &kernel::single("b2c_message_order");
        $orderMsg = $oMsg->getList('*', array('order_id' => $order_id, 'object_type' => 'order'), $offset=0, $limit=-1, 'time DESC');
        
        //$oMsg->sethasreaded($orderid);
        //$aItems = $order->getItemList($orderid);
        $subsdf = array('order_objects'=>array('*',array('order_items'=>array('*',array(':products'=>'*')))));
        $sdf_order = $order->dump($order_id,'*',$subsdf);
        
        $order_items = array();
        $gift_items = array();
        $this->get_goods_detail($sdf_order, $order_items, $gift_items);
        
        $render->pagedata['ordermsg'] = $orderMsg;
        $render->pagedata['goodsItems'] = $order_items;
        $render->pagedata['giftsItems'] = $gift_items;
        $render->pagedata['orderid'] = $order_id;
        $render->pagedata['site_base_url'] = kernel::base_url();

       return $render->fetch('admin/order/od_msg.html',$this->app->app_id);
    }
    
    public function column_editbutton($row)
    {
        $obj_order = $this->app->model('orders');
        $arr_order = $obj_order->dump($row['order_id']);
        
        if ($arr_order['pay_status'] == '0' && !$arr_order['ship_status'] && $arr_order['status'] == 'active')
        {
            return '<a href="index.php?app=b2c&ctl=admin_order&act=showEdit&p[0]='.$row['order_id'].'&finder_id=' . $_GET['_finder']['finder_id'] . '" target="_blank">编辑</a>';
        }
        
        return '';
    }
    
    public $column_printer = '打印';
    public function column_printer($row)
    {
        $res_url = $this->app->res_url;
        $html = '<div class="cell">
            <img height="16px" width="16px" style="margin: 3px 0pt 0pt 2px;" src="' . $res_url . '/bundle/print-icon.gif">';
        $html.='<a href="index.php?app=b2c&ctl=admin_order&act=printing&p[0]=1&p[1]=' . $row['order_id'] . '" title="快递单打印" target="_blank">购</a>
                            <a href="index.php?app=b2c&ctl=admin_order&act=printing&p[0]=2&p[1]=' . $row['order_id'] . '" title="快递单打印" target="_blank">配</a>
                              <a href="index.php?app=b2c&ctl=admin_order&act=printing&p[0]=4&p[1]=' . $row['order_id'] . '" title="快递单打印" target="_blank">合</a>
                            ';
         $app_express = app::get('express');
        if (isset($app_express) && $app_express && is_object($app_express))
        {
            if ($app_express->is_installed())
            {                        
                $html.='<a href="index.php?app=b2c&ctl=admin_order&act=printing&p[0]=8&p[1]=' . $row['order_id'] . '" title="快递单打印" target="_blank">递</a></div>';
                                   
            }
            else
            {
                ;
            }
        } 
            return $html;
    }
}
