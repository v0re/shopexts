<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
/**
 * 订单促销过滤
 * $ 2010-05-04 17:30 $
 */
class b2c_cart_postfilter_promotion implements b2c_interface_cart_postfilter {
    private $app;
    private $_rules = null;
    private $FREE_SHIPPING_ITEM = 1;
    private $FREE_SHIPPING_ORDER = 2;

    public function __construct(&$app){
        $this->app = $app;
        $this->o_cond = kernel::single('b2c_sales_order_aggregator_combine');
        $this->o_sales_order = $this->app->model('sales_rule_order');
    }

    public function filter(&$aData,&$aResult,$aConfig = array()) {
        $this->use_rules = $this->pass_rules = $this->all_rules = $this->_rules = null;
        // 取出符合当前购物车条件的规则(已经使用了conditions过滤)
        $this->_filter_rules($aResult,$aConfig);
         
        // 只是对goods进行处理待解决 其它类型购物车项待扩展

        if(!isset($aResult['object']['goods']) || !is_array($aResult['object']['goods'])) return false;
        
        foreach($aResult['object']['goods'] as &$object) {
            $this->_apply_to_item($object,$aResult);
        }
        
        
        
        if(isset($aResult['object']['coupon']) && !empty($aResult['object']['coupon'])) {
            foreach ($aResult['object']['coupon'] as &$val) {
                if(isset($this->all_rules[$val['rule_id']])) {
                    $val['name'] = $this->all_rules[$val['rule_id']]['cpns_name'];
                    $val["description"] = $aResult['promotion']['coupon'][$val['rule_id']]['desc'];//$this->all_rules[$val['rule_id']]['description'];
                    if(!$this->use_rules || !is_array($this->use_rules)) continue ;
                    if(isset($this->use_rules[$val['rule_id']])) {
                        if(!$this->use_rules[$val['rule_id']]['show'])  $val['used'] = true;
                        $this->use_rules[$val['rule_id']]['show'] = true;
                    }
                } else {
                    $val['name'] = $val['coupon'];
                    $val["description"] = '<font color="red">该优惠券可能已过期或未启用！~</font>';
                }
                
                
            }
        }
         
        $this->dehistory();
         
        
    }



    // 初始化订单促销规则(根据当前时间,登陆用户等级 从数据库中取出订单促销规则)
    private function _init_rules(){
        if (!$this->_rules){
            $mSRO = $this->o_sales_order;
            $arrMemberInfo = kernel::single("b2c_frontpage")->get_current_member();
            
            $aFilter = array(
                'member_lv' => ($arrMemberInfo['member_lv'] ? $arrMemberInfo['member_lv'] : -1), // todo 这里要改成登陆用户的会员等级
                'current_time' => time(),
                );

            /**     
            $this->_rules = $mSRO->getList('rule_id, description, conditions, action_conditions, action_solution, free_shipping, stop_rules_processing',
                                                       $aFilter,
                                                       $start=0, $limit=-1, 'sort_order desc, rule_id desc');
            //*/
                                                    
            $sSql = "SELECT sdb_b2c_sales_rule_order.rule_id,cpns_name, description, conditions, action_conditions, action_solution, free_shipping, stop_rules_processing, rule_type FROM `sdb_b2c_sales_rule_order` LEFT JOIN `sdb_b2c_coupons` ON `sdb_b2c_sales_rule_order`.rule_id = `sdb_b2c_coupons`.rule_id
                         WHERE ". $this->_filter_sql($aFilter) ."
                         ORDER BY  sort_order DESC,  sdb_b2c_sales_rule_order.rule_id DESC";

            $this->_rules = $mSRO->db->select($sSql);
            is_array($this->_rules) or $this->_rules=array();
            foreach($this->_rules as $_k => &$rule) {
                foreach($rule as $_k1 => &$value) {
                    if(in_array($_k1, array('rule_id', 'description', 'cpns_name', 'rule_type', 'free_shipping'))) continue;
                    if(in_array(strtolower($value), array('true', 'false'))) {
                        $value = (strtolower($value)=='true') ? true : false;
                        continue;
                    }
                    $value = is_array($value) ? $value : unserialize($value);
                }
            }
        }

        return true;
    }

    // 开发这个 主要是为了测试用例的
    public function getRule() {
        return $this->_rules;
    }

    private function _init_rules_order($aConfig) {
        // todo 订单修改时初始化 需要处理的规则
    }

    
    
    
    private function _filter_sql($aFilter) {
        $aWhere[] = "status = 'true'"; // 开启状态
        
        if (isset($aFilter['member_lv'])){
            $aWhere[] = sprintf(' (find_in_set(\'%s\', member_lv_ids))', $aFilter['member_lv']);
            unset($aFilter['member_lv']);
        }

        if (isset($aFilter['current_time'])){
            $aWhere[] = sprintf(' (%s >= from_time or from_time=0)',
                               $aFilter['current_time']);
            $aWhere[] = sprintf(' (%s <= to_time or to_time=0)', $aFilter['current_time']);
            unset($aFilter['current_time']);
        }
        return implode(' AND ',$aWhere);
    }
    
    
    
