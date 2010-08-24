<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_ctl_admin_reship extends desktop_controller{

    var $workground = 'b2c_ctl_admin_order';

    function index(){
        $this->finder('b2c_mdl_reship',array(
            'title'=>'退货单',
            'allow_detail_popup'=>true,
            'params'=>array(
                'bill_type' => 'reship',
            )
            ));
    }
    

    function addnew(){
        echo __FILE__.':'.__LINE__;
    }

}
