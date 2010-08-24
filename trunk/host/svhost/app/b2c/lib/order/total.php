<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_order_total
{
    /**
     * 生成订单总计详细页面
     * @params object 控制器
     * @params object cart objects
     * @params array sdf array
     */
    public function order_total_method(&$controller,$cart,$sdf_order)
    {
        $payment_detail = $this->payment_detail($controller,$cart,$sdf_order);
        $controller->pagedata['order_detail'] = &$payment_detail;
        $controller->pagedata['trigger_tax'] = $controller->app->getConf("site.trigger_tax");
        $controller->pagedata['tax_ratio'] = $controller->app->getConf("site.tax_ratio");
        return $controller->fetch("site/cart/checkout_total.html");
    }
    
    /** 
     * 生成订单总计详细
     * @params object 控制器
     * @params object cart objects
     * @params array sdf array
     */
    public function payment_detail(&$controller,$cart,$sdf_order)
    {
        $objMath = kernel::single('ectools_math');
        //$cart_info = $cart->get_objects(true);
        $cart_info = $cart;
        $cost_item = $cart_info['subtotal'];//购物车里商品总费用
        $cost_item = $objMath->number_minus(array($cost_item, $cart_info['discount_amount_prefilter']));
        $items_weight = $cart_info['subtotal_weight'];//购物车里商品总重量
        if (isset($cart_info['is_free_shipping']) && $cart_info['is_free_shipping'])
            $items_weight = 0;
        else
        {
            if ($cart_info['object']['goods'])
            {
                foreach ($cart_info['object']['goods'] as $item_obj)
                {
                    if (isset($item_obj['is_free_shipping']) && $item_obj['is_free_shipping'])
                    {
                        if (isset($item_obj['obj_items']['products'][0]) && $item_obj['obj_items']['products'][0])
                        {
                            $product_item = $item_obj['obj_items']['products'][0];
                            if ($product_item['package_use'])
                            {
                                $items_weight_added = $objMath->number_multiple(array($product_item['package_unit'], $item_obj['quantity'], $product_item['weight']));
                            }
                            else
                            {
                                $items_weight_added = $objMath->number_multiple(array($product_item['weight'], $item_obj['quantity']));
                            }
                            
                            $items_weight = $objMath->number_minus(array($items_weight, $items_weight_added));
                        }
                    }
                }
            }
        }
        
        $objCurrency = app::get('ectools')->model('currency');
        $arrDefCurrency = $objCurrency->getDefault();
        $strDefCurrency = $arrDefCurrency['cur_code'];
        $aCur = $objCurrency->getcur($sdf_order['cur']);
        
        if($sdf_order['shipping_id'])
        {
            $dlytype = $controller->app->model('dlytype');//配送方式
            $dlytype_info = $dlytype->dump($sdf_order['shipping_id'],'*');
            
            if($sdf_order['is_protect'] === 'true' || $sdf_order['is_protect'] === '1' || $sdf_order['is_protect'] === true){//配送设置了保价
                //$cost_protect = ($cost_item*$dlytype_info['protect_rate']);
                $cost_protect = $objMath->number_multiple(array($cost_item, $dlytype_info['protect_rate']));
                $cost_protect = $cost_protect>$dlytype_info['minprice']?$cost_protect:$dlytype_info['minprice'];//保价费
            }
            
            if (!$dlytype_info['setting'])
            {            
                $arrArea = explode(':', $sdf_order['area']);
                $area_id = $arrArea[2];
                if (isset($dlytype_info['area_fee_conf']) && $dlytype_info['area_fee_conf'])
                {
                    $area_fee_conf = unserialize($dlytype_info['area_fee_conf']);
                     foreach($area_fee_conf as $k=>$v)
                     {
                        $areas = explode(',',$v['areaGroupId']);
                        
                        // 再次解析字符
                        foreach ($areas as &$strArea)
                        {
                            if (strpos($strArea, '|') !== false)
                            {
                                $strArea = substr($strArea, 0, strpos($strArea, '|'));
                            }
                        }
                        
                        // 取当前area id对应的最上级的区域id
                        $objRegions = app::get('ectools')->model('regions');
                        $arrRegion = $objRegions->dump($area_id);
                        while ($row = $objRegions->getRegionByParentId($arrRegion['p_region_id']))
                        {
                            $arrRegion = $row;
                            $area_id = $row['region_id'];
                        }
                        
                        if(in_array($area_id,$areas))
                        {
                            //如果地区在其中，优先使用地区设置的配送费用，及公式
                            $dlytype_info['firstprice'] = $v['firstprice'];
                            $dlytype_info['continueprice'] = $v['continueprice'];
                            $dlytype_info['dt_expressions'] = $v['dt_expressions'];
                            
                            break;
                        }
                    }
                }
            }
            
            $cost_freight = utils::cal_fee($dlytype_info['dt_expressions'],$items_weight,$cost_item);//配送费
        }
        
        if (isset($cart_info['is_free_shipping']) && $cart_info['is_free_shipping'])
        {
            $cost_freight = 0;
            $cost_protect = 0;
        }
            
        if($sdf_order['payment'] && $sdf_order['payment'] != -1)
        {
            $payment_info = app::get('ectools')->model('payment_cfgs')->getPaymentInfo($sdf_order['payment']);
            $pay_fee = $payment_info['pay_fee'];//支付费率
        }
        else
        {
            $pay_fee = 0;
        }
        
        if ($sdf_order['is_tax'] == 'true')
        {
            $cost_tax = $objMath->number_multiple(array($controller->app->getConf("site.tax_ratio"), $cost_item)); 
        }
        
        $total_amount = $objMath->number_plus(array($cost_item, $cost_protect, $cost_freight));        
        $cost_payment = $objMath->number_multiple(array($total_amount, $pay_fee));
        $total_amount = $objCurrency->amount_nocur($objMath->number_plus(array($total_amount, $cost_payment, $cost_tax)), $sdf_order['cur'], false, false);
        $total_amount = $objMath->number_minus(array($total_amount, $cart_info['discount_amount_order']));
        $demical = $controller->app->getConf('system.money.operation.decimals');
        $odr_decimals = $controller->app->getConf('system.money.decimals');
        $system_money_operation_carryset = $controller->app->getConf('system.money.operation.carryset');
        $total_amount_odr = $objMath->get($total_amount, $odr_decimals);
        $order_discount = $objMath->number_minus(array($total_amount, $total_amount_odr));
        if ($total_amount < 0)
            $total_amount = 0;

        // 取到商店积分规则
        $policy_method = $controller->app->getConf("site.get_policy.method");
        switch ($policy_method)
        {
            case '1':
                $subtotal_consume_score = 0;
                $subtotal_gain_score = 0;
                $totalScore = 0;
                break;
            case '2':
                $subtotal_consume_score = round($cart_info['subtotal_consume_score']);
                $policy_rate = $controller->app->getConf('site.get_rate.method');
                $subtotal_gain_score = round($objMath->number_multiple(array($total_amount_odr, $policy_rate)));
                $totalScore = round($objMath->number_minus(array($subtotal_gain_score, $subtotal_consume_score)));                
                break;
            case '3':
                $subtotal_consume_score = round($cart_info['subtotal_consume_score']);
                $subtotal_gain_score = round($cart_info['subtotal_gain_score']);
                $totalScore = round($objMath->number_minus(array($subtotal_gain_score, $subtotal_consume_score)));
                break;
            default:
                $subtotal_consume_score = 0;
                $subtotal_gain_score = 0;
                $totalScore = 0;
                break;
        }        
        
        if ($sdf_order['member_id'])
        {
            // 得到当前会员的积分
            $obj_members = $controller->app->model('members');
            $arr_member = $obj_members->dump($sdf_order['member_id'], 'point');
            $member_point = $arr_member['score']['total'];
            
            $totalScore = $member_point;
        }
        else
        {
            $totalScore = 0;
        }
        
        $payment_detail = array('cost_item'=>$objCurrency->amount_nocur($cost_item, $sdf_order['cur'], false, false),
                                'cost_protect'=>$objCurrency->amount_nocur($cost_protect, $sdf_order['cur'], false, false),
                                'cost_freight'=>$objCurrency->amount_nocur($cost_freight, $sdf_order['cur'], false, false),
                                'cost_payment'=>$objCurrency->amount_nocur($cost_payment, $sdf_order['cur'], false, false),
                                'total_amount'=>$total_amount_odr,
                                'currency' => $sdf_order['cur'],
                                'pmt_amount' => $cart_info['discount_amount'],
                                'cost_tax' => $cost_tax,
                                'trigger_tax' => $sdf_order['is_tax'],
                                'discount' => $order_discount,
                                'cur_code' => $strDefCurrency,
                                'cur_display' => $sdf_order['cur'],
                                'cur_rate' => $aCur['cur_rate'],
                                'final_amount' => $objCurrency->changer_odr($total_amount, $sdf_order['cur'], true, false, $odr_decimals, $system_money_operation_carryset),
                                'tax_company' => $sdf_order['tax_company'],
                                'totalConsumeScore' => $subtotal_consume_score,
                                'totalGainScore' => $subtotal_gain_score,
                                'totalScore' => $totalScore,
                                );
        //print_r($payment_detail);exit;
        return $payment_detail;
    }
}
