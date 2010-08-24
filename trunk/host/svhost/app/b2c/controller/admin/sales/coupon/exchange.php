<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_ctl_admin_sales_coupon_exchange extends desktop_controller{

   
    function index() {
        $this->finder('b2c_mdl_coupons', array(
                'title'=>'积分兑换优惠券',
                'use_buildin_recycle' => false,
                'use_buildin_setcol' => false,
                'use_buildin_refresh' => false,
                'use_buildin_tagedit' => false,
                'actions'=>array(
                                array('label'=>'添加兑换规则','href'=>'index.php?app=b2c&ctl=admin_sales_coupon_exchange&act=add'),
                                array('label'=>'删除', 'submit'=>'index.php?app=b2c&ctl=admin_sales_coupon_exchange&act=delete'),
                            ),'finder_cols'=>'cpns_name,cpns_point','finder_aliasname'=>'1',
                'object_method'=>array('getlist'=>'getlist_exchange','count'=>'getlist_exchange_count'),
                ));
    }
    

    public function add() {
        $filter = array('cpns_status' => '1', 'cpns_type' => '1',);
        $this->pagedata['cpns'] = $this->app->model('coupons')->getList('*', $filter);
        
        if(count($this->pagedata['cpns'])<1) $this->pagedata['nodata'] = true;
        $this->page('admin/sales/coupon/exchange/add.html');
        
    }
    
    
    public function save() {
        $this->begin('index.php?app=b2c&ctl=admin_sales_coupon_exchange&act=index');
        $arr['cpns_point'] = $_POST['cpns_point'];
        $arr['cpns_id'] = $_POST['cpns_id'];
        $flag = false;
        if($arr['cpns_id']) {
            $o = $this->app->model('coupons');
            if($o->dump($arr['cpns_id'])) {
                $flag = $o->save($arr);
            }
        } else {
            $this->end(false,  '请选择优惠券！');
        }
        $this->end($flag, ($flag ? '添加成功！' : '添加失败！'));
    }
    
    public function delete() {
        foreach($_POST['cpns_id'] as $cpns_id) {
            $arr['cpns_point'] = null;
            $arr['cpns_id'] = $cpns_id;
            $this->app->model('coupons')->save($arr);
        }
    }
    
    

}
