<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class desktop_ctl_network extends desktop_controller{

    var $workground = 'desktop_ctl_system';
    var $require_super_op = true;

    function index(){
        $params = array(
                'title'=>'网络连接',
                'actions'=>array(
                        array('href'=>'index.php?ctl=network&act=new_link',
                                'label'=>'新建连接',
                                'target'=>'dialog::{title:\'新建网络连接\',width:500,height:300}'),
                            )
            );
        $this->finder('base_mdl_network',$params);
    }

    function group($group_id){
        $netgroup = &$this->app->model('netgroup');
        $group = $netgroup->instance($group_id);
        $this->sidePanel();
        echo '<iframe width="100%" height="100%" src="'.$group['group_url'].'" ></iframe>';
    }

    function new_link(){
        $this->display('network/new_link.html');
    }

    function ping_node(){
        $netgroup = &$this->app->model('network');
        if($netgroup->invite($_POST['site_url'],$_POST['message'])){
            echo '已发送申请，等待对方确认';
        }
    }

}
