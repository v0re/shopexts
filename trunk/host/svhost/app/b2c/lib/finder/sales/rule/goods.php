<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_finder_sales_rule_goods{
    var $column_control = '操作';
    var $detail_basic = '查看';
    
    
    public function __construct($app) {
        $this->app = $app;
    }
    
    
    function column_control($arr){
        $row = $arr;
        if( !isset($row['apply_time']) || !isset($row['create_time']) ) {
            $row = $this->app->model('sales_rule_goods')->dump($row['rule_id']);
        }
        $str  =  '<a href="index.php?app=b2c&ctl=admin_sales_goods&act=edit&p[0]='.$row['rule_id'].'" target="_blank">编辑</a>';
        if( ($row['apply_time'] < $row['create_time']) || empty($row['apply_time']) ) 
            $str .= '&nbsp;<a href="index.php?app=b2c&ctl=admin_sales_goods&act=apply&p[0]='.$row['rule_id'].'" target="dialog::{width:300,height:120,resizeable:false,title:\'应用规则\'}">应用</a>';
        if( !empty($row['apply_time']) ) 
            $str .= '&nbsp;<a href="index.php?app=b2c&ctl=admin_sales_goods&act=clear&p[0]='.$row['rule_id'].'" >取消应用</a>';
        $row = null;
        return $str;
    }
    
    
    function detail_basic($id){
        $arr = $this->app->model('sales_rule_goods')->dump($id); 
        $render = $this->app->render();
        
        
        //会员等级
        if($arr['member_lv_ids']) {
            $member_lv_id = explode(',', $arr['member_lv_ids']);
            $member = $this->app->model('member_lv')->getList('*', array('member_lv_id'=>$member_lv_id) );
            if(count($member_lv_id)>count($member)) {
                $member[] = array('name'=>'非会员');
            }
            $render->pagedata['member'] = $member;
        }
        
        //过滤条件
        if($arr['conditions']) {
            if($arr['c_template']) {
                $render->pagedata['conditions'] = kernel::single($arr['c_template'])->tpl_name;
            }
        }
        
        //优惠方案
        if($arr['action_solution']) {
            if($arr['s_template']) {
                $render->pagedata['action_solution'] = kernel::single($arr['s_template'])->name;
            }
        }
        $render->pagedata['rules'] = $arr;
        return $render->fetch('admin/sales/finder/goods.html');
    }
}
