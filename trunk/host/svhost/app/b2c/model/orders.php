<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class b2c_mdl_orders extends dbeav_model{
    var $has_tag = true;
    var $defaultOrder = array('createtime','DESC');
    var $has_many = array(
        'order_objects'=>'order_objects',
        'order_pmt'=>'order_pmt'
    );
    
    public $arr_print_type = array(
        'ORDER_PRINT_CART' => 1,
        'ORDER_PRINT_SHEET' => 2,
        'ORDER_PRINT_MERGE' => 4,
        'ORDER_PRINT_DLY' => 8,
    );
    
    /**
     * 得到唯一的订单编号
     * @params null
     * @return string 订单编号
     */
    public function gen_id()
    {
        $i = rand(0,9999);
        do{
            if(9999==$i){
                $i=0;
            }
            $i++;
            $order_id = date('YmdH').str_pad($i,4,'0',STR_PAD_LEFT);
            $row = $this->db->selectrow('SELECT order_id from sdb_b2c_orders where order_id ='.$order_id);
        }while($row);
        return $order_id;
    }
    
    /**
     * 重载订单标准数据
     * @params array - standard data format
     * @params boolean 是否必须强制保存
     */
    public function save(&$sdf,$mustUpdate = null)
    {
        $is_save = parent::save($sdf, $mustUpdate);
        return $is_save;
    }
    
    /**
     * 通过会员的编号得到orders标准数据格式
     * @params string member id
     * @params string page number
     * @return array sdf 数据
     */
    public function fetchByMember($member_id, $nPage)
    {
        $limit = $this->app->getConf("selllog.display.listnum");
        if (!$limit) 
            $limit = 10;
        $limitStart = $nPage * $limit;
        $sdf_orders = $this->getList('*', array('member_id' => $member_id), $limitStart, $limit, 'last_modified DESC');
        
        // 生成分页组建
        $countRd = $this->count(array('member_id' => $member_id));
        $total = ceil($countRd/$limit);
        $current = $nPage;
        $token = '';
        $arrPager = array(
            'current' => $current,
            'total' => $total,
            'token' => $token,
        );
        
        $subsdf = array('order_objects'=>array('*',array('order_items'=>array('*',array(':products'=>'*')))));
        foreach ($sdf_orders as &$arr_order)
        {
            $arr_order = $this->dump($arr_order['order_id'], '*', $subsdf);
        }
        $arrdata['data'] = $sdf_orders;
        $arrdata['pager'] = $arrPager;
        
        return $arrdata;
    }
    
    /**
     * 返回订单字段的对照表
     * @params string 状态
     * @params string key value
     */
    public function trasform_status($type='status', $val)
    {
        switch($type){
            case 'status':
                $tmpArr = array(
                            'active' => __('活动'),
                            'finish' => __('完成'),
                            'dead' => __('死单'),
                );
                return $tmpArr[$val];
            break;
            case 'pay_status':
                $tmpArr = array(
                            0 => __('未付款'),
                            1 => __('已付款'),
                            2 => __('付款至担保方'),
                            3 => __('部分付款'),
                            4 => __('部分退款'),
                            5 => __('已退款'),
                );
                return $tmpArr[$val];
            break;
            case 'ship_status':
                $tmpArr = array(
                            0 => __('未发货'),
                            1 => __('已发货'),
                            2 => __('部分发货'),
                            3 => __('部分退货'),
                            4 => __('已退货'),
                );
                return $tmpArr[$val];
            break;
        }
    }
    
    /**
     * 取到最新的order
     * @params int 最新显示的数量
     * @return array 数据结果
     */
    public function getLastestOrder($number)
    {
        return $this->getList('*', array(), 0, $number, 'createtime DESC');
    }
    
    /**
     *  得到货品信息
     * @param $orderid
     * @param $goodsbn
     * @return array 货品信息数组
     */
    public function getProductInfo($orderid, $goodsbn){
        $aOrder = parent::dump($orderid, 'member_id');
        $mdl_goods = $this->app->model('goods');
        $mdl_products = $this->app->model('products');
        $subsdf = array('product'=>array('*'));
        $filter = array('bn'=>$goodsbn,'marketable'=>'true');
        $goods = $mdl_goods->getList('*',$filter);
        $goods = $goods[0]; 
        $aProduct = $mdl_products->getList('*',array('goods_id'=>$goods['goods_id'],'bn'=>$goodsbn));
        $aProduct = $aProduct[0];
       if(!$aProduct['product_id']) return 'none';

        $freezTime = $this->app->getConf('system.goods.freez.time');
        if($freezTime==0 && $aProduct['store']-intval($aProduct['freez']) < 1){
            return 'understock';
        }
        if($aOrder['member_id']){
            $oMember = $this->app->model('members');
            $aMember = $oMember->dump($aOrder['member_id'], 'member_lv_id');
            $mdl_goods_lv_price = $this->app->model('goods_lv_price');
            $filter = array('product_id'=>$aProduct['product_id'],'level_id'=>intval($aMember['member_lv_id']));
            $mPrice = $mPrice[0];
            if(!$mPrice['mprice']){
                $mPrice['mprice'] = $aProduct['price'];
            }
        }else{
            $oLevel = $this->app->model('member_lv');
            $aLevel = $oLevel->getList('dis_count', array('default_lv'=>1));
            $mPrice['mprice'] = $aProduct['price'] * ($aLevel[0]['dis_count'] ? $aLevel[0]['dis_count'] : 1);
        }
        $mdl_order_items = $this->app->model('order_items');
        $order_items = $mdl_order_items->getList('*',array('order_id'=>$orderid,'product_id'=>$aProduct['product_id']));
        if($order_items){
            return 'exist';
        }

        return array_merge($aProduct, $mPrice);
    }
    
    /**
     * 得到特定订单的所有日志
     * @params string order id
     * @params int page num
     * @params int page limit
     * @return array log list
     */
    public function getOrderLogList($order_id, $page=0, $limit=-1)
    {
        $objlog = $this->app->model('order_log');
        $arrlogs = array();
        $arr_returns = array();
        
        if ($limit < 0)
        {
            $arrlogs = $objlog->getList('*', array('rel_id' => $order_id));
        }
        
        $limitStart = $page * $limit;
        
        $arrlogs_all = $objlog->getList('*', array('rel_id' => $order_id));
        $arrlogs = $objlog->getList('*', array('rel_id' => $order_id), $limitStart, $limit);
        if ($arrlogs)
        {
            foreach ($arrlogs as &$logitems)
            {
                switch ($logitems['behavior'])
                {
                    case 'creates':
                        $logitems['behavior'] = "创建";
                        break;
                    case 'updates':
                        $logitems['behavior'] = "修改";
                        break;
                    case 'payments':
                        $logitems['behavior'] = "支付";
                        break;
                    case 'refunds':
                        $logitems['behavior'] = "退款";
                        break;
                    case 'delivery':
                        $logitems['behavior'] = "发货";
                        break;
                    case 'reship':
                        $logitems['behavior'] = "退货";
                        break;
                    case 'finish':
                        $logitems['behavior'] = "完成";
                        break;
                    case 'cancel':
                        $logitems['behavior'] = "作废";
                        break;
                    default:
                        break;
                }
            }
        }
        
        $arr_returns['page'] = count($arrlogs_all);
        $arr_returns['data'] = $arrlogs;
        
        return $arr_returns;
    }
    
    public function order_refund_finish(&$sdf, $status='succ')
    {
        $this->op_id = $sdf['op_id'];
        $this->op_name = $sdf['op_name'];
        
        // 处理库存
        $obj_checkorder = kernel::service('b2c_order_apps', array('content_path'=>'b2c_order_checkorder'));
        $arrFreez = $obj_checkorder->checkOrderFreez('refund', $sdf['order_id']);
        $subsdf = array('order_objects'=>array('*',array('order_items'=>array('*',array(':products'=>'*')))));
        $sdf_order = $this->dump($sdf['order_id'], '*', $subsdf);
        
        // 解除库存量的冻结.
        if ($arrFreez['unfreez'])
        {
            $is_unfreeze = true;                    
            
            $objGoods = &$this->app->model('goods');
            foreach($sdf_order['order_objects'] as $k => $v)
            {
                foreach ($v['order_items'] as $arrItem)
                {
                    $is_unfreeze = $objGoods->unfreez($arrItem['products']['goods_id'], $arrItem['products']['product_id'], $arrItem['quantity']);
                }
            }
        }
        
        if ($sdf_order['member_id'])
        {
            // 预存款和积分处理
            $obj_members_point = $this->app->model('member_point');
             // 退还订单消费积分
            if ($sdf['return_score'] > ($sdf_order['score_g'] - $sdf_order['score_u']))
                $sdf['return_score'] = $sdf_order['score_g'] - $sdf_order['score_u'];
            $obj_members_point->change_point($sdf_order['member_id'], (0 - intval($sdf['return_score'])), $msg, 'order_refund_use', 1);
            if ($sdf['payment'] == 'deposit')
            {
                // 退款到预存款账号
                $objAdvance = $this->app->model("member_advance");
                $objAdvance->add($sdf_order['member_id'], $sdf['money'], '预存款退款', $msg, $sdf['refund_id'], $sdf['order_id'], $sdf['payment'], '退还订单消费');
            }
        }
        
        // 更新order的信息
        $is_saveOdr = false;
        $objMath = kernel::single('ectools_math');
        
        $order_data['order_id'] = $sdf['order_id'];
        //$sdf_order = $this->dump($sdf['order_id'],'*');
        
        $order_data['payed'] = $objMath->number_minus(array($sdf_order['payed'], $sdf['money']));
        if ($order_data['payed'] == '0')
            $order_data['pay_status'] = '5';
        else
            $order_data['pay_status'] = '4';
        
        
        $is_saveOdr = $this->save($order_data);
        
        // 更新退款日志结果
        if ($is_saveOdr)
        {
            $objorder_log = $this->app->model('order_log');
            
            $sdf_order_log = array(
                'rel_id' => $sdf['order_id'],
                'op_id' => $this->op_id,
                'op_name' => $this->op_name,
                'alttime' => time(),
                'bill_type' => 'order',
                'behavior' => 'refunds',
                'result' => ($is_saveOdr) ? 'SUCCESS' : 'FAILURE',
            );
            
            $log_id = $objorder_log->save($sdf_order_log);
        }
        
        $aUpdate['order_id'] = $sdf['order_id'];
        if ($sdf_order['member_id'])
        {
            $member = $this->app->model('members');
            $arr_member = $member->dump($sdf_order['member_id'], '*', array(':account@pam'=>'*'));
        }
        $aUpdate['email'] = (!$sdf_order['member_id']) ? $sdf_order['consignee']['email'] : $arr_member['contact']['email'];
        $aUpdate['pay_status'] = ($order_data['pay_status'] == '5') ? 'REFUND_ALL' : 'REFUND_PART'; 
                                
        $this->fireEvent('refund', $aUpdate, $sdf_order['member_id']);
        
        return $is_saveOdr;
    }
    
    /**
     * 订单的邮件触发器
     * @params string 订单处理动作
     * @params array 订单数据
     * @params int member id
     */
    public function fireEvent($action , &$object, $member_id=0)
    {
         $trigger = &$this->app->model('trigger');
         
         return $trigger->object_fire_event($action, $object, $member_id, $this);
    }
    
    /**
     * filter字段显示修改
     * @params string 字段的值
     * @return string 修改后的字段的值
     */
    public function modifier_payment($row)
    {
        if ($row == '-1')
        {
            // 货到付款
            return '货到付款';
        }
        
        $obj_paymentmethod = app::get('ectools')->model('payment_cfgs');
        $arr_data = $obj_paymentmethod->getPaymentInfo($row);
        
        return $arr_data['app_name'] ? $arr_data['app_name'] : $row;
    }
    
    public function modifier_member_id($row)
    {
        if ($row === 0 || $row == '0'){
            return '非会员顾客';
        }    
        else{
            $obj_member = app::get('pam')->model('account');
            $sdf = $obj_member->dump($row);
            return $sdf['login_name'];
        }
    }
}
