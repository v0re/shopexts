<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_order_dlytype
{
    /**
     * 选择配送方式的接口
     * @params object 控制器入口
     * @params string 最后一级地区的id
     * @params array 标准购物车数据
     */
    public function select_delivery_method(&$controller, $area_id='', $sdf_cart)
    {
        //$dly_types = &$controller->app->model('dlytype');
        //$all_dly_types = $dly_types->get_dlytype($area_id);
        $all_dly_types = $this->get_dlytype($controller, $area_id);
        
        foreach ($all_dly_types as $rows)
        {
            $rows['money'] = utils::cal_fee($rows['dt_expressions'], $sdf_cart['subtotal_weight'], $sdf_cart['subtotal'], $rows['firstprice']);
            $shipping[] = $rows;
        }
        
        $controller->pagedata['shippings'] = &$shipping;

        return $controller->fetch("site/cart/checkout_shipping.html");
    }
    
    /**
     * 得到相应地区的配送方式
     * @params object 控制器对象
     * @params string area id
     */
    private function get_dlytype(&$controller, $area_id)
    {
        $objdlytype = $controller->app->model('dlytype');
        $dlytype = $objdlytype->getList('*','',0,-1);
        
        if ($dlytype && is_array($dlytype))
        {
            $setting_0 = $setting_1 = array();
            foreach ($dlytype as $key=>$value)
            {
                if ($value['setting']==1)
                {
                    //统一费用
                    $setting_1[$key] = $value;
                }
                else
                {
                    if ($value['def_area_fee'] == 'true')
                    {
                        $setting_0[$key] = $value;
                    }
                    
                    $area_fee_conf = unserialize($value['area_fee_conf']);
                    if ($area_fee_conf && is_array($area_fee_conf))
                    {
                        foreach ($area_fee_conf as $k=>$v)
                        {
                            $areas = explode(',',$v['areaGroupId']);
                            
                            // 再次解析字符
                            foreach ($areas as &$strArea)
                            {
                                if (strpos($strArea, '|') !== false)
                                {
                                    $strArea = substr($strArea, 0, strpos($strArea, '|'));
                                }
                            }
                            
                            // 取当前area id对应的最上级的区域id
                            $objRegions = app::get('ectools')->model('regions');
                            $arrRegion = $objRegions->dump($area_id);
                            while ($row = $objRegions->getRegionByParentId($arrRegion['p_region_id']))
                            {
                                $arrRegion = $row;
                                $area_id = $row['region_id'];
                            }
                            
                            if(in_array($area_id,$areas)){//如果地区在其中，优先使用地区设置的配送费用，及公式
                                $value['firstprice'] = $v['firstprice'];
                                $value['continueprice'] = $v['continueprice'];
                                //if($v['dt_useexp']==1){
                                $value['dt_expressions'] = $v['dt_expressions'];
                                //}
                                $setting_0[$key] = $value;
                                break;
                            }
                        }
                    }
                }
            }
            
            $return = array_merge($setting_1,$setting_0);
            
            return $return;
        }
        
        return array();
    }
}
