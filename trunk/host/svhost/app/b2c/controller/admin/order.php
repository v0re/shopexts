<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_ctl_admin_order extends desktop_controller{

    var $workground = 'b2c_ctl_admin_order';
    
    /**
     * 构造方法
     * @params object app object
     * @return null
     */
    public function __construct($app)
    {
        parent::__construct($app);
        header("cache-control: no-store, no-cache, must-revalidate");
        $this->objMath = kernel::single('ectools_math');
    }

    public function index(){
        $this->finder('b2c_mdl_orders',array(
            'title'=>'订单列表',
            'allow_detail_popup'=>true,
            'actions'=>array(
                            array('label'=>'添加订单','icon'=>'add.gif','href'=>'index.php?app=b2c&ctl=admin_order&act=addnew','target'=>'_blank'),
                            array('label'=>'打印样式','icon'=>'add.gif','href'=>'index.php?app=b2c&ctl=admin_order&act=showPrintStyle'),
                            array('label'=>'打印选定订单','icon'=>'add.gif','submit'=>'index.php?app=b2c&ctl=admin_order&act=toprint','target'=>'_blank'),
                        ),'use_buildin_set_tag'=>true,'use_buildin_recycle'=>true,'use_buildin_filter'=>true,'use_view_tab'=>true,
            ));
    }
    
    /**
     * 桌面订单相信汇总显示
     * @param null
     * @return null
     */
    public function _views(){
        $mdl_order = $this->app->model('orders');
        $sub_menu = array(
            0=>array('label'=>__('全部'),'optional'=>false,'filter'=>""),
            1=>array('label'=>__('未处理'),'optional'=>false,'filter'=>array('pay_status'=>array('0'),'ship_status'=>array('0'),'status'=>'active')),
            2=>array('label'=>__('已付款待发货'),'optional'=>false,'filter'=>array('pay_status'=>array('1','2','3'),'ship_status'=>array('0','2'),'status'=>'active')),
            3=>array('label'=>__('已发货'),'optional'=>false,'filter'=>array('ship_status'=>array('1'),'status'=>'active')),
            4=>array('label'=>__('已完成'),'optional'=>false,'filter'=>array('status'=>'finish')),
            5=>array('label'=>__('已退款'),'optional'=>false,'filter'=>array('pay_status'=>array('4','5'),'status'=>'active')),
            6=>array('label'=>__('已退货'),'optional'=>false,'filter'=>array('ship_status'=>array('3','4'),'status'=>'active')),
            7=>array('label'=>__('已作废'),'optional'=>false,'filter'=>array('status'=>'dead')),
        );
        //新留言订单
        $filter = array('adm_read_status'=>'false');
        $orders_num = kernel::single('b2c_message_order')->count($filter);
        $_filter['order_id'] = array();
        foreach(kernel::single('b2c_message_order')->getList('order_id',$filter) as $order){
            $_filter['order_id'][] = $order['order_id'];
        }
        $sub_menu[8] = array('label'=>__('新留言订单'),'optional'=>true,'filter'=>$_filter,'addon'=>$orders_num,'href'=>'index.php?app=b2c&ctl=admin_order&act=index&view=8&view_from=dashboard');

        $mdl_orders = $this->app->model('orders');
        //今日订单
        $today_filter = array(
                    '_createtime_search'=>'between',
                    'createtime_from'=>date('Y-m-d',strtotime('TODAY')),
                    'createtime_to'=>date('Y-m-d'),
                    'createtime' => date('Y-m-d'),
                    '_DTIME_'=>
                        array(
                            'H'=>array('createtime_from'=>'00','createtime_to'=>date('H')),
                            'M'=>array('createtime_from'=>'00','createtime_to'=>date('i'))
                        )
                );
        $today_order = $mdl_orders->count($today_filter);
        $sub_menu[9] = array('label'=>__('今日订单'),'optional'=>true,'filter'=>$today_filter,'addon'=>$today_order,'href'=>'index.php?app=b2c&ctl=admin_order&act=index&view=9&view_from=dashboard');

        //昨日订单
        $date = strtotime('yesterday');
        $yesterday_filter = array(
                    '_createtime_search'=>'between',
                    'createtime_from'=>date('Y-m-d',$date),
                    'createtime_to'=>date('Y-m-d',strtotime('today')),
                    'createtime' => date('Y-m-d',$date),
                    '_DTIME_'=>
                        array(
                            'H'=>array('createtime_from'=>'00','createtime_to'=>date('H',$date)),
                            'M'=>array('createtime_from'=>'00','createtime_to'=>date('i',$date))
                        )
                );
        $yesterday_order = $mdl_orders->count($yesterday_filter);
        $sub_menu[10] = array('label'=>__('昨日订单'),'optional'=>true,'filter'=>$yesterday_filter,'addon'=>$yesterday_order,'href'=>'index.php?app=b2c&ctl=admin_order&act=index&view=10&view_from=dashboard');

        //今日已付款订单
        $today_filter = array_merge($today_filter,array('pay_status'=>'1'));
        $today_payed = $mdl_orders->count($today_filter);
        $sub_menu[11] = array('label'=>__('今日已付款'),'optional'=>true,'filter'=>$today_filter,'addon'=>$today_payed,'href'=>'index.php?app=b2c&ctl=admin_order&act=index&view=11&view_from=dashboard');

        //昨日已付款订单
        $yesterday_filter = array_merge($yesterday_filter,array('pay_status'=>'1'));
        $yesterday_payed = $mdl_orders->count($yesterday_filter);
        $sub_menu[12] = array('label'=>__('昨日已付款'),'optional'=>true,'filter'=>$yesterday_filter,'addon'=>$yesterday_payed,'href'=>'index.php?app=b2c&ctl=admin_order&act=index&view=11&view_from=dashboard');

        if(isset($_GET['optional_view'])) $sub_menu[$_GET['optional_view']]['optional'] = false;


        foreach($sub_menu as $k=>$v){
            if($v['optional']==false){
                $show_menu[$k] = $v;
                $show_menu[$k]['filter'] = $v['filter']?$v['filter']:null;
                $show_menu[$k]['addon'] = $mdl_order->count($v['filter']);
                $show_menu[$k]['href'] = 'index.php?app=b2c&ctl=admin_order&act=index&view='.($k).(isset($_GET['optional_view'])?'&optional_view='.$_GET['optional_view'].'&view_from=dashboard':'');
            }elseif(($_GET['view_from']=='dashboard')&&$k==$_GET['view']){
                $show_menu[$k] = $v;
            }
        }
        return $show_menu;
    }
    
    /**
     * 添加订单
     * @param null
     * @return null
     */
    public function addnew(){
        $this->pagedata['finder_id'] = $_GET['finder_id'];
        $this->singlepage('admin/order/detail/page.html');
    }
    
    /**
     * 订单创建的第二步
     * @param null
     * @return null
     */
    public function create()
    {
        $order = &$_POST['order'];
        $member_point = 0;
        if (!empty($order['member_id']))
        {
            $objMember = &$this->app->model('members');
            $aUser = $objMember->dump($order['member_id']);
            if (empty($aUser['pam_account']['account_id']))
            {
                header('Content-Type:text/jcmd; charset=utf-8');
                echo '{error:"不存在的会员名称！",_:null}';
                //echo __('<script>alert("不存在的会员名称!")</script>');
                exit;
            }
            // 得到当前会员的积分
            $member_point = $aUser['score']['total'];
        }
        else
        {
            $aUser['pam_account']['account_id'] = 0;
            $aUser['member_lv']['member_group_id'] = 0;
        }
        $_SESSION['tmp_admin_create_order'] = array();
        $_SESSION['tmp_admin_create_order']['member'] = $aUser;

        if(!$order['product_id']){//todo goods_id为product_id，遗留问题
            //echo __('<script>MessageBox.error("没有购买商品或者购买数量为0!",{autohide:5000});alert("xx");</script>');
            header('Content-Type:text/jcmd; charset=utf-8');
            echo '{error:"没有购买商品或者购买数量为0！",_:null}';
            exit;
        }
        
        $data = array();
        // 生成购物车数据
        $mdl_product = $this->app->model('products');
        foreach($_POST['order']['product_id'] as $product_id)
        {
            $product = $mdl_product->dump($product_id,'*');
            $data['goods'][] = array('goods'=>array(
                'goods_id'=>$product['goods_id'],
                'product_id'=>$product['product_id'],
                'adjunct' => 'na',
                'num' =>$_POST['goodsnum'][$product_id]
            ));
        }
        
        // 购物券数据
        if (isset($_POST['coupon']) && $_POST['coupon'])
        {
            foreach ($_POST['coupon'] as $arr_coupon)
            {
                $data['coupon'][] = array(
                    'coupon'=> $arr_coupon['name'],
                );
            }
        }
        
        //$data['coupon'][] = array('coupon' => 'B12124444744800002');
        
        $obj_mCart = $this->app->model('cart');
        if ($order['member_id'])
        {
            $member_indent = md5(kernel::single('base_session')->sess_id());
            $data_org = $obj_mCart->get_cookie_cart_arr($member_indent);
            if ($data_org)
                $obj_mCart->del_cookie_cart_arr($member_indent);
            
            if ($_COOKIE['orders']['last_member_id'])
            {
                $member_indent = md5($_COOKIE['orders']['last_member_id'] . kernel::single('base_session')->sess_id());
                $data_org = $obj_mCart->get_cookie_cart_arr($member_indent);
                
                if ($data_org)
                    $obj_mCart->del_cookie_cart_arr($member_indent);        
            }
            
            setcookie('orders[last_member_id]', $order['member_id']);
            $member_indent = md5($order['member_id'] . kernel::single('base_session')->sess_id());
        }
        else
            $member_indent = md5(kernel::single('base_session')->sess_id());
               
        $obj_mCart->set_cookie_cart_arr($data, $member_indent);            
        $arr_cart_objects = $obj_mCart->get_cart_object($data);
        
        if (!isset($arr_cart_objects['cart_status']) || !$arr_cart_objects['cart_status'] || $arr_cart_objects['cart_status'] == 'true')
        {
            if($aUser['pam_account']['account_id'])
            {
                $member_addrs = &$this->app->model('member_addrs');
                $addrlist = $member_addrs->getList('*',array('member_id'=>$aUser['pam_account']['account_id']));
                
                foreach ($addrlist as $rows)
                {
                    if (empty($rows['tel']))
                    {
                        $str_tel = __('手机：').$rows['mobile'];
                    }
                    else
                    {
                        $str_tel = __('电话：').$rows['tel'];
                    }
                    
                    $addr[] = array(
                        'addr_id'=> $rows['addr_id'],
                        'def_addr'=>$rows['def_addr'],
                        'addr_region'=> $rows['area'],
                        'addr_label'=> $rows['addr'].__(' (收货人：').$rows['name'].' '.$str_tel.__(' 邮编：').$rows['zip'].')'
                    );
                }
                
                $this->pagedata['addrlist'] = $addr;
                $this->pagedata['is_allow'] = (count($addr)<5 ? 1 : 0);
                $this->pagedata['address']['member_id'] = $aUser['pam_account']['account_id'];
            }
            
            $currency = app::get('ectools')->model('currency');
            $this->pagedata['currencys'] = $currency->getList('cur_id,cur_code,cur_name');

            //$obj_payments = new b2c_payment_getlist();
            //$this->pagedata['payment_html'] = $obj_payments->get_view($this, $this->user->user_id);
            $obj_payments = new ectools_payment_select();
            $sdf_payment = array();
            $this->pagedata['payment_html'] = $obj_payments->select_pay_method($this, $sdf_payment, $order['member_id'], true);
            $this->pagedata['member_id'] = $aUser['pam_account']['account_id'];
            
            // 得到税金的信息
            $this->pagedata['trigger_tax'] = $this->app->getConf("site.trigger_tax");
            $this->pagedata['tax_ratio'] = $this->app->getConf("site.tax_ratio");
            
            $demical = $this->app->getConf('system.money.operation.decimals');
            
            $total_item = $this->objMath->number_minus(array($arr_cart_objects["subtotal"], $$arr_cart_objects['discount_amount_prefilter']));
            // 取到商店积分规则
            $policy_method = $this->app->getConf("site.get_policy.method");
            switch ($policy_method)
            {
                case '1':
                    $subtotal_consume_score = 0;
                    $subtotal_gain_score = 0;
                    $totalScore = 0;
                    break;
                case '2':
                    $subtotal_consume_score = round($arr_cart_objects['subtotal_consume_score']);
                    $policy_rate = $this->app->getConf('site.get_rate.method');
                    $subtotal_gain_score = round($this->objMath->number_plus(array(0, $arr_cart_objects['subtotal_gain_score'])));
                    $totalScore = round($this->objMath->number_minus(array($subtotal_gain_score, $subtotal_consume_score)));
                    break;
                case '3':
                    $subtotal_consume_score = round($arr_cart_objects['subtotal_consume_score']);
                    $subtotal_gain_score = round($arr_cart_objects['subtotal_gain_score']);
                    $totalScore = round($this->objMath->number_minus(array($subtotal_gain_score, $subtotal_consume_score)));
                    break;
                default:
                    $subtotal_consume_score = 0;
                    $subtotal_gain_score = 0;
                    $totalScore = 0;
                    break;
            }
            
            $total_amount = $this->objMath->number_minus(array($arr_cart_objects["subtotal"], $arr_cart_objects['discount_amount']));
            // 得到cart total支付的信息
            $this->pagedata['order_detail'] = array(
                'cost_item' => $total_item,
                'total_amount' => $total_amount,
                'currency' => $this->app->getConf('site.currency.defalt_currency'),
                'pmt_amount' => $arr_cart_objects['discount_amount'],
                'totalConsumeScore' => $subtotal_consume_score,
                'totalGainScore' => $subtotal_gain_score,
                'totalScore' => $member_point,
                'cur_code' => $strDefCurrency,
                'cur_display' => $strDefCurrency,
                'cur_rate' => $aCur['cur_rate'],
                'final_amount' => $currency->changer($total_amount, $this->app->getConf("site.currency.defalt_currency"), true),
            );
            
            $odr_decimals = $this->app->getConf('system.money.decimals');
            $total_amount = $this->objMath->get($this->pagedata['order_detail']['total_amount'], $odr_decimals);        
            $this->pagedata['order_detail']['discount'] = $this->objMath->number_minus(array($this->pagedata['order_detail']['total_amount'], $total_amount));
            $this->pagedata['order_detail']['total_amount'] = $total_amount;
            $this->pagedata['order_detail']['current_currency'] = $strDefCurrency;
        }
        else
        {
            $this->pagedata['cart_error_html'] = $arr_cart_objects['cart_error_html'];
        }
        $this->pagedata['finder_id'] = $_POST['finder_id'];
        $this->pagedata['cart_status'] = (!isset($arr_cart_objects['cart_status']) || !$arr_cart_objects['cart_status'] || $arr_cart_objects['cart_status'] == 'true') ? true : false;
        $this->display('admin/order/order_create.html');
    }
    
    /**
     * 打印选定订单
     * @param null
     * @return null
     */
    public function toprint()
    {
        if ($_POST['order_id'])
        {
            $aInput = $_POST['order_id'];
        }
        elseif ($orderid)
        {
            $aInput = array($orderid);
        }
        else
        {
            $this->begin('index.php?app=b2c&ctl=admin_order&act=index');
            $this->end(false, __('打印失败：订单参数传递出错'));
            exit();
        }

        $oCur = app::get('ectools')->model('currency');


        $dbTmpl = $this->app->model('member_systmpl');
        foreach ($aInput as $orderid)
        {
            $aData = array();
            $objOrder = $this->app->model('orders');
            $subsdf = array('order_objects'=>array('*',array('order_items'=>array('*',array(':products'=>'*')))));
            $orderInfo = $objOrder->dump($orderid, '*', $subsdf);#print_r($orderInfo);exit;
            $aData = $orderInfo;
            $aCur = $oCur->getcur($aData['currency']);
            $aData['currency'] = $aCur['cur_name'];

            $objMember = $this->app->model('members');
            $aMember = $objMember->dump($orderInfo['member_id'], '*', array(':account@pam'=>'*'));
            $aData['member'] = $aMember;
            $payment = app::get('ectools')->model('payment_cfgs');
            $aPayment = $payment->getPaymentInfo($aData['payinfo']['pay_app_id']);#print_r($orderInfo);exit;
            $aData['payment'] = $aPayment['app_name'];

            $aData['shopname'] = app::get('site')->getConf('site.name');
            $aData['shopaddress'] = $this->app->getConf('store.address');
            $aData['shoptelphone'] = $this->app->getConf('store.telephone');
            $aData['shopzip'] = $this->app->getConf('store.zip_code');
            #$aItems = $objOrder->getItemList($orderid);
            #$aItems = $orderInfo;
            /*
            foreach($aItems as $k => $rows){
            $aItems[$k]['addon'] = unserialize($rows['addon']);
            if($rows['minfo'] && unserialize($rows['minfo'])){
            $aItems[$k]['minfo'] = unserialize($rows['minfo']);
            }else{
            $aItems[$k]['minfo'] = array();
            }
            if($aItems[$k]['addon']['adjname']) $aItems[$k]['name'] .= __('<br>配件：').$aItems[$k]['addon']['adjname'];
            $aItems[$k]['catname'] = $objOrder->getCatByPid($rows['product_id']);
            }*/
            #$aData['goodsItems'] = $orderInfo['order_objects'];
            $goods = $this->app->model('goods');
            $goods_cat = $this->app->model('goods_cat');
            foreach ($orderInfo['order_objects'] as $val)
            {
                foreach ( $val['order_items'] as $v)
                {
                    if ($v['item_type'] != 'gift')
                    {
                        $cat_id = $goods->dump($v['goods_id'],'cat_id');
                        $arrcat_name = $goods_cat->dump($cat_id['category']['cat_id'],'cat_name');
                        $v['catname'] = $arrcat_name['cat_name']?$arrcat_name['cat_name']:'---';
                    }
                    
                    if ($v['item_type'] === 'gift')
                    {
                        $row = $goods->getList('params',array('goods_id' => $v['goods_id'],'goods_type' => 'gift'));
                        $v['point'] = $row[0]['params']['consume_score']?$row[0]['params']['consume_score']:0;
                        $aData['giftItems'][] = $v;
                    }
                    elseif ($v['item_type'] === 'adjunct')
                    {
                        $v['name'] = __('<br>配件：').$v['name'].'('.$v['products']['spec_info'].')'; 
                        $aData['goodsItems'][] = $v;
                    }
                    else 
                        $aData['goodsItems'][] = $v;
                }

            }
            $this->pagedata['pages'][] = $dbTmpl->fetch('admin/order/orderprint',array('order'=>$aData));
        }
        $this->pagedata['shopname'] = $aData['shopname'];

        $this->display('admin/order/print_order.html');
    }
    
    /**
     * 打印订单的接口
     * @param string 打印类型
     * @param string order id
     * @return null
     */
    public function printing($type,$order_id)
    {
        $order = &$this->app->model('orders');
        $member = &$this->app->model('members');
        //$order->setPrintStatus($order_id,$type,true);
        
        $subsdf = array('order_objects'=>array('*',array('order_items'=>array('*',array(':products'=>'*')))));
        $orderInfo = $order->dump($order_id, '*', $subsdf);
        $orderInfo['self'] = $this->objMath->number_minus(array(0, $orderInfo['discount'], $orderInfo['pmt_amount']));
        
        $memberInfo = $member->dump($orderInfo['member_id'], 'point');
        $order_items = array();
        $gift_items = array();
        foreach ($orderInfo['order_objects'] as $k=>$v)
        {
            $index = 0;
            $index_adj = 0;
            $index_gift = 0;
            if ($v['obj_type'] == 'goods')
            {
                foreach ($v['order_items'] as $key => $item)
                {            
                    $objGoods = $this->app->model('goods');
                    $arrGoods = $objGoods->dump($item['goods_id'], 'goods_id,cat_id,score,price,name,udfimg,thumbnail_pic,small_pic,big_pic,image_default_id');
                    $objGoodsCat = $this->app->model('goods_cat');
                    $arrGoodsCat = $objGoodsCat->dump($arrGoods['category']['cat_id'], 'cat_name');
                       
                    if ($item['item_type'] != 'gift')
                    {
                        $gItems[$k]['addon'] = unserialize($item['addon']);
                        
                        if ($item['minfo'] && unserialize($item['minfo']))
                        {
                            $gItems[$k]['minfo'] = unserialize($item['minfo']);
                        }
                        else
                        {
                            $gItems[$k]['minfo'] = array();
                        }
                        
                        if ($item['item_type'] == 'product')
                        {  
                            $order_items[$k] = $item;
                            $order_items[$k]['small_pic'] = $arrGoods['image_default_id'];
                            $order_items[$k]['is_type'] = $v['obj_type'];
                            $order_items[$k]['item_type'] = $arrGoodsCat['cat_name'];
                            
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
                            $order_items[$k]['adjunct'][$index_adj]['small_pic'] = $arrGoods['image_default_id'];
                            $order_items[$k]['adjunct'][$index_adj]['is_type'] = $v['obj_type'];
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
                        $objGoods = app::get('gift')->model('goods');
                        $arrGoods = $objGoods->dump($item['goods_id'], '*');
                        
                        $order_items[$k]['gifts'][$index_gift] = $item;
                        $order_items[$k]['gifts'][$index_gift]['small_pic'] = $arrGoods['image_default_id'];
                        $order_items[$k]['gifts'][$index_gift]['is_type'] = $v['obj_type'];
                        $order_items[$k]['gifts'][$index_gift]['item_type'] = $arrGoods['category']['cat_name'];
                        
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
            else
            {
                foreach ($v['order_items'] as $gift_key => $gift_item)
                {
                    if (isset($gift_items[$gift_item['goods_id']]) && $gift_items[$gift_item['goods_id']])
                        $gift_items[$gift_item['goods_id']]['nums'] = $objMath->number_plus(array($gift_items[$gift_item['goods_id']]['nums'], $item['quantity']));
                    else
                    {
                        $objGoods = app::get('gift')->model('goods');
                        $arrGoods = $objGoods->dump($item['goods_id'], '*');
                        
                        $gift_items[$gift_item['goods_id']] = array(
                            'goods_id' => $gift_item['goods_id'],
                            'bn' => $gift_item['bn'],
                            'nums' => $gift_item['quantity'],
                            'name' => $gift_item['name'],
                            'item_type' => $arrGoods['category']['cat_name'],
                            'price' => $gift_item['price'],
                            'quantity' => $gift_item['quantity'],
                            'sendnum' => $gift_item['sendnum'],
                            'small_pic' => $arrGoods['image_default_id'],
                            'is_type' => $v['obj_type'],
                        );
                    }
                }
            }
        }
        
        $order_sum = $this->sum_order($orderInfo['member_id']);
        $this->pagedata['goodsItem'] = $order_items;
        $this->pagedata['giftsItem'] = $gift_items;
        $this->pagedata['orderInfo'] = $orderInfo;
        $this->pagedata['orderSum'] = $order_sum;
        $this->pagedata['res_url'] = $this->app->res_url;
        $this->pagedata['memberPoint'] = $memberInfo['score']['total'] ? $memberInfo['score']['total'] : 0;
        $this->pagedata['storeplace_display_switch'] = $this->app->getConf('storeplace.display.switch');
        $this->pagedata['defaultImage'] = $imageDefault['S']['default_image'];
        $this->pagedata['shop'] = array(
            'name'=>app::get('site')->getConf('site.name'),
            'url'=>kernel::base_url(true),
            'email'=>$this->app->getConf('store.email'),
            'tel'=>$this->app->getConf('store.telephone'),
            'logo'=>$this->app->getConf('site.logo')
        );
        
        switch($type)
        {
            case $order->arr_print_type['ORDER_PRINT_CART']:  /*购物清单*/
                $this->pagedata['printType'] = array("cart");
                $this->pagedata['printContent']['cart'] = true;
                $this->pagedata['memberPoint'] = $memberInfo['score']['total'] ? $memberInfo['score']['total'] : 0;
                $this->display('admin/order/print.html');
                break;

            case $order->arr_print_type['ORDER_PRINT_SHEET']:    /*配货单*/
                $this->pagedata['printContent']['sheet'] = true;
                $this->pagedata['memberPoint'] = $memberInfo['score']['total'] ? $memberInfo['score']['total'] : 0;
                $this->display('admin/order/print.html');
                break;

            case $order->arr_print_type['ORDER_PRINT_MERGE']:    /*联合打印*/
                $this->pagedata['printType'] = array("cart");
                $this->pagedata['printContent']['cart'] = true;
                $this->pagedata['printContent']['sheet'] = true;
                $this->pagedata['memberPoint'] = $memberInfo['point']?$memberInfo['point']:0;
                $this->display('admin/order/print.html');
                break;

            case $order->arr_print_type['ORDER_PRINT_DLY']:    /*快递单打印*/
                $printer = &app::get('express')->model('dly_center');
                $this->pagedata['dly_centers'] = $printer->getList('dly_center_id,name',array('disable'=>'false'));
                $this->pagedata['default_dc'] = $this->app->getConf('system.default_dc');
                $this->pagedata['the_dly_center'] = $printer->dump($this->pagedata['default_dc']?$this->pagedata['default_dc']:$this->pagedata['dly_centers'][0]['dly_center_id']);

                $printer = &app::get('express')->model('print_tmpl');
                $this->pagedata['printers'] = $printer->getList('prt_tmpl_id,prt_tmpl_title',array('shortcut'=>'true'));
                $this->pagedata['type'] = 'ORDER_PRINT_DLY';

                $this->singlepage('admin/order/detail/printer.html');
                break;
            default:
                echo __('无效的打印类型');
                break;
        }
    }
    
    /**
     * 求出同一个会员对应订单的总额
     * @param string member id
     * @return array 订单数组
     */
    public function sum_order($member_id=null)
    {
        $obj_order = $this->app->model('orders');
        $aData = $obj_order->getList('total_amount',array('member_id' => $member_id));
        if($aData){
            $row['sum'] = count($aData);
            $row['sum_pay'] = 0;
            foreach($aData as $val){
                $row['sum_pay'] = $row['sum_pay']+$val['total_amount'];
            }
        }
        else{
            $row['sum'] = 0;
            $row['sum_pay'] = 0;
        }
        return $row;
    }
    
    /**
     * 保存订单的收货地址
     * @param string order id
     * @return null
     */
    public function save_addr($order_id)
    {
        $obj_order = $this->app->model('orders');
        $arr_order = $obj_order->dump($order_id);
        
        $arr_order['consignee']['name'] = $_POST['order']['ship_name'];
        $arr_order['consignee']['area'] = $_POST['order']['ship_area'];
        $arr_order['consignee']['zip'] = $_POST['order']['ship_zip'];
        $arr_order['consignee']['addr'] = $_POST['order']['ship_addr'];
        $arr_order['consignee']['mobile'] = $_POST['order']['ship_mobile'];
        $arr_order['consignee']['telephone'] = $_POST['order']['ship_tel'];
        $arr_order['consignee']['memo'] = $_POST['order']['order_memo'];
        
        if($obj_order->save($arr_order)){
            echo 'ok';
        }
    }

    /**
     * 产生支付页面
     * @params string order id
     * @return string html
     */
    public function gopay($order_id)
    {
        if (!$order_id)
        {
            echo __('订单号传递出错');
            return false;
        }
        
        $this->pagedata['orderid'] = $order_id;
        $objOrder = &$this->app->model('orders');
        $aORet = $objOrder->dump($order_id);

        $this->pagedata['op_name'] = 'admin';
        //$this->pagedata['typeList'] = array('online'=>__("在线支付"), 'offline'=>__("线下支付"), 'deposit'=>__("预存款支付"));
        $this->pagedata['typeList'] = array('online'=>__("在线支付"), 'offline'=>__("线下支付"));
        $this->pagedata['pay_type'] = ($aPayid['pay_type'] == 'ADVANCE' ? 'deposit' : 'offline');
        // 此时为支付状态
        $this->pagedata['bill_type'] = "payments";

        if ($aORet['member_id'] > 0)
        {
            $objPayments = &app::get('ectools')->model('payments');
            $aRet = $objPayments->getAccount();
            $this->pagedata['member'] = $aRet;
        }
        else 
        {
            $this->pagedata['member'] = array();
        }
        $this->pagedata['order'] = $aORet;
        
        $aAccount = array(__('--使用已存在帐户--'));
        if (isset($aRet) && $aRet)
        {
            foreach ($aRet as $account_info)
            {
                $str_bank = $account_info['bank'] ? $account_info['bank'] : '0';
                $str_account = $account_info['account'] ? $account_info['account'] : '0';
                $aAccount[$str_bank."-".$str_account] = $str_bank." - ".$str_account;
            }
        }
        
        $opayment = app::get('ectools')->model('payment_cfgs');
        $this->pagedata['payment'] = $opayment->getList('*', array('status' => 'true', 'is_frontend' => true));
        if (!$aORet['member_id'])
        {
            if ($this->pagedata['payment'])
            {
                foreach ($this->pagedata['payment'] as $key=>$arr_payments)
                {
                    if (trim($arr_payments['app_id']) == 'deposit')
                    {
                        unset($this->pagedata['payment'][$key]);
                    }
                }
            }
        }
        $this->pagedata['pay_account'] = $aAccount;

        $this->display('admin/order/gopay.html');
    }
    
    /**
     * 订单开始支付
     * @params null
     * @return null
     */
    public function dopay()
    {
        $sdf = $_POST;
        $this->begin();
        
        //todo 生产sdf
        $objOrders = $this->app->model('orders');
        $sdf_order = $objOrders->dump($sdf['order_id'], '*');
        
        $obj_checkorder = kernel::service('b2c_order_apps', array('content_path'=>'b2c_order_checkorder'));
        if (!$obj_checkorder->check_order_pay($sdf['order_id'],$sdf,$message))
        {            
            $this->end(false, $message);            
        }
        
        $objPay = kernel::single("ectools_pay");
        $payment_id = $sdf['payment_id'] = $objPay->get_payment_id();        
        
        $arrOperations = array(
            'op_id' => $sdf['op_id'],
            'op_name' => $sdf['op_name'],
        );
        
        if (!isset($sdf['payment']) || !$sdf['payment'])
        {
            $sdf['pay_app_id'] = $sdf_order['payinfo']['pay_app_id'];
            
            $cost_payments_rate = $this->objMath->number_div(array($sdf['money'], $sdf_order['total_amount']));
            $cost_payment = $this->objMath->number_multiple(array($sdf_order['payinfo']['cost_payment'], $cost_payments_rate));
        }
        else
        {
            $sdf['pay_app_id'] = $sdf['payment'];
            
            $cost_payments_rate = $this->objMath->number_div(array($sdf['money'], $sdf_order['total_amount']));
            $cost_payment = $this->objMath->number_multiple(array($sdf_order['payinfo']['cost_payment'], $cost_payments_rate));
        }
        
        $sdf['currency'] = $sdf_order['currency'];
        $sdf['payinfo']['cost_payment'] = $cost_payment;
                
        $sdf['pay_object'] = 'order';
        $sdf['member_id'] = $sdf['op_id'] = $this->user->user_id;
        $sdf['op_name'] = $this->user->user_data['account']['login_name'];
        $sdf['status'] = 'succ';        
        
        $time = time();
        
        $is_payed = $objPay->gopay($sdf, $msg);
        if (!$is_payed)
        {
            eval("\$msg = __(\"$msg\");");
            $this->end(false, $msg); 
        }
        
        // 订单的处理
        $obj_pay_lists = kernel::servicelist("order.pay_finish");
        foreach ($obj_pay_lists as $order_pay_service_object)
        {
            $class_name = get_class($order_pay_service_object);
            if (strpos($class_name, $this->app->app_id . '_') !== false)
            {
                $order_pay_service_object->order_pay_finish($sdf, 'succ', 'Back');
                break;
            }
        }
        
        $this->end(true, __('此次订单支付成功！'));
    }
    
    /**
     * 生成退款单页面
     * @params string order id
     * @return string html
     */
    public function gorefund($order_id)
    {
        if (!$order_id)
        {
            echo __('订单号传递出错');
            return false;
        }
        
        $this->pagedata['orderid'] = $order_id;
        $objOrder = &$this->app->model('orders');
        $aORet = $objOrder->dump($order_id);
        
        $this->pagedata['payment_id'] = $aORet['payment'];
        $this->pagedata['op_name'] = 'admin';
        
        if ($aORet['member_id'])
            $this->pagedata['typeList'] = array('online'=>__("在线支付"), 'offline'=>__("线下支付"), 'deposit'=>__("预存款支付"));
        else
            $this->pagedata['typeList'] = array('online'=>__("在线支付"), 'offline'=>__("线下支付"));
            
        $this->pagedata['pay_type'] = ($aPayid['pay_type'] == 'ADVANCE' ? 'deposit' : 'offline');

        if ($aORet['member_id'] > 0)
        {
            $objMember = &$this->app->model('members');
            $aRet = $objMember->dump($aORet['member_id']);
            $this->pagedata['member'] = $aRet;
        }
        else
        {
            $this->pagedata['member'] = array();
        }
        $this->pagedata['order'] = $aORet;

        $aAccount = array(__('--使用已存在帐户--'));
        if (isset($aRet) && $aRet)
        {
            foreach ($aRet as $v){
                $aAccount[$v['bank']."-".$v['account']] = $v['bank']." - ".$v['account'];
            }
        }
        $this->pagedata['pay_account'] = $aAccount;
        
        $opayment = app::get('ectools')->model('payment_cfgs');
        $this->pagedata['payment'] = $opayment->getList('*', array('status' => 'true', 'is_frontend' => true));
        if (!$aORet['member_id'])
        {
            if ($this->pagedata['payment'])
            {
                foreach ($this->pagedata['payment'] as $key=>$arr_payments)
                {
                    if (trim($arr_payments['app_id']) == 'deposit')
                    {
                        unset($this->pagedata['payment'][$key]);
                    }
                }
            }
        }

        $this->display('admin/order/gorefund.html');
    }
    
    /**
     * 退款处理
     * @params null
     * @return null
     */
    public function dorefund()
    {
        if(!$order_id) $order_id = $_POST['order_id'];
        else $_POST['order_id'] = $order_id;

        $sdf = $_POST;
        
        $this->begin();
       
        $obj_checkorder = kernel::service('b2c_order_apps', array('content_path'=>'b2c_order_checkorder'));
        if (!$obj_checkorder->check_order_refund($sdf['order_id'],$sdf,$message))
        {
             $this->end(false, $message);
        }

        $obj_order = &$this->app->model('orders');
        $subsdf = array('order_objects'=>array('*',array('order_items'=>array('*',array(':products'=>'*')))));
        $sdf_order = $obj_order->dump($sdf['order_id'],'*',$subsdf);

        if (!$sdf['money'])
        {
            //退款金额不是从弹出的退款单里输入而来
            $sdf['money'] = $sdf_order['payed'];
            $sdf['return_score'] = $sdf_order['score_g'];
        }

        $refunds = app::get('ectools')->model('refunds');

        $objOrder->op_id = $this->user->user_id;
        $objOrder->op_name = $this->user->user_data['account']['name'];
        $sdf['op_id'] = $this->user->user_id;
        $sdf['op_name'] = $this->user->user_data['account']['login_name'];
        $sdf['status'] = 'succ';
        unset($sdf['inContent']);
        
        $objPaymemtcfg = app::get('ectools')->model('payment_cfgs');
        $sdf['payment'] = ($sdf['payment']) ? $sdf['payment'] : $sdf_order['payinfo']['pay_app_id'];
        if ($sdf['payment'] == '-1')
        {
            $arrPaymentInfo['app_name'] = "货到付款";
            $arrPaymentInfo['app_version'] = "1.0";
        }
        else
            $arrPaymentInfo = $objPaymemtcfg->getPaymentInfo($sdf['payment']);
            
        $time = time();
        $refund_id = $refunds->gen_id();
        
        $sdfOrder = array(
            'refund_id' => $refund_id,
            'order_id' => $sdf['order_id'],
            'member_id' => $sdf_order['member_id'],
            'account' => $sdf['account'],
            'bank' => $sdf['bank'],
            'pay_account' => $sdf['pay_account'],
            'currency' => $sdf_order['currency'],
            'money' => $sdf['money'],
            'paycost' => $sdf_order['cost_payment'],
            'cur_money' => $sdf_order['cur_money'],
            'pay_type' => $sdf['pay_type'],
            'pay_app_id' => $sdf['payment'],
            'pay_name' => $arrPaymentInfo['app_name'],
            'pay_ver' => $arrPaymentInfo['app_version'],
            'op_id' => $sdf['op_id'],
            't_begin' => $time,
            't_payed' => $time,
            't_confirm' => $time,
            'status' => 'ready',
            'memo' => '',
            'trade_no' => '',
            'orders' => array(
                    array(
                        'rel_id' => $sdf['order_id'],
                        'bill_type' => 'refunds',
                        'pay_object' => 'order',
                        'bill_id' => $refund_id,
                        'money' => $sdf['money'],
                    )
                )
        );
        
        $sdf['refund_id'] = $refund_id;        
        $obj_refunds = kernel::single("ectools_refund");
        if ($obj_refunds->generate($sdfOrder, $this, $msg))
        {
            $obj_mdl_refunds = app::get('ectools')->model('refunds');
            $arr_refunds = $obj_mdl_refunds->dump($refund_id, '*', '*');
            
            if ($obj_order->order_refund_finish($sdf, 'succ'))
            {                
                $arr_refunds['status'] = 'succ';
                $is_save = $obj_mdl_refunds->save($arr_refunds);
                if ($is_save)
                {
                    $obj_api_refund = kernel::single("b2c_order_refund");
                    $obj_api_refund->send_request($arr_refunds);
                }
                $this->end(true, __('退款成功'));
            }
            else
            {
                $arr_refunds['status'] = 'failed';
                $obj_mdl_refunds->save($arr_refunds);
                $this->end(false, __('退款失败'));
            }        
        }
        else
        {
            $this->end(false, __('退款失败'));
        }
    }
    
    /**
     * 产生订单发货页面
     * @params string order id
     * @return string html
     */
    public function godelivery($order_id)
    {
        if (!$order_id)
        {
            echo __('订单号传递出错');
            return false;
        }
        $this->pagedata['orderid'] = $order_id;
        $objOrder = &$this->app->model('orders');
        $subsdf = array('order_objects'=>array('*',array('order_items'=>array('*',array(':products'=>'*')))));
        $aORet = $objOrder->dump($order_id,'*',$subsdf);
        $order_items = array();

        foreach ($aORet['order_objects'] as $k=>$v)
        {
            $order_items = array_merge($order_items,$v['order_items']);
        }
        $this->pagedata['items'] = $order_items;
        $shippings = $this->app->model('dlytype');
        $this->pagedata['shippings'] = $shippings->getList('*');

        $dlycorp = $this->app->model('dlycorp');
        $this->pagedata['corplist'] = $dlycorp->getList('*');
        $this->pagedata['order'] = $aORet;
        $this->pagedata['order']['protectArr'] = array('false'=>__('否'), 'true'=>__('是'));
        
        // 获得minfo
        $arrItems = array();
        $gift_items = array();
        if ($this->pagedata['order']['order_objects'])
        {    
            $app_gift = app::get('gift');
            $gift_is_installed = false;
            if ($app_gift->is_installed())
            {
                $gift_is_installed = true;
                $objGiftGoods = $app_gift->model('goods');
            }
            
            foreach ($this->pagedata['order']['order_objects'] as $arrOdrObjects)
            {
                if ($arrOdrObjects['obj_type'] == 'goods')
                {
                    $index_gift = 0;
                    foreach ($arrOdrObjects['order_items'] as $arrOdrItems)
                    {
                        if ($arrOdrItems['item_type'] != 'gift')
                        {
                            $good_id = $arrOdrItems['products']['goods_id'];
                            $product_id = $arrOdrItems['products']['product_id'];
                            $arrAddon = unserialize($arrOdrItems['addon']);
                            
                            if (isset($arrOdrItems['products']['spec_info']) && $arrOdrItems['products']['spec_info'])
                            {
                                $arrOdrItems['products']['name'] = $arrOdrItems['products']['name'] . '(' . $arrOdrItems['products']['spec_info'] . ')';
                            }
                            
                            $arrItems[] = array(
                                'bn' => $arrOdrItems['bn'],
                                'name' => $arrOdrItems['name'],
                                'minfo' => $arrAddon,
                                'addon' => $arrAddon,
                                'products' => array(
                                    'name' => $arrOdrItems['products']['name'] ? $arrOdrItems['products']['name'] : $arrOdrItems['name'],
                                    'store' => $arrOdrItems['products']['store'] ? $arrOdrItems['products']['store'] : $arrOdrItems['store'],
                                ),
                                'quantity' => $arrOdrItems['quantity'],
                                'sendnum' => $arrOdrItems['sendnum'],
                                'product_id' => $product_id,
                                'item_id' => $arrOdrItems['item_id'],
                                'needsend' => $this->objMath->number_minus(array($arrOdrItems['quantity'], $arrOdrItems['sendnum'])),
                            );
                        }
                        else
                        {
                            if ($gift_is_installed)
                            {
                                $arrGiftGoods = $objGiftGoods->dump($arrOdrItems['goods_id'], 'store');
                                $gift_items[$index_gift++] = array(
                                    'goods_id' => $arrOdrItems['goods_id'],
                                    'nums' => $arrOdrItems['quantity'],
                                    'name' => $arrOdrItems['name'],
                                    'point' => $arrOdrItems['score'] ? $arrOdrItems['score'] : '0',
                                    'sendnum' => $arrOdrItems['sendnum'],
                                    'store' => is_null($arrGiftGoods['store']) ? '无限库存' : $arrGiftGoods['store'],
                                    'needsend' => $this->objMath->number_minus(array($arrOdrItems['quantity'], $arrOdrItems['sendnum'])),
                                    'item_id' => $arrOdrItems['item_id'],
                                );
                            }
                        }
                    }
                }
                else
                {
                    if ($gift_is_installed)
                    { 
                        foreach ($arrOdrObjects['order_items'] as $gift_key => $gift_item)
                        {
                            $arrGoods = $objGiftGoods->dump($gift_item['goods_id'], '*');
                            
                            if (isset($gift_items[$index_gift]) && $gift_items[$index_gift])
                            {
                                $gift_items[$gift_item['goods_id']]['nums'] = $this->objMath->number_plus(array($gift_items[$gift_item['goods_id']]['nums'], $gift_item['quantity']));
                                $gift_items[$gift_item['goods_id']]['sendnum'] = $this->objMath->number_plus(array($gift_items[$gift_item['goods_id']]['sendnum'], $gift_item['sendnum']));
                                $gift_items[$gift_item['goods_id']]['needsend'] = $this->objMath->number_plus(array($gift_items[$gift_item['goods_id']]['nums'], $gift_items[$gift_item['goods_id']]['sendnum']));
                            }
                            else
                            {                           
                                $gift_items[$index_gift++] = array(
                                    'goods_id' => $gift_item['goods_id'],
                                    'nums' => $gift_item['quantity'],
                                    'name' => $gift_item['name'],
                                    'point' => $gift_item['score'] ? $gift_item['score'] : '0',
                                    'sendnum' => $gift_item['sendnum'],
                                    'store' => is_null($arrGoods['store']) ? '无限库存' : $arrGoods['store'],
                                    'needsend' => $this->objMath->number_minus(array($gift_item['quantity'], $gift_item['sendnum'])),
                                    'item_id' => $gift_item['item_id'],
                                );
                            }
                        }
                    }
                }
            }
        }
        
        $this->pagedata['items'] = $arrItems;
        $this->pagedata['giftItems'] = $gift_items;
        // 得到物流公司的信息
        $objDlytype = $this->app->model('dlytype');
        $arrDlytype = $objDlytype->dump($this->pagedata['order']['shipping']['shipping_id']);
        $this->pagedata['corp_id'] = $arrDlytype['corp_id'];
        
        // 获得赠品信息 todo. 赠品结构未定
        
        $this->display('admin/order/godelivery.html');
    }
    
    /**
     * 发货订单处理
     * @params null
     * @return null
     */
    public function dodelivery()
    {
        $obj_order = &$this->app->model('orders');
        if(!$order_id) $order_id = $_POST['order_id'];
        else $_POST['order_id'] = $order_id;
        
        $sdf = $_POST;

        $sdf['opid'] = $this->user->user_id;
        $sdf['opname'] = $this->user->user_data['account']['login_name'];
        $this->begin();
        
        $obj_server = kernel::service('svhost_server', array('content_path'=>'svhost_server'));
        $domain = 'test.com';#todo 
        if(!$obj_server->create($domain,$message)){
            $this->end(false, $message);
        }
        
        $obj_checkorder = kernel::service('b2c_order_apps', array('content_path'=>'b2c_order_checkorder'));
        if (!$obj_checkorder->check_order_delivery($sdf['order_id'],$sdf,$message))
        {
            $this->end(false, $message);
        }
       
        // 处理支付单据.
        $objB2c_delivery = b2c_order_delivery::getInstance($this->app, $this->app->model('delivery'));
        if ($objB2c_delivery->generate($sdf, $this, $message))
        {            
            $this->end(true, __('发货成功'));
        }
        else
        {
            $this->end(false, $message);
        }
    }
    
    /**
     * 订单退货页面
     * @params stirng orderid
     * @return string html
     */
    public function goreship($order_id)
    {
        if (!$order_id)
        {
            echo __('订单号传递出错');
            return false;
        }
        $this->pagedata['orderid'] = $order_id;
        
        $objOrder = &$this->app->model('orders');
        $subsdf = array('order_objects'=>array('*',array('order_items'=>array('*',array(':products'=>'*')))));
        $aORet = $objOrder->dump($order_id,'*',$subsdf);
        $order_items = array();
        foreach ($aORet['order_objects'] as $k=>$v)
        {
            $order_items = array_merge($order_items,$v['order_items']);
        }
        
        if (isset($order_items) && $order_items)
        {
            foreach ($order_items as &$items)
            {
                if (isset($items['products']['spec_info']) && $items['products']['spec_info'])
                {
                    $items['name'] = $items['products']['name'] . '(' . $items['products']['spec_info'] . ')';
                }
            }
        }

        $this->pagedata['order'] = $aORet;
        $this->pagedata['order']['protectArr'] = array('false'=>__('否'), 'true'=>__('是'));
        $shippings = $this->app->model('dlytype');
        $this->pagedata['shippings'] = $shippings->getList('*');
        $dlycorp = $this->app->model('dlycorp');
        $this->pagedata['corplist'] = $dlycorp->getList('*');
        $this->pagedata['items'] = $order_items;
        
        // 得到物流公司的信息
        $objDlytype = $this->app->model('dlytype');
        $arrDlytype = $objDlytype->dump($this->pagedata['order']['shipping']['shipping_id']);
        $this->pagedata['order']['shipping']['corp_id'] = $arrDlytype['corp_id'];
        $objDelivery = $this->app->model('delivery');
        $arrDeliverys = $objDelivery->getList('*', array('order_id' => $order_id));
        $this->pagedata['order']['shipping']['cost_shipping'] = '0';
        
        foreach ($arrDeliverys as $arrDeliveryInfo)
        {
            $this->pagedata['order']['shipping']['cost_shipping'] = $this->objMath->number_plus(array($this->pagedata['order']['shipping']['cost_shipping'], $arrDeliveryInfo['money']));
        }

        $this->display('admin/order/goreship.html');
    }
    
    /**
     * 订单退货
     * @params null
     * @return null
     */
    public function doreship()
    {
        if(!$order_id) $order_id = $_POST['order_id'];
        else $_POST['order_id'] = $order_id;

        $sdf = $_POST;
        
        $this->begin();
        $obj_checkorder = kernel::service('b2c_order_apps', array('content_path'=>'b2c_order_checkorder'));
        
        if (!$obj_checkorder->check_order_reship($sdf['order_id'],$sdf,$message))
        {
            $this->end(false, $message);
        }

        $sdf['op_id'] = $this->user->user_id;
        $sdf['opname'] = $this->user->user_data['account']['login_name'];
        $reship = &$this->app->model('reship');
        $sdf['reship_id'] = $reship->gen_id();
        $reship->op_id = $this->user->user_id;
        $reship->op_name = $this->user->user_data['account']['login_name'];
        
        
        // 处理支付单据.
        $b2c_order_reship = b2c_order_reship::getInstance($this->app, $reship);
        if ($b2c_order_reship->generate($sdf, $this, $message))
        {
            $this->end(true, __('退货成功'));
        }
        else
        {
            $this->end(false, $message);
        }
    }
    
    /**
     * 订单取消
     * @params string order id
     * @return null
     */
    public function docancel($order_id)
    {
        $this->begin('index.php?app=b2c&ctl=admin_order&act=index');
        
        $obj_checkorder = kernel::service('b2c_order_apps', array('content_path'=>'b2c_order_checkorder'));
        if (!$obj_checkorder->check_order_cancel($order_id,'',$message))
        {
           $this->end(false, $message);
        }
        
        $sdf['order_id'] = $order_id;
        $sdf['op_id'] = $this->user->user_id;
        $sdf['opname'] = $this->user->user_data['account']['login_name'];
        
        $b2c_order_cancel = kernel::single("b2c_order_cancel");
        if ($b2c_order_cancel->generate($sdf, $this, $message))
        {
            $this->end(true, __('订单取消成功！'));
        }
        else
        {
            $this->end(false, __('订单取失败！'));
        }
    }
    
    /**
     * 订单完成
     * @params string oder id
     * @return boolean 成功与否
     */
    public function dofinish($order_id)
    {
        $this->begin('index.php?app=b2c&ctl=admin_order&act=index');
        
        $obj_checkorder = kernel::service('b2c_order_apps', array('content_path'=>'b2c_order_checkorder'));
        if (!$obj_checkorder->check_order_finish($order_id,'',$message))
        {
            $this->end(false, $message);
        }
        
        $sdf['order_id'] = $order_id;
        $sdf['op_id'] = $this->user->user_id;
        $sdf['opname'] = $this->user->user_data['account']['login_name'];
        
        $objOrder = &$this->app->model('orders');
        
        $b2c_order_finish = kernel::single("b2c_order_finish");
        if ($b2c_order_finish->generate($sdf, $this, $message))
        {
            $this->end(true, __('完成订单成功！'));
        }
        else
        {
            $this->end(false, __('完成订单失败！'));
        }
    }
    
    /**
     * 订单备注添加，修改
     * @param null
     * @return null
     */
    public function saveMarkText()
    {
        $msg = "";
        
        $obj_order_remark = kernel::single("b2c_order_remark");
        $_POST['op_name'] = $this->user->user_data['account']['login_name'];
        $is_success = $obj_order_remark->update($_POST, $msg);
        
        if ($is_success)
        {
            echo '{success:"保存成功.",_:null,mark_text:"'.$_POST['mark_text'].'",mark_type:"'.$_POST['mark_type'].'"}';exit;
        }
        else
        {
            echo '{error:"'.$msg.'",_:null,mark_text:"'.$_POST['mark_text'].'",mark_type:"'.$_POST['mark_type'].'"}';exit;
        }
    }
    
    /**
     * 查找相对应支付方式
     * @param null
     * @return null
     */
    public function shipping()
    {
        $area_id = ($_POST['area']);
        $obj_delivery = new b2c_order_dlytype();
        $sdf = array();
        
        $member_indent = md5($_POST['member_id'] . kernel::single('base_session')->sess_id());
        $obj_mCart = $this->app->model('cart');        
        $data = $obj_mCart->get_cookie_cart_arr($member_indent);        
        $arr_cart_objects = $obj_mCart->get_cart_object($data);        
        
        echo $obj_delivery->select_delivery_method($this,$area_id,$arr_cart_objects);
    }
    
    /**
     * 计算订单总计信息
     * @param null
     * @return null
     */
    public function total()
    {    
        if ($_POST['member_id'])
            $member_indent = md5($_POST['member_id'] . kernel::single('base_session')->sess_id());
        else
            $member_indent = md5(kernel::single('base_session')->sess_id());
        
        $obj_mCart = $this->app->model('cart');        
        $data = $obj_mCart->get_cookie_cart_arr($member_indent);        
        $arr_cart_objects = $obj_mCart->get_cart_object($data);
        
        $obj_total = new b2c_order_total();
        $sdf_order = $_POST;
        echo $obj_total->order_total_method($this,$arr_cart_objects,$sdf_order);exit;
    }
    
    /**
     * 得到订单相应的支付信息
     * @param null
     * @return null
     */
    public function payment()
    {
        $obj_payment_select = new ectools_payment_select();
        $sdf = $_POST;
        echo $obj_payment_select->select_pay_method($this, $sdf, $sdf['member_id'], true);exit;
    }
    
    /**
     * 添加订单的接口
     * @param null
     * @return null
     */
    public function docreate()
    {
        $this->begin("index.php?app=b2c&ctl=admin_order&act=addnew");
        
        $msg = "";
        if (!$_POST['delivery']['ship_area'] || !$_POST['delivery']['ship_addr_area'] || !$_POST['delivery']['ship_addr'] || !$_POST['delivery']['ship_name'] || (!$_POST['delivery']['ship_email'] && !$_POST['member_id']) || !$_POST['delivery']['ship_mobile'] || !$_POST['delivery']['shipping_id'] || !$_POST['payment']['pay_app_id'])
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
            
            if (!$_POST['delivery']['ship_email'] && !$this->user->user_id)
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
        if ($_POST['member_id'] && isset($_POST['delivery']['is_save']) && $_POST['delivery']['is_save'] && !$_POST['delivery']['addr_id'])
        {
            if ($_POST['delivery']['ship_name'] && $_POST['delivery']['ship_mobile'] && $_POST['delivery']['ship_area'] && $_POST['delivery']['ship_addr'])
            {
                $obj_member_addr = $this->app->model('member_addrs');
                $count = $obj_member_addr->count(array('member_id' => $_POST['member_id']));
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
                    
                    $is_save = $obj_members->insertRec($arrMemberAddr, $_POST['member_id'], $message);
                }
            }
        }
        
        if (!$_POST['member_id'])
            $member_indent = md5(kernel::single('base_session')->sess_id());
        else
            $member_indent = md5($_POST['member_id'] . kernel::single('base_session')->sess_id());
        
        $obj_mCart = $this->app->model('cart');
        $data = $obj_mCart->get_cookie_cart_arr($member_indent);         
        $objCarts = $obj_mCart->get_cart_object($data);
        $is_empty = $obj_mCart->is_empty($objCarts);
        if ($is_empty)
        {
            $this->end(false, __('购物车为空，操作失败！'));
            
        }
        
        $order = &$this->app->model('orders');
        $_POST['order_id'] = $order_id = $order->gen_id();
        $order_data = array();
        $obj_order_create = kernel::single("b2c_order_create");
        $order_data = $obj_order_create->generate($_POST, $member_indent);
        $result = $obj_order_create->save($order_data, $msg);
        
        // 取到日志模块
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
            'op_id' => $this->user->user_id,
            'op_name' => $this->user->user_data['account']['login_name'],
            'alttime' => time(),
            'bill_type' => 'order',
            'behavior' => 'creates',
            'result' => ($result) ? 'SUCCESS' : 'FAILURE',
            'log_text' => $log_text,
        );
        
        $log_id = $orderLog->save($sdf_order_log);
        
        if ($result)
        {            
            // 订单成功后清除购物车的的信息            
            $cart_model = $this->app->model('cart');
            $cart_model->del_cookie_cart_arr($member_indent);
            
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
                $obj_dlytype = $this->app->model('dlytype');
                $arr_dlytype = $obj_dlytype->dump($order_data['shipping']['shipping_id'], 'dt_name');
                
                if ($order_data['member_id'])
                {
                    $obj_members = $this->app->model('members');
                    $arrPams = $obj_members->dump($order_data['member_id'], '*', array(':account@pam' => array('*')));
                }
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
                    'goods_url' => kernel::base_url(1).kernel::url_prefix().app::get('site')->router()->gen_url(array('app'=>'b2c','ctl'=>'site_product','act'=>'index','arg0'=>$good_id)),
                    'thumbnail_pic' => base_storager::image_path($arr_goods['image_default_id']),
                    'goods_name' => $goods_name,
                    'ship_status' => '',
                    'pay_status' => 'Nopay',
                    'is_frontend' => false,
                );
                $order->fireEvent('create', $arr_updates, $order_data['member_id']);
            }
        }
        
        if ($result)
            $this->end(true, '订单创建成功', "index.php?app=b2c&ctl=admin_order&act=index");
        else
            $this->end(false, $msg, "index.php?app=b2c&ctl=admin_order&act=index");
    }
    
    /**
     * 管理员保存订单留言的回复
     * @params null
     * @return null
     */
    public function saveOrderMsgText()
    {   
        $_POST['author_id'] = $this->user->user_id;
        $_POST['author'] = __('管理员');
        $_POST['to_type'] = 'member';
        //$obj_api_order = kernel::service("api.b2c.order");
        $obj_order_message = kernel::single("b2c_order_message");
        
        if (!$obj_order_message->create($_POST, $msg))
        {
            $this->begin();
            $this->end(false,__('保存留言失败！'));
        }
        else
        {            
            $oMsg = &kernel::single("b2c_message_order");
            $orderMsg = $oMsg->getList('*', array('order_id' => $_POST['msg']['orderid'], 'object_type' => 'order'), $offset=0, $limit=-1, 'time DESC');
            $this->pagedata['ordermsg'] = $orderMsg;
            echo $this->fetch("admin/order/od_msg_item.html");
        }
    }
    
    /**
     * 显示订单详情的接口
     * @param string order id
     * @return null
     */
    public function showEdit($orderid)
    {
        $this->path[] = array('text'=>__('订单编辑'));
        $objOrder = &$this->app->model('orders');
        $aOrder = $objOrder->dump($orderid,'*');
        $aOrder['discount'] = 0 - $aOrder['discount'];

        $objCurrency = app::get('ectools')->model("currency");
        $aCur = $objCurrency->getSysCur();

        $subsdf = array('order_objects'=>array('*',array('order_items'=>array('*',array(':products'=>'*')))));
        $aORet = $objOrder->dump($orderid,'*',$subsdf);
        $order_items = array();
        foreach($aORet['order_objects'] as $k=>$v)
        {
            $index = 0;
            $index_adj = 0;
            $index_gift = 0;
            if ($v['obj_type'] == 'goods')
            {
                foreach($v['order_items'] as $key => $item)
                {             
                    $objGoods = $this->app->model('goods');
                    $arrGoods = $objGoods->dump($item['goods_id'], 'goods_id,cat_id     ,score,price,name,udfimg,thumbnail_pic,small_pic,big_pic,image_default_id');
                    $objGoodsCat = $this->app->model('goods_cat');
                    $arrGoodsCat = $objGoodsCat->dump($arrGoods['category']['cat_id'], 'cat_name');
                       
                    if ($item['item_type'] != 'gift')
                    {
                        $gItems[$k]['addon'] = unserialize($item['addon']);
                        if($item['minfo'] && unserialize($item['minfo'])){
                            $gItems[$k]['minfo'] = unserialize($item['minfo']);
                        }else{
                            $gItems[$k]['minfo'] = array();
                        }
                        
                        if ($item['item_type'] == 'product')
                        {  
                            $order_items[$k] = $item;
                            $order_items[$k]['small_pic'] = $arrGoods['image_default_id'];
                            $order_items[$k]['is_type'] = $v['obj_type'];
                            $order_items[$k]['item_type'] = $arrGoodsCat['cat_name'];
                            
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
                            $order_items[$k]['adjunct'][$index_adj]['small_pic'] = $arrGoods['image_default_id'];
                            $order_items[$k]['adjunct'][$index_adj]['is_type'] = $v['obj_type'];
                            $order_items[$k]['adjunct'][$index_adj]['item_type'] = $arrGoodsCat['cat_name'];
                            
                            if (isset($item['products']['spec_info']) && $item['products']['spec_info'])
                            {
                                $order_items[$k]['adjunct'][$index_adj]['name'] = $item['products']['name'] . '(' . $item['products']['spec_info'] . ')';
                            }
                            else
                                $order_items[$k]['adjunct'][$index_adj]['name'] = $item['name'];
                            
                            $index_adj++;
                        }
                    }
                    else
                    {
                        $objGoods = app::get('gift')->model('goods');
                        $arrGoods = $objGoods->dump($item['goods_id'], '*');
                            
                        $order_items[$k]['gifts'][$index_gift] = $item;
                        $order_items[$k]['gifts'][$index_gift]['small_pic'] = $arrGoods['image_default_id'];
                        $order_items[$k]['gifts'][$index_gift]['is_type'] = $v['obj_type'];
                        $order_items[$k]['gifts'][$index_gift]['item_type'] = $arrGoods['category']['cat_name'];
                        
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
            else
            {
                foreach ($v['order_items'] as $gift_key => $gift_item)
                {
                    if (isset($gift_items[$gift_item['goods_id']]) && $gift_items[$gift_item['goods_id']])
                        $gift_items[$gift_item['goods_id']]['nums'] = $this->objMath->number_plus(array($gift_items[$gift_item['goods_id']]['nums'], $item['quantity']));
                    else
                    {                    
                        $objGoods = app::get('gift')->model('goods');
                        $arrGoods = $objGoods->dump($item['goods_id'], '*');
                        
                        $gift_items[$gift_item['goods_id']] = array(
                            'goods_id' => $gift_item['goods_id'],
                            'bn' => $gift_item['bn'],
                            'nums' => $gift_item['quantity'],
                            'name' => $gift_item['name'],
                            'item_type' => $arrGoods['category']['cat_name'],
                            'price' => $gift_item['price'],
                            'quantity' => $gift_item['quantity'],
                            'sendnum' => $gift_item['sendnum'],
                            'small_pic' => $arrGoods['image_default_id'],
                            'is_type' => $v['obj_type'],
                        );
                    }
                }
            }
        }
        
        $aOrder['items'] = $order_items;
        $aOrder['gifts'] = $gift_items;
        
        if ($aOrder['member_id'] > 0)
        {
            $objMember = &$this->app->model('members');
            $aOrder['member'] = $objMember->dump($aOrder['member_id'], '*',array( ':account@pam'=>array('*')));
            $aOrder['ship_email'] = $aOrder['member']['email'];
        }
        else
        {
            $aOrder['member'] = array();
        }
        
        $objDelivery = &$this->app->model('dlytype');
        $aArea = app::get('ectools')->model('regions')->getList('*',null,0,-1);
        foreach ($aArea as $v)
        {
            $aTmp[$v['name']] = $v['name'];
        }
        $aOrder['deliveryArea'] = $aTmp;

        $aRet = $objDelivery->getList('*',null,0,-1);
        foreach ($aRet as $v)
        {
            $aShipping[$v['dt_id']] = $v['dt_name'];
        }
        $aOrder['selectDelivery'] = $aShipping;

        $objPayment = app::get('ectools')->model('payment_cfgs');
        
        $aRet = $objPayment->getList('*', array('status' => 'true', 'is_frontend' => true));
        if (!$aORet['member_id'])
        {
            if ($aRet)
            {
                foreach ($aRet as $key=>$arr_payments)
                {
                    if (trim($arr_payments['app_id']) == 'deposit')
                    {
                        unset($aRet[$key]);
                    }
                }
            }
        }
        $aPayment[-1] = '货到付款';
        foreach ($aRet as $v)
        {
            $aPayment[$v['app_id']] = $v['app_name'];
        }

        $aOrder['selectPayment'] = $aPayment;

        $objCurrency = app::get('ectools')->model("currency");
        $aRet = $objCurrency->curAll();
        foreach ($aRet as $v)
        {
            $aCurrency[$v['cur_code']] = $v['cur_name'];
        }
        $aOrder['curList'] = $aCurrency;
        $aOrder['cur_name'] = $aCurrency[$aOrder['currency']];
        $this->pagedata['order'] = $aOrder;
        $this->pagedata['finder_id'] = $_GET['finder_id'];
        $this->singlepage('admin/order/detail/page_has_btn.html');
    }
    
    /**
     * 计算订单交互数据
     * @param null
     * @return null
     */
    public function caculate_item_total()
    {
        if ($_POST)
        {
            if ($_POST['json_arr'] && $_POST['operaction'])
            {
                $arr_org_obj = json_decode($_POST['json_arr']);
                $arr_org = array();
                foreach ($arr_org_obj as $str_obj)
                {
                    $arr_org[] = strval($str_obj);
                }
                
                $result = "";
                switch (trim($_POST['operaction']))
                {
                    case 'plus':
                        $result = $this->objMath->number_plus($arr_org);
                        break;
                    case 'minus':
                        $result = $this->objMath->number_minus($arr_org);
                        break;
                    case 'multiple':
                        $result = $this->objMath->number_multiple($arr_org);
                        break;
                    case 'div':
                        $result = $this->objMath->number_div($arr_org);
                        break;
                    default:
                        break;
                }                
                
                echo $result;exit;
            }
        }
    }
    
    /**
     * 添加货品项目
     * @param null
     * @return string 生成后的html.
     */
    public function addItem()
    {
        if($_POST['order_id']){
            $flag = true;
            while($flag){
                $randomValue = rand(1,200);
                if(!in_array($randomValue, (array)$_POST['aItems'])){
                    $flag = false;
                }
            }
            $loopValue = count($_POST['aItems']) + 1;
            $objOrder = &$this->app->model('orders');
            $productInfo = $objOrder->getProductInfo($_POST['order_id'], $_POST['newbn']);
            if (isset($productInfo['spec_info']) && $productInfo['spec_info'])
            {
                $productInfo['name'] = $productInfo['name'] . '(' . $productInfo['spec_info'] . ')';
            }

            if($productInfo == 'none'){
                $aOrder['alertJs'] = __("商品货号输入不正确，没有该商品或者商品已经下架。\n注意：如果是多规格商品，请输入规格编号.");
            }elseif($productInfo == 'exist'){
                $aOrder['alertJs'] = __('订单中存在相同的商品货号。');
            }
            elseif($productInfo == 'understock'){
                $aOrder['alertJs'] = __('商品库存不足。');
            }
            if(in_array($_POST['newbn'],(array)$_POST['add_bn'])){
                 $aOrder['alertJs'] = __('该商品货号已存在。');
            }
            if($aOrder['alertJs']){
                echo $aOrder['alertJs'];
                exit;
            }
            $returnValue = '<tr>';
            $returnValue .= '<input type="hidden" value="'.$productInfo['product_id'].'" name="aItems[product_id]['.$productInfo['product_id'].'_0]">';
            $returnValue .= '<input type="hidden" value="0" name="aItems[object_id]['.$productInfo['product_id'].'_0]">';
            $returnValue .= '<td>'.$productInfo['bn'].'<input type="hidden" name="add_bn[]" value="'.$productInfo['bn'].'"></td>';
            $returnValue .= '<td>'.$productInfo['name'].'</td>';
            $returnValue .= '<td><input type="text" vtype="unsigned" size="8" value="'.$productInfo['mprice'].'" name="aPrice['.$productInfo['product_id'].'_0]" class="x-input itemPrice_'.$productInfo['product_id'] . '-0 itemrow" required="true" autocomplete="off"></td>';
            $returnValue .= '<td><input type="text" vtype="positive" size="4" value="1" name="aNum['.$productInfo['product_id'].'_0]" class="x-input itemNum_'.$productInfo['product_id'].'-0 itemrow" required="true" autocomplete="off"></td>';
            $returnValue .= '<td class="itemSub_'.$productInfo['product_id'] . '-0 itemCount Colamount">'.$productInfo['mprice'].'</td>';
            $returnValue .= '<td><img class="imgbundle" app="desktop" onclick="delgoods(this)" style="cursor: pointer;" title="删除" src="/ecos/app/desktop/statics/bundle/delecate.gif"></td>';
            $returnValue .= '</tr>';
            echo $returnValue;
        }
    }
    
    /**
     * 修改订单item项目，用于ajax请求
     * @param null
     * @return unknown_type
     */
    public function toEdit()
    {
        $_POST['user_id'] = $this->user->user_id;
        $_POST['account']['login_name'] = $this->user->user_data['account']['login_name'];
        
        $arr_data = $this->_process_fields($_POST);
        $obj_order = $this->app->model('orders');
        $result = $obj_order->save($arr_data);
        
        if (count($_POST['aItems']))
        {
            if ($result)
            {
                if ($this->editOrder($_POST, true, $msg))
                {                
                    header('Content-Type:text/jcmd; charset=utf-8');
                    echo '{success:"成功.",_:null,order_id:"'.$_POST['order_id'].'"}';
                }
                else
                {
                    $this->begin('index.php?app=b2c&ctl=admin_order&act=showEdit&p[0]=' . $_POST['order_id']);
                    if (isset($msg) && $msg)
                        eval("\$msg = __(\"$msg\");");
                    $this->end(false, $msg);
                }
            }
            else
            {
                $this->begin('index.php?app=b2c&ctl=admin_order&act=showEdit&p[0]=' . $_POST['order_id']);
                if (isset($msg) && $msg)
                    eval("\$msg = __(\"$msg\");");
                $this->end(false, $msg);
            }
        }
        else
        {
            $this->begin('index.php?app=b2c&ctl=admin_order&act=showEdit&p[0]=' . $_POST['order_id']);
            $this->end(false, __('订单详细不错在，请确认！'));
        }
    }
    
    /**
     * 管理员手工编辑订单
     * @params string 提交过来的数组
     * @params boolean 附件库存不足时是否需要删除的标记
     * @return string message
     */
    private function editOrder(&$aData, $delMark=true, &$message='')
    {
        $obj_orders = $this->app->model('orders');
        if ($aData['order_id'] == '')
        {
            $obj_orders->save($aData);
        }
        else
        {
            $orderid = $aData['order_id'];
        }
        
        $mdl_order_items = $this->app->model('order_items');
        $mdl_order_objects = $this->app->model('order_objects');
        $mdl_goods = $this->app->model('goods');
        $mdl_products = $this->app->model('products');
        $addStore = array();
        $is_error = false;
        
        if (isset($aData['aItems']['product_id']) && $aData['aItems']['product_id'] && isset($aData['aItems']['object_id']) && $aData['aItems']['object_id'])
            foreach($aData['aItems']['product_id'] as $key => $productId)
            {
                //得到订单数据，不包含下级的数据
                $aStore = $mdl_products->dump($productId,'*');
                $storage_enable = $this->app->getConf('site.storage.enabled');
                $object_id = $aData['aItems']['object_id'][$key];
                if (!is_null($aStore['store']) && $aStore['store'] !== '')
                {
                    $rows = $mdl_order_items->getList('*',array('order_id'=>$orderid,'product_id'=>$productId,'obj_id'=>$object_id));
                    $aRet = $rows[0];
                    $gStore = $this->objMath->number_plus($this->objMath->number_minus(array(floatval($aStore['store']), floatval($aStore['freez']))), floatval($aRet['nums']));
                    if($gStore < $aData['aNum'][$key] && $storage_enable != 'true'){
                        //return false;
                        $is_error = true;                        
                        $message .= $aRet['name'] . '，商品货号：' . $aRet['bn'] . '的货品库存不足！' . "\r\n";
                        $aData['aNum'][$key] = $aRet['nums'];
                        
                        continue;
                    }
                    
                    // 需要改变的库存
                    if (isset($addStore[$productId]) && $addStore[$productId])
                        $addStore[$productId] = $this->objMath->number_plus(array($addStore[$productId], $this->objMath->number_minus(array(floatval($aData['aNum'][$key]), floatval($aRet['nums'])))));
                    else 
                        $addStore[$productId] = $this->objMath->number_minus(array(floatval($aData['aNum'][$key]), floatval($aRet['nums'])));
                }
            }
       
        if (isset($aData['ajunctItems']['product_id']) && $aData['ajunctItems']['product_id'] && isset($aData['ajunctItems']['object_id']) && $aData['ajunctItems']['object_id'])
        {
            // 得到商品允许添加的配件数目
            if (isset($aData['ajunctItems']['goods_id']) && $aData['ajunctItems']['goods_id'])
            {
                $arr_goods = $mdl_goods->dump($aData['ajunctItems']['goods_id']);
                $arr_ajunct = unserialize($arr_goods['adjunct']);
                if (is_null($arr_ajunct['max_num']))
                    $max_junct_nums = 99999;
                else
                    $max_junct_nums = $arr_ajunct['max_num'];
            }
            else
            {
                $max_junct_nums = 0;
            }
            foreach($aData['ajunctItems']['product_id'] as $key => $productId)
            {
                //得到订单数据，不包含下级的数据
                $aStore = $mdl_products->dump($productId,'*');
                $storage_enable = $this->app->getConf('site.storage.enabled');
                $object_id = $aData['ajunctItems']['object_id'][$key];
                if (!is_null($aStore['store']) && $aStore['store'] !== '')
                {
                    $rows = $mdl_order_items->getList('*',array('order_id'=>$orderid,'product_id'=>$productId,'obj_id'=>$object_id));
                    $aRet = $rows[0];
                    $gStore = $this->objMath->number_plus($this->objMath->number_minus(array(floatval($aStore['store']), floatval($aStore['freez']))), floatval($aRet['nums']));
                    if($gStore < $aData['ajunctNum'][$key] && $storage_enable != 'true'){
                        //return false;
                        $is_error = true;                        
                        $message .= $aRet['name'] . '，商品货号：' . $aRet['bn'] . '的货品库存不足！' . "\r\n";
                        $aData['ajunctNum'][$key] = $aRet['nums'];
                        
                        continue;
                    }
                    else
                    {
                        if ($max_junct_nums < $aData['ajunctNum'][$key])
                        {
                            $is_error = true;
                            $message .= $aRet['name'] . '，配件货号：' . $aRet['bn'] . '的购买量超过了允许购买的最大值！' . "\r\n";
                            $aData['ajunctNum'][$key] = $aRet['nums'];
                            
                            continue;
                        }
                    }
                    
                    if (isset($addStore[$productId]) && $addStore[$productId])
                        $addStore[$productId] = $this->objMath->number_plus(array($addStore[$productId], $this->objMath->number_minus(array(floatval($aData['ajunctNum'][$key]), floatval($aRet['nums'])))));
                    else 
                        $addStore[$productId] = $this->objMath->number_minus(array(floatval($aData['ajunctNum'][$key]), floatval($aRet['nums'])));
                }
            }
        }
        
        reset($aData['aItems']['product_id']);
        if ($aData['ajunctItems']['product_id'])
            reset($aData['ajunctItems']['product_id']);
        if ($aData['ajunctItems']['product_id'])
            $aData['aItems']['product_id'] = array_merge($aData['aItems']['product_id'], $aData['ajunctItems']['product_id']);
        if ($aData['ajunctItems']['object_id'])
            $aData['aItems']['object_id'] = array_merge($aData['aItems']['object_id'], $aData['ajunctItems']['object_id']);
        if ($aData['ajunctPrice'])
            $aData['aPrice'] = array_merge($aData['aPrice'], $aData['ajunctPrice']);
        if ($aData['ajunctNum'])
            $aData['aNum'] = array_merge($aData['aNum'], $aData['ajunctNum']);

        $itemsFund = 0;
        $item_weight = 0;
        $cost_item = 0;
        $arr_insert_objects = array();
        foreach($aData['aItems']['product_id'] as $key => $productId)
        {
            $aItem = array();
            $aItem['order_id'] = $orderid;
            $aItem['product_id'] = $productId;
            $aItem['price'] = $aData['aPrice'][$key];
            $aItem['quantity'] = $aData['aNum'][$key];
            $aItem['amount'] = $this->objMath->number_multiple(array($aItem['price'], $aItem['quantity']));
            $object_id = $aData['aItems']['object_id'][$key];
            
            $cost_item = $this->objMath->number_plus(array($cost_item, $aItem['amount']));
            
            //todo 库存冻结量,库存是否足够 / 商品配件
            $rows = $mdl_order_items->dump(array('order_id'=>$orderid,'product_id'=>$productId,'obj_id'=>$object_id));
            if(isset($rows['item_id']) && $rows['item_id']){
                $item_weight = $this->objMath->number_plus(array($item_weight, $this->objMath->number_multiple(array($rows['weight'], $aItem['quantity']))));
                $aProduct['edit'][] = array(
                    'product_id' => $productId,
                    'object_id' => $object_id,
                );
                $aItem['item_id'] = $rows['item_id'];
                $mdl_order_items->save($aItem);
            }else{
                
                $aPdtinfo = $mdl_products->dump($productId, 'goods_id, bn, name, cost, store, weight');
                $item_weight = $this->objMath->number_plus(array($item_weight, $this->objMath->number_multiple(array($aPdtinfo['weight'], $aItem['quantity']))));
                $aPdtinfo['weight'] *= $aItem['quantity'];
                unset($aPdtinfo['price']);
                $aGoodsinfo = $mdl_goods->dump($aPdtinfo['goods_id'], 'type_id');
                
                $aItem = array_merge($aItem, $aPdtinfo);
                
                $aItem['type_id'] = $aGoodsinfo['type']['type_id'];
                $arr_insert_objects[] = 
                    array(
                        'obj_type'=> 'goods',  //goods,gift,taobao, api...
                        'obj_alias'=> '商品区块',
                        'goods_id'=>$aItem['goods_id'],
                        'order_id'=>$aItem['order_id'],
                        'bn'=>$aItem['bn'],
                        'name'=>$aItem['name'],
                        'price'=>$aItem['price'],
                        'quantity'=>1,
                        'amount'=>$aItem['amount'],
                        'weight'=>$aItem['weight'],
                        'score'=>0,//todo 积分
                        'order_items'=>array(
                            array(
                                'products'=>array('product_id'=>$productId),
                                'goods_id'=>$aItem['goods_id'],
                                'order_id'=>$aItem['order_id'],
                                'item_type'=>'product',
                                'bn'=>$aItem['bn'],
                                'name'=>$aItem['name'],
                                'type_id'=>$aItem['type_id'],
                                'cost'=>$aItem['cost'],
                                'quantity'=>$aItem['quantity'],
                                'sendnum'=>0,
                                'amount'=>$aItem['amount'],
                                'price'=>$aItem['price'],
                                'weight'=>$aItem['weight'],
                                'addon'=>0,
                                )
                        )
                    
                    );
                
                $aProduct['edit'][] = array(
                    'product_id' => $productId,
                    'object_id' => $object_id,
                );
            }

            $itemsFund = $this->objMath->number_plus(array($itemsFund, $aItem['amount']));

            $freezTime = $this->app->getConf('system.goods.freez.time');
            $tmpdata = array();
            if($freezTime == 1)
            {
                $tmpdata['product_id'] = $productId;
                if(isset($addStore[$productId]))
                {
                    if(floatval($addStore[$productId])>=0)
                    {
                        // 冻结库存
                        $tmpdata['freez'] = $this->objMath->number_plus(array($row['freez'], floatval($addStore[$productId])));
                        $mdl_goods->freez($aItem['goods_id'], $productId, abs(floatval($addStore[$productId])));
                    }
                    else
                    {
                        $tmpdata['freez'] = $this->objMath->number_plus(array($row['freez'], floatval($addStore[$productId])));
                        $mdl_goods->unfreez($aItem['goods_id'], $productId, abs(floatval($addStore[$productId])));
                    }                    
                }
            }
        }
      
        if($aData['shipping_id'])
        {
            $dlytype = $this->app->model('dlytype');//配送方式
            $dlytype_info = $dlytype->dump($aData['shipping_id'],'*');
            
            if($aData['is_protect'] == 'true' || $aData['is_protect'] == '1'){//配送设置了保价
                //$cost_protect = ($cost_item*$dlytype_info['protect_rate']);
                $cost_protect = $this->objMath->number_multiple(array($cost_item, $dlytype_info['protect_rate']));
                $cost_protect = $cost_protect>$dlytype_info['minprice']?$cost_protect:$dlytype_info['minprice'];//保价费
            }
            
            if (!$dlytype_info['setting'])
            {            
                $arrArea = explode(':', $aData['ship_area']);
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
            
            $aData['cost_freight'] = utils::cal_fee($dlytype_info['dt_expressions'],$item_weight,$cost_item);//配送费
        }
        
        if ($delMark)
        {
            $this->execDelItems($orderid, $aProduct['edit']);
        } 
        else
        {
            //$itemsFund = $this->getCostItems($orderid);
        }
        
        // 存储新增的订单项目
        if (isset($arr_insert_objects) && $arr_insert_objects)
        {
            foreach ($arr_insert_objects as $order_objects)
            {
                $mdl_order_objects->save($order_objects);
            }
        }
        
        $aDataTmp['cost_item'] = $itemsFund;
        $aDataTmp['shipping']['cost_shipping'] = $aData['cost_freight'];
        $aDataTmp['total_amount'] = $this->objMath->number_minus(array($this->objMath->number_plus(array($itemsFund, $aData['cost_freight'], $aData['cost_protect'], $aData['cost_payment'], $aData['cost_tax'], $aData['discount'])), $aData['pmt_order']));
        $aDataTmp['weight'] = $item_weight;
        $aDataTmp['discount'] = abs($aData['discount']);
        $rate = $obj_orders->dump($orderid, 'cur_rate');
        $aDataTmp['cur_amount'] = $this->objMath->number_multiple(array($aDataTmp['total_amount'], $rate['cur_rate']));

        $aDataTmp['order_id'] = $orderid;
        
        if ($obj_orders->save($aDataTmp) && !$is_error)
        {            
            // 添加日志.
            $orderLog = $this->app->model("order_log");
            $sdf_order_log = array(
                'rel_id' => $orderid,
                'op_id' => $aData['user_id'],
                'op_name' => $aData['account']['login_name'],
                'alttime' => time(),
                'bill_type' => 'order',
                'behavior' => 'updates',
                'result' => 'SUCCESS',
            );
            $orderLog->save($sdf_order_log);
            
            return true;
        }
        else
        {
            return false;
        }
        
        return $aMsg;
    }
    
    /**
     * 删除订单产品的某项
     * @params string order id
     * @params item array 
     */
    private function execDelItems($orderid, &$aItems)
    {
        $freezTime = $this->app->getConf('system.goods.freez.time');
        $obj_orders_items = $this->app->model('order_items');
        $obj_orders_objects = $this->app->model('order_objects');
        $obj_goods = $this->app->model('goods');        
        
        $aRets = $obj_orders_items->getList('*', array('order_id' => $orderid));
        $arr_items = array();
        foreach ($aRets as $items)
        {
            $arr_items = array(
                'product_id' => $items['product_id'],
                'object_id' => $items['obj_id'],
            );
            
            if (array_search($arr_items, $aItems) === false)
            {
                if ($items['item_type'] != 'gift')
                {
                    // 解冻库存
                    if ($freezTime == '1')
                    {
                        $productId = $items['product_id'];
                        $nums = $items['nums'];
                        $obj_goods->unfreez($items['goods_id'], $items['product_id'], $nums);
                    }
                    
                    // 在数据表中删除此数据项
                    if ($items['item_type'] == 'product')
                    {
                        // 删除主商品并删除订单对象
                        $sqlString = "DELETE FROM sdb_b2c_order_items WHERE order_id = '".$orderid."' AND obj_id = '".$items['obj_id'];
                        $obj_orders_items->db->exec($sqlString);
                        $sqlString = "DELETE FROM sdb_b2c_order_objects WHERE order_id = '".$orderid."' AND obj_id = '" . $items['obj_id'];
                        $obj_orders_items->db->exec($sqlString);
                    }
                    else
                    {
                        // 删除配件
                        $sqlString = "DELETE FROM sdb_b2c_order_items WHERE order_id = '".$orderid."' AND product_id ='".$items['product_id']."' AND obj_id = '".$items['obj_id']."' AND item_type != 'gift'";
                        $obj_orders_items->db->exec($sqlString);
                    }
                }
            }
        }
   }
    
    /**
     * 规整sdf数据
     * @params null
     * @return array 格式数据
     */
    private function _process_fields($sdf)
    {
        $sdf['is_protect'] = isset($sdf['is_protect']) ? $sdf['is_protect'] : 'false';
        $sdf['cost_protect'] = isset($sdf['cost_protect']) ? $sdf['cost_protect'] : '0.00';
        $sdf['is_tax'] = isset($sdf['is_tax']) ? $sdf['is_tax'] : 'false';
        $sdf['discount'] = 0 - $sdf['discount'];
        $sdf['order_id'] = $sdf['order_id'];


        $sdf['cost_tax'] = trim($sdf['cost_tax']) ? trim($sdf['cost_tax']) : 0;
        $sdf['discount'] = $sdf['discount'];
        $sdf['is_protect'] = $sdf['is_protect'];
        $sdf['is_tax'] = $sdf['is_tax'];

        $sdf['pmt_order'] = $sdf['pmt_order'];

        $shipping = &$this->app->model('dlytype');
        $aShip = $shipping->dump($sdf['shipping_id']);
        
        $sdf['shipping'] = array(
            'shipping_id'=>$sdf['shipping_id'],    
            'shipping_name'=>$aShip['dt_name'],    
            'cost_shipping'=>$sdf['cost_freight'],    
            'is_protect'=>$sdf['is_protect'],    
            'cost_protect'=>$sdf['cost_protect'],    
        );



        $sdf['payinfo'] = array(
            'cost_payment'=>$sdf['cost_payment'],
            'pay_app_id' => $sdf['payment']
            );

        $sdf['consignee'] = array(
            'name'=>$sdf['receiver_name'],  
            'addr'=>$sdf['ship_addr'],
            'zip'=>$sdf['ship_zip'],
            'telephone'=>$sdf['ship_tel'],
            'r_time'=>$sdf['ship_time'],
            'mobile'=>$sdf['ship_mobile'],
            'email'=>$sdf['ship_email'],
            'area'=>$sdf['ship_area']
        );

        $sdf['tax_company'] = $sdf['tax_company'];
        $sdf['weight'] = $sdf['weight'];
        $sdf['last_modified'] = time();
        
        return $sdf;
    }
    
    /**
     * 设置订单样式
     * @param null
     * @return null
     */
    public function showPrintStyle()
    {
        $this->path[] = array('text'=>__('订单打印格式设置'));
        $dbTmpl = $this->app->model('member_systmpl');
        $filetxt = $dbTmpl->get('/admin/order/orderprint');
        $cartfiletxt = $dbTmpl->get('/admin/order/print_cart');
        $sheetfiletxt = $dbTmpl->get('/admin/order/print_sheet');
        $this->pagedata['styleContent'] = $filetxt;
        $this->pagedata['styleContentCart'] = $cartfiletxt;
        $this->pagedata['styleContentSheet'] = $sheetfiletxt;
        $this->page('admin/order/printstyle.html');
    }
    
    /**
     * 保存订单打印样式
     * @param null
     * @return null
     */
    public function savePrintStyle()
    {
        $this->begin('index.php?app=b2c&ctl=admin_order&act=showPrintStyle');
        $dbTmpl = $this->app->model('member_systmpl');
        $dbTmpl->set('/admin/order/print_sheet', $_POST["txtcontentsheet"]);
        $dbTmpl->set('/admin/order/print_cart', $_POST["txtcontentcart"]);
        $this->end($dbTmpl->set('/admin/order/orderprint', $_POST["txtcontent"]),__('订单打印模板保存成功'));
    }
    
    /**
     * rebackPrintStyle
     *
     * @access public
     * @return void
     */
    public function rebackPrintStyle(){
        $this->begin('index.php?app=b2c&ctl=admin_order&act=showPrintStyle');
        $dbTmpl = $this->app->model('member_systmpl');
        $dbTmpl->clear('/admin/order/print_sheet');
        $dbTmpl->clear('/admin/order/print_cart');
        $this->end($dbTmpl->clear('/admin/order/orderprint'),__('恢复默认值成功'));
    }
}
