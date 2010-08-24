<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

/**
 * b2c order interactor with center
 * shopex team
 * dev@shopex.cn
 */
class b2c_api_order implements b2c_api_interface_order
{
    /**
     * app object
     */
    public $app;
    
    /**
     * ectools_math object
     */
    public $objMath;
    
    /**
     * 构造方法
     * @param object app
     */
    public function __construct($app)
    {
        $this->app = $app;
        $this->objMath = kernel::single('ectools_math');
    }
    
    /**
     * 订单创建
     * @param array sdf
     * @param string member indent
     * @param string message
     * @return boolean success or failure
     */
    public function create(&$sdf, &$thisObj)
    {
        // 创建订单是和中心的交互
        $order = &$this->app->model('orders');
        $result = $order->save($sdf);//todo order_items表product_id字段未插入
        
        if (!$result)
        {
            trigger_error('订单生成失败！', E_USER_ERROR);
        }
        else
        {
            return true;
        }
    }
    
    /**
     * 订单修改
     * @param array sdf
     * @return boolean sucess of failure
     */
    public function update(&$sdf, &$thisObj)
    {
        // 修改订单是和中心的交互
        if (!isset($sdf['order_bn']) || !$sdf['order_bn'])
        {
            $thisObj->send_user_error(__('需要更新的库存不存在！'), array());
        }
        else
        {
            $objOrder = $this->app->model('orders');
            $arr_order = $objOrder->dump($sdf['order_bn']);
            
            if ($arr_order)
            {
                $arr_data_receive = json_decode($sdf['consignee'], true);
                
                if (!$arr_data_receive)
                {
                    return false;
                }
                else
                {
                    $obj_regions = app::get('ectools')->model('regions');
                    $arr_regions = $obj_regions->dump(array('local_name' => $arr_data_receive['distinct']));
                    
                    $arr_data['order_id'] = $sdf['order_bn'];
                    if (isset($sdf['last_modified']) && $sdf['last_modified'])
                        $arr_data['last_modified'] = $sdf['last_modified'];
                    if (isset($sdf['is_tax']) && $sdf['is_tax'])
                    {
                        $arr_data['is_tax'] = $sdf['is_tax'];                
                        $arr_data['tax_title'] = $sdf['tax_title'];
                        $arr_data['cost_tax'] = $sdf['cost_tax'];
                    }
                    if (isset($sdf['cost_item']) && $sdf['cost_item'])
                        $arr_data['cost_item'] = $sdf['cost_item'];
                    if (isset($sdf['total_amount']) && $sdf['total_amount'])
                        $arr_data['total_amount'] = $sdf['total_amount'];
                    if (isset($sdf['discount']) && $sdf['discount'])
                        $arr_data['discount'] = $sdf['discount'];
                    if (isset($sdf['payed']) && $sdf['payed'])
                        $arr_data['payed'] = $sdf['payed'];
                    if (isset($sdf['currency']) && $sdf['currency'])
                        $arr_data['currency'] = $sdf['currency'];
                    if (isset($sdf['cur_rate']) && $sdf['cur_rate'])
                        $arr_data['cur_rate'] = $sdf['cur_rate'];
                    if (isset($sdf['cur_amount']) && $sdf['cur_amount'])
                        $arr_data['cur_amount'] = $sdf['cur_amount'];
                    if (isset($sdf['score_u']) && $sdf['score_u'])
                        $arr_data['score_u'] = $sdf['score_u'];
                    if (isset($sdf['score_g']) && $sdf['score_g'])
                        $arr_data['score_g'] = $sdf['score_g'];
                    if (isset($sdf['shipping']) && $sdf['shipping'])
                    {
                        $arr_data['shipping'] = json_decode($sdf['shipping'], true);
                    }
                    if (isset($sdf['payinfo']) && $sdf['payinfo'])
                    {
                        $arr_data['payinfo'] =json_decode($sdf['payinfo'], true);
                    }
                    if ($arr_regions)
                        $arr_data['consignee'] = array(
                            'name' => $arr_data_receive['name'],
                            'addr' => $arr_data_receive['addr'],
                            'zip' => $arr_data_receive['zip'],
                            'telephone' => $arr_data_receive['telephone'],
                            'mobile' => $arr_data_receive['mobile'],
                            'email' => $arr_data_receive['email'],
                            'area' => $arr_regions['package'] . ":" . $arr_data_receive['states'] . "/" . $arr_data_receive['city'] . "/" . $arr_data_receive['distinct'] . ":" . $arr_regions['region_id'],
                        );
                    else
                        $arr_data['consignee'] = array(
                            'name' => $arr_data_receive['name'],
                            'addr' => $arr_data_receive['addr'],
                            'zip' => $arr_data_receive['zip'],
                            'telephone' => $arr_data_receive['telephone'],
                            'mobile' => $arr_data_receive['mobile'],
                            'email' => $arr_data_receive['email'],
                            'area' => "",
                        );
                    
                    $result = $objOrder->save($arr_data);//订单基本信息更改
                    
                    if (!$result)
                    {
                        $thisObj->send_user_error(__('订单基本信息修改失败！'), array());
                    }
                    
                    // 记录订单日志
                    $objorder_log = $this->app->model('order_log');
                    $log_text = "订单收货人信息修改！";
                    $sdf_order_log = array(
                        'rel_id' => $sdf['order_bn'],
                        'op_id' => '1',
                        'op_name' => 'admin',
                        'alttime' => time(),
                        'bill_type' => 'order',
                        'behavior' => 'updates',
                        'result' => 'SUCCESS',
                        'log_text' => $log_text,
                    );
                    $log_id = $objorder_log->save($sdf_order_log);
                    
                    return true;
                }
            }
            else
            {
                $thisObj->send_user_error(__('订单不存在！'), array());
            }
        }        
    }
    
