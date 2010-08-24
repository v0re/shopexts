<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
/**
 * 商品促销规则 数据库处理
 * $ 2010-05-16 12:33 $
 */
class b2c_mdl_sales_rule_goods extends dbeav_model
{
    
    
    
    
    public function pre_recycle($data=array()) {
        $oGPR = $this->app->model('goods_promotion_ref');
        $param = array('status'=>'false');
        $filter  = array_splice($data, 0, 1);
        return $oGPR->update($param, $filter);
    }
    
    
    
    public function suf_restore($sdf=0) {
        $id = $sdf['rule_id'];
        if(!$id) return false;
        $oGPR = $this->app->model('goods_promotion_ref');
        $param = array('status'=>'true');
        $filter  = array('rule_id'=>$id);
        return $oGPR->update($param, $filter);
    }
    
    
    public function suf_delete($id=0) {
        if(!$id) return false;
        $oGPR = $this->app->model('goods_promotion_ref');
        $filter  = array('rule_id'=>$id);
        return $oGPR->delete($filter);
    }
    
    
}// mdl_goods_rule class end
