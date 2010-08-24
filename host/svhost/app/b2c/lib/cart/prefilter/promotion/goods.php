<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
/**
 * 商品促销预过滤
 * $ 2010-04-29 11:52 $
 */
class b2c_cart_prefilter_promotion_goods implements b2c_interface_cart_prefilter {
    private $app;

    public function __construct(&$app){
        $this->app = $app;
    }

    public function filter(&$aResult,$aConfig) {


        // 没有商品数据
        if(empty($aResult['object']['goods'])) return false;


        if(!isset($aConfig['promotion']['goods'])) {//购物车的时候
            $aGoodsId = array();

            foreach($aResult['object']['goods'] as $row) {
                if(empty($row['obj_items']['products']['0']['goods_id'])) continue;
                $aGoodsId[] = $row['obj_items']['products']['0']['goods_id'];
            }
            $aConfig = $this->_init_rule(array_unique($aGoodsId),array('current_time'=>time()));
        } else {
            $aConfig = $aConfig['promotion']['goods'];
        }

        $this->_filter($aResult,$aConfig);
    }

    /**
     * 初始化商品过滤规
     *
     * @param array $aGoodsId // array(xxx,xxx,xxx);
     */
    private function _init_rule($aGoodsId,$filter = array()) {
        if(empty($aGoodsId)) return false;
        $filter['goods_id'] = $aGoodsId;
        $arrMemberInfo = kernel::single("b2c_frontpage")->get_current_member();
        
        $filter['member_lv'] = $arrMemberInfo['member_lv'] ? $arrMemberInfo['member_lv'] : -1;   
        $filter['member_lv'] = -1;
        $sSql = "SELECT * FROM sdb_b2c_goods_promotion_ref
                 WHERE ".$this->_filter_sql($filter)."
                 ORDER BY sort_order DESC";

        $aResult = $this->app->model('cart')->db->select($sSql);
        if(empty($aResult)) return false;
        //是否允许同一商品有多个预过滤规则
        return utils::array_change_key($aResult,'goods_id', 1);
    }

    /**
     * sql过滤的where条件
     */
    private function _filter_sql($aFilter) {
        $aWhere[] = "status = 'true'"; // 开启状态
        

        if(isset($aFilter['goods_id'])) {
            $aWhere[] = " goods_id IN (".implode(',',$aFilter['goods_id']).")";
        }
        
        if (isset($aFilter['member_lv'])){
            $aWhere[] = ' (find_in_set(\''. $aFilter['member_lv'] .'\', member_lv_ids))';
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

    private function _filter(&$aResult,$aConfig) {
        
        if(empty($aConfig)) return false; // 不需要过滤

        foreach($aResult['object']['goods'] as &$row) {
            $iGoodsId = $row['obj_items']['products']['0']['goods_id'];
            $tmp = $aConfig[$iGoodsId];
            if($tmp) $this->_filter_product($aResult, $row,$tmp);


        }

        $aConfig = null;
        //old 只显示符合当前商品的促销规则
        //$aResult['promotion']['goods'] = $aConfig;
    }

    // 单商品
    //单商品存在多维数组嘛？
    private function _filter_product(&$aData, &$aResult, &$aConfig) {
        if(isset($aConfig['goods_id'])) $aConfig[] = $aConfig;
        

        foreach($aConfig as $key=>$rule) {

            $action_solution = is_array($rule['action_solution']) ? $rule['action_solution'] : unserialize($rule['action_solution']);
            $temp_solution_name = $this->_action($aResult, $action_solution);
            
            //优惠执行成功时返回解决方案适用的lib
            if($temp_solution_name)  {
                $aConfig[$key]['used'] = true; // 这个优惠执行过

                //只显示符合当前商品的促销规则 
                
                $oDefault = kernel::single($temp_solution_name);
                if(isset($aData['promotion']['goods'][$rule['rule_id']])) {
                    if($oDefault->score_add) continue;
                    $aData['promotion']['goods'][$rule['rule_id']]['discount_amount'] += $aResult['subtotal'] - $aResult['quantity']*$aResult['obj_items']['products'][0]['price']['buy_price'];
                    if( empty($rule['description']) )
                        $aData['promotion']['goods'][$rule['rule_id']]['desc'] = $aResult['obj_items']['products'][0]['new_name'] .'、'. $aData['promotion']['goods'][$rule['rule_id']]['desc'];
                } else {
                    $aData['promotion']['goods'][$rule['rule_id']] = array(
                        'rule_id'   =>  $rule['rule_id'],
                        'discount_amount' => 0,
                        'desc'  => (empty($rule['description']) ? ($aResult['obj_items']['products'][0]['new_name'] . $oDefault->getString()) : $rule['description'] ),
                    );
                    if($oDefault->score_add) continue;
                    $aData['promotion']['goods'][$rule['rule_id']]['discount_amount'] = $aResult['subtotal'] - $aResult['quantity']*$aResult['obj_items']['products'][0]['price']['buy_price'];
                }
                
                /**
                $aData['promotion']['goods'][$aResult['obj_ident']] = array(
                                                        //'desc_pre' => $oDefault->desc_pre,
                                                        //'desc_post'=> $oDefault->desc_post,
                                                        //'amount'   => $action_solution[$temp_solution_name]['total_amount'],
                                                        //'desc'       => $oDefault->getString($action_solution[$temp_solution_name]),
                                                        'desc'       => $rule['description'],
                                                        'goods_name' => $aResult['obj_items']['products'][0]['new_name'],
                                                        'rule_id' => $rule['rule_id'],
                                                        'discount_amount' => &$aResult['discount_amount_prefilter'],
                                                    );
                //*/
                
                // 不再执行下去 互斥
                if($rule['stop_rules_processing'] == 'true') break;

            }
        }

    }

    // 执行优惠
    private function _action(&$aResult,$aConfig){


        //exit;
        if(!$aConfig) return false;
        foreach($aConfig as $key=>$row) {
            try{
                // 执行指定优惠方案
                $o = kernel::single($key);
                if(method_exists($o, 'get_status')) {
                    if(!$o->get_status()) return false;
                }
                $o->apply($aResult,$row);
            }catch (Exception $e){//没有相关的优惠方法
                return false; // 出现错误返回false
            }

            return $key;
        }
    }
}
?>