    /**
     * 订单备注
     * @param array sdf
     * @param string message
     * @return boolean success or failure
     */
    public function remark(&$sdf, &$thisObj)
    {
        // 备注订单是和中心的交互
        $order = $this->app->model('orders');
        $arr_order = $order->dump($sdf['order_bn']);
        
        if ($arr_order)
        {
            $mem_info = json_decode($sdf['memo'], true);
            $data['order_id'] = $sdf['order_bn'];
            $data['mark_text'] = $mem_info['op_content'];
            $data['mark_type'] = $sdf['mark_type'];
            
            $is_success = $order->save($data);
            if ($is_success)
            {
                return true;
            }
            else
            {
                $thisObj->send_user_error(__('订单备注保存失败！'), array());
            }
        }
        else
        {
            $thisObj->send_user_error(__('此订单不存在！'), array());
        }
    }
    
    /**
     * 订单留言
     * @param array sdf
     * @param string message
     * @return boolean success or failure
     */
    public function leave_message(&$sdf, &$thisObj)
    {
        // 订单留言是和中心的交互
        if (!isset($sdf['order_bn']) || !$sdf['order_bn'])
        {
            $order = &$this->app->model('orders');
            $arrOrder = $order->dump($sdf['order_bn'], 'member_id');
            $arr_memo = json_decode($sdf['memo'], true);
            if ($arrOrder)
            {
                $objMember = $this->app->model('members');
                $arrMember = $objMember->dump($arrOrder['member_id'], 'name');                
                $oMsg = kernel::single("b2c_message_order");
                
                $order_id = $sdf['order_bn'];
                $arrData['title'] = __('订单 ') . $sdf['order_bn'] . '管理员留言';
                $arrData['comment'] = htmlspecialchars($arr_memo['op_content']);
                $arrData['to_id'] = $arrOrder['member_id'];
                $arrData['to_uname'] = $arrMember['contact']['name'] ? $arrMember['contact']['name'] : '顾客';
                $arrData['for_comment_id'] = 0;
                $arrData['author_id'] = 0;
                $arrData['order_id'] = $order_id;
                $arrData['object_type'] = 'order';
                $arrData['author'] = $sdf['op_name'];
                $arrData['time'] = $sdf['op_time'];
                $arrData['ip'] = $_SERVER['REMOTE_ADDR'];        
                
                if (!$oMsg->save($arrData))
                {
                    $thisObj->send_user_error(__('订单留言保存失败！'), array());
                }
                else
                {
                    return true;
                }
            }
            else
            {
                $thisObj->send_user_error(__('订单不存在！'), array());
            }
        }
        else
        {
            $thisObj->send_user_error(__('订单号未发送！'), array());
        }
    }
    
