<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class b2c_order_create extends b2c_api_rpc_request
{
    /**
     * 构造方法
     * @param object app
     */
    public function __construct($app)
    {
        parent::__construct($app);
        $this->objMath = kernel::single('ectools_math');
    }
    
    /**
     * 订单标准数据生成
     * @params array - 订单数据
     * @params string - 唯一标识
     * @return boolean - 成功与否
     */
    public function generate(&$sdf, $member_indent='')
    {
        $order_data = array();
        
        $order_data['order_id'] = $sdf['order_id'];
        $order_data['member_id'] = ($sdf['member_id']) ? $sdf['member_id'] : 0;
        $this->_chgdata($sdf, $order_data, $member_indent);

        return $order_data;
    }
    
    /**
     * @params array sdf
     * @params array
     * @params string
     * @params string message
     */
    private function _chgdata(&$sdf, &$order_data, $member_indent='')
    {
        $now = time();
        $objCurrency = app::get('ectools')->model("currency")->getcur($sdf['payment']['currency']);
        
        // 得到shipping name
        $objDlytype = $this->app->model("dlytype");
        $shipping_name = $objDlytype->dump($sdf['delivery']['shipping_id'], 'dt_name');
        $order_data = array(
            'order_id'=>$order_data['order_id'],
            'member_id'=>$order_data['member_id'],
            'confirm' => 'N',
            'status'=>'active',     //active/dead/cancel/finish
            'pay_status'=>'0', 
            'ship_status'=>'0',     //0/1/2/3/4
            'is_delivery'=>'Y',     //Y/N
            'createtime'=>$now,
            'last_modified'=>$now,
            'memo'=>$sdf['memo'],
            'ip'=>$_SERVER['REMOTE_ADDR'],
            'title' => '订单明细介绍',
            'shipping' => array('shipping_id' => $sdf['delivery']['shipping_id'],
                                'is_protect' => ($sdf['delivery']['is_protect'][$sdf['delivery']['shipping_id']]) ? 'true' : 'false',
                                'shipping_name' => $shipping_name['dt_name'],
                                'cost_shipping' => 10,
                                'cost_protect' => 0,
                                ),
            'payinfo' => array('pay_app_id' => ($sdf['payment']['pay_app_id'] != '-1') ? $sdf['payment']['pay_app_id'] : '货到付款'),
            'currency' => $sdf['payment']['currency'],
            'cur_rate' => $objCurrency['cur_rate'],
            'is_tax' => ($sdf['payment']['is_tax'] ? 'true' : 'false'),
            'tax_title' => $sdf['payment']['tax_company'],
        );
        
        $obj_mCart = $this->app->model('cart');
        if ($member_indent)
        {
            $data = $obj_mCart->get_cookie_cart_arr($member_indent);               
            $objCarts = $obj_mCart->get_cart_object($data);
        }
        else
        {
            $objCarts = $this->app->model('cart')->get_objects(true);
        }
        
        // 购物车是否为空
        $is_empty = $this->app->model('cart')->is_empty($objCarts);
        if ($is_empty)
        {
            $msg = '购物车为空！';
            return false;
        }
        
        $order_data['weight'] = $objCarts['subtotal_weight'];
        $order_data['itemnum'] = $objCarts['items_quantity'];
        // 计算cart的总费用
        $obj_total = new b2c_order_total();
        $sdf_order = array('payment'=>$sdf['payment']['pay_app_id'],'shipping_id'=>$sdf['delivery']['shipping_id'],'is_protect'=>($sdf['delivery']['is_protect'][$sdf['delivery']['shipping_id']] ? $sdf['delivery']['is_protect'][$sdf['delivery']['shipping_id']] : 0),'currency'=>$sdf['payment']['currency'],'is_tax'=>($sdf['payment']['is_tax'] ? $sdf['payment']['is_tax'] : 'false'), 'tax_company'=>($sdf['payment']['tax_company'] ? $sdf['payment']['tax_company'] : ''),'area'=>$sdf['delivery']['ship_area']);
        $order_detail = $obj_total->payment_detail($this->app->controller('site_order'),$objCarts,$sdf_order);
        
        // 订单显示方式        
        $system_money_decimals = $this->app->getConf('system.money.decimals');
        $system_money_operation_carryset = $this->app->getConf('system.money.operation.carryset');
        $order_data['cost_item'] = $this->objMath->formatNumber($order_detail['cost_item'], $system_money_decimals, $system_money_operation_carryset);
        $order_data['cost_tax'] = $this->objMath->formatNumber($this->objMath->number_multiple(array($this->app->getConf('site.tax_ratio'), $order_detail['cost_item'])), $system_money_decimals, $system_money_operation_carryset);
        //$order_data['tax_company'] = $sdf['payment']['tax_company'];     
        $order_data['shipping']['cost_shipping'] = $this->objMath->formatNumber($order_detail['cost_freight'], $system_money_decimals, $system_money_operation_carryset);
        $order_data['shipping']['cost_protect'] = $this->objMath->formatNumber($order_detail['cost_protect'], $system_money_decimals, $system_money_operation_carryset);
        $order_data['payinfo']['cost_payment'] = $this->objMath->formatNumber($order_detail['cost_payment'], $system_money_decimals, $system_money_operation_carryset);
        $order_data['total_amount'] = $this->objMath->formatNumber($order_detail['total_amount'], $system_money_decimals, $system_money_operation_carryset);
        $order_data['cur_amount'] = app::get('ectools')->model("currency")->changer_odr($order_data['total_amount'], $this->app->getConf("site.currency.defalt_currency"), true, false, $system_money_decimals, $system_money_operation_carryset);
        $order_data['pmt_goods'] = $this->objMath->formatNumber($objCarts['discount_amount_prefilter'], $system_money_decimals, $system_money_operation_carryset);
        $order_data['pmt_order'] = $this->objMath->formatNumber($objCarts['discount_amount_order'], $system_money_decimals, $system_money_operation_carryset);
        $order_data['discount'] = $order_detail['discount'];
        $order_data['payed'] = "0.00";
        
        $order_data['score_u'] = $objCarts['subtotal_consume_score'];
        $order_data['score_g'] = $objCarts['subtotal_gain_score'];
        $site_get_policy_method = $this->app->getConf('site.get_policy.method');
        if ($site_get_policy_method == '2')
        {
            $site_get_rate_method = $this->app->getConf('site.get_rate.method');
            $other_fee = $this->objMath->number_plus(array($order_detail['cost_freight'], $order_detail['cost_payment']));
            $other_fee = $this->objMath->number_multiple(array($other_fee, $site_get_rate_method));
            $order_data['score_g'] = round($this->objMath->number_plus(array($order_data['score_g'], $other_fee)));
        }
        
        $order_data['consignee'] =  array(
            'name'=>$sdf['delivery']['ship_name'],
            'addr'=>$sdf['delivery']['ship_addr_area'].$sdf['delivery']['ship_addr'],
            'zip'=>$sdf['delivery']['ship_zip'],
            'telephone'=>$sdf['delivery']['ship_tel'],
            'mobile'=>$sdf['delivery']['ship_mobile'],
            'email'=>$sdf['delivery']['ship_email'],
            'area'=>$sdf['delivery']['ship_area'],
            'r_time'=> ($sdf['delivery']['specal_day']?$sdf['delivery']['specal_day']:$sdf['delivery']['day']).$sdf['delivery']['time'],
            'meta'=>array()
        );

        $this->_order_items($sdf, $order_data, $objCarts['object']);
        if (isset($objCarts['promotion']) && $objCarts['promotion'])
            $this->_order_pmts($order_data, $objCarts['promotion']);
        
    }
    
    /**
     * 取到订单优惠规则
     * @params array 订单详细数组地址
     * @params array 订单规则数组
     * @return null
     */
    private function _order_pmts(&$order_data, $order_pmts)
    {
        if (isset($order_pmts) && is_array($order_pmts) && $order_pmts)
        {
            foreach ($order_pmts as $type=>$arr_pmt_odrs)
            {
                foreach ($arr_pmt_odrs as $key=>$arr_pmts_items)
                {
                    $order_data['order_pmt'][] = array(
                        'pmt_id' => $arr_pmts_items['rule_id'],
                        'order_id' => $order_data['order_id'],
                        'pmt_type' => $type,
                        'pmt_amount' => floatval($arr_pmts_items['discount_amount']),
                        'pmt_memo' => $arr_pmts_items['desc'],
                        'pmt_describe' => $arr_pmts_items['desc'],
                    );
                }
            }
        }
    }
    
    /**
     * 取到购物车的goods信息
     * @params array sdf
     * @params array - 取地址数组
     * @return null
     */
    private function _order_items(&$sdf, &$order_data, $orderObj)
    {
        if (is_array($orderObj) && $orderObj)
        {
            foreach ($orderObj as $obj_type=>$arrObjInfo)
            {
                // Orders - 分成购物券和订单
                if (is_array($arrObjInfo) && $arrObjInfo)
                {
                    $store_mark = $this->app->getConf('system.goods.freez.time');
                    $storage_enable = $this->app->getConf('site.storage.enabled');
                    $objGoods = $this->app->model('goods');
                    
                    if ($obj_type != "coupon" && $obj_type != "gift")
                    {
                        // Order Objects.
                        $index = count($order_data['order_objects']);
                        foreach ($arrObjInfo as $arrObjItems)
                        {
                            // 订单附加信息
                            $strAddon = "";
                            $arrAddon = array();
                            if ($sdf['minfo'])
                            {
                                if ($sdf['minfo'][$arrObjItems['obj_items']['products'][0]['product_id']])
                                {
                                    $arrAddon  = $sdf['minfo'][$arrObjItems['obj_items']['products'][0]['product_id']];
                                    $strAddon .= serialize($arrAddon);
                                }
                            }
                            
                            if ($arrObjItems['obj_items']['products'][0]['package_use'] == '1')
                            {                                
                                $order_data['order_objects'][$index] = array(
                                    'order_id' => $order_data['order_id'],
                                    'obj_type' => $obj_type,
                                    'obj_alias' => '商品区块',
                                    'goods_id' => $arrObjItems['obj_items']['products'][0]['goods_id'],
                                    'bn' => $arrObjItems['obj_items']['products'][0]['bn'],
                                    'name' => $arrObjItems['obj_items']['products'][0]['name'],
                                    'price' => $arrObjItems['obj_items']['products'][0]['price']['price'],
                                    'quantity'=> $arrObjItems['quantity'],
                                    'amount'=> $this->objMath->number_minus(array($arrObjItems['obj_items']['subtotal'], $arrObjItems['obj_items']['discout_amount'])),
                                    'weight'=> $arrObjItems['subtotal_weight'],
                                    'score'=> $arrObjItems['subtotal_gain_score'],
                                    'order_items' => array(
                                        array(
                                            'products' => array('product_id'=>$arrObjItems['obj_items']['products'][0]['product_id']),
                                            'goods_id'=>$arrObjItems['obj_items']['products'][0]['goods_id'],
                                            'order_id' => $order_data['order_id'],
                                            'item_type'=>'product',
                                            'bn'=>$arrObjItems['obj_items']['products'][0]['bn'],
                                            'name'=>$arrObjItems['obj_items']['products'][0]['name'],
                                            'type_id'=>$arrObjItems['obj_items']['products'][0]['type_id'],
                                            'cost'=>$arrObjItems['obj_items']['products'][0]['price']['cost'],
                                            'quantity'=>$this->objMath->number_multiple(array($arrObjItems['obj_items']['products'][0]['quantity'], $arrObjItems['quantity'])),
                                            'sendnum'=>0,
                                            'amount'=>$this->objMath->number_multiple(array($arrObjItems['obj_items']['products'][0]['price']['buy_price'], $arrObjItems['quantity'])),
                                            'score' => $this->objMath->number_minus(array($arrObjItems['obj_items']['products'][0]['gain_score'], $arrObjItems['obj_items']['products'][0]['consume_score'])),
                                            'price'=>$arrObjItems['obj_items']['products'][0]['price']['buy_price'],
                                            'weight'=>$arrObjItems['obj_items']['products'][0]['weight'],
                                            'addon'=>$strAddon,
                                        ),
                                    ),
                                );
                            }
                            else
                                $order_data['order_objects'][$index] = array(
                                    'order_id' => $order_data['order_id'],
                                    'obj_type' => $obj_type,
                                    'obj_alias' => ($obj_type == 'goods') ? '商品区块' : '捆绑销售',
                                    'goods_id' => $arrObjItems['obj_items']['products'][0]['goods_id'],
                                    'bn' => $arrObjItems['obj_items']['products'][0]['bn'],
                                    'name' => $arrObjItems['obj_items']['products'][0]['name'],
                                    'price' => $arrObjItems['obj_items']['products'][0]['price']['price'],
                                    'quantity'=> $arrObjItems['quantity'],
                                    'amount'=> $this->objMath->number_minus(array($arrObjItems['obj_items']['subtotal'], $arrObjItems['obj_items']['discout_amount'])),
                                    'weight'=> $arrObjItems['subtotal_weight'],
                                    'score'=> $arrObjItems['subtotal_gain_score'],
                                    'order_items' => array(
                                        array(
                                            'products' => array('product_id'=>$arrObjItems['obj_items']['products'][0]['product_id']),
                                            'goods_id'=>$arrObjItems['obj_items']['products'][0]['goods_id'],
                                            'order_id' => $order_data['order_id'],
                                            'item_type'=>'product',
                                            'bn'=>$arrObjItems['obj_items']['products'][0]['bn'],
                                            'name'=>$arrObjItems['obj_items']['products'][0]['name'],
                                            'type_id'=>$arrObjItems['obj_items']['products'][0]['type_id'],
                                            'cost'=>$arrObjItems['obj_items']['products'][0]['price']['cost'],
                                            'quantity'=>$this->objMath->number_multiple(array($arrObjItems['obj_items']['products'][0]['quantity'], $arrObjItems['quantity'])),
                                            'sendnum'=>0,
                                            'amount'=>$this->objMath->number_multiple(array($arrObjItems['obj_items']['products'][0]['price']['buy_price'], $arrObjItems['quantity'])),
                                            'score' => $this->objMath->number_minus(array($arrObjItems['obj_items']['products'][0]['gain_score'], $arrObjItems['obj_items']['products'][0]['consume_score'])),
                                            'price'=>$arrObjItems['obj_items']['products'][0]['price']['buy_price'],
                                            'weight'=>$arrObjItems['obj_items']['products'][0]['weight'],
                                            'addon'=>$strAddon,
                                        ),
                                    ),
                                );
                            
                            // 添加附件和赠品todo...
                            if (isset($arrObjItems['adjunct']) && $arrObjItems['adjunct'])
                            {
                                $str_start = count($order_data['order_objects'][$index]['order_items']);
                                foreach ($arrObjItems['adjunct'] as $key=>$adjunctItems)
                                {
                                    $order_data['order_objects'][$index]['order_items'][$key + $str_start] = array(
                                        'products' => array('product_id'=>$adjunctItems['product_id']),
                                        'goods_id' => $adjunctItems['goods_id'],
                                        'order_id' => $order_data['order_id'],
                                        'item_type'=>'adjunct',
                                        'bn' => $adjunctItems['bn'],
                                        'name' => $adjunctItems['name'],
                                        'type_id' => $adjunctItems['type_id'],
                                        'cost' => $adjunctItems['price']['cost'],
                                        'quantity' => $adjunctItems['quantity'],
                                        'sendnum' => 0,
                                        'amount' => $this->objMath->number_multiple(array($adjunctItems['price']['buy_price'], $this->objMath->number_multiple(array($adjunctItems['quantity'], $arrObjItems['quantity'])))),
                                        'price' => $adjunctItems['price']['buy_price'],
                                        'weight' => $adjunctItems['weight'],
                                        'addon' => "",
                                    ); 
                                    
                                    // 处理adjunct库存冻结
                                    if ($store_mark == '1' && $storage_enable != 'true')
                                        $objGoods->freez($adjunctItems['goods_id'], $adjunctItems['product_id'], $this->objMath->number_multiple(array($adjunctItems['quantity'], $arrObjItems['quantity'])));
                                }
                            }
                            // 赠品...
                            if (isset($arrObjItems['gift']) && $arrObjItems['gift'])
                            {
                                $str_start = count($order_data['order_objects'][$index]['order_items']);
                                foreach ($arrObjItems['gift'] as $key=>$adgiftItems)
                                {
                                    $order_data['order_objects'][$index]['order_items'][$key + $str_start] = array(
                                        'products' => array('product_id'=>$adgiftItems['product_id']),
                                        'goods_id' => $adgiftItems['goods_id'],
                                        'order_id' => $order_data['order_id'],
                                        'item_type'=>'gift',
                                        'bn' => $adgiftItems['bn'],
                                        'name' => $adgiftItems['name'],
                                        'type_id' => $adgiftItems['type_id'],
                                        'cost' => $adgiftItems['price']['cost'],
                                        'quantity' => $adgiftItems['quantity'],
                                        'sendnum' => 0,
                                        'amount' => $this->objMath->number_multiple(array($adgiftItems['price']['buy_price'], $this->objMath->number_multiple(array($adgiftItems['quantity'], $arrObjItems['quantity'])))),
                                        'price' => $adgiftItems['price']['buy_price'],
                                        'weight' => $adgiftItems['weight'],
                                        'addon' => "",
                                    ); 
                                    
                                    // 处理赠品库存冻结
                                    if ($store_mark == '1' && $storage_enable != 'true')
                                    {
                                        if (isset($obj_app_gift) && $obj_app_gift && is_object($obj_app_gift))
                                        {
                                            $obj_goods_gift = $obj_app_gift->model('goods');
                                        }
                                        else
                                        {
                                            $obj_app_gift = app::get('gift');
                                            $obj_goods_gift = $obj_app_gift->model('goods');
                                        }
                                        $obj_goods_gift->freez($adgiftItems['goods_id'], $this->objMath->number_multiple(array($adgiftItems['quantity'], $arrObjItems['quantity'])));
                                    }
                                }
                            }                        
                            
                             // 处理product订单冻结
                            if ($store_mark == '1' && $storage_enable != 'true')
                                $objGoods->freez($arrObjItems['obj_items']['products'][0]['goods_id'], $arrObjItems['obj_items']['products'][0]['product_id'], $arrObjItems['quantity']);
                            
                            $index++;
                        }
                    }
                    else
                    {
                        if ($obj_type == "gift")
                        {
                            $index = count($order_data['order_objects']);
                            // 订单赠送的赠品...
                            if (isset($arrObjInfo['order']) && $arrObjInfo['order'])
                            {
                                foreach ($arrObjInfo['order'] as $arr_gift_info)
                                {
                                    $order_data['order_objects'][$index++] = array(
                                        'order_id' => $order_data['order_id'],
                                        'obj_type' => 'gift',
                                        'obj_alias' => '商品区块',
                                        'goods_id' => $arr_gift_info['goods_id'],
                                        'bn' => $arr_gift_info['bn'],
                                        'name' => $arr_gift_info['name'],
                                        'price' => $arr_gift_info['price']['price'],
                                        'quantity'=> $arr_gift_info['quantity'],
                                        'amount'=> $this->objMath->number_multiple(array(0, $arr_gift_info['quantity'])),
                                        'weight'=> $arr_gift_info['weight'],
                                        'score'=> $arr_gift_info['params']['consume_score'],
                                        'order_items' => array(
                                            array(
                                                'products' => array('product_id'=>$arr_gift_info['product_id']),
                                                'goods_id'=> $arr_gift_info['goods_id'],
                                                'order_id' => $order_data['order_id'],
                                                'item_type'=>'gift',
                                                'bn'=> $arr_gift_info['bn'],
                                                'name'=> $arr_gift_info['name'],
                                                'type_id'=> ($arr_gift_info['type_id'] ? $arr_gift_info['type_id'] : 0),
                                                'cost'=> $arr_gift_info['price']['cost'],
                                                'quantity'=> $arr_gift_info['quantity'],
                                                'sendnum'=>0,
                                                'amount'=>$this->objMath->number_multiple(array(0, $arrObjItems['quantity'])),
                                                'score' => $arr_gift_info['params']['consume_score'],
                                                'price'=> $arr_gift_info['price']['buy_price'],
                                                'weight'=> $arr_gift_info['weight'],
                                                'addon'=> "",
                                            ),
                                        ),
                                    );
                                    
                                    // 冻结库存...
                                    if (isset($obj_app_gift) && $obj_app_gift && is_object($obj_app_gift))
                                    {
                                        $obj_goods_gift = $obj_app_gift->model('goods');
                                    }
                                    else
                                    {
                                        $obj_app_gift = app::get('gift');
                                        $obj_goods_gift = $obj_app_gift->model('goods');
                                    }
                                    
                                    $obj_goods_gift->freez($arr_gift_info['goods_id'], $arr_gift_info['quantity']);
                                }
                            }
                            
                            // 积分兑换的赠品...
                            if (isset($arrObjInfo['cart']) && $arrObjInfo['cart'])
                            {
                                foreach ($arrObjInfo['cart'] as $arr_gift_info)
                                {
                                    $order_data['order_objects'][$index++] = array(
                                        'order_id' => $order_data['order_id'],
                                        'obj_type' => 'gift',
                                        'obj_alias' => '商品区块',
                                        'goods_id' => $arr_gift_info['info']['goods_id'],
                                        'bn' => $arr_gift_info['info']['bn'],
                                        'name' => $arr_gift_info['info']['name'],
                                        'price' => $arr_gift_info['info']['price']['price'],
                                        'quantity'=> $arr_gift_info['info']['quantity'],
                                        'amount'=> $this->objMath->number_multiple(array(0, $arr_gift_info['info']['quantity'])),
                                        'weight'=> $arr_gift_info['info']['weight'],
                                        'score'=> $arr_gift_info['info']['params']['consume_score'],
                                        'order_items' => array(
                                            array(
                                                'products' => array('product_id'=>$arr_gift_info['info']['product_id']),
                                                'goods_id'=> $arr_gift_info['info']['goods_id'],
                                                'order_id' => $order_data['order_id'],
                                                'item_type'=>'gift',
                                                'bn'=> $arr_gift_info['info']['bn'],
                                                'name'=> $arr_gift_info['info']['name'],
                                                'type_id'=> ($arr_gift_info['info']['type_id'] ? $arr_gift_info['info']['type_id'] : 0),
                                                'cost'=> $arr_gift_info['info']['price']['cost'],
                                                'quantity'=> $arr_gift_info['info']['quantity'],
                                                'sendnum'=>0,
                                                'amount'=>$this->objMath->number_multiple(array(0, $arrObjItems['quantity'])),
                                                'score' => $arr_gift_info['info']['params']['consume_score'],
                                                'price'=> $arr_gift_info['info']['price']['buy_price'],
                                                'weight'=> $arr_gift_info['info']['weight'],
                                                'addon'=> "",
                                            ),
                                        ),
                                    );
                                    
                                    // 冻结库存...
                                    if (isset($obj_app_gift) && $obj_app_gift && is_object($obj_app_gift))
                                    {
                                        $obj_goods_gift = $obj_app_gift->model('goods');
                                    }
                                    else
                                    {
                                        $obj_app_gift = app::get('gift');
                                        $obj_goods_gift = $obj_app_gift->model('goods');
                                    }
                                    
                                    $obj_goods_gift->freez($arr_gift_info['goods_id'], $arr_gift_info['quantity']);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    
    /**
     * 订单保存
     * @param array sdf
     * @param string member indent
     * @param string message
     * @return boolean success or failure
     */
    public function save(&$sdf, &$msg='')
    {
         // 创建订单是和中心的交互
        $order = &$this->app->model('orders');
        $result = $order->save($sdf);//todo order_items表product_id字段未插入        
        //$result = true;
        
        if (!$result)
        {
            $msg = "订单生成失败！";
            return false;
        }
        else
        {
            // 与中心交互
            $this->request($sdf);
            
            return true;
        }
    }
    
    /**
     * 订单创建
     * @param array sdf
     * @return boolean success or failure
     */
    protected function request(&$sdf)
    {
        $arr_data['tid'] = $sdf['order_id'];
        $arr_data['title'] = 'Order Create';
        $arr_data['created'] = date('Y-m-d H:i:s', $sdf['createtime']);
        $arr_data['modified'] = date('Y-m-d H:i:s', $sdf['last_modified']);
        $arr_data['status'] = ($sdf['status'] == 'active') ? 'TRADE_ACTIVE' : 'TRADE_CLOSED';
        $arr_data['pay_status'] = ($sdf['pay_status'] == '0' || !$sdf['pay_status']) ? 'PAY_NO' : 'PAY_FINISH';
        $arr_data['ship_status'] = ($sdf['ship_status'] == '0' || !$sdf['ship_status']) ? 'SHIP_NO' : 'SHIP_FINISH';        
        $arr_data['has_invoice'] = ($sdf['is_tax'] == 'true' || $sdf['is_tax'] === true) ? true : false;
        $arr_data['invoice_title'] = $sdf['tax_title'];
        $arr_data['invoice_fee'] = $sdf['cost_tax'];
        $arr_data['total_goods_fee'] = $sdf['cost_item'];
        $arr_data['total_trade_fee'] = $sdf['total_amount'];
        $arr_data['discount_fee'] = $sdf['discount'];
        $arr_data['payed_fee'] = $sdf['payed'];
        $arr_data['currency'] = $sdf['currency'];
        $arr_data['currency_rate'] = $sdf['cur_rate'];
        $arr_data['total_currency_fee'] = $sdf['cur_amount'];
        $arr_data['buyer_obtain_point_fee'] = $sdf['score_g'];
        $arr_data['point_fee'] = $sdf['score_u'];
        $arr_data['total_weight'] = $sdf['weight'];
        $arr_data['receiver_time'] = $sdf['consignee']['r_time'] ? $sdf['consignee']['r_time'] : '';
        $arr_data['shipping_tid'] = $sdf['shipping']['shipping_id'];
        $arr_data['shiptype_name'] = $sdf['shipping']['shipping_name'];
        $arr_data['shipping_fee'] = $sdf['shipping']['cost_shipping'];
        $arr_data['is_protect'] = $sdf['shipping']['is_protect'];
        $arr_data['protect_fee'] = $sdf['shipping']['cost_protect'];
        $opayment = app::get('ectools')->model('payment_cfgs');
        $arr_payment = $opayment->getPaymentInfo($sdf['payinfo']['pay_app_id']);
        $arr_data['paytype_name'] = $arr_payment['app_display_name'];
        $arr_data['is_cod'] = $sdf['payinfo']['pay_app_id'] == '货到付款' ? 'true' : 'false';
        $arr_data['receiver_name'] = $sdf['consignee']['name'];
        $arr_data['receiver_email'] = $sdf['consignee']['email'] ? $sdf['consignee']['email'] : '';
        $arr_data['receiver_mobile'] = $sdf['consignee']['mobile'];
        $arr_states = explode(':', $sdf['consignee']['area']);
        $str_states = $arr_states[1];
        $arr_states = explode('/', $str_states);
        $arr_data['receiver_state'] = trim($arr_states[0]);
        $arr_data['receiver_city'] = trim($arr_states[1]);
        $arr_data['receiver_district'] = trim($arr_states[2]);
        $arr_data['receiver_address'] = $sdf['consignee']['addr'];
        $arr_data['receiver_zip'] = $sdf['consignee']['zip'] ? $sdf['consignee']['zip'] : '';
        $arr_data['receiver_phone'] = $sdf['consignee']['telephone'] ? $sdf['consignee']['telephone'] : '';
        $arr_data['commission_fee'] = $sdf['payinfo']['cost_payment'];
        $arr_data['trade_memo'] = '';
        $arr_data['orders_number'] = 1;
        
        $index = 0;
        foreach ($sdf['order_objects'] as $odr_obj)
        {
            $arr_data['orders']['order'][$index] = array(
                'oid' => intval($sdf['order_id']),
                'type' => ($odr_obj['obj_type'] == 'goods') ? 'goods' : 'gift',
                'type_alias' => $odr_obj['obj_alias'],
                'iid' => $odr_obj['goods_id'],
                'title' => $odr_obj['name'],
                'items_num' => intval($odr_obj['quantity']),
                'order_status' => 'SHIP_NO',
                'total_order_fee' => $this->objMath->number_multiple(array($odr_obj['price'], $odr_obj['quantity'])),
                'discount_fee' => 0,
                'consign_time' => '',
                'order_items' => array('item' => array()),
                'weight' => $odr_obj['weight'],
            );
            
            foreach ($odr_obj['order_items'] as $odr_item)
            {
                $arr_data['orders']['order'][$index]['order_items']['item'][] = array(
                    'sku_id' => $odr_item['products']['product_id'],
                    'iid' => $odr_item['goods_id'],
                    'bn' => $odr_item['bn'],
                    'name' => $odr_item['name'],
                    'weight' => $odr_item['weight'],
                    'score' => $odr_item['score'],
                    'price' => $odr_item['price'],
                    'num' => $odr_item['quantity'],
                    'sendnum' => $odr_item['sendnum'],
                    'total_item_fee' => 0,
                    'item_type' => $odr_item['item_type'],
                );
            }
            
            $index++;
        }
        
        if ($arr_data['orders'])
            $arr_data['orders'] = json_encode($arr_data['orders']);
        
        $arr_callback = array(
            'class' => 'b2c_api_callback_app', 
            'method' => 'callback',
            'params' => array(
                'method' => 'store.trade.add',
                'tid' => $sdf['order_id'],
            ),
        );
        
        // 回朔和请求
        parent::request('store.trade.add', $arr_data, $arr_callback, 'Order Create', 1);
        
        return true;
    }
}