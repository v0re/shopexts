<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_payment_getlist
{
    var $name='ȡ֧ʽб';
    function get_view($ctl, $member_id=0)
    {
        $payment_cfg = app::get('ectools')->model('payment_cfgs');
        $payments = array();
        $rePayment = $payment_cfg->getList('*', array('status' => 'true', 'is_frontend' => true));
        if (!$member_id)
        {
            $arr_members = $ctl->get_current_member();
            $member_id = $arr_members['member_id'];
        }
        if ($rePayment)
        {
            foreach($rePayment as $key=>$payment){
                /*$payment_app = $payment_cfg->model($payment['app_id']);
                if($payment_app->is_ready()){
                    $payment['name'] = $payment_app->display_name;
                    $payment['extra'] = $payment_app->extend();
                    $payment['intro'] = $payment_app->intro();
                    $payments[] = $payment;
                }*/
                if (!$member_id)
                {
                    if (trim($payment['app_id']) == 'deposit')
                    {
                        unset($rePayment[$key]);
                        continue;
                    }
                }
                $payments[] = $payment;
            }/*
            foreach($payments as $k=>$v){
                $class_name = $v['app_class'];
                $app = new $class_name();
            }*/
            $ctl->pagedata['payments'] = &$payments;
            return $ctl->fetch("site/common/paymethod.html");
        }
    }
}
