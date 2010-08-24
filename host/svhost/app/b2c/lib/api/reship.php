<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

/**
 * b2c reship interactor with center
 * shopex team
 * dev@shopex.cn
 */
class b2c_api_reship
{
    /**
     * app object
     */
    public $app;
    
    /**
     * 构造方法
     * @param object app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }
    
    /**
     * 退货单创建
     * @param array sdf
     * @return boolean success or failure
     */
    public function create(&$sdf, $thisObj)
    {
        $oreship = $this->app->model('reship');
        
        if (!$sdf['reship_bn'] || !$sdf['order_bn'] || !isset($sdf['reship_bn']) || !isset($sdf['order_bn']))
        {
            $thisObj->send_user_error(__('退货单tid没有收到！'), array());
        }
        else
        {
            // 退货单创建是和中心的交互
            $cnt = $oreship->count(array('reship_bn' => $sdf['reship_bn']));
            
            if (!$cnt)
            {
                $arr_items = json_decode($sdf['items'], true);
                $order_item = $this->app->model('order_items');
                $o = $this->app->model('reship_items');
                $objMath = kernel::single('ectools_math');
                $order = $this->app->model('orders');
                
                $obj_products = $this->app->model('products');
                $obj_dlytype = $this->app->model('dlytype');
                $arr_dlytype = $obj_dlytype->dump(array('dt_name' => $sdf['delivery']));
                $obj_dlycorp = $this->app->model('dlycorp');
                $arr_dlycorp = $obj_dlycorp->dump(array('name' => $sdf['logi_name']));
                $obj_regions = app::get('ectools')->model('regions');
                $arr_regions = $obj_regions->dump(array('local_name' => $sdf['ship_distinct']));
                $reship_id = $oreship->gen_id();
                
                $arr_data = array(
                    'money' => $sdf['money'],
                    'order_id' => $sdf['order_bn'],
                    'is_protect' => ($sdf['is_protect']) ? 'true' : 'false',
                    'delivery' => $arr_dlytype['dt_id'] ? $arr_dlytype['dt_id'] : 0,
                    'reship_id' => $reship_id,
                    'reship_bn' => $sdf['reship_bn'],
                    'logi_id' => $arr_dlycorp['corp_id'] ? $arr_dlycorp['corp_id'] : 0,
                    'logi_no' => $sdf['logi_no'],
                    'logi_name' => $sdf['logi_name'],
                    'ship_name' => $sdf['ship_name'],
                    'ship_area' => $arr_regions['package'] . ":" . $sdf['ship_states'] . "/" . $sdf['ship_city'] . "/" . $sdf['ship_distinct'] . ":" . $arr_regions['region_id'],
                    'ship_addr' => $sdf['ship_addr'],
                    'ship_zip' => $sdf['ship_zip'],
                    'ship_tel' => $sdf['ship_tel'],
                    'ship_mobile' => $sdf['ship_mobile'],
                    'ship_email' => $sdf['ship_email'],
                    'memo' => $sdf['memo'],
                );
                
                $arr_data['member_id'] = $sdf['member_id'] ? $sdf['member_id'] : 0;
                $arr_data['t_begin'] = strtotime($sdf['timestamp']);
                $arr_data['op_name'] = $sdf['buyer_uname'] ? $sdf['buyer_uname'] : '';
                
                $arr_data['status'] = $sdf['status'];
                
                $oreship->save($arr_data);
                
                $items = array();
                $has_error = false;
                $nonGoods = 0;    //是否完全退货商品标识
                $failitems = array();
                if ($arr_items)
                {
                    foreach ($arr_items as $arr_item_info)
                    {
                        //$arr_item_info = (array)$arr_item_info;
                        $arr_products = $order_item->dump(array('order_id' => $sdf['order_bn'], 'bn' => $arr_item_info['product_bn']));
                       
                        if ($arr_products['products']['product_id'])
                        {
                            if ($objMath->number_minus(array($arr_products['sendnum'], $arr_item_info['number'])) >= 0)
                            {
                                if ($objMath->number_minus(array($arr_products['sendnum'], $arr_item_info['number'])) > 0)
                                    $nonGoods = 4;
                                else
                                    $nonGoods = 0;
                                    
                                $items = array(
                                    'reship_id' => $reship_id,
                                    'item_type' => $arr_item_info['item_type'] == 'goods' ? 'goods' : 'gift',
                                    'product_id' => $arr_products['products']['product_id'],
                                    'product_bn' => $arr_item_info['product_bn'],
                                    'product_name' => $arr_item_info['product_name'],
                                    'number' => $arr_item_info['number'],                    
                                );
                            }
                            else
                            {
                                $has_error = true;
                                $failitems[] = array(
                                    'reship_id' => $reship_id,
                                    'item_type' => $arr_item_info['item_type'] == 'goods' ? 'goods' : 'gift',
                                    'product_id' => $arr_products['products']['product_id'],
                                    'product_bn' => $arr_item_info['product_bn'],
                                    'product_name' => $arr_item_info['product_name'],
                                    'number' => $arr_item_info['number'],                    
                                );
                                continue;
                            }
                        }                        
                        else
                        {
                            $has_error = true;
                            $failitems[] = array(
                                    'reship_id' => $reship_id,
                                    'item_type' => $arr_item_info['item_type'] == 'goods' ? 'goods' : 'gift',
                                    'product_id' => $arr_products['products']['product_id'],
                                    'product_bn' => $arr_item_info['product_bn'],
                                    'product_name' => $arr_item_info['product_name'],
                                    'number' => $arr_item_info['number'],                    
                                );
                            continue;
                        }
                        
                        if ($o->save($items))
                        {
                            //更新发货量
                            $tmp = $order_item->dump(array('order_id' => $sdf['order_bn'], 'bn' => $items['product_bn']),'*');
                            $update_data['sendnum'] = $objMath->number_minus(array($tmp['sendnum'], $items['number']));
                            $update_data['item_id'] = $tmp['item_id'];
                             
                            if ($order_item->save($update_data))
                            {
                                $storage_enable = $this->app->getConf('site.storage.enabled');
                        
                                if ($storage_enable != 'true')
                                {
                                    //更新库存
                                    $obj_checkorder = kernel::service('b2c_order_apps', array('content_path'=>'b2c_order_checkorder'));
                                    $arrStatus = $obj_checkorder->checkOrderFreez('reship', $sdf['order_bn']);
                                    
                                    if ($arrStatus['unstore'])
                                    {
                                        $update_data_p = array();
                                        $products = $this->app->model('products');
                                        
                                        $tmp_p = $products->dump($items['product_id'],'*');
                                        if (!is_null($tmp_p['store']) || $tmp_p['store'] === '')
                                        {
                                            $update_data_p['store'] = $objMath->number_plus(array($tmp_p['store'], $items['number']));
                                            $update_data_p['product_id'] = $tmp_p['product_id'];
                                            
                                            $products->save($update_data_p);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    
                    //没有完全发货
                    if ($nonGoods != 4) 
                        $aUpdate['ship_status'] = 4;
                    else 
                        $aUpdate['ship_status'] = 3;
                    
                    $aUpdate['order_id'] = $sdf['order_bn'];
                    
                    $arr_order = $order->dump($sdf['order_bn']);
                    if ($arr_order)
                        $order->save($aUpdate);
                    
                    if (!$has_error)
                        return array('reship_id' => $reship_id);
                    else
                    {
                        $thisObj->send_user_error(__('退货单明细单保存有误！'), $failitems);
                    }
                }
            }
            else
            {
                $thisObj->send_user_error(__('退货单已经存在了！'), array());
            }
        }
    }
    
    /**
     * 退货单修改
     * @param array sdf
     * @return boolean sucess of failure
     */
    public function update(&$sdf, $thisObj)
    {
        // 退货单修改是和中心的交互
        $oreship = $this->app->model('reship');
        $arr_data = $oreship->dump(array('reship_bn' => $sdf['reship_bn'], 'order_id' => $sdf['order_bn']));
        
        if ($arr_data)
        {
            $obj_dlytype = $this->app->model('dlytype');
            $arr_dlytype = $obj_dlytype->dump(array('dt_name' => $sdf['delivery']));
            $obj_dlycorp = $this->app->model('dlycorp');
            $arr_dlycorp = $obj_dlycorp->dump(array('name' => $sdf['logi_name']));
            $obj_regions = app::get('ectools')->model('regions');
            $arr_regions = $obj_regions->dump(array('local_name' => $sdf['ship_distinct']));
            $reship_id = $oreship->gen_id();
            $order = $this->app->model('orders');
            $objMath = kernel::single('ectools_math');
            
            if (isset($arr_regions) && $arr_regions)
            {
                $arr_data = array(
                    'is_protect' => ($sdf['is_protect']) ? 'true' : 'false',
                    'logi_id' => $arr_dlycorp['corp_id'] ? $arr_dlycorp['corp_id'] : 0,
                    'logi_name' => $sdf['logi_name'],
                    'ship_name' => $sdf['ship_name'],
                    'ship_area' => $arr_regions['package'] . ":" . $sdf['ship_states'] . "/" . $sdf['ship_city'] . "/" . $sdf['ship_distinct'] . ":" . $arr_regions['region_id'],
                    'ship_addr' => $sdf['ship_addr'],
                    'ship_zip' => $sdf['ship_zip'],
                    'ship_tel' => $sdf['ship_tel'],
                    'ship_mobile' => $sdf['ship_mobile'],
                    'ship_email' => $sdf['ship_email'],
                );
            }
            
            if ($arr_dlytype)
                $arr_data['delivery'] = $arr_dlytype['dt_id'];
            else
                $arr_data['delivery'] = 0;
            if ($sdf['memo'])
                $arr_data['memo'] = $sdf['memo'];
            if ($sdf['money'])
                $arr_data['money'] = $sdf['money'];
            if ($sdf['logi_no'])
                $arr_data['logi_no'] = $sdf['logi_no'];
            if ($sdf['status'])
                $arr_data['status'] = $sdf['status'];
                
            $is_save = $oreship->save($arr_data);
            
            if (!$is_save)
            {
                $thisObj->send_user_error(__('退货单信息修改失败！'), array());
            }
            
            $is_part_reship = false;
            $o = $this->app->model('order_items');
            $arr_order_items = $o->getList('*', array('order_id' => $sdf['order_bn']));
            $arr_reship_items = array();
            if ($arr_order_items)
            {
                foreach ($arr_order_items as $arr_item)
                {
                    if ($arr_item['sendnum'] == $arr_item['nums'])
                    {
                        $is_part_reship = true;
                    }
                    else
                    {
                        $arr_reship_items[] = array(
                            'number' => $objMath->number_minus(array($arr_item['nums'], $arr_item['sendnum'])),
                            'name' => $arr_item['name'],
                        );
                    }
                }
            }
            
            if ($sdf['status'] == 'succ')
            {
                if ($is_part_delivery)
                {
                    $ship_status = '3';
                }
                else
                {
                    $ship_status = '4';
                }
                
                $aUpdate = array();
                $aUpdate['order_id'] = $sdf['order_bn'];
                $aUpdate['ship_status'] = $ship_status;

                $order->save($aUpdate);
                
                // 更新退款日志结果
                $log_text = "";
                if ($ship_status == '4')
                {
                    $log_text = "订单<a style=\"color: rgb(0, 51, 102); font-weight: bolder; text-decoration: underline;\" title=\"点击查看详细\" onclick=\"show_delivery_item(this,&quot;120100818000001&quot;," . htmlentities(json_encode($arr_reship_items), ENT_QUOTES) . ")
        \" href=\"javascript:void(0)\">全部商品</a>退货完成";
                }
                
                if ($ship_status == '3')
                {
                    $log_text = "订单<a style=\"color: rgb(0, 51, 102); font-weight: bolder; text-decoration: underline;\" title=\"点击查看详细\" onclick=\"show_delivery_item(this,&quot;120100818000001&quot;," . htmlentities(json_encode($arr_reship_items), ENT_QUOTES) . ")
        \" href=\"javascript:void(0)\">部分商品</a>已退货";
                }
                $objorder_log = $this->app->model('order_log');
                $sdf_order_log = array(
                    'rel_id' => $sdf['order_bn'],
                    'op_id' => $sdf['op_id'],
                    'op_name' => $sdf['opname'],
                    'alttime' => time(),
                    'bill_type' => 'order',
                    'behavior' => 'reship',
                    'result' => 'SUCCESS',
                    'log_text' => $log_text,
                );
                $log_id = $objorder_log->save($sdf_order_log);
                
                //退货监控
                $sdf_order = $order->dump($sdf['order_bn'],'*');
                $aUpdate['order_id'] = $sdf['order_bn'];
                $aUpdate['ship_status'] = $ship_status;
                $aUpdate['total_amount'] = $sdf_order['total_amount'];
                $aUpdate['is_tax'] = $sdf_order['is_tax'];
                $aUpdate['member_id'] = $sdf_order['member_id'];
                $aUpdate['delivery'] = $arr_data;
                $aUpdate['ship_billno'] = $arr_data['logi_no'];
                // 取得物流公司的名称
                $obj_dlycorp = $this->app->model('dlycorp');
                $arr_dlycorp = $obj_dlycorp->dump($arr_data['delivery'], 'name');
                if ($arr_dlycorp)
                    $aUpdate['ship_corp'] = $arr_dlycorp['name'];
                else
                    $aUpdate['ship_corp'] = "";
                if ($sdf_order['member_id'])
                {
                    $member = $this->app->model('members');
                    $arr_member = $member->dump($sdf_order['member_id'], '*', array(':account@pam'=>'*'));
                }
                $aUpdate['email'] = (!$sdf_order['member_id']) ? $sdf_order['consignee']['email'] : $arr_member['contact']['email'];
                
                $order->fireEvent('returned', $aUpdate, $sdf_order['member_id']);
            }
        }
        else
        {
            $thisObj->send_user_error(__('退货单不存在！'), array());
        }
        
        return true;
    }
}