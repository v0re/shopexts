<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class b2c_order_reship extends b2c_order_operation
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
    }
    
    /**
     * 类静态构造实例的唯一入口
     * @params object app object
     * @params object model object
     * @return object b2c_order_reship的对象
     */
    public static function getInstance($app, $model)
    {
        if (is_object(self::$instance))
        {
            return self::$instance;
        }
        
        self::$instance = new b2c_order_reship($app, $model);
        
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
        $order = $controller->app->model('orders');
        $subsdf = array('order_objects'=>array('*',array('order_items'=>array('*',array(':products'=>'*')))));
        $sdf_order = $order->dump($sdf['order_id'],'*',$subsdf);
        $order_items = array();
        
        foreach ($sdf_order['order_objects'] as $k=>$v)
        {
            $order_items = array_merge($order_items,$v['order_items']);
        }
        
        if (isset($sdf['send']))
        {
            if($sdf['logi_id'])
            {
                $oCorp = &$controller->app->model('dlycorp');
                $aCorp = $oCorp->dump($sdf['logi_id'],'*');
            }
            
            $reship = array(
                'money' => floatval($sdf['money']) + floatval($sdf['cost_protect']),
                'is_protect' => $sdf['is_protect'],
                'delivery' => $sdf['delivery'],
                'reship_id' => $sdf['reship_id'],
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
            $reship = array(
                'money' => $sdf_order['cost_freight']+$sdf_order['cost_protect'],
                'is_protect' => $sdf_order['is_protect'],
                'delivery' => $sdf_order['shipping']['shipping_name'],
                'reship_id' => $this->gen_id(),
                'logi_id' => '',
                'logi_no' => $sdf['logi_no'],
                'logi_name' => $sdf['logi_name'],
                'ship_name' => $sdf_order['ship_name'],
                'ship_area' => $sdf_order['ship_area'],
                'ship_addr' => $sdf_order['ship_addr'],
                'ship_zip' => $sdf_order['ship_zip'],
                'ship_tel' => $sdf_order['ship_tel'],
                'ship_mobile' => $sdf_order['ship_mobile'],
                'ship_email' => $sdf_order['ship_email']
            );
        }

        $reship['order_id'] = $sdf['order_id'];
        $reship['member_id'] = $sdf_order['member_id'];
        $reship['t_begin'] = time();
        $reship['op_name'] = $sdf['opname'];
        $v['type'] = 'reship';
        $reship['status'] = 'progress';
        $reship_id = $reship['reship_id'];
        
        //遍历订单明细
        $aBill = array();
        if (!isset($sdf['send']))
        {//非弹窗确认
           foreach ($order_items as $key=>$dinfo)
           {
                $dinfo['send'] = $dinfo['sendnum'];//须退货=已发送
                $order_items[$key] = $dinfo;
           }
        }
        else
        {//弹窗确认
            foreach ($order_items as $key=>$dinfo)
            {
                if (isset($sdf['send'][$dinfo['item_id']]) && floatval($sdf['send'][$dinfo['item_id']]) > 0)
                {//弹窗并且输入了发货数量>=1
                    if (floatval($sdf['send'][$dinfo['item_id']]) > $dinfo['sendnum'])
                    {
                        $msg = "超过实际需要的退货量！";
                        return false;
                    }
                    elseif (floatval($sdf['send'][$dinfo['item_id']]) == $dinfo['sendnum'])
                    {//足量退货
                        $dinfo['send'] = $dinfo['sendnum'];////须退货=已发送
                    }
                    else
                    {//部分退货
                        $dinfo['send'] = $sdf['send'][$dinfo['item_id']];
                        $nonGoods = 4;
                    }
                }
                else
                {
                    if ($dinfo['sendnum'] == $dinfo['quantity'])
                        $nonGoods = 4;
                }
                $order_items[$key] = $dinfo;
            }
        }

        $oreship = $controller->app->model('reship');
        
        if ($order_items)
        {        
            //实体商品
            $iLoop = 0;
            $arr_items = array();
            foreach ($order_items as $dinfo)
            {
                $item = array(
                            'order_item_id' => $dinfo['item_id'],
                            'order_id' => $sdf['order_id'],
                            'reship_id' => $reship['reship_id'],
                            'item_type' => ($dinfo['is_type']=='pkg' ? $dinfo['is_type'] : 'goods'),
                            'product_id' => $dinfo['products']['product_id'],
                            'product_bn' => $dinfo['bn'],
                            'product_name' => $dinfo['name'].$dinfo['addon']['adjname'],
                            'number' => $dinfo['send'] );
                            
                if ($dinfo['send'])
                {
                    $items[] = $dinfo;
                    $arr_items[] = array(
                        'number' => $dinfo['send'],
                        'name' => $dinfo['name'].$dinfo['addon']['adjname'],
                    );
                    $this->toInsertItem($item);
                    $iLoop++;
                }
            }
            
            if ($iLoop > 0)
            {
                $is_save = $oreship->save($reship);
                //$obj_api_reship = kernel::service("api.b2c.reship");
                //$is_save = $obj_api_reship->create($reship);
                if (!$is_save)
                {
                    $msg = '退货单生成失败！';
                    return false;
                }
                
                //$arr_reship = $oreship ->dump($reship['reship_id'], 'status');
                $reship['status'] = 'succ';
                $is_save = $oreship->save($reship);
                //$is_save = $obj_api_reship->update($arr_reship);                
                
                if (!$is_save)
                {
                    $msg = '退货单修改失败！';
                    return false;
                }
            }
        }


        //没有完全退货
        if ($nonGoods != 4)
        {
            $aUpdate['ship_status'] = 4;
        }
        else
        {
            $aUpdate['ship_status'] = 3;
        }
        
        $aUpdate['order_id'] = $sdf['order_id'];
        $aUpdate['ship_status'] = $aUpdate['ship_status'];

        $order->save($aUpdate);
        
        $order_reship = $controller->app->model('order_delivery');
        $order_reship_data = array('order_id'=>$reship['order_id'],'dly_id'=>$reship_id,'dlytype'=>'reship','items'=>($items));
        $order_reship->save($order_reship_data);

        $aUpdate['total_amount'] = $sdf_order['total_amount'];
        $aUpdate['is_tax'] = $sdf_order['is_tax'];
        $aUpdate['member_id'] = $sdf_order['member_id'];
        $aUpdate['delivery'] = $reship;
        $aUpdate['ship_billno'] = $reship['logi_no'];
        // 取得物流公司的名称
        $obj_dlycorp = $this->app->model('dlycorp');
        $arr_dlycorp = $obj_dlycorp->dump($reship['logi_id'], 'name');
        $aUpdate['ship_corp'] = $arr_dlycorp['name'];
        if ($sdf_order['member_id'])
        {
            $member = $this->app->model('members');
            $arr_member = $member->dump($sdf_order['member_id'], '*', array(':account@pam'=>'*'));
        }
        $aUpdate['email'] = (!$sdf_order['member_id']) ? $sdf_order['consignee']['email'] : $arr_member['contact']['email'];
        
        $order->fireEvent('returned', $aUpdate, $sdf_order['member_id']);

        //取得发货的具体信息，add by hujianxin
        $message_part1 = "";
        $message = "";

        $ship_status = $aUpdate['ship_status'];

        if ($ship_status == '4')
        {   //全部发货
            $message_part1 = "完全退货";
        }
        else if ($ship_status == '3')
        {    //部分发货
            $message_part1 = "部分退货";
        }
        
        $message = "订单<!--order_id=".$sdf['order_id']."&reship_id=".$reship['reship_id']."&ship_status=".$ship_status."-->".$message_part1;
        
        // 更新退款日志结果
        $log_text = "";
        if ($ship_status == '4')
        {
            $log_text = "订单<a style=\"color: rgb(0, 51, 102); font-weight: bolder; text-decoration: underline;\" title=\"点击查看详细\" onclick=\"show_delivery_item(this,&quot;120100818000001&quot;," . htmlentities(json_encode($arr_items), ENT_QUOTES) . ")
\" href=\"javascript:void(0)\">全部商品</a>退货完成";
        }
        
        if ($ship_status == '3')
        {
            $log_text = "订单<a style=\"color: rgb(0, 51, 102); font-weight: bolder; text-decoration: underline;\" title=\"点击查看详细\" onclick=\"show_delivery_item(this,&quot;120100818000001&quot;," . htmlentities(json_encode($arr_items), ENT_QUOTES) . ")
\" href=\"javascript:void(0)\">部分商品</a>已退货";
        }
        $objorder_log = $this->app->model('order_log');
        $sdf_order_log = array(
            'rel_id' => $sdf['order_id'],
            'op_id' => $sdf['op_id'],
            'op_name' => $sdf['opname'],
            'alttime' => time(),
            'bill_type' => 'order',
            'behavior' => 'reship',
            'result' => 'SUCCESS',
            'log_text' => $log_text,
        );
        $log_id = $objorder_log->save($sdf_order_log);

        return true;
    }
    
    /**
     * 更新订单各个items
     * @params array 标准数据数组
     * @return boolean 更新是否成功
     */
    private function toInsertItem(&$data)
    {
        $order_item = $this->app->model('order_items');
        $o = $this->app->model('reship_items');
        $objMath = kernel::single('ectools_math');
        
        if ($o->save($data))
        {
            //更新发货量
            $tmp = $order_item->dump($data['order_item_id'],'*');
            $update_data['sendnum'] = $objMath->number_minus(array($tmp['sendnum'], $data['number']));
            $update_data['item_id'] = $tmp['item_id'];
             
            if ($order_item->save($update_data))
            {
               return $this->restore_stock($data);
            }
            else
            {
                return false;    
            }
        }
        
        return false;
    }
}
