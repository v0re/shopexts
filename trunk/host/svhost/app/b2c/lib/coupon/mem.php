<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_coupon_mem {
    
    
    public function __construct() {
        $this->app = app::get('b2c');
    }
    
    
    public function exchange($id=0, $m_id=0, $point=0) {
        
        $id = (int)$id;
        if( empty($id) || empty($m_id) ) return false;
        
        $o = $this->app->model('coupons');
        $arr = $o->dump($id);
        if( empty($arr) ) return false;
        if( $point<$arr['cpns_point'] ) return false;
        $arr_m_c['memc_code'] = $o->_makeCouponCode($arr['cpns_gen_quantity']+1, $arr['cpns_prefix'], $arr['cpns_key']);
        if(!$o->isDownloadAble($arr_m_c['memc_code'])) return false; //是否可下载 B类
        $arr['cpns_gen_quantity'] += 1;
        $o->save($arr);
        $arr_m_c['cpns_id'] = $arr['cpns_id'];
        $arr_m_c['member_id'] = $m_id;
        $arr_m_c['memc_used_times'] = 0;
        $arr_m_c['memc_gen_time'] = time();
        return $this->app->model('member_coupon')->save($arr_m_c);
    }
    
    
    
    public function get_list_m($m_id=0) {
        if( empty($m_id) ) return false;
        $filter = array('member_id'=>$m_id);
        $arr = $this->app->model('member_coupon')->_get_list('*', $filter);
        return $arr;
    }
    
    
    
    public function get_list() {
        $sql = "SELECT * FROM `sdb_b2c_coupons` WHERE cpns_point is not null AND cpns_status='1'";
        $arr = $this->app->model('coupons')->db->select($sql);
        foreach( $arr as &$row ) {
            if(empty($row['rule_id'])) continue;
            $arr_rule_info = $this->app->model('sales_rule_order')->dump($row['rule_id'], 'from_time,to_time,member_lv_ids');
            $row['time'] = $arr_rule_info;
        }
        return $arr;
    }
    
    
    
    public function use_c($memc_code ='', $uid=0) {
        $uid = (int)$uid;
        if( empty($uid) ) return false;
        if( empty($memc_code) ) return false;
        $o = $this->app->model('member_coupon');
        $couponFlag = $this->app->model('coupons')->getFlagFromCouponCode($memc_code);
        
        if( strtolower($couponFlag)!='b' ) return false;
        $coupons = $this->app->model('coupons')->getCouponByCouponCode($memc_code);
        $coupons = $coupons[0];
        $arr['memc_code'] = $memc_code;
        $arr['memc_used_times'] = 1;
        $arr['cpns_id'] = $coupons['cpns_id'];
        $m_coupon = $o->dump($memc_code);
        if( empty($m_coupon) ) $arr['member_id'] = $uid;
        return $o->save($arr);
    }
    
    
    
    
    
    
}

