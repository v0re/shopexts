<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

/**
 * b2c payment interactor with center
 * shopex team
 * dev@shopex.cn
 */
class b2c_api_payment
{
    /**
     * app object
     */
    public $app;
    
    /**
     * 构造方法
     * @param object app
     */
    public function __construct($app)
    {
        $this->app = app::get('ectools');
        $this->app_b2c = $app;
    }
    
    /**
     * 支付单创建
     * @param array sdf
     * @param string message
     * @return boolean success or failure
     */
    public function create(&$sdf, &$thisObj)
    {
        // 创建订单是和中心的交互
        $is_payed = false; 
        $objModelPay = $this->app->model('payments');
        $obj_order = $this->app_b2c->model('orders');
        $objMath = kernel::single('ectools_math');
        
        if (!isset($sdf['payment_bn']) || !$sdf['payment_bn'] || !isset($sdf['order_bn']) || !$sdf['order_bn'])
        {
            $thisObj->send_user_error(__('支付单tid没有收到！'), array());
        }
        else
        {
            // 生成支付单据.
            $payment_id = $sdf['payment_id'] = $objModelPay->gen_id();
            $paymentArr = array(
                'payment_id' => $sdf['payment_id'],
                'payment_bn' => $sdf['payment_bn'],
                'account' => $sdf['account'],
                'bank' => $sdf['bank'],
                'pay_account' => $sdf['pay_account'] ? $sdf['pay_account'] : '付款帐号',
                'currency' => $sdf['currency'],
                'money' => $sdf['money'],
                'paycost' => $sdf['paycost'],
                'cur_money' => $sdf['cur_money'],
                'pay_type' => $sdf['pay_type'],
                'pay_app_id' => $sdf['paymethod'],
                'pay_name' => $sdf['paymethod'],
                'pay_ver' => '1.0',
                'op_id' => '0',
                'ip' => $sdf['ip'],
                't_begin' => $sdf['t_begin'],
                't_payed' => $sdf['t_begin'],
                't_confirm' => $sdf['t_end'],
                'status' => $sdf['status'],
                'trade_no' => $sdf['trade_no'],
                'memo' => $sdf['memo'],
                'return_url' => '',
                'orders' => array(
                    array(
                        'rel_id' => $sdf['order_bn'],
                        'bill_type' => 'payments',
                        'pay_object' => 'order',
                        'bill_id' => $sdf['payment_id'],
                        'money' => $sdf['money'],
                    )
                )
            );
                            
            $is_save = $objModelPay->save($paymentArr);
            
            if ($is_save)
            {
                // 修改订单状态.
                $arr_order = $obj_order->dump($sdf['order_bn']);
                $arr_order['order_id'] = $sdf['order_bn'];
                if ($objMath->number_plus(array($arr_order['payed'], $sdf['money'])) >= $arr_order['total_amount'] && $sdf['status'] == 'succ')
                {
                    $arr_order['pay_status'] = '1';
                    $arr_order['payed'] = $arr_order['total_amount'];
                }
                elseif ($objMath->number_plus(array($arr_order['payed'], $sdf['money'])) >= $arr_order['total_amount'] && $sdf['status'] == 'progress')
                {
                    $arr_order['pay_status'] = '2';
                    $arr_order['payed'] = $arr_order['total_amount'];
                }
                else
                {
                    $arr_order['pay_status'] = '3';
                    $arr_order['payed'] = $objMath->number_plus(array($arr_order['payed'], $sdf['money']));
                }            
                
                $is_payed = $obj_order->save($arr_order);
            }
            if ($is_save && $is_payed)
            {
                $objOrderLog = $this->app_b2c->model("order_log");                
                $sdf_order_log = array(
                    'rel_id' => $sdf['order_bn'],
                    'op_id' => "1",
                    'op_name' => "admin",
                    'alttime' => time(),
                    'bill_type' => 'order',
                    'behavior' => 'payments',
                    'result' => 'SUCCESS',
                    'log_text' => '订单' . $sdf['order_bn'] . '付款' . $sdf['money'],
                );
                    
                $log_id = $objOrderLog->save($sdf_order_log);
                
                if ($arr_order['member_id'])
                {
                    $member = $this->app_b2c->model('members');
                    $arr_member = $member->dump($arr_order['member_id'], '*', array(':account@pam'=>'*'));
                }
                
                $aUpdate['order_id'] = $sdf['order_bn'];
                $aUpdate['paytime'] = date('Y-m-d', time());
                $aUpdate['money'] = $sdf['money'];
                $aUpdate['email'] = (!$arr_order['member_id']) ? $arr_order['consignee']['email'] : $arr_member['contact']['email'];
                $aUpdate['pay_status'] = $arr_order['pay_status'];
                $aUpdate['is_frontend'] = false;
                
                $obj_order->fireEvent("payed", $aUpdate, $arr_order['member_id']);
        
                return array('payment_id' => $payment_id);
            }
            else
            {
                $thisObj->send_user_error(__('支付单生成失败！'), array());
            }
        }
    }
    
    /**
     * 支付单修改
     * @param array sdf
     * @param string message
     * @return boolean sucess of failure
     */
    public function update(&$sdf, &$thisObj)
    {
        // 修改支付单是和中心的交互
        $objPayments = $this->app->model('payments');
        $obj_order = $this->app_b2c->model('orders');
        
        $arr_payments = $this->dump(array('payment_bn' => $sdf['payment_bn']), '*', '*');
        
        $arr_refunds['account'] = $sdf['account'] ? $sdf['account'] : '';
        $arr_refunds['bank'] = $sdf['bank'] ? $sdf['bank'] : '';
        $arr_refunds['pay_account'] = $sdf['pay_account'] ? $sdf['pay_account'] : '';
        $arr_refunds['op_id'] = $sdf['op_id'] ? $sdf['op_id'] : 0;
        $arr_refunds['ip'] = $sdf['ip'] ? $sdf['ip'] : '';
        $arr_refunds['t_payed'] = $sdf['t_begin'] ? $sdf['t_begin'] : '';
        $arr_refunds['t_confirm'] = $sdf['t_end'] ? $sdf['t_end'] : '';
        $arr_refunds['status'] = $sdf['status'] ? $sdf['status'] : '';
        $arr_refunds['trade_no'] = $sdf['trade_no'] ? $sdf['trade_no'] : '';
        $arr_refunds['memo'] = $sdf['memo'] ? $sdf['memo'] : '';
        
        $is_save = $objPayments->save($data);
        
        if ($arr_refunds['status'] == 'succ')
            if (isset($arr_payments['orders']) && $arr_payments['orders'])
                foreach ($arr_payments['orders'] as $key=>$arr_order)
                {
                    $arr_odrs = $obj_order->dump($key);
                    $arr_odr_data = array(
                        'order_id' => $key,
                        'status' => '1',
                    );
                    
                    $obj_order->save($arr_odr_data);
                }
        
        if ($is_save)
            return true;
        else
        {
            $thisObj->send_user_error(__('支付单修改失败！'), array());
        }
    }
}