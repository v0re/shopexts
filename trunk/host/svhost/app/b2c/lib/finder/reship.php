<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_finder_reship{

    var $detail_reship = '参数设置';
    function __construct($app){
        $this->app = $app;
    }

    function detail_reship($payment_id){
        $payment = $this->app->model('reship');
        if($_POST['reship_id']){
            $sdf = $_POST;
            unset($sdf['_method']);
            unset($sdf['reship']);
            if($payment->save($sdf));
            {
                echo 'ok';
            }
        }else{
            $subsdf = array('reship_items' => '*');
            $sdf_payment = $payment->dump($payment_id, '*', $subsdf);
            if($sdf_payment){
                $render = $this->app->render();
                
                $render->pagedata['reships'] = $sdf_payment;
                if (isset($render->pagedata['reships']['member_id']) && $render->pagedata['reships']['member_id'])
                {
                    $obj_pam = app::get('pam')->model('account');
                    $arr_pam = $obj_pam->dump(array('account_id' => $render->pagedata['reships']['member_id'], 'account_type' => 'member'), 'login_name');
                    $render->pagedata['reships']['member_id'] = $arr_pam['login_name'];
                }
                if (isset($render->pagedata['reships']['op_id']) && $render->pagedata['reships']['op_id'])
                {
                    $obj_pam = app::get('pam')->model('account');
                    $arr_pam = $obj_pam->dump(array('account_id' => $render->pagedata['reships']['op_id']), 'login_name');
                    $render->pagedata['reships']['op_id'] = $arr_pam['login_name'];
                }
                
                return $render->fetch('admin/order/reship.html',$this->app->app_id);
                /*$this->ui = new base_component_ui($this);
                $html .= $this->ui->form_start();
                foreach($sdf_payment as $k=>$val){
                    $v['value'] = $val;
                    $v['name'] = $k;
                     $v['type'] = 'label';
                   $v['title'] = $payment->schema['columns'][$k]['label'];
                    $html .= $this->ui->form_input($v);
                }
        
                $html .= $this->ui->form_end(0);
                return $html;*/
            }else{
                return '无内容';
            }
        }
    }
}
