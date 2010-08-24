<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class b2c_order_pay extends b2c_api_rpc_request
{    
    /**
     * 公开构造方法
     * @params app object
     * @return null
     */
    public function __construct($app)
    {        
        parent::__construct($app);
    }
    
    /**
     * 最终的克隆方法，禁止克隆本类实例，克隆是抛出异常。
     * @params null
     * @return null
     */
    final public function __clone()
    {
        trigger_error("此类对象不能被克隆！", E_USER_ERROR);
    }
    
    /**
     * 订单支付后的处理
     * @params array 支付完的信息
     * @params 支付时候成功的信息
     */
    public function pay_finish(&$sdf, $status='succ')
    {
        // redirect to payment list page.
        $arrOrderbillls = $sdf['orders'];
        $is_success = true;
        $str_op_id = "";
        $str_op_name =  "";
        $objMath = kernel::single('ectools_math');
        
        foreach ($arrOrderbillls as $rel_id=>$objOrderbills)
        {
            switch ($objOrderbills['bill_type'])
            {
                case 'payments':
                    if ($sdf['pay_type'] == 'online')
                    {
                        switch ($objOrderbills['pay_object'])
                        {
                            case 'order':
                                //$objOrders = $this->app->model("orders");
                                $this->__order_payment($objOrderbills['rel_id'], $sdf, $status);
                                break;
                            case 'recharge':
                                $objAdvance = $this->app->model("member_advance");
                                $status = $objAdvance->add($sdf_order['member_id'], $objOrderbills['money'], '前台预存款充值', $msg, $sdf['payment_id'], $objOrderbills['rel_id'], $sdf['pay_app_id'], $sdf_order['memo']);
                                
                                if ($objOrderbills['rel_id'])
                                {
                                    $obj_members = $this->app->model('members');
                                    $arr_members = $obj_members->dump($objOrderbills['rel_id']);
                                    $this->str_op_id = $objOrderbills['rel_id'];
                                    $this->str_op_name = $arr_members['name'];
                                }
                                else
                                {
                                    $this->str_op_id = '0';
                                    $this->str_op_name = '';
                                }
                                
                                $errorMsg[] = ($status == 'succ' || $status === true) ? ($arrPayments['app_name'] . "支付交易号: " . $sdf['trade_no'] . "前台预存款充值成功！") : "订单号：" . $objOrderbills['rel_id'] . ' ' . $arrPayments['app_name'] . ' ' . $msg;
                                break;
                            case 'joinfee':                            
                                break;
                            default:
                                break;
                        }
                    }
                    else
                    {
                        switch ($objOrderbills['pay_object'])
                        {
                            case 'order':
                                //$objOrders = $this->app->model("orders");
                                $this->__order_payment($objOrderbills['rel_id'], $sdf, $status);
                                break;
                            case 'recharge':
                                $objAdvance = $this->app->model("member_advance");
                                $status = $objAdvance->add($sdf_order['member_id'], $objOrderbills['money'], '前台预存款支付', $msg, $sdf['payment_id'], $objOrderbills['rel_id'], $sdf['pay_app_id'], '');
                                
                                if ($objOrderbills['rel_id'])
                                {
                                    $obj_members = $this->app->model('members');
                                    $arr_members = $obj_members->dump($objOrderbills['rel_id']);
                                    $this->str_op_id = $objOrderbills['rel_id'];
                                    $this->str_op_name = $arr_members['name'];
                                }
                                else
                                {
                                    $this->str_op_id = '0';
                                    $this->str_op_name = '';
                                }
                                
                                $errorMsg[] = ($status == 'succ' || $status === true) ? (" 线下支付交易号: " . $sdf['trade_no'] . "前台预存款充值成功！") : "订单号：" . $objOrderbills['rel_id'] . ' 线下支付交易 ' . $msg;
                                break;
                            case 'joinfee':                            
                                break;
                            default:
                                break;
                        }
                    }
                    break;
                case 'refunds':
                    // 只支持预存款
                    $objAdvance = $this->app->model("member_advance");
                    $sdf_order = $this->dump($objOrderbills['rel_id'], '*');
                    
                    // Order information update.
                    if ($sdf['money'] < $sdf_order['cur_amount'] && $status != 'failed')
                        $pay_status = '4';
                    else if ($status == 'succ')
                        $pay_status = '5';
                    else
                        $pay_status = '2';
                        
                    $arrOrder = array(
                        'order_id' => $objOrderbills['rel_id'], 
                        'pay_app_id' => $sdf['pay_app_id'],
                        'payed' => $objMath->number_minus(array($sdf_order['payed'], $sdf['money'])) < 0 ? 0 : $objMath->number_minus(array($sdf_order['payed'], $sdf['money'])),
                        'pay_status' => $pay_status,
                    );
                    $this->save($arrOrder);
                    
                    $status = $objAdvance->add($sdf_order['member_id'], $sdf['payed'], '后台订单退款', $errorMsg, $sdf['payment_id'], '', 'deposit', $sdf_order['memo']);
                    break;
            }
            
            // 改变日志操作结果
            if (is_object($this->app) && $this->app)
            {                
                $objOrderLog = $this->app->model("order_log");
                if ($status == 'succ' || $status === true || $status == 'progress')
                    $status_log = 'SUCCESS';
                else
                    $status_log = 'FAILURE';
                
                $sdf_order_log = array(
                    'rel_id' => $objOrderbills['rel_id'],
                    'op_id' => ($this->from == 'Back') ? "1" : $this->str_op_id,
                    'op_name' => ($this->from == 'Back') ? "admin" : $this->str_op_name,
                    'alttime' => time(),
                    'bill_type' => $objOrderbills['pay_object'],
                    'behavior' => $objOrderbills['bill_type'],
                    'result' => $status_log,
                    'log_text' => '订单' . $objOrderbills['rel_id'] . '付款' . $objOrderbills['money'],
                );
                    
                //$objOrderLog->changeResult($rel_id, $objOrderbills['pay_object'], "payments", $status_log);
                $log_id = $objOrderLog->save($sdf_order_log);                
            }
            
            if ($status_log == 'FAILURE')
                $is_success = false;
        }
        
        // Redirect page.
        if ($sdf['return_url'] && $is_success)
        {                
            header("Location: " . $sdf['return_url']);
        }
        
        return $is_success;
    }
    
    private function __order_payment($rel_id, &$sdf, $status='succ')
    {
        $objMath = kernel::single('ectools_math');
        $obj_orders = $this->app->model('orders');
        $subsdf = array('order_objects'=>array('*',array('order_items'=>array('*',array(':products'=>'*')))));
        $sdf_order = $obj_orders->dump($rel_id, '*', $subsdf);
        $order_items = array();
        
        if ($sdf_order['member_id'])
        {
            $obj_members = $this->app->model('members');
            $arr_members = $obj_members->dump($sdf_order['member_id'], '*', array(':account@pam' => array('*')));
            $this->str_op_id = $sdf_order['member_id'];
            $this->str_op_name = $arr_members['pam_account']['login_name'];
        }
        else
        {
            $this->str_op_id = '0';
            $this->str_op_name = '';
        }
        
        // Order information update.
        if ($objMath->number_plus(array($sdf_order['payed'], $sdf['money'])) < $sdf_order['total_amount'] && $status != 'failed')
            $pay_status = '3';
        else if ($status == 'succ' || $status == 'progress')
        {
            if ($status == 'succ')
                $pay_status = '1';
            else
                $pay_status = '2';
        }
        else
            $pay_status = '0';
            
        $arrOrder = array(
            'order_id' => $rel_id, 
            'pay_app_id' => $sdf['pay_app_id'],
            'payed' => ($objMath->number_plus(array($sdf_order['payed'], $sdf['money'])) > $sdf_order['total_amount']) ? $sdf_order['total_amount'] : $objMath->number_plus(array($sdf_order['payed'], $sdf['money'])),
            'pay_status' => $pay_status,
        );
        
        $obj_orders->save($arrOrder);
        
        // 支付完了，预存款
        if ($sdf['pay_app_id'] == 'deposit')
        {
            $objAdvance = $this->app->model("member_advance");
            
            $status = $objAdvance->deduct($sdf_order['member_id'], $sdf['money'], '预存款支付订单', $msg, $sdf['payment_id'], $rel_id, 'deposit', $sdf_order['memo']);
            $errorMsg[] = $msg;
        }
        else
        {
            $errorMsg[] = ($status == 'succ' || $status === true) ? ("订单号：" . $rel_id . ' ' . $arrPayments['app_name'] . "支付交易号: " . $sdf['trade_no'] . "，交易成功！") : "订单号：" . $rel_id . ' ' . $arrPayments['app_name'] . "支付交易失败！";
        }
        
        // 为会员添加积分
        if (isset($sdf_order['member_id']) && $sdf_order['member_id'] && $arrOrder['payed'] == $sdf_order['total_amount'])
        {    
            $policy_method = $this->app->getConf("site.get_policy.method");
            //$level_switch = $this->app->getConf("site.level_switch");
            if ($policy_method > 1)
            {                                        
                $objPoint = $this->app->model('member_point');
                // 使用的积分
                $objPoint->change_point($sdf_order['member_id'], intval($sdf_order['score_u']), $msg, 'order_pay_use', 1);
                // 获得积分
                $objPoint->change_point($sdf_order['member_id'], intval($sdf_order['score_g']), $msg, 'order_pay_get', 2);
            }
            
            // 增加经验值
            $obj_member = $this->app->model('members');
            $obj_member->change_exp($sdf_order['member_id'], floor($sdf_order['total_amount']));
        }
        
        if ($pay_status == '1')
            $sdf['pay_status'] = 'PAY_FINISH';
        else if ($pay_status == '2')
            $sdf['pay_status'] = 'PAY_TO_MEDIUM';
        else if ($pay_status == '3')
            $sdf['pay_status'] = 'PAY_PART';
        else
            $sdf['pay_status'] = 'FAILED';
          
        $this->request($sdf);
        
        // 冻结库存
        $storage_enable = $this->app->getConf('site.storage.enabled');
        if ($arrOrder['payed'] == $sdf_order['total_amount'])
        {
            if ($storage_enable != 'true')
            {
                $store_mark = $this->app->getConf('system.goods.freez.time');
                if ($store_mark == '2')
                {
                    $objGoods = $this->app->model('goods');
                    foreach ($sdf_order['order_objects'] as $k=>$v)
                    {
                        $order_items = array_merge($order_items,$v['order_items']);
                    }
                    
                    // 判断是否已经发过货.                                        
                    if ($sdf_order['ship_status'] == '1' || $sdf_order['ship_status'] == '2')
                    {
                        foreach ($order_items as $key=>$dinfo)
                        {
                            if ($dinfo['products']['sendnum'] < $dinfo['products']['nums'])
                            {
                                $semds = $objMath->number_plus(array($dinfo['nums'], $dinfo['sendnum']));
                                if ($semds > 0)
                                    if ($dinfo['item_type'] != 'gift')
                                        $objGoods->freez($dinfo['goods_id'], $dinfo['products']['product_id'], $semds);
                                    else
                                    {
                                        $app_gift = app::get('gift');
                                        if ($app_gift->is_installed())
                                        {
                                            $obj_gift_goods = $app_gift->model('goods');
                                            $obj_gift_goods->freez($dinfo['goods_id'], $semds);
                                        }
                                    }
                            }
                        }
                    }
                    else
                    {                                            
                        foreach ($order_items as $key=>$dinfo)
                        {
                            if ($dinfo['item_type'] != 'gift')
                                $objGoods->freez($dinfo['goods_id'], $dinfo['products']['product_id'], $dinfo['quantity']);
                            else
                            {
                                $app_gift = app::get('gift');
                                if ($app_gift->is_installed())
                                {
                                    $obj_gift_goods = $app_gift->model('goods');
                                    $obj_gift_goods->freez($dinfo['goods_id'], $dinfo['quantity']);
                                }
                            }
                        }
                    }
                }
            }
            else
            {
                $store_mark = $this->app->getConf('system.goods.freez.time');
                if ($store_mark == '2')
                {
                    
                    foreach ($sdf_order['order_objects'] as $k=>$v)
                    {
                        $order_items = array_merge($order_items,$v['order_items']);
                    }
                    
                    // 判断是否已经发过货.                                        
                    if ($sdf_order['ship_status'] == '1' || $sdf_order['ship_status'] == '2')
                    {
                        foreach ($order_items as $key=>$dinfo)
                        {
                            if ($dinfo['products']['sendnum'] < $dinfo['products']['nums'])
                            {
                                $semds = $objMath->number_plus(array($dinfo['nums'], $dinfo['sendnum']));
                                if ($semds > 0)
                                    if ($dinfo['item_type'] == 'gift')
                                    {
                                        $app_gift = app::get('gift');
                                        if ($app_gift->is_installed())
                                        {
                                            $obj_gift_goods = $app_gift->model('goods');
                                            $obj_gift_goods->freez($dinfo['goods_id'], $semds);
                                        }
                                    }
                            }
                        }
                    }
                    else
                    {                                            
                        foreach ($order_items as $key=>$dinfo)
                        {
                            if ($dinfo['item_type'] == 'gift')
                            {
                                $app_gift = app::get('gift');
                                if ($app_gift->is_installed())
                                {
                                    $obj_gift_goods = $app_gift->model('goods');
                                    $obj_gift_goods->freez($dinfo['goods_id'], $dinfo['quantity']);
                                }
                            }
                        }
                    }
                }                
            }
        }
        
        if ($sdf_order['member_id'])
        {
            $member = $this->app->model('members');
            $arr_member = $member->dump($sdf_order['member_id'], '*', array(':account@pam'=>'*'));
        }
        
        $aUpdate['order_id'] = $rel_id;
        $aUpdate['paytime'] = date('Y-m-d', time());
        $aUpdate['money'] = $sdf['money'];
        $aUpdate['email'] = (!$sdf_order['member_id']) ? $sdf_order['consignee']['email'] : $arr_member['contact']['email'];
        $aUpdate['pay_status'] = $sdf['pay_status'];
        $aUpdate['is_frontend'] = ($this->from == 'Back') ? false: true;
        
        $obj_orders->fireEvent("payed", $aUpdate, $sdf_order['member_id']);
    }
    
    public function order_pay_finish(&$sdf, $status='succ', $from='Back')
    {
        $this->from = $from;
        return $this->pay_finish($sdf, $status);
    }
    
    protected function request(&$sdf)
    {
        $payments_status = array(
            'succ' => 'SUCC',
            'failed' => 'FAILED',
            'cancel' => 'CANCEL',
            'error' => 'ERROR',
            'invalid' => 'INVALID',
            'progress' => 'PROGRESS',
            'timeout' => 'TIMEOUT',
            'ready' => 'READY',
        );
        $arr_data = array();
        $arr_data['tid'] = $sdf['orders'][0]['rel_id'];
        $arr_data['payment_id'] = $sdf['payment_id'];
        $arr_data['seller_bank'] = $sdf['bank'];
        $arr_data['seller_account'] = $sdf['account'];
        $arr_data['buyer_account'] = $sdf['pay_account'];
        $arr_data['currency'] = $sdf['currency'];
        $arr_data['pay_fee'] = $sdf['money'];
        $arr_data['paycost'] = $sdf['paycost'];
        $arr_data['currency_fee'] = $sdf['cur_money'];
        $arr_data['pay_type'] = $sdf['pay_type'];
        $arr_data['payment_type'] = $sdf['pay_name'];
        $arr_data['t_begin'] = date('Y-m-d H:i:s', $sdf['t_begin']);
        $arr_data['t_end'] = date('Y-m-d H:i:s', $sdf['t_payed']);
        $arr_data['status'] = $payments_status[$sdf['status']];
        $arr_data['memo'] = $sdf['memo'];
        $arr_data['outer_no'] = $sdf['trade_no'];        
        
        $arr_callback = array(
            'class' => 'b2c_api_callback_app', 
            'method' => 'callback',
            'params' => array(
                'method' => 'store.trade.payment.add',
                'tid' => $arr_data['tid'],
            ),
        );
        //$rst = $this->app->matrix()->call('store.trade.payment.add', $arr_data);
        parent::request('store.trade.payment.add', $arr_data, $arr_callback, 'Payment Add', 1);
    }
}
