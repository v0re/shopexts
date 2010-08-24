<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class b2c_order_delivery extends b2c_order_operation
{
    // 私有化实例，单件模式使用.
    private static $instance;
    
    /**
     * 私有构造方法，不能直接实例化，只能通过调用getInstance静态方法被构造
     * @params null
     * @return null
     */
    private function __construct($app, $model)
    {        
        // 异常处理
        if (is_null($model) || !$model)
        {
            trigger_error("应用对象不能为空！", E_USER_ERROR);
        }
        
        $this->app = $app;
        $this->model = $model;
        $this->objMath = kernel::single('ectools_math');
    }
    
    /**
     * 类静态构造实例的唯一入口
     * @params object app object
     * @params object model object
     * @return object b2c_order_delivery的对象
     */
    public static function getInstance($app, $model)
    {
        if (is_object(self::$instance))
        {
            return self::$instance;
        }
        
        self::$instance = new b2c_order_delivery($app, $model);
        
        return self::$instance;
    }
    
    /**
     * 最终的克隆方法，禁止克隆本类实例，克隆是抛出异常。
     * @params null
     * @return null
     */
    final public function __clone()
    {
        trigger_error("此类对象不能被克隆！", E_USER_ERROR);
    }
    
    /**
     * 创建退货单
     * @params array - 订单数据
     * @params obj - 应用对象
     * @params string - 支付单生成的记录
     * @return boolean - 创建成功与否
     */
    public function generate($sdf, &$controller=null, &$msg='')
    {        
        $manual = true;
        // 得到delivery的一些信息
        $sdf['delivery_id'] = $this->model->gen_id();
        $this->model->op_id = $controller->user->user_id;
        $this->model->op_name = $controller->user->user_data['name'];
        
        // 处理返货单据信息，得到订单的发送量。 
        $order = $controller->app->model('orders');
        $odelivery = $controller->app->model('delivery');
        $subsdf = array('order_objects'=>array('*',array('order_items'=>array('*',array(':products'=>'*')))));
        $sdf_order = $order->dump($sdf['order_id'],'*',$subsdf);
        $order_items = array();
        
        foreach($sdf_order['order_objects'] as $k=>$v){
            $order_items = array_merge($order_items,$v['order_items']);
        }
        
        $this->objMath = kernel::single('ectools_math');
         
        if (isset($sdf['send']) || isset($sdf['send']))
        {
            if ($sdf['logi_id'])
            {
                $oCorp = &$controller->app->model('dlycorp');
                $aCorp = $oCorp->dump($sdf['logi_id'],'*');
            }
            
            $delivery = array(
                'money' => $this->objMath->number_plus(array($sdf['money'], $sdf['cost_protect'])),
                'is_protect' => $sdf['is_protect'],
                'delivery' => $sdf['delivery'],
                'delivery_id' => $sdf['delivery_id'],
                'logi_id' => $sdf['logi_id'],
                'logi_no' => $sdf['logi_no'],
                'logi_name' => $aCorp['name'],
                'ship_name' => $sdf['ship_name'],
                'ship_area' => $sdf['ship_area'],
                'ship_addr' => $sdf['ship_addr'],
                'ship_zip' => $sdf['ship_zip'],
                'ship_tel' => $sdf['ship_tel'],
                'ship_mobile' => $sdf['ship_mobile'],
                'ship_email' => $sdf['ship_email'],
                'memo' => $sdf['memo']
            );
        }
        else
        {
            $delivery = array(
                'money' => $this->objMath->number_plus(array($sdf_order['cost_freight'], $sdf_order['cost_protect'])),
                'is_protect' => $sdf_order['shipping']['is_protect'],
                'delivery' => $sdf_order['shipping']['method'],
                'delivery_id' => $odelivery->gen_id(),
                'logi_id' => '',
                'logi_no' => $sdf['logi_no'],
                'logi_name' => $sdf['logi_name'],
                'ship_name' => $sdf_order['consignee']['name'],
                'ship_area' => $sdf_order['consignee']['area'],
                'ship_addr' => $sdf_order['consignee']['addr'],
                'ship_zip' => $sdf_order['consignee']['zip'],
                'ship_tel' => $sdf_order['consignee']['telephone'],
                'ship_mobile' => $sdf_order['consignee']['mobile'],
                'ship_email' => $sdf_order['consignee']['email']
            );
        }
        
        /**
        *    @function:    订单明细赋值,读取订单详细表sdb_order_items的addon字段
        *    @params:
        *        @$dinfo['addon']:        订单序列化字段，存放订单物品等资料
        *        @$delivery['op_name']:    订单操作人员
        *        @$aUpdate['ship_status']:订单发货状态 1为发货状态
        */
        $delivery['order_id'] = $sdf['order_id'];
        $delivery['member_id'] = $sdf_order['member_id'];
        $delivery['t_begin'] = time();
        $delivery['op_name'] = $sdf['opname'];
        $delivery['type'] = 'delivery';
        $delivery['status'] = 'progress';
        $delivery_id = $delivery['delivery_id'];
        
        //遍历订单明细
        $aBill = array();
        $nonGoods = 0;    //是否完全发货商品标识
        if (!isset($sdf['send']) && !isset($sdf['gift_send']))
        {//非弹窗确认
           foreach($order_items as $key=>$dinfo)
           {
                if ($dinfo['item_type'] != 'gift')
                    $dinfo['send'] = $dinfo['quantity']-$dinfo['sendnum'];//须发送=未发送
                else
                    $dinfo['gift_send'] = $dinfo['quantity']-$dinfo['sendnum'];//须发送=未发送
                $order_items[$key] = $dinfo;
           }
        }
        else
        {//弹窗确认
            foreach ($order_items as $key=>$dinfo)
            {
                if ($dinfo['item_type'] != 'gift')
                {
                    if (isset($sdf['send'][$dinfo['item_id']]) && floatval($sdf['send'][$dinfo['item_id']]) > 0)
                    {//弹窗并且输入了发货数量>=1
                        if (floatval($sdf['send'][$dinfo['item_id']]) > $this->objMath->number_minus(array($dinfo['quantity'], $dinfo['sendnum'])))
                        {
                            $msg = "发货数量超过需要发货量！";
                            return false;
                        }
                        elseif (floatval($sdf['send'][$dinfo['item_id']]) == $this->objMath->number_minus(array($dinfo['quantity'], $dinfo['sendnum'])))
                        {//足量发送
                            $dinfo['send'] = $this->objMath->number_minus(array($dinfo['quantity'], $dinfo['sendnum']));//须发送=未发送
                        }
                        else
                        {//部分发送
                            $dinfo['send'] = floatval($sdf['send'][$dinfo['item_id']]);
                            $nonGoods = 1;
                        }
                    }
                    else
                    {
                        if ($this->objMath->number_minus(array($dinfo['quantity'], $dinfo['sendnum'])) > 0)
                            $nonGoods = 1;
                    }
                }
                else
                {
                    // 赠品发送过程...
                    if (isset($sdf['gift_send'][$dinfo['item_id']]) && floatval($sdf['gift_send'][$dinfo['item_id']]) > 0)
                    {
                        //弹窗并且输入了发货数量>=1
                        if (floatval($sdf['gift_send'][$dinfo['item_id']]) > $this->objMath->number_minus(array($dinfo['quantity'], $dinfo['sendnum'])))
                        {
                            $msg = "发货数量超过需要发货量！";
                            return false;
                        }
                        elseif (floatval($sdf['gift_send'][$dinfo['item_id']]) == $this->objMath->number_minus(array($dinfo['quantity'], $dinfo['sendnum'])))
                        {
                            //足量发送
                            $dinfo['gift_send'] = $this->objMath->number_minus(array($dinfo['quantity'], $dinfo['sendnum']));//须发送=未发送
                        }
                        else
                        {
                            //部分发送
                            $dinfo['gift_send'] = floatval($sdf['gift_send'][$dinfo['item_id']]);
                            $nonGoods = 1;
                        }
                    }
                }
                $order_items[$key] = $dinfo;
            }
        }

        if ($order_items)
        {        
            //实体商品
            $arr_items = array();
            if ($manual || (!$manual && $this->app->getConf('system.auto_delivery_physical') != 'no'))
            {
                if (!$manual)
                {
                    $delivery['status'] = ($this->app->getConf('system.auto_delivery_physical')=='yes' ? 'progress' : 'ready');
                }
                $iLoop = 0;
                foreach ($order_items as $dinfo)
                {
                                                        
                    if($dinfo['send'])
                    {
                        $item = array(
                                'order_item_id' => $dinfo['item_id'],
                                'order_id' => $sdf['order_id'],
                                'delivery_id' => $delivery['delivery_id'],
                                'item_type' => $dinfo['item_type'],
                                'product_id' => $dinfo['products']['product_id'],
                                'product_bn' => $dinfo['bn'],
                                'product_name' => $dinfo['name'].$dinfo['addon']['adjname'],
                                'number' => $dinfo['send'],
                            );
                            
                        $items[] = $dinfo;
                        $arr_items[] = array(
                            'number' => $dinfo['send'],
                            'name' => $dinfo['name'].$dinfo['addon']['adjname'],
                        );
                        $this->toInsertItem($item);
                        $iLoop++;
                    }
                    
                    if ($dinfo['gift_send'])
                    {
                        $item = array(
                                'order_item_id' => $dinfo['item_id'],
                                'order_id' => $sdf['order_id'],
                                'delivery_id' => $delivery['delivery_id'],
                                'item_type' => $dinfo['item_type'],
                                'goods_id' => $dinfo['goods_id'],
                                'product_bn' => $dinfo['bn'],
                                'product_name' => $dinfo['name'],
                                'number' => $dinfo['gift_send'],
                            );
                            
                        $items[] = $dinfo;
                        $arr_items[] = array(
                            'number' => $dinfo['gift_send'],
                            'name' => $dinfo['name'].$dinfo['addon']['adjname'],
                        );
                        $this->toInsertItem($item);
                        $iLoop++;
                    }
                    
                }
            }
            
            if($iLoop > 0)
            {
                $is_save = $odelivery->save($delivery);
                if (!$is_save)
                {
                    $msg = '发货单生成失败！';
                    return false;
                }
                
                $arr_delivery['status'] = 'succ';
                $is_save = $odelivery->save($delivery);
                if (!$is_save)
                {
                    $msg = '发货单修改失败！';
                    return false;
                }
            }
            
            $order_delivery = $controller->app->model('order_delivery');
            $order_delivery_data = array('order_id'=>$delivery['order_id'],'dly_id'=>$delivery_id,'dlytype'=>'delivery','items'=>($items));
            $order_delivery->save($order_delivery_data);
        }        
        
        //没有完全发货
        if ($nonGoods) 
            $aUpdate['ship_status'] = '2';
        else 
            $aUpdate['ship_status'] = '1';
            
        $aUpdate['order_id'] = $sdf['order_id'];
        $aUpdate['ship_status'] = $aUpdate['ship_status'];        
        $order->save($aUpdate);

        $aUpdate['total_amount'] = $sdf_order['total_amount'];
        $aUpdate['is_tax'] = $sdf_order['is_tax'];
        $aUpdate['member_id'] = $sdf_order['member_id'];
        $aUpdate['delivery'] = $delivery;
        $aUpdate['ship_billno'] = $delivery['logi_no'];
        $aUpdate['ship_corp'] = $delivery['logi_name'];
        // 配送方式名称
        $obj_dlytype = $this->app->model('dlytype');
        $arr_dlytype = $obj_dlytype->dump($delivery['delivery']['delivery'], 'dt_name');
        $aUpdate['delivery']['delivery'] = $arr_dlytype['dt_name'];
        if ($sdf_order['member_id'])
        {
            $member = $this->app->model('members');
            $arr_member = $member->dump($sdf_order['member_id'], '*', array(':account@pam'=>'*'));
        }
        $aUpdate['email'] = (!$sdf_order['member_id']) ? $sdf_order['consignee']['email'] : $arr_member['contact']['email'];
        
        $order->fireEvent('shipping', $aUpdate, $sdf_order['member_id']);

        //取得发货的具体信息，add by hujianxin
        $message_part1 = "";
        $message = "";

        $ship_status = $aUpdate['ship_status'];

        if ($ship_status == '1')
        {   //全部发货
            $message_part1 = "发货完成";
        }
        else if ($ship_status == '2')
        {    //部分发货
            $message_part1 = "已发货";
        }
        
        $message = "订单<!--order_id=".$sdf['order_id']."&delivery_id=".$delivery['delivery_id']."&ship_status=".$ship_status."-->".$message_part1;
        
        $log_text = "";
        if ($ship_status == '1')
        {
            $log_text = "订单<a style=\"color: rgb(0, 51, 102); font-weight: bolder; text-decoration: underline;\" title=\"点击查看详细\" onclick=\"show_delivery_item(this,&quot;120100818000001&quot;," . htmlentities(json_encode($arr_items), ENT_QUOTES) . ")
\" href=\"javascript:void(0)\">全部商品</a>发货完成，" . (($aCorp) ? "物流公司：<a href=\"" . $aCorp['request_url'] . "\" title=\"" . $aCorp['name'] . "\" _target=\"blank\" class=\"lnk\">" . $aCorp['name'] . "</a>（可点击进入物流公司网站跟踪配送）" : "") . (($delivery['logi_no']) ? "物流单号：" . $delivery['logi_no'] : "");
        }
        
        if ($ship_status == '2')
        {
            $log_text = "订单<a style=\"color: rgb(0, 51, 102); font-weight: bolder; text-decoration: underline;\" title=\"点击查看详细\" onclick=\"show_delivery_item(this,&quot;120100818000001&quot;," . htmlentities(json_encode($arr_items), ENT_QUOTES) . ")
\" href=\"javascript:void(0)\">部分商品</a>已发货，" . (($aCorp) ? "物流公司：<a href=\"" . $aCorp['request_url'] . "\" title=\"" . $aCorp['name'] . "\" _target=\"blank\" class=\"lnk\">" . $aCorp['name'] . "</a>（可点击进入物流公司网站跟踪配送）" : "") . (($delivery['logi_no']) ? "物流单号：" . $delivery['logi_no'] : "");
        }
        
        // 更新发货日志结果
        $objorder_log = $this->app->model('order_log');        
        $sdf_order_log = array(
            'rel_id' => $sdf['order_id'],
            'op_id' => $sdf['opid'],
            'op_name' => $sdf['opname'],
            'alttime' => time(),
            'bill_type' => 'order',
            'behavior' => 'delivery',
            'result' => 'SUCCESS',
            'log_text' => $log_text,
        );
        $log_id = $objorder_log->save($sdf_order_log);
        
        return true;
    }
    
    /**
     * 修改各个item的相关信息
     * @params array 修改的data
     * @return boolean 成功与否的
     */
    private function toInsertItem(&$data)
    {
        // 三个模型实体对象
        $order_item = $this->app->model('order_items');
        $o = $this->app->model('delivery_items');
        $arr_data = $data;
        unset($arr_data['item_type']);
         
        if ($o->save($arr_data))
        {
            //更新发货量
            $is_update_store = false;
            $tmp = $order_item->dump($data['order_item_id'],'*');
            $update_data['sendnum'] = $this->objMath->number_plus(array($tmp['sendnum'], $data['number']));
            
            if ($tmp['nums'] > $update_data['sendnum'])
                $is_update_store = false;
            else
                $is_update_store = true;
                
            $update_data['item_id'] = $tmp['item_id'];
            
            if ($is_update_store && $order_item->save($update_data))
            {
                return $this->minus_stock($data);
            }
            else
            {
                return false;    
            }

        }
        return false;
    }
}
