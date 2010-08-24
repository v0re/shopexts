<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class b2c_order_checkorder{
    
    public function __construct($app)
    {
        $this->app = $app;
    }
    
    //订单处理的6个流程之一:支付
    public function check_order_pay($order_id,$sdf_post=array(),&$msg)
    {
        $order = $this->app->model('orders');
        $sdf_order = $order->dump($order_id,'*');

        //当前处理流程的状态开关
        if (!$this->checkstatus($order_id, $action='pay', $sdf_order, $msg))
        {
            return false;
        }


        $nonPay = $sdf_order['total_amount'] - $sdf_order['payed'];
        if (isset($sdf_post['money']))
        {//支付金额是从弹出的支付单里输入而来
            if (floatval($sdf_post['money'])>$nonPay || floatval($sdf_post['money']) <= 0)
            {//输入金额不是大就是小
                $msg = __('支付失败：支付总金额不在订单金额范围');
                return false;
            }
            
            $payMoney = floatval($sdf_post['money']);
            $pay_type = $sdf_post['payment'];
        }
        else
        {
            $payMoney = $nonPay;
            $pay_type = $sdf_order['payment'];
        }

        if ($pay_type=='deposit')
        {
            if (!$sdf_order['member_id'])
            {
                $msg = "查询预存款账户失败！";
                return false;
            }
            else
            {
                //支付通过预存款，需检查预存款是否足够
                $obj_advance = $this->app->model('member_advance');
                if (!$obj_advance->check_account($sdf_order['member_id'],$msg,$sdf_post['money']))
                {
                    return false;
                }
            }            
        }
        return true;
    }
    
    //订单处理的6个流程之二:发货
    public function check_order_delivery($order_id,$sdf_post=array(),&$msg)
    {
        $order = $this->app->model('orders');
        $sdf_order = $order->dump($order_id,'*');
        $subsdf = array('order_objects'=>array('*',array('order_items'=>array('*',array(':products'=>'*')))));
        $sdf_order = $order->dump($order_id,'*',$subsdf);

        //当前处理流程的状态开关
        if (!$this->checkstatus($order_id, 'delivery', $sdf_order, $msg))
        {
            return false;
        }

        $order_items = array();
        foreach ($sdf_order['order_objects'] as $k=>$v)
        {
            $order_items = array_merge($order_items,$v['order_items']);
        }
        
        foreach ($order_items as $key=>$dinfo)
        {
            if (floor($sdf_post['send'][$dinfo['item_id']]) > 0)
            {
                if ($sdf_post['send'][$dinfo['item_id']] > $dinfo['quantity'] - $dinfo['sendnum'])
                {
                    $msg .= __('商品：').$dinfo['name'].__('发货超出购买量');
                    return false;
                }
            }
        }
        
        return true;
    }
    
    //订单处理的6个流程之三:完成
    public function check_order_finish($order_id,$sdf_post,&$msg)
    {
        $order = $this->app->model('orders');
        $sdf_order = $order->dump($order_id,'*');
        if(!$this->checkstatus($order_id, 'finish', $sdf_order, $msg)){
            return false;
        }
        return true;
    }
    
    //订单处理的6个流程之四:退款
    public function check_order_refund($order_id,$sdf_post,&$msg)
    {
        $order = $this->app->model('orders');
        $sdf_order = $order->dump($order_id,'*');

        if($sdf_post['money']){//退款金额是从弹出的退款单里输入而来
        
            if($sdf_post['money']>$sdf_order['payed']){
                  $msg = __('退款失败：退款金额不再范围之内');
                  return false;
            }
        }

        return true;
    }
    
    //订单处理的6个流程之四:退货
    public function check_order_reship($order_id,$sdf_post,&$msg)
    {
        $order = $this->app->model('orders');
        $subsdf = array('order_objects'=>array('*',array('order_items'=>array('*',array(':products'=>'*')))));
        $sdf_order = $order->dump($order_id, '*', $subsdf);
        
        if (!$this->checkstatus($order_id, 'reship', $sdf_order, $msg))
        {
            return false;
        }


        $order_items = array();
        foreach ($sdf_order['order_objects'] as $k=>$v)
        {
            $order_items = array_merge($order_items,$v['order_items']);
        }

        foreach ($order_items as $key=>$dinfo)
        {
            if ($sdf_post['send'][$dinfo['item_id']] > 0)
            {
                if ($sdf_post['send'][$dinfo['item_id']] > $dinfo['sendnum'])
                {
                    $msg .= __('商品：').$dinfo['name'].__('退货量超出发发货量');
                    return false;
                }
            }
        }

        return true;
    }
    
    //订单处理的6个流程之六:作废
    public function check_order_cancel($order_id,$sdf_post,&$msg)
    {

        $order = $this->app->model('orders');
        $sdf_order = $order->dump($order_id,'*');

        if(!$this->checkstatus($order_id, 'cancel', $sdf_order, $msg)){
            return false;
        }

        return true;
    }
    
    /**
     * 判断是否需要要冻结还是解冻库存
     * @params string 操作行为
     * @params string order id
     * @return array 例子array('freez' => true, 'unfreez' => false, 'store' => false, 'unstore' => false)
     */
    public function checkOrderFreez($operation='pay', $order_id)
    {
        $store_mark = $this->app->getConf('system.goods.freez.time');
        $objOrders = $this->app->model('orders');
        $sdf_orders = $objOrders->dump($order_id, 'order_id,status,pay_status,ship_status');
        
        switch ($operation)
        {
            case 'order':
                // 下单的时候
                if ($store_mark == '1')
                {
                    // 需要冻结库存
                    return array(
                        'freez' => true,
                        'unfreez' => false,
                        'store' => false,
                        'unstore' => false,
                    );
                }
                else
                {
                    // 无需冻结库存
                }
                break;
            case 'pay':
                if ($store_mark == '2')
                {
                    // 需要冻结库存
                    return array(
                        'freez' => true,
                        'unfreez' => false,
                        'store' => false,
                        'unstore' => false,
                    );
                }
                else
                {
                    // 无需冻结库存
                }
                break;
            case 'delivery':
                if ($sdf_orders['pay_status'] == '1' || $sdf_orders['pay_status'] == '5')
                {
                    if ($sdf_orders['ship_status'] == '3' || $sdf_orders['ship_status'] == '4')
                        return array(
                            'freez' => false,
                            'unfreez' => false,
                            'store' => true,
                            'unstore' => false,
                        );
                    else
                        return array(
                            'freez' => false,
                            'unfreez' => true,
                            'store' => true,
                            'unstore' => false,
                        );
                }
                else
                {
                    if ($store_mark == 2)
                        return array(
                            'freez' => false,
                            'unfreez' => false,
                            'store' => true,
                            'unstore' => false,
                        );
                    else
                        return array(
                            'freez' => false,
                            'unfreez' => true,
                            'store' => true,
                            'unstore' => false,
                        );
                }
                break;
            case 'finish':
                break;
            case 'refund':
                if ($sdf_orders['ship_status'] == '1' || $sdf_orders['ship_status'] == '2')
                {
                    return array(
                        'freez' => false,
                        'unfreez' => false,
                        'store' => false,
                        'unstore' => false,
                    );
                }
                else
                {
                    return array(
                        'freez' => false,
                        'unfreez' => true,
                        'store' => false,
                        'unstore' => false,
                    );
                }
                break;
            case 'reship':
                return array(
                    'freez' => false,
                    'unfreez' => false,
                    'store' => false,
                    'unstore' => true,
                );
                break;
            default:// cancel.
                if ($store_mark == '2')
                {
                    // 无需任何操作
                    return array(
                        'freez' => false,
                        'unfreez' => false,
                        'store' => false,
                        'unstore' => false,
                    );
                }
                else
                {
                    // 需要解冻
                    return array(
                        'freez' => false,
                        'unfreez' => true,
                        'store' => false,
                        'unstore' => false,
                    );
                }
                break;
        }
    }
    
    /** 
     * 检查订单的当前状态
     * @params string order id
     * @params string 处理订单的动作
     * @params array 订单标准数据
     * @params string message
     */
    public function checkstatus($order_id, $action='pay', $sdf_order='', &$msg)
    {
        if (!$sdf_order)
        {
            $sdf_order = $this->app->model('orders')->dump($order_id, 'status, pay_status, ship_status');
        }
        
        switch ($action)
        {
            case 'pay':
                if ($sdf_order['status'] != 'active')
                {
                    $msg = __('订单状态锁定，不能支付！');
                    return false;
                }
                if ($sdf_order['pay_status'] > 0 && $sdf_order['pay_status'] != 3)
                {
                    $msg = __('订单已支付，不能重复支付！');
                    return false;
                }
            break;
            case 'refund':
                if ($sdf_order['status'] != 'active')
                {
                    $msg = __('订单状态锁定，不能退款！');
                    return false;
                }
                
                if ($sdf_order['pay_status'] == 0)
                {
                    $msg = __('订单未支付，不能退款！');
                    return false;
                }
                
                if ($sdf_order['pay_status'] == 5)
                {
                    $msg = __('订单已退款，不能重复退款！');
                    return false;
                }
            break;
            case 'delivery':
                if ($sdf_order['status'] != 'active')
                {
                    $msg = __('订单状态锁定，不能发货！');
                    return false;
                }
            break;
            case 'reship':
                if ($sdf_order['status'] != 'active')
                {
                    $msg = __('订单状态锁定，不能退货！');
                    return false;
                }
                
                if ($sdf_order['ship_status'] == 0)
                {
                    $msg = __('订单未发货，不能退货！');
                    return false;
                }
                
                if ($sdf_order['ship_status'] == 4)
                {
                    $msg = __('订单已退货，不能重复退货！');
                    return false;
                }
            break;
            case 'cancel':
                if ($sdf_order['status'] != 'active')
                {
                    $msg = __('订单状态锁定，不能取消！');
                    return false;
                }
                
                if ($sdf_order['pay_status'] > 0 || $sdf_order['ship_status'] > 0)
                {
                    $msg = __('订单已进入流程，不能取消！');
                    return false;
                }
            case 'finish':
                if ($sdf_order['status'] != 'active')
                {
                    $msg = __('订单状态锁定，不能归档！');
                    return false;
                }
            case '':
                ;
            break;
            case 'delete':
                if ($sdf_order["status"] == "finish")
                {
                    $msg = "此订单已经处于激活状态，不能删除了！";
                    return false;
                }
            break;
        }
        return true;
    }

    function check_basic(&$order_mdl, &$sdf, &$message)
    {
        if(!$sdf['order_id']){
            $sdf['order_id'] = $order_mdl->gen_id();
        }
    //todo 调用currency class
    //    $oCur = &$order_mdl->system->loadModel('currency');
    //    $currency = $oCur->instance($sdf['currency']);
        $sdf['cur_rate'] = ($currency['cur_rate']>0 ? $currency['cur_rate']:1);
        
        $sdf['createtime'] = time();
        $sdf['last_modified'] = time();
        $sdf['ip'] = getenv('REMOTE_ADDR');
        
    /*    if($sdf['is_tax'] && $order_mdl->system->getConf('site.trigger_tax')){
            $sdf['is_tax'] = 'true';
            $sdf['cost_tax'] = $sdf['total_amount'] * $order_mdl->system->getConf('site.tax_ratio');
            $sdf['total_amount'] += $sdf['cost_tax'];
        }
        */
        $newNum = $order_mdl->getOrderDecimal($sdf['total_amount']);
        $sdf['discount'] = floatval($sdf['total_amount'] - $newNum);
        $sdf['total_amount'] = $newNum;
        
        $sdf['cur_amount'] = $sdf['total_amount'] * $sdf['cur_rate'];
        
        if ($sdf['payinfo']!="-1"){
            //----检测该支付方式是否还有子选项，如快钱选择银行
    //        $payment=$order_mdl->system->loadModel('trading/payment');
    //        $payment->recgextend($data,$postInfo,$extendInfo);
            $sdf['extend']=serialize($extendInfo);
            //------------------------------------------------
        }
    
    //    getRefer($sdf);    //推荐下单
        
        return true;
    }
    
    function check_delivery(&$order_mdl,&$sdf,&$message)
    {
        if($sdf['is_delivery'] == 'Y'){
            if(!$sdf['shipping']['shipping_id']){
                $message[] = '没有选择配送方式';
                return false;
            }
    
            if(trim($sdf['consignee']['name']) == ''
                || trim($sdf['consignee']['area']) == ''
                || (trim($sdf['consignee']['telephone']) == '' && trim($sdf['consignee']['mobile']) == '')
                || trim($sdf['consignee']['addr']) == ''){
                $message[] = '配送信息未填写完整';
                return false;
            }
    
            //todo 根据配送地区技术用费价格；
            $sdf['shipping']['cost_shipping'] = $sdf['shipping']['cost_shipping'];
            if($delivery[$sdf['shipping']['shipping_id']]){
                //todo 当前配送地区是否支持选择的支付方式
                $message[] = '当前地区不支持该配送方式';
                return false;
            }
            
            return true;
        }else{
            return true;
        }
    }
    
    function check_goods(&$order_mdl,$sdf,&$message)
    {
//    $oCart = &$order_mdl->system->loadModel('trading/cart');
//    $oCart->check_objects();    //确认购物车库存 get_items_quantity

    return true;
    }
    
    function check_payment(&$order_mdl,&$sdf,&$message)
    {
        if(!$sdf['payinfo']['pay_app_id']){
            $message[] = __("提交不成功，未选择支付方式!");
        }
        
        if(!$sdf['member_id'] && $sdf['payinfo']['pay_app_id'] == 'DEPOSIT'){
            $message[] = __("未登录用户不允许预存款支付");
            return false;
        }else{
            //todo get_plugin_info(); $config;
            if(isset($config[$sdf['currency']])){
                $message[] = __("支付方式不支持当前币别支付");
                return false;
            }
    //        $sdf['payinfo']['cost_payment'] = $config['fee'] * $sdf['total_amount'];
            $sdf['payinfo']['pay_name'] = $sdf['payinfo']['pay_name'];
            
    //        $sdf['total_amount'] += $sdf['payinfo']['cost_payment'];
            return true;
        }
    }
    
    function check_point(&$order_mdl,&$sdf,&$message)
    {
        if($sdf['score_u']){
//            $oMemberPoint = $this->app->model('memberPoint');
//            $member_score = $oMemberPoint->getMemberPoint($sdf['member_id']);
            if($sdf['score_u'] > $member_score){
                $message = __('用户积分不足');
                return false;
            }
        }
        
        return true;
    }
    
}
