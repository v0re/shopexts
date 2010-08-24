<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class desktop_ctl_rpcnotify extends desktop_controller{
    
    var $workground = 'desktop_ctl_system';
    var $limit = 20;

    function index(){
        $this->finder('base_mdl_rpcnotify',array(
            'title'=>'通知',
            'use_buildin_recycle' => false,
            #'use_buildin_setcol' => false,
            #'use_buildin_tagedit' => false,
            ));
    }
    
    function get() {
        $filter = array();
        $arr = app::get('base')->model('rpcnotify')->getList( '*',$filter, 0, $this->limit );
        echo json_encode($arr);
    }
    
}
