<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
/**
 * 订单促销规则处理 service
 * $ 2010-05-11 13:28 $
 */
class b2c_sales_solution_process
{
    
    
    /**
     * 获取模板列表信息
     *
     */
    public function getTemplateList($flag=true) {
        $aResult = array();
        foreach(kernel::servicelist('b2c_promotion_solution_tpl_apps') as $object) {
            if(method_exists($object, 'get_status')) {
                if(!$object->get_status()) {
                    continue;
                }
            }
            $aResult[get_class($object)] = $object->name;
        }
        $tmp = array('goods'=>'符合条件的商品');
        if($flag) $tmp['order'] = '订单';

        foreach( $tmp as $type => $val) {
            foreach($aResult as $class_name => $name) {
                $aTemp[$type][$class_name] = $val . $name;
            }
        }
        return $aTemp;
    }
    
    
     public function getTemplate($tpl_name,$aData = array(), $type='') {
        $oTC = kernel::single($tpl_name);
        $t = $oTC->config($aData[$tpl_name]); 
        return ($type=='goods' ? '商品' : '订单' ) . $oTC->config($aData[$tpl_name]);
    }
    
    public function getType($aData = array(), $key) {
        if(!$aData)return false;
        return $aData[$key]['type'];
    }
    
    
    
}
?>
