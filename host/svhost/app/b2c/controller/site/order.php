<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_ctl_site_order extends b2c_frontpage{

    var $noCache = true;

    public function __construct(&$app){
        parent::__construct($app);
        $this->header .= '<meta name="robots" content="noindex,noarchive,nofollow" />';
        $this->_response->set_header('Cache-Control', 'no-store');
        $this->title=__('订单中心');
        $this->objMath = kernel::single("ectools_math");
    }

    public function create()
    {
        // 判断顾客登录方式.
        $login_type = $this->app->getConf('site.login_type');
        $arrMember = $this->get_current_member();
        
        if ($login_type == 'href' && !$arrMember['member_id'] && $_COOKIE['S']['ST_ShopEx-Anonymity-Buy'] != 'true')
            $this->redirect(array('app'=>'b2c','ctl'=>'site_cart','act'=>'loginbuy','arg0'=>'1'));

        // checkout url
        //$url_checkout = $this->gen_url(array('app'=>'b2c','ctl'=>'site_cart','act'=>'checkout'));
        $this->begin(array('app'=>'b2c','ctl'=>'site_cart','act'=>'checkout'));
        
        $this->mCart = $this->app->model('cart');
        $aCart = $this->mCart->get_objects(true);        
        if ($this->mCart->is_empty($aCart))
        {
            $this->end(false,__('操作失败，购物车为空！'),$this->gen_url(array('app'=>'b2c','ctl'=>'site_cart','act'=>'index')));
        }
        
        $msg = "";
        if (!$_POST['delivery']['ship_area'] || !$_POST['delivery']['ship_addr_area'] || !$_POST['delivery']['ship_addr'] || !$_POST['delivery']['ship_name'] || (!$_POST['delivery']['ship_email'] && !$arrMember['member_id']) || !$_POST['delivery']['ship_mobile'] || !$_POST['delivery']['shipping_id'] || !$_POST['payment']['pay_app_id'])
        {
            if (!$_POST['delivery']['ship_area'] || !$_POST['delivery']['ship_addr_area'])
            {
                $msg .= "收货地区不能为空！<br />";
            }
            
            if (!$_POST['delivery']['ship_addr'])
            {
                $msg .= "收货地址不能为空！<br />";
            }
            
            if (!$_POST['delivery']['ship_name'])
            {
                $msg .= "收货人姓名不能为空！<br />";
            }
            
            if (!$_POST['delivery']['ship_email'] && !$arrMember['member_id'])
            {
                $msg .= "Email不能为空！<br />";
            }
            
            if (!$_POST['delivery']['ship_mobile'])
            {
                $msg .= "手机号码不能为空！<br />";
            }
            
            if (!$_POST['delivery']['shipping_id'])
            {
                $msg .= "配送方式不能为空！<br />";
            }
            
            if (!$_POST['payment']['pay_app_id'])
            {
                $msg .= "支付方式不能为空！<br />";
            }
            
            if (strpos($msg, '<br />') !== false)
            {
                $msg = substr($msg, 0, strlen($msg) - 6);
            }
            eval("\$msg = __(\"$msg\");");

            $this->end(false, $msg);
        }
        
        // 添加收货地址
        if ($arrMember['member_id'] && isset($_POST['delivery']['is_save']) && $_POST['delivery']['is_save'] && !$_POST['delivery']['addr_id'])
        {
            if ($_POST['delivery']['ship_name'] && $_POST['delivery']['ship_mobile'] && $_POST['delivery']['ship_area'] && $_POST['delivery']['ship_addr'])
            {
                $obj_member_addr = $this->app->model('member_addrs');
                $count = $obj_member_addr->count(array('member_id' => $arrMember['member_id']));
                if ($count < 5)
                {
                    $obj_members = $this->app->model('members');
                    $arrMemberAddr = array(
                        'name' => $_POST['delivery']['ship_name'],
                        'phone' => array(
                                        'mobile' => $_POST['delivery']['ship_mobile'],
                                        'telephone' => $_POST['delivery']['ship_tel'] ? $_POST['delivery']['ship_tel'] : '',
                                    ),
                        'area' => $_POST['delivery']['ship_area'],
                        'addr' => $_POST['delivery']['ship_addr'],
                        'zipcode' => $_POST['delivery']['ship_zip'] ? $_POST['delivery']['ship_zip'] : '',
                    );
                    
                    $obj_members->insertRec($arrMemberAddr, $arrMember['member_id'], $message);
                }
            }
        }
        
        $obj_dlytype = $this->app->model('dlytype');
        if ($_POST['payment']['pay_app_id'] == '-1')
        {
            $arr_dlytype = $obj_dlytype->dump($_POST['delivery']['shipping_id'], '*');
            if ($arr_dlytype['has_cod'] == 'false')
            {
                $this->end(false, $this->app->_("ship_method_consistent_error"));
            }
        }
        
        $obj_filter = kernel::single('b2c_site_filter');
        $_POST = $obj_filter->check_input($_POST);
        
        //$obj_api_order = kernel::service("api.b2c.order");
        $order = &$this->app->model('orders');
        $_POST['order_id'] = $order_id = $order->gen_id();
        $_POST['member_id'] = $arrMember['member_id'] ? $arrMember['member_id'] : 0;
        $order_data = array();
        $obj_order_create = kernel::single("b2c_order_create");
        $order_data = $obj_order_create->generate($_POST);
        
        $result = $obj_order_create->save($order_data, $msg);
        //$result = true;
        // 取到日志模块
        if ($arrMember['member_id'])
        {
            $obj_members = $this->app->model('members');
            $arrPams = $obj_members->dump($arrMember['member_id'], '*', array(':account@pam' => array('*')));
        }
        
        // remark create
        $obj_order_create = kernel::single("b2c_order_remark");
        $arr_remark = array(
            'order_bn' => $order_id,
            'mark_text' => $_POST['memo'],
            'op_name' => (!$arrMember['member_id']) ? '顾客' : $arrPams['pam_account']['login_name'],
            'mark_type' => 'b0',
        );
        
        $log_text = "";
        if ($result)
        {
            $log_text = "订单创建成功！";
        }
        else
        {
            $log_text = "订单创建失败！";
        }
        $orderLog = $this->app->model("order_log");
        $sdf_order_log = array(
            'rel_id' => $order_id,
            'op_id' => $arrMember['member_id'],
            'op_name' => (!$arrMember['member_id']) ? '顾客' : $arrPams['pam_account']['login_name'],
            'alttime' => time(),
            'bill_type' => 'order',
            'behavior' => 'creates',
            'result' => 'SUCCESS',
            'log_text' => $log_text,
        );
        
        $log_id = $orderLog->save($sdf_order_log);
        
        if ($result)
        {    
            foreach(kernel::servicelist('b2c_save_post_om') as $object) 
            {
                $object->set_arr($order_id, 'order');
            }
            
            // 设定优惠券不可以使用
            $objCarts = $this->app->model('cart')->get_objects(true);
            if (isset($objCarts['object']['coupon']) && $objCarts['object']['coupon'])
            {
                $obj_coupon = kernel::single("b2c_coupon_mem");
                foreach ($objCarts['object']['coupon'] as $coupons)
                {
                    if($coupons['used'])
                        $obj_coupon->use_c($coupons['coupon'], $arrMember['member_id']);
                }                
            }            
        
            // 订单成功后清除购物车的的信息
            $this->cart_model = &$this->app->model('cart_objects');
            $this->cart_model->remove_object();
            
            // 生成cookie有效性的验证信息
            setcookie('ST_ShopEx-Order-Buy', md5($this->app->getConf('certificate.token').$order_id));
            setcookie("S[ST_ShopEx-Anonymity-Buy]", "false", time() - 3600);
            
            // 得到物流公司名称
            if ($order_data['order_objects'])
            {
                $itemNum = 0;
                $good_id = "";
                $goods_name = "";
                foreach ($order_data['order_objects'] as $arr_objects)
                {
                    if ($arr_objects['order_items'])
                    {
                        if ($arr_objects['obj_type'] == 'goods')
                        {
                            $obj_goods = $this->app->model('goods');
                            $good_id = $arr_objects['order_items'][0]['goods_id'];
                            $arr_goods = $obj_goods->dump($good_id);
                        }
                            
                        foreach ($arr_objects['order_items'] as $arr_items)
                        {
                            $itemNum = $this->objMath->number_plus(array($itemNum, $arr_items['quantity']));
                            if ($arr_objects['obj_type'] == 'goods')
                            {
                                if ($arr_items['item_type'] == 'product')
                                    $goods_name .= $arr_items['name'] . ($arr_items['products']['spec_info'] ? '(' . $arr_items['products']['spec_info'] . ')' : '') . '(' . $arr_items['quantity'] . ')';
                            }
                        }
                    }
                }
                $arr_dlytype = $obj_dlytype->dump($order_data['shipping']['shipping_id'], 'dt_name');
                $arr_updates = array(
                    'order_id' => $order_id,
                    'total_amount' => $order_data['total_amount'],
                    'shipping_id' => $arr_dlytype['dt_name'],
                    'ship_mobile' => $order_data['consignee']['mobile'],
                    'ship_tel' => $order_data['consignee']['telephone'],
                    'ship_addr' => $order_data['consignee']['addr'],
                    'ship_email' => $order_data['consignee']['email'] ? $order_data['consignee']['email'] : '',
                    'ship_zip' => $order_data['consignee']['zip'],
                    'ship_name' => $order_data['consignee']['name'],
                    'member_id' => $order_data['member_id'] ? $order_data['member_id'] : 0,
                    'uname' => (!$order_data['member_id']) ? '顾客' : $arrPams['pam_account']['login_name'],
                    'itemnum' => count($order_data['order_objects']),
                    'goods_id' => $good_id,
                    'goods_url' => kernel::base_url(1).kernel::url_prefix().$this->gen_url(array('app'=>'b2c','ctl'=>'site_product','act'=>'index','arg0'=>$good_id)),
                    'thumbnail_pic' => base_storager::image_path($arr_goods['image_default_id']),
                    'goods_name' => $goods_name,
                    'ship_status' => '',
                    'pay_status' => 'Nopay',
                    'is_frontend' => true,
                );
                $order->fireEvent('create', $arr_updates, $order_data['member_id']);
            }
        }        
        
        if ($result)
            $this->end(true, $this->app->_("订单生成成功！"), $this->gen_url(array('app'=>'b2c','ctl'=>'site_order','act'=>'index','arg0'=>$order_id)));
        else
            $this->end(false, $msg, $this->gen_url(array('app'=>'b2c','ctl'=>'site_cart','act'=>'checkout')));
    }

    public function index($order_id, $selecttype=false)
    {
        $objOrder = &$this->app->model('orders');
        $sdf = $objOrder->dump($order_id);
        
        // 校验订单的会员有效性.
        $is_verified = ($this->_check_verify_member($sdf['member_id'])) ? $this->_check_verify_member($sdf['member_id']) : false;
        
        // 校验订单的有效性.
        if ($_COOKIE['ST_ShopEx-Order-Buy'] != md5($this->app->getConf('certificate.token').$order_id) && !$is_verified)
        {
            $this->begin();
            $this->end(false,  __('订单无效！'), $this->gen_url(array('app'=>'site','ctl'=>'default','act'=>'index')));
        }
        
        if(!$sdf){
            exit;
        }
        
        $sdf['total_amount'] = $this->objMath->number_minus(array($sdf['total_amount'], $sdf['payed']));
        $sdf['cur_amount'] = $this->objMath->number_multiple(array($sdf['total_amount'], $sdf['cur_rate']));

        $this->pagedata['order'] = $sdf;

        if($selecttype){
            $selecttype = 1;
        }else{
            $selecttype = 0;
        }
        $this->pagedata['order']['selecttype'] = $selecttype;

        //        $objCur = app::get('ectools')->model('currency');
        //        $aCur = $objCur->getDefault();
        $opayment = app::get('ectools')->model('payment_cfgs');
        $this->pagedata['payments'] = $opayment->getList('*', array('status' => 'true', 'is_frontend' => true));
        $system_money_decimals = $this->app->getConf('system.money.decimals');
        $system_money_operation_carryset = $this->app->getConf('system.money.operation.carryset');
        foreach ($this->pagedata['payments'] as $key=>&$arrPayments)
        {
            if (!$sdf['member_id'])
            {
                if (trim($arrPayments['app_id']) == 'deposit')
                {
                    unset($this->pagedata['payments'][$key]);
                    continue;
                }
            }
            
            if ($arrPayments['app_id'] == $this->pagedata['order']['payinfo']['pay_app_id'])
            {
                $this->pagedata['order']['payinfo']['pay_name'] = $arrPayments['app_name'];
                $arrPayments['cur_money'] = $this->objMath->formatNumber($this->pagedata['order']['cur_amount'], $system_money_decimals, $system_money_operation_carryset);
                $arrPayments['total_amount'] = $this->objMath->formatNumber($this->pagedata['order']['total_amount'], $system_money_decimals, $system_money_operation_carryset);
            }
            else
            {
                $arrPayments['total_amount'] = $this->objMath->number_minus(array($this->pagedata['order']['total_amount'], $this->pagedata['order']['payed']));
                if ($this->pagedata['order']['payinfo']['cost_payment'] > 0)
                {
                    $cost_payments_rate = $this->objMath->number_div(array($arrPayments['total_amount'], $this->pagedata['order']['total_amount']));
                    $cost_payment = $this->objMath->number_multiple(array($this->pagedata['order']['payinfo']['cost_payment'], $cost_payments_rate));
                    $arrPayments['total_amount'] = $this->objMath->number_minus(array($arrPayments['total_amount'], $cost_payment));
                    $arrPayments['total_amount'] = $this->objMath->number_plus(array($arrPayments['total_amount'], $this->objMath->number_multiple(array($arrPayments['total_amount'], $arrPayments['pay_fee']))));
                }
                else
                {
                    $cost_payment = $this->objMath->number_multiple(array($arrPayments['total_amount'], $arrPayments['pay_fee']));
                    $arrPayments['total_amount'] = $this->objMath->number_plus(array($arrPayments['total_amount'], $cost_payment));
                }
                
                $arrPayments['total_amount'] = $this->objMath->formatNumber($arrPayments['total_amount'], $system_money_decimals, $system_money_operation_carryset);
                $arrPayments['cur_money'] = $this->objMath->formatNumber($this->objMath->number_multiple(array($arrPayments['total_amount'], $this->pagedata['order']['cur_rate'])), $system_money_decimals, $system_money_operation_carryset);
            }
        }
        
        if ($this->pagedata['order']['payinfo']['pay_app_id'] == '货到付款')
        {
            $this->pagedata['order']['payinfo']['pay_app_id'] = '-1';
            $this->pagedata['order']['payinfo']['pay_name'] = '货到付款';
        }
                
        $objCur = app::get('ectools')->model('currency');
        $aCur = $objCur->getFormat($this->pagedata['order']['currency']);
        $this->pagedata['order']['cur_def'] = $aCur['sign'];
        
        $this->pagedata['return_url'] = $this->gen_url(array('app'=>'b2c','ctl'=>'site_paycenter','act'=>'result'));
        $this->pagedata['res_url'] = $this->app->res_url;
        $this->set_tmpl('order_index');
        $this->page('site/order/index.html');
    }

    public function detail($order_id, $selecttype=false)
    {
        $objOrder = &$this->app->model('orders');
        $subsdf = array('order_objects'=>array('*',array('order_items'=>array('*',array(':products'=>'*')))), 'order_pmt'=>array('*'));
        $sdf = $objOrder->dump($order_id, '*', $subsdf);
        $this->objMath = kernel::single("ectools_math");
        
        // 校验订单的会员有效性.
        $is_verified = ($this->_check_verify_member($sdf['member_id'])) ? $this->_check_verify_member($sdf['member_id']) : false;
        
        // 校验订单的有效性.
        if ($_COOKIE['ST_ShopEx-Order-Buy'] != md5($this->app->getConf('certificate.token').$order_id) && !$is_verified)
        {
            $this->begin();
            $this->end(false,  __('订单无效！'), array('app'=>'site','ctl'=>'default','act'=>'index'));
        }
        
       $order_items = array();
        
        if ($sdf['payinfo']['pay_app_id'] == '货到付款')
        {
            $sdf['payinfo']['pay_key'] = '-1';
        }
        else if ($sdf['payinfo']['pay_app_id'] == 'offline')
        {
            $sdf['payinfo']['pay_key'] = 'OFFLINE';
        }
        else if ($sdf['payinfo']['pay_app_id'] == 'deposit')
        {
            $sdf['payinfo']['pay_key'] = 'DEPOSIT';
        }
        else
        {
            $sdf['payinfo']['pay_key'] = $sdf['payinfo']['pay_app_id'];
        }
        
        if(!$sdf){
            exit;
        }
        $sdf['cur_money'] = ($sdf['total_amount'] - $sdf['payed']) * $sdf['cur_rate'];
        
        $arrMember = $this->get_current_member();

        if (!$sdf['consignee']['email'] && $arrMember['member_id'])
            $sdf['consignee']['email'] = $arrMember['email'];
            
        $this->pagedata['order'] = $sdf;

        if($selecttype){
            $selecttype = 1;
        }else{
            $selecttype = 0;
        }
        $this->pagedata['order']['selecttype'] = $selecttype;

        //        $objCur = app::get('ectools')->model('currency');
        //        $aCur = $objCur->getDefault();
        if ($this->pagedata['order']['payinfo']['pay_key'] != '-1')
        {
            $opayment = app::get('ectools')->model('payment_cfgs');
            $this->pagedata['payments'] = $opayment->getList('*', array('status' => 'true', 'is_frontend' => true));
            foreach ($this->pagedata['payments'] as &$arrPayments)
            {
                if (!$arrMember['member_id'])
                {
                    if (trim($arrPayments['app_id']) == 'deposit')
                    {
                        unset($arrPayments);
                        continue;
                    }
                }
                
                if ($arrPayments['app_id'] == $this->pagedata['order']['payinfo']['pay_app_id'])
                    $this->pagedata['order']['payinfo']['pay_name'] = $arrPayments['app_name'];
            }
        }
        else
        {
            $this->pagedata['order']['payinfo']['pay_name'] = '货到付款';
        }
        
        $objCur = app::get('ectools')->model('currency');
        $aCur = $objCur->getFormat($this->pagedata['order']['currency']);
        $this->pagedata['order']['cur_def'] = $aCur['sign'];
        $aCur = $objCur->getcur($this->pagedata['order']['currency']);
        $this->pagedata['order']['currency'] = $aCur['cur_name'];
        
        // 生成所有的items
        $this->objMath = kernel::single('ectools_math');
        $app_gift = app::get('gift');
        $gift_is_installed = false;
        if ($app_gift->is_installed())
        {
            $gift_is_installed = true;
            $objGiftGoods = $app_gift->model('goods');
        }
        
        foreach ($this->pagedata['order']['order_objects'] as $k=>$arrOdr_object)
        {
            $index = 0;
            $index_adj = 0;
            $index_gift = 0;
            if ($arrOdr_object['obj_type'] == 'goods')
            {            
                foreach($arrOdr_object['order_items'] as $key => $item)
                {                                        
                    if ($item['item_type'] != 'gift')
                    {
                        $objGoods = $this->app->model('goods');
                        $arrGoods = $objGoods->dump($item['goods_id'], 'goods_id,cat_id,score,price,name,udfimg,thumbnail_pic,small_pic,big_pic,image_default_id');
                        $objGoodsCat = $this->app->model('goods_cat');
                        $arrGoodsCat = $objGoodsCat->dump($arrGoods['category']['cat_id'], 'cat_name');
                    
                        $gItems[$k]['addon'] = unserialize($item['addon']);
                        if($item['addon'] && unserialize($item['addon'])){
                            $gItems[$k]['minfo'] = unserialize($item['addon']);
                        }else{
                            $gItems[$k]['minfo'] = array();
                        }
                        
                        if ($item['item_type'] == 'product')
                        {  
                            $order_items[$k] = $item;
                            $order_items[$k]['thumbnail_pic'] = $arrGoods['image_default_id'];
                            $order_items[$k]['is_type'] = $arrOdr_object['obj_type'];
                            $order_items[$k]['item_type'] = $arrGoodsCat['cat_name'];
                            $order_items[$k]['minfo'] = $gItems[$k]['minfo'];
                            
                            if (isset($item['products']['spec_info']) && $item['products']['spec_info'])
                            {
                                $order_items[$k]['name'] = $item['products']['name'] . '(' . $item['products']['spec_info'] . ')';
                            }
                            else
                            {
                                $order_items[$k]['name'] = $item['products']['name'];
                            }
                        }
                        else
                        {
                            $order_items[$k]['adjunct'][$index_adj] = $item;
                            $order_items[$k]['adjunct'][$index_adj]['thumbnail_pic'] = $arrGoods['image_default_id'];
                            $order_items[$k]['adjunct'][$index_adj]['is_type'] = $arrOdr_object['obj_type'];
                            $order_items[$k]['adjunct'][$index_adj]['item_type'] = $arrGoodsCat['cat_name'];
                            
                            if (isset($item['products']['spec_info']) && $item['products']['spec_info'])
                            {
                                $order_items[$k]['adjunct'][$index_adj]['name'] = $item['products']['name'] . '(' . $item['products']['spec_info'] . ')';
                            }
                            else
                                $order_items[$k]['adjunct'][$index_adj]['name'] = $item['products']['name'];
                            
                            $index_adj++;
                        }
                    }
                    else
                    {
                        if ($gift_is_installed)
                        {
                            $arrGoods = $objGiftGoods->dump($item['goods_id'], '*');
                            
                            $order_items[$k]['gifts'][$index_gift] = $item;
                            $order_items[$k]['gifts'][$index_gift]['thumbnail_pic'] = $arrGoods['image_default_id'];
                            $order_items[$k]['gifts'][$index_gift]['is_type'] = $arrOdr_object['obj_type'];
                            $order_items[$k]['gifts'][$index_gift]['item_type'] = $arrGoodsCat['category']['cat_name'];
                            
                            if (isset($item['products']['spec_info']) && $item['products']['spec_info'])
                            {
                                $order_items[$k]['gifts'][$index_gift]['name'] = $item['products']['name'] . '(' . $item['products']['spec_info'] . ')';
                            }
                            else
                                $order_items[$k]['gifts'][$index_gift]['name'] = $item['name'];
                                
                            $index_gift++;
                        }
                    }
                }
            }
            else
            {
                if ($gift_is_installed)
                {
                    foreach ($arrOdr_object['order_items'] as $gift_key => $gift_item)
                    {
                        if (isset($gift_items[$gift_item['goods_id']]) && $gift_items[$gift_item['goods_id']])
                            $gift_items[$gift_item['goods_id']]['nums'] = $this->objMath->number_plus(array($gift_items[$gift_item['goods_id']]['nums'], $gift_item['quantity']));
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
                                'thumbnail_pic' => $arrGoods['image_default_id'],
                                'is_type' => $arrOdr_object['obj_type'],
                                'amount' => $gift_item['amount'],
                            );
                        }
                    }
                }
            }
        }
        
        if (isset($this->pagedata['order']['order_pmt']) && $this->pagedata['order']['order_pmt'])
        {
            foreach ($this->pagedata['order']['order_pmt'] as &$arr_order_pmt)
            {
                if ($arr_order_pmt['pmt_type'] == 'coupon')
                {
                    $this->pagedata['order']['coupon_p'][] = $arr_order_pmt;
                    unset($arr_order_pmt);
                }
            }
        }
        
        $this->pagedata['order']['order_items'] = $order_items;
        $this->pagedata['order']['gifts'] = $gift_items;
        $this->pagedata['order']['cost_item'] = $this->objMath->number_minus(array($this->pagedata['order']["cost_item"], $this->pagedata['aCart']['discount_amount_prefilter']));
        
        $this->pagedata['return_url'] = $this->app->router()->gen_url(array('app'=>'b2c','ctl'=>'site_paycenter','act'=>'result'));
        $this->pagedata['res_url'] = $this->app->res_url;
        $this->set_tmpl('order_detail');
        $this->page('site/order/detail.html');
    }

}
