<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

/**
 * b2c delivery interactor with center
 * shopex team
 * dev@shopex.cn
 */
class b2c_api_delivery
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
     * 发货单创建
     * @param array sdf
     * @return boolean success or failure
     */
    public function create(&$sdf, $thisObj)
    {
        // 发货单创建是和中心的交互
        $odelivery = $this->app->model('delivery');
        
        if (!$sdf['delivery_bn'] || !$sdf['order_bn'] || !isset($sdf['delivery_bn']) || !isset($sdf['order_bn']))
        {
            $thisObj->send_user_error(__('发货单tid没有收到！'), array());
        }
        else
        {
            $cnt = $odelivery->count(array('delivery_bn' => $sdf['delivery_bn']));
            if (!$cnt)
            {
                // save the delivery and order items
                $arr_items = json_decode($sdf['items']);
                $order_item = $this->app->model('order_items');
                $o = $this->app->model('delivery_items');
                $objMath = kernel::single('ectools_math');
                //$order = $this->app->model('orders');
                
                $obj_products = $this->app->model('products');
                $obj_dlytype = $this->app->model('dlytype');
                $arr_dlytype = $obj_dlytype->dump(array('dt_name' => $sdf['delivery']));
                $obj_dlycorp = $this->app->model('dlycorp');
                $arr_dlycorp = $obj_dlycorp->dump(array('name' => $sdf['logi_name']));
                $obj_regions = app::get('ectools')->model('regions');
                $arr_regions = $obj_regions->dump(array('local_name' => $sdf['ship_distinct']));
                $order_delivery = $this->app->model('order_delivery');
                $delivery_id = $odelivery->gen_id();
                $arr_data = array(
                    'money' => $sdf['money'],
                    'order_id' => $sdf['order_bn'],
                    'is_protect' => ($sdf['is_protect']) ? 'true' : 'false',
                    'delivery' => $arr_dlytype['dt_id'] ? $arr_dlytype['dt_id'] : 0,
                    'delivery_id' => $delivery_id,
                    'delivery_bn' => $sdf['delivery_bn'],
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
                //$arr_data['type'] = 'delivery';

                $arr_data['status'] = $sdf['status'];
                
                $odelivery->save($arr_data);
                
                $items = array();
                $has_error = false;
                $nonGoods = 0;    //是否完全发货商品标识
                $fail_items = array();
                if ($arr_items)
                {
                    foreach ($arr_items as $arr_item_info)
                    {
                        $arr_item_info = (array)$arr_item_info;
                        $arr_products = $order_item->dump(array('order_id' => $sdf['order_bn'], 'bn' => $arr_item_info['product_bn']));
                        
                        if ($arr_products['products']['product_id'])
                        {
                            if ($objMath->number_plus(array($arr_item_info['number'], $arr_products['sendnum'])) <= $arr_products['quantity'])
                            {
                                if ($objMath->number_plus(array($arr_item_info['number'], $arr_products['sendnum'])) < $arr_products['quantity'])
                                    $nonGoods = 1;
                                else
                                    $nonGoods = 0;
                                    
                                $items = array(
                                    'delivery_id' => $delivery_id,
                                    'order_item_id' => $arr_products['item_id'],
                                    'item_type' => $arr_item_info['item_type'] == 'product' ? 'goods' : 'gift',
                                    'product_id' => $arr_products['products']['product_id'],
                                    'product_bn' => $arr_item_info['product_bn'],
                                    'product_name' => $arr_item_info['product_name'],
                                    'number' => $objMath->number_plus(array($arr_item_info['number'], $arr_products['sendnum'])),                    
                                );
                            }
                            else
                            {
                                $has_error = true;
                                $fail_items[] = array(
                                    'delivery_id' => $delivery_id,
                                    'order_item_id' => $arr_products['item_id'],
                                    'item_type' => $arr_item_info['item_type'] == 'product' ? 'goods' : 'gift',
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
                            $fail_items[] = array(
                                'delivery_id' => $delivery_id,
                                'order_item_id' => $arr_products['item_id'],
                                'item_type' => $arr_item_info['item_type'] == 'product' ? 'goods' : 'gift',
                                'product_id' => $arr_products['products']['product_id'],
                                'product_bn' => $arr_item_info['product_bn'],
                                'product_name' => $arr_item_info['product_name'],
                                'number' => $arr_item_info['number'],                    
                            );
                            
                            $has_error = true;
                            continue;
                        }
                        
                        if ($o->save($items))
                        {
                            //更新发货量
                            $is_update_store = false;
                            $tmp = $order_item->dump($items['order_item_id'],'*');
                            $update_data['sendnum'] = $objMath->number_plus(array($tmp['sendnum'], $items['number']));
                            
                            if ($tmp['nums'] > $update_data['sendnum'])
                                $is_update_store = false;
                            else
                                $is_update_store = true;
                                
                            $update_data['item_id'] = $tmp['item_id'];
                            
                            if ($is_update_store && $order_item->save($update_data))
                            {
                                $storage_enable = $this->app->getConf('site.storage.enabled');
                        
                                if ($storage_enable != 'true')
                                {
                                    $obj_checkorder = kernel::service('b2c_order_apps', array('content_path'=>'b2c_order_checkorder'));
                                    $arrStatus = $obj_checkorder->checkOrderFreez('delivery', $sdf['order_bn']);
                                    // 裁剪库存
                                    $products = $this->app->model('products');
                                
                                    $update_data_p = array();
                                    $tmp_p = $products->dump($items['product_id'],'*');
                                    
                                    if (!is_null($tmp_p['store']) || $tmp_p['store'] === '')
                                    {
                                        if ($arrStatus['store'])
                                            $update_data_p['store'] = $objMath->number_minus(array($tmp_p['store'], $arr_items['number']));
                                        if ($arrStatus['unfreez'])
                                            $update_data_p['freez'] = $objMath->number_minus(array($tmp_p['freez'], $arr_items['number']));
                                            
                                        $update_data_p['product_id'] = $tmp_p['product_id'];
                                        
                                        $products->save($update_data_p);
                                    }
                                }
                            }
                        }
                    }
                    
                    $order_delivery_data = array('order_id'=>$sdf['order_bn'],'dly_id'=>$delivery_id,'dlytype'=>'delivery','items'=>($items));
                    if ($order_delivery_data)
                        $order_delivery->save($order_delivery_data);
                    
                    if (!$has_error)
                    {                        
                        return array('delivery_id' => $delivery_id);
                    }
                    else
                    {
                        $thisObj->send_user_error(__('发货明细单保存有误！'), $fail_items);
                    }
                }
            }
            else
            {
                $thisObj->send_user_error(__('发货单已经存在了！'), array());
            }
        }
    }
    
    /**
     * 发货单修改
     * @param array sdf
     * @return boolean sucess of failure
     */
    public function update(&$sdf, $thisObj)
    {
        // 发货单修改是和中心的交互
        $odelivery = $this->app->model('delivery');
        $arr_data = $odelivery->dump(array('delivery_bn' => $sdf['delivery_bn'], 'order_id' => $sdf['order_bn']));
        
        if (isset($arr_data) && $arr_data)
        {
            $obj_dlytype = $this->app->model('dlytype');
            $arr_dlytype = $obj_dlytype->dump(array('dt_name' => $sdf['delivery']));
            $obj_dlycorp = $this->app->model('dlycorp');
            $arr_dlycorp = $obj_dlycorp->dump(array('name' => $sdf['logi_name']));
            $obj_regions = app::get('ectools')->model('regions');
            $arr_regions = $obj_regions->dump(array('local_name' => $sdf['ship_distinct']));
            $order = $this->app->model('orders');
            
            if (isset($arr_regions) && $arr_regions)
            {
                $arr_data['is_protect'] = ($sdf['is_protect']) ? 'true' : 'false';
                $arr_data['logi_id'] = $arr_dlycorp['corp_id'] ? $arr_dlycorp['corp_id'] : 0;
                $arr_data['logi_name'] = $sdf['logi_name'];
                $arr_data['ship_name'] = $sdf['ship_name'];
                $arr_data['ship_area'] = $arr_regions['package'] . ":" . $sdf['ship_states'] . "/" . $sdf['ship_city'] . "/" . $sdf['ship_distinct'] . ":" . $arr_regions['region_id'];
                $arr_data['ship_addr'] = $sdf['ship_addr'];
                $arr_data['ship_zip'] = $sdf['ship_zip'];
                $arr_data['ship_tel'] = $sdf['ship_tel'];
                $arr_data['ship_mobile'] = $sdf['ship_mobile'];
                $arr_data['ship_email'] = $sdf['ship_email'];
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
             
            $is_save = $odelivery ->save($arr_data);
           
            if (!$is_save)
            {
                $thisObj->send_user_error(__('发货单保存失败！'), array());
            }
            
            // 判断此订单是否完全发货
            $is_part_delivery = false;
            $o = $this->app->model('order_items');
            $arr_order_items = $o->getList('*', array('order_id' => $sdf['order_bn']));
            if ($arr_order_items)
            {
                foreach ($arr_order_items as $arr_item)
                {
                    if ($arr_item['sendnum'] != $arr_item['nums'])
                    {
                        $is_part_delivery = true;
                        
                        if ($arr_item['sendnum'] > 0)
                            $arr_delivery_items[] = array(
                                'number' => $arr_item['sendnum'],
                                'name' => $arr_item['name'],
                            );
                    }
                    else
                    {
                        $arr_delivery_items[] = array(
                            'number' => $arr_item['nums'],
                            'name' => $arr_item['name'],
                        );
                    }
                }
            }
            
            if ($sdf['status'] == 'succ')
            {
                if ($is_part_delivery)
                {
                    $ship_status = '2';
                }
                else
                {
                    $ship_status = '1';
                }
                
                $aUpdate = array();
                $aUpdate['order_id'] = $sdf['order_bn'];
                $aUpdate['ship_status'] = $ship_status;

                $order->save($aUpdate);
                
                $log_text = "";
                if ($ship_status == '1')
                {
                    $log_text = "订单<a style=\"color: rgb(0, 51, 102); font-weight: bolder; text-decoration: underline;\" title=\"点击查看详细\" onclick=\"show_delivery_item(this,&quot;120100818000001&quot;," . htmlentities(json_encode($arr_delivery_items), ENT_QUOTES) . ")
    \" href=\"javascript:void(0)\">全部商品</a>发货完成，" . (($arr_dlycorp) ? "物流公司：<a href=\"" . $arr_dlycorp['request_url'] . "\" title=\"" . $arr_dlycorp['name'] . "\" _target=\"blank\" class=\"lnk\">" . $arr_dlycorp['name'] . "</a>（可点击进入物流公司网站跟踪配送）" : "") . (($sdf['logi_no']) ? "物流单号：" . $sdf['logi_no'] : "");
                }
                
                if ($ship_status == '2')
                {
                    $log_text = "订单<a style=\"color: rgb(0, 51, 102); font-weight: bolder; text-decoration: underline;\" title=\"点击查看详细\" onclick=\"show_delivery_item(this,&quot;120100818000001&quot;," . htmlentities(json_encode($arr_delivery_items), ENT_QUOTES) . ")
    \" href=\"javascript:void(0)\">部分商品</a>已发货，" . (($arr_dlycorp) ? "物流公司：<a href=\"" . $arr_dlycorp['request_url'] . "\" title=\"" . $arr_dlycorp['name'] . "\" _target=\"blank\" class=\"lnk\">" . $arr_dlycorp['name'] . "</a>（可点击进入物流公司网站跟踪配送）" : "") . (($sdf['logi_no']) ? "物流单号：" . $sdf['logi_no'] : "");
                }

                // 更新发货日志结果
                $objorder_log = $this->app->model('order_log');        
                $sdf_order_log = array(
                    'rel_id' => $sdf['order_bn'],
                    'op_id' => '1',
                    'op_name' => 'admin',
                    'alttime' => time(),
                    'bill_type' => 'order',
                    'behavior' => 'delivery',
                    'result' => 'SUCCESS',
                    'log_text' => $log_text,
                );
                $log_id = $objorder_log->save($sdf_order_log);
                
                // 监控订单发货
                $sdf_order = $order->dump($sdf['order_bn'],'*');
                $aUpdate['order_id'] = $sdf['order_bn'];
                $aUpdate['ship_status'] = $ship_status;
                $aUpdate['total_amount'] = $sdf_order['total_amount'];
                $aUpdate['is_tax'] = $sdf_order['is_tax'];
                $aUpdate['member_id'] = $sdf_order['member_id'];
                $aUpdate['delivery'] = $arr_data;
                $aUpdate['ship_billno'] = $arr_data['logi_no'];
                $aUpdate['ship_corp'] = $arr_dlycorp['name'] ? $arr_dlycorp['name'] : '';
                // 配送方式名称
                $obj_dlytype = $this->app->model('dlytype');
                $arr_dlytype = $obj_dlytype->dump($arr_data['delivery'], 'dt_name');
                if ($arr_dlytype)
                    $aUpdate['delivery']['delivery'] = $arr_dlytype['dt_name'];
                else
                    $aUpdate['delivery']['delivery'] = "";
                if ($sdf_order['member_id'])
                {
                    $member = $this->app->model('members');
                    $arr_member = $member->dump($sdf_order['member_id'], '*', array(':account@pam'=>'*'));
                }
                $aUpdate['email'] = (!$sdf_order['member_id']) ? $sdf_order['consignee']['email'] : $arr_member['contact']['email'];
                
                $order->fireEvent('shipping', $aUpdate, $sdf_order['member_id']);
            
            }
            
            return true;
        }
        else
        {
            $thisObj->send_user_error(__('需要修改的发货单不存在！'), array());
        }
    }
}