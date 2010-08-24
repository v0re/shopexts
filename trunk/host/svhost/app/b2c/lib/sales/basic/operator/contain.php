<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
/**
 * 操作符 contian (包含,不包含) [string,array]
 * $ 2010-05-11 18:24 $
 */
class b2c_sales_basic_operator_contain implements b2c_interface_sales_operator
{
    public function getOperators() {
        return array(
                    '()'=>  array('name'=>'包含',     'value'=>'()', 'type'=>'contain', 'object'=>'b2c_sales_basic_operator_contain', 'alias'=>array('()')),
                    '!()'=> array('name'=>'不包含',   'value'=>'!()', 'type'=>'contain','object'=>'b2c_sales_basic_operator_contain', 'alias'=>array('!()')),
               );
    }

    /**
     * Enter description here...
     *
     * @param array $aCondition // array(
     *                                'attribute'=>'xxx',
     *                                'operator'=>'xxx',
     *                                'value'   => 'xxx'
     *                             )
     */
    public function getString($aCondition) {
        $sWhere = '';
        switch($aCondition['operator']){
            case '()':   // 包含
               if(is_array($aCondition['value'])) { // in
                   $sWhere = " IN ('".implode("','",$aCondition['value'])."')";
               }
               if(is_string($aCondition['value'])) { // like
                   $sWhere = " LIKE '%".$aCondition['value']."%'";
               }
               break;
            case '!()': // 不包含
               if(is_array($aCondition['value'])) { // in
                   $sWhere = " NOT IN ('".implode("','",$aCondition['value'])."')";
               }
               break;
        }
        if(empty($sWhere)) return false;

        if(is_array($aCondition['attribute'])) {
            return " ".$aCondition['attribute']['ref_id']." IN (SELECT `".$aCondition['attribute']['pkey']."` FROM ".$aCondition['attribute']['table']." WHERE ".$aCondition['attribute']['attribute'].$sWhere.") ";
        } else {
            return $aCondition['attribute'].$sWhere;
        }
    }


    /**
     * validate
     *
     * @param string $operator  // 操作符
     * @param mix $value        // 规则里设定的值
     * @param mix $validate     // 购物车项中取出的对应的'attribute'[path] 的值
     * @return boolean
     */
    public function validate($operator,$value,$validate) {
        
        switch($operator) {
            case '()':
                if(is_array($value)) return in_array($validate,$value);
                return (strpos($value,$validate) >= 0);break;
            case '!()':
                if(is_array($value)) return !in_array($validate,$value);
                return (strpos($value,$validate) === false);break;
        }
        return false;
    }
}
?>
