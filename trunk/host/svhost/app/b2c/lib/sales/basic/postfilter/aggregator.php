<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
/**
 * postfilter aggregator基类
 * $ 2010-05-09 19:39 $
 */
class b2c_sales_basic_postfilter_aggregator extends b2c_sales_basic_aggregator
{
    
    
    
    
    
    // 集合器的处理(默认)
    public function validate($cart_objects, &$condition) {

        $all = $condition['aggregator'] === 'all';
        $true = (bool)$condition['value'];
        if(!isset($condition['conditions'])) {
            return true;
        }
        if( !is_array( $condition['conditions'] ) ) return false;
        foreach ($condition['conditions'] as $_cond) {
            if( !is_object($this->$_cond['type']) )
                $this->$_cond['type'] = kernel::single($_cond['type']);
            $oCond = $this->$_cond['type'];

            $validated = $oCond->validate($cart_objects, $_cond); // return boolean
            if ($all && $validated !== $true) { // 所有不符合 如果有一个满足返回false
                return false;
            } elseif (!$all && $validated === $true) {// 任意一条符合 则返回true
                return true;
            }
        }

        return $all ? true : false;
    }
}
?>