    /**
     * 订单状态更新
     * @param array sdf
     * @return boolean true or false.
     */
    public function status_update(&$sdf, &$thisObj)
    {
        // 取消订单是和中心的交互
        $order = $this->app->model('orders');
        $arr_data['status'] = $sdf['status'];
        $arr_data['order_id'] = $sdf['order_bn'];
        
        $arr_order = $order->dump($sdf['order_bn']);
        if ($arr_order)
        {
            $is_save = $order->save($arr_data);
            
            if ($is_save)
            {
                if ($sdf['status'] == 'dead')
                {
                    $aUpdate['order_id'] = $sdf['order_bn'];
                    $sdf_order = $order->dump($sdf['order_bn']);
                    if ($sdf_order['member_id'])
                    {
                        $member = $this->app->model('members');
                        $arr_member = $member->dump($sdf_order['member_id'], '*', array(':account@pam'=>'*'));
                    }
                    $aUpdate['email'] = (!$sdf_order['member_id']) ? $sdf_order['consignee']['email'] : $arr_member['contact']['email'];
                    $order->fireEvent("cancel", $aUpdate, $sdf_order['member_id']);
                }
                
                // 记录订单日志
                $objorder_log = $this->app->model('order_log');
                $log_text = "订单状态修改！";
                $sdf_order_log = array(
                    'rel_id' => $sdf['order_bn'],
                    'op_id' => '1',
                    'op_name' => 'admin',
                    'alttime' => time(),
                    'bill_type' => 'order',
                    'behavior' => 'updates',
                    'result' => 'SUCCESS',
                    'log_text' => $log_text,
                );
                $log_id = $objorder_log->save($sdf_order_log);
                
                return true;
            }
            else
            {
                $thisObj->send_user_error(__('订单状态修改失败！'), array());
            }
        }
        else
        {
            $thisObj->send_user_error(__('订单不存在！'), array());
        }
    }
    
    /**
     * 订单支付状态更新接口
     * @param array sdf
     * @return boolean true or false
     */
    public function pay_status_update(&$sdf, &$thisObj)
    {
        $order = $this->app->model('orders');
        $arr_data['pay_status'] = $sdf['pay_status'];
        $arr_data['order_id'] = $sdf['order_bn'];
        
        $arr_order = $order->dump($sdf['order_bn']);
        
        if ($arr_order)
        {
            $is_save = $order->save($arr_data);
            
            if ($is_save)
            {
                // 记录订单日志
                $objorder_log = $this->app->model('order_log');
                $log_text = "订单支付状态修改！";
                $sdf_order_log = array(
                    'rel_id' => $sdf['order_bn'],
                    'op_id' => '1',
                    'op_name' => 'admin',
                    'alttime' => time(),
                    'bill_type' => 'order',
                    'behavior' => 'updates',
                    'result' => 'SUCCESS',
                    'log_text' => $log_text,
                );
                $log_id = $objorder_log->save($sdf_order_log);
                
                return true;
            }
            else
            {
                $thisObj->send_user_error(__('订单支付状态修改失败！'), array());
            }
        }
        else
        {
            $thisObj->send_user_error(__('订单不存在！'), array());
        }
    }
    
    /**
     * 订单发货状态更新接口
     * @param array sdf
     * @return boolean true or false
     */
    public function ship_status_update(&$sdf, &$thisObj)
    {
        $order = $this->app->model('orders');
        $arr_data['ship_status'] = $sdf['ship_status'];
        $arr_data['order_id'] = $sdf['order_bn'];
        
        $arr_order = $order->dump($sdf['order_bn']);
        
        if ($arr_order)
        {
            $is_save = $order->save($arr_data);
            
            if ($is_save)
            {
                // 记录订单日志
                $objorder_log = $this->app->model('order_log');
                $log_text = "订单发货状态修改！";
                $sdf_order_log = array(
                    'rel_id' => $sdf['order_bn'],
                    'op_id' => '1',
                    'op_name' => 'admin',
                    'alttime' => time(),
                    'bill_type' => 'order',
                    'behavior' => 'updates',
                    'result' => 'SUCCESS',
                    'log_text' => $log_text,
                );
                $log_id = $objorder_log->save($sdf_order_log);
                
                return true;
            }
            else
            {
                $thisObj->send_user_error(__('订单发货状态修改失败！'), array());
            }
        }
        else
        {
            $thisObj->send_user_error(__('订单不存在！'), array());
        }
    }
}