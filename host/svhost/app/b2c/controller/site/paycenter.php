<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_ctl_site_paycenter extends b2c_frontpage{

    var $noCache = true;

    public function __construct(&$app){
        parent::__construct($app);
        $this->_response->set_header('Cache-Control', 'no-store');
        if(!$this->action) $this->action = 'index';
        $this->action_view = $this->action.".html";
    }
    
    /**
     * 生成支付单据处理
     * @params string - pay_object ('order','recharge','joinfee')
     * @return null
     */
    public function dopayment($pay_object='order')
    {
        if ($pay_object)
        {
            $arrMember = $this->get_current_member();
            $objOrders = $this->app->model('orders');
            $objPay = kernel::single('ectools_pay');
            $objMath = kernel::single('ectools_math');
            // 得到商店名称
            $shopName = $this->app->getConf('system.shopname');
            // Post payment information.
            $sdf = $_POST['payment'];
            
            $payment_id = $sdf['payment_id'] = $objPay->get_payment_id();
            
            if ($arrMember)
                $sdf['member_id'] = $arrMember['member_id'];
            
            $sdf['pay_object'] = $pay_object;
            $sdf['shopName'] = $shopName;
            
            if ($pay_object == 'order')
            {
                //线下支付
                if ($sdf['pay_app_id'] == 'offline')
                {
                    if (isset($sdf['member_id']) && $sdf['member_id'])
                        $this->begin(array('app'=>'b2c','ctl'=>'site_member','act'=>'orders'));
                    else
                        $this->begin(array('app'=>'b2c','ctl'=>'site_order','act'=>'index', 'arg0'=>$sdf['order_id']));
                }
            
                $arrOrders = $objOrders->dump($sdf['order_id'], '*');
                if ($arrOrders['payinfo']['pay_app_id'] != $sdf['pay_app_id'])
                {
                    //$class_name = "ectools_payment_plugin_" . ($sdf['pay_app_id']);
                    $class_name = "";
                    $obj_app_plugins = kernel::servicelist("ectools_payment.ectools_mdl_payment_cfgs");
                    foreach ($obj_app_plugins as $obj_app)
                    {
                        $app_class_name = get_class($obj_app);
                        $arr_class_name = explode('_', $app_class_name);
                        if (isset($arr_class_name[count($arr_class_name)-1]) && $arr_class_name[count($arr_class_name)-1])
                        {
                            if ($arr_class_name[count($arr_class_name)-1] == $sdf['pay_app_id'])
                            {
                                $pay_app_ins = $obj_app;
                                $class_name = $app_class_name;
                            }
                        }
                        else
                        {
                            if ($app_class_name == $sdf['pay_app_id'])
                            {
                                $pay_app_ins = $obj_app;
                                $class_name = $app_class_name;
                            }
                        }
                    }
                    $strPaymnet = app::get('ectools')->getConf($class_name);
                    $arrPayment = unserialize($strPaymnet);
                    
                    $cost_payment = $objMath->number_multiple(array($objMath->number_minus(array($arrOrders['total_amount'], $arrOrders['payinfo']['cost_payment'])), $arrPayment['setting']['pay_fee']));
                    $total_amount = $objMath->number_plus(array($objMath->number_minus(array($arrOrders['total_amount'], $arrOrders['payinfo']['cost_payment'])), $cost_payment));
                    $cur_money = $objMath->number_div(array($total_amount, $arrOrders['cur_rate']));
                    
                    // 更新订单支付信息
                    $arr_updates = array(
                        'order_id' => $sdf['order_id'],
                        'payinfo' => array(
                                        'pay_app_id' => $sdf['pay_app_id'],
                                        'cost_payment' => $objMath->number_multiple(array($cost_payment, $arrOrders['cur_rate'])),
                                    ),
                        'total_amount' => $total_amount,
                        'cur_amount' => $cur_money,
                    );
                    
                    $objOrders->save($arr_updates);
                    
                    $arrOrders = $objOrders->dump($sdf['order_id'], '*');
                }
                
                if ($sdf['pay_app_id'] == 'offline')
                {
                     $this->end(true, '订单已成功提交了');
                }
                
                if (!$sdf['pay_app_id'])
                    $sdf['pay_app_id'] = $arrOrders['payinfo']['pay_app_id'];
                    
                $sdf['currency'] = $arrOrders['currency'];
                $sdf['total_amount'] = $arrOrders['total_amount'];
                $sdf['payed'] = $arrOrders['payed'] ? $arrOrders['payed'] : '0.000';
                $sdf['money'] = $objMath->number_div(array($objMath->number_minus(array($sdf['total_amount'], $arrOrders['payed'])), $arrOrders['cur_rate']));
                $sdf['payinfo']['cost_payment'] = $arrOrders['payinfo']['cost_payment'];                
            }
            
            if ($sdf['pay_app_id'] == 'deposit')
                $sdf['return_url'] = "";
            else
                if (!$sdf['return_url'])
                    $sdf['return_url'] = $this->gen_url(array('app'=>'b2c','ctl'=>'site_paycenter','act'=>'result', 'arg0'=>$payment_id));
            
            $sdf['status'] = 'ready';
            $is_payed = $objPay->generate($sdf, $this, $msg);
            
            if ($sdf['pay_app_id'] == 'deposit')
            {
                // 预存款支付                    
                if (isset($arrMember['member_id']) && $arrMember['member_id'])
                    $this->begin(array('app'=>'b2c','ctl'=>'site_member','act'=>'orders'));
                else
                    $this->begin(array('app'=>'b2c','ctl'=>'site_order','act'=>'index', 'arg0'=>$sdf['order_id']));
                        
                if ($is_payed)                                    
                    $this->end(true, '预存款支付成功！', array('app'=>'b2c','ctl'=>'site_paycenter','act'=>'result', 'arg0'=>$sdf['payment_id']));                
                else
                    $this->end(false, $msg);
            }     
        }
    }

    public function result($payment_id)
    {
        $oPayment = app::get('ectools')->model('payments');
        $subsdf = array('orders'=>array('*'));
        $sdf_payment = $oPayment->dump($payment_id, '*', $subsdf);
        
        if ($sdf_payment['orders'])
        {
            // 得到订单日志
            $objOrderlog = $this->app->model('order_log');
            foreach ($sdf_payment['orders'] as $order_id=>$arrOrderbills)
            {
                $orderlog = $objOrderlog->get_latest_orderlist($arrOrderbills['rel_id'], $arrOrderbills['pay_object'], $arrOrderbills['bill_type']);
                $arrOrderlogs[$orderlog['log_id']] = $orderlog;
            }
        
            $this->pagedata['payment'] = &$sdf_payment;
            $this->pagedata['payment']['order_id'] = $order_id;
            $this->pagedata['orderlog'] = $arrOrderlogs;
        }

        //$this->__tmpl = 'paycenter/result_'.$sdf_payment['pay_type'].'.html';
        $this->page('site/paycenter/result_'.$sdf_payment['pay_type'].'.html');
        //$this->output();
    }
}
