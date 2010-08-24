<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class shopex_payment_select_method{
    
    function get_view()
    {
        $render = app::get('b2c')->render();
        $payment_cfg = app::get('b2c')->model('payment_cfgs');
        $payments = array();
        $rePayment = $payment_cfg->getList();
echo 111;exit;
        foreach($rePayment as $payment){
            $payment_app = $payment_cfg->load($payment['app_id']);
            if($payment_app->is_ready()){
                $payment['name'] = $payment_app->display_name;
                $payment['extra'] = $payment_app->extend();
                $payment['intro'] = $payment_app->intro();
                $payments[] = $payment;
            }
        }
        $render->pagedata['payments'] = &$payments;
        return $render->fetch("site/common/paymethod.html");
    }
}