    /**
     * 过滤订单促销规则(cart_objects符合conditions 的促销规则) 去掉当前购物车内不符合的促销规则
     *
     * @param array $cart_objects
     */
    private function _filter_rules($cart_objects,$aConfig = array()){
        if(!empty($aConfig)) {
            $this->_init_rules_order($aConfig);
        } else {
            if (!$this->_rules) $this->_init_rules();
        }

        $validated = false;
        foreach($this->_rules as $_k => $rule){

            $oCond = $this->o_cond;

            $this->all_rules[$rule['rule_id']] = $rule;
            $validated = $oCond->validate($cart_objects,$rule['conditions']);
            if(!$validated){
                unset($this->_rules[$_k]);
            }
        }

    }

    private function _apply_action(&$object, &$cart_object, &$rule) {
        
        //优惠方案不存在直接返回
        if(!$rule['action_solution']) return false;
        foreach ($rule['action_solution'] as $key => &$val) {
            if($val['used']) continue;

            if(!is_string($key))continue;
            
            $o = kernel::single($key);
            if(method_exists($o, 'get_status')) {
                if(!$o->get_status()) return false;
            }
            
                
                
            //针对于符合条件的商品
            if($val['type']=='goods') {
                kernel::single($key)->apply($object, $val, $cart_object);
            } else {
                //订单
                kernel::single($key)->apply_order($object, $val, $cart_object);
                $val['used'] = true;
            }
            
        }
        return $key;
    }

    private function _apply_to_item(&$object, &$cart_object){
        
        $oCond = $this->o_cond;
        
        $arr_use_rule = array();
        foreach($this->_rules as &$rule) {
            // 如果action_conditions['conditions']不为空 验证要否对此商品进行优惠  为空 表示对全部购物车商品
            if(isset($rule['action_conditions']['conditions']) && !empty($rule['action_conditions']['conditions'])) {
                // 不符合则跳过
                if (!$oCond->validate($object, $rule['action_conditions'])) continue;
            }

            // 是否免运费
            switch($rule['free_shipping']){
                case $this->FREE_SHIPPING_ITEM:
                    $object['is_free_shipping'] = true;
                    break;
                case $this->FREE_SHIPPING_ORDER:
                    $cart_object['is_free_shipping'] = true;
                    break;
            }

            $this->all_rules[$rule['rule_id']] = $rule;
            
            if($this->stop_rules_processing && !$this->pass_rules) break;
            
            if(isset($this->pass_rules) && !$this->pass_rules[$rule['rule_id']] && $this->stop_rules_processing) break;
            //用户适用的订单规则
            //$this->use_rules[$rule['rule_id']] = $rule;
            $arr_use_rule[$rule['rule_type']][] = &$rule;
            if( $rule['stop_rules_processing'] ) break;
            //$tmp_use_rule = &$rule;
        }
        
        
        if( isset($arr_use_rule) && is_array($arr_use_rule) ) {
            foreach( $arr_use_rule as $arr ) {
                if( is_array($arr) ) {
                    foreach( $arr as $tmp_use_rule ) {
                        $this->use_rules[$tmp_use_rule['rule_id']] = $tmp_use_rule;
                        
                        // 执行优惠处理
                        $temp_solution_name = $this->_apply_action($object, $cart_object, $tmp_use_rule);
                         
                        //优惠执行成功时返回解决方案适用的lib
                        if($temp_solution_name) {

                            //规则针对商品时处理购物车内所有符合条件的商品
                            $solu_u_type = $tmp_use_rule['action_solution'][$temp_solution_name]['type'];
                            if($solu_u_type=='goods') {
                               $this->pass_rules[$tmp_use_rule['rule_id']] = true;
                            } else {  //针对订单
                                $this->stop_rules_processing = $tmp_use_rule['stop_rules_processing'];
                            }

                            //购物车：应用的优惠方案显示
                            
                            $oDefault = kernel::single($temp_solution_name);
                            $tmp_promotion_name = 'order';
                            
                            if( strtolower($tmp_use_rule['rule_type'])=='c' ) $tmp_promotion_name = 'coupon';

                            if(isset($cart_object['promotion'][$tmp_promotion_name][$tmp_use_rule['rule_id']])) {
                                if($oDefault->score_add) continue;
                                $cart_object['promotion'][$tmp_promotion_name][$tmp_use_rule['rule_id']]['discount_amount'] += $object['discount_amount_order'];
                                if( $solu_u_type=='goods' && empty($rule['description']) ) 
                                    $cart_object['promotion'][$tmp_promotion_name][$tmp_use_rule['rule_id']]['desc'] = $object['obj_items']['products'][0]['new_name'] .'、'. $cart_object['promotion'][$tmp_promotion_name][$tmp_use_rule['rule_id']]['desc'];
                            } else {
                                
                                $cart_object['promotion'][$tmp_promotion_name][$tmp_use_rule['rule_id']] = array(
                                    'rule_id'   =>  $tmp_use_rule['rule_id'],
                                    'discount_amount' =>  0,
                                    'desc'  => ( empty($rule['description']) ? ( ( $solu_u_type=='goods' ? $object['obj_items']['products'][0]['new_name'] : '' ) . $oDefault->getString() ) : $tmp_use_rule['description'] ),
                                );
                                if($oDefault->score_add) continue;
                                $cart_object['promotion'][$tmp_promotion_name][$tmp_use_rule['rule_id']]['discount_amount'] = $object['discount_amount_order'];
                            }
                        }
                    }
                }
            }
        }
    }
    
    
    
    
    private function dehistory() {
        $this->use_rules = $this->pass_rules = $this->all_rules = $this->_rules = $this->stop_rules_processing = null;
    }
    
}
?>
