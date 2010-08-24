<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class payments extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->model = app::get('b2c')->model('payments');
    }

    public function testInsert()
    {
        $payment_id = $this->model->gen_id();

        $paymnetArr = array(
                'payment_id' => $payment_id,
                'account' => '收款帐户1',
                'bank' => '快钱',
                'pay_account' => '付款帐号',
                'currency' => 'CNY',
                'money' => '100',
                'paycost' => '1',
                'cur_money' => '50',
                'pay_type' => 'online',
                'pay_app_id' => '99bill',
                'pay_name' => '快钱',
                'pay_ver' => '1.0',
                'op_id' => '1',
                'ip' => '127.0.0.1',
                't_begin' => '1234567899',
                't_payed' => '1234567899',
                't_confirm' => '1234567899',
                'status' => 'ready',
                'trade_no' => '快钱交易号:78912',
                'memo' => '说明',
                'orders' => array(
                    array(
                        'rel_id' => '20100113199221',
                        'bill_type' => 'refunds',
                        'pay_object' => 'recharge',
                        'bill_id' => $payment_id,
                        'money' => '50',
                    )
                )
            );
        
        /*$paymnetArr['rel_id'] = "2010011319922222";
        $paymnetsArr[] = $paymnetArr;*/
        
        $this->model->generate($paymnetArr, $this);
        $row = $this->model->db->selectrow('select * from sdb_b2c_payments where payment_id='.$payment_id);
        $this->assertEquals($row['payment_id'],$payment_id);
    }
    
    public function atestPay()
    {        
        $payment['rel_id'] = '20100113199221';
        $payment['bill_type'] = 'refunds';
        $payment['pay_object'] = 'recharge';
        $payment['payment_id'] = $this->model->gen_id();
        $payment['account'] = '收款帐户1';
        $payment['bank'] = '支付宝';
        $payment['pay_account'] = '付款帐号';
        $payment['currency'] = 'CNY';
        $payment['money'] = '100';
        $payment['paycost'] = '1';
        $payment['cur_money'] = '50';
        $payment['pay_type'] = 'online';
        $payment['pay_key'] = 'alipay';
        $payment['pay_name'] = '支付宝';
        $payment['pay_ver'] = '1.0';
        $payment['op_id'] = '1';
        $payment['ip'] = '127.0.0.1';
        $payment['t_begin'] = '1234567899';
        $payment['t_end'] = '1234567899';
        $payment['status'] = 'ready';
        $payment['trade_no'] = '支付宝交易号:78912';
        $payment['memo'] = '说明';
        
        $paymnetsArr[] = $payment;
        $this->model->save($payment);
        //$row = $this->model->db->selectrow('select * from sdb_b2c_orders where order_id='.$orderArr['order_id']);
        //$this->assertEquals($row['pay_status'],$orderArr['pay_status']);
    }
    
    public function atestConsign()
    {
        $orderArr['order_id'] = '20100113199229';
        $orderArr['ship_status'] = '1';
        $this->model->pay($orderArr);
        $row = $this->model->db->selectrow('select * from sdb_b2c_orders where order_id='.$orderArr['order_id']);
        $this->assertEquals($row['ship_status'],$orderArr['ship_status']);
    }
    
}
