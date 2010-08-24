<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class ectools_finder_refunds{
    
    var $detail_info = '退款单明细';
    
    function __construct($app){
        $this->app=$app;
        }
        
    function detail_info($refund_id){
        
        $refund= $this->app->model('refunds');
        $sdf_refund = $refund->dump($refund_id);
        if($sdf_refund){
            $render = $this->app->render();
            
            $render->pagedata['refunds'] = $sdf_refund;
            if (isset($render->pagedata['refunds']['member_id']) && $render->pagedata['refunds']['member_id'])
            {
                $obj_pam = app::get('pam')->model('account');
                $arr_pam = $obj_pam->dump(array('account_id' => $render->pagedata['refunds']['member_id'], 'account_type' => 'member'), 'login_name');
                $render->pagedata['refunds']['member_id'] = $arr_pam['login_name'];
            }
            if (isset($render->pagedata['refunds']['op_id']) && $render->pagedata['refunds']['op_id'])
            {
                $obj_pam = app::get('pam')->model('account');
                $arr_pam = $obj_pam->dump(array('account_id' => $render->pagedata['refunds']['op_id']), 'login_name');
                $render->pagedata['refunds']['op_id'] = $arr_pam['login_name'];
            }
            
            return $render->fetch('refund/refund.html',$this->app->app_id);
            /*$ui= new base_component_ui($this);
            $html .= $ui->form_start();
            foreach($sdf_refund as $k=>$val){
                $v['value'] = $val;
                $v['name'] = $k;
                $v['type'] = 'label';
                $v['title'] = $refund->schema['columns'][$k]['label'];
                $html .= $ui->form_input($v);
            }
            
            $html .= $ui->form_end(0);
            return $html;*/
        }else{
            return '无内容';
        }
    }
    
}
