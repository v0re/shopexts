<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class desktop_ctl_cachemgr extends desktop_controller 
{
    
    public function index() 
    {
        $this->pagedata['enable'] = (get_class(cachemgr::instance()) == 'base_cache_nocache') ? 'false' : 'true';
        $this->page('cachemgr/index.html');
    }//End Function

    public function status() 
    {
        $this->pagedata['status'] = 'current';
        $this->index();
        if(cachemgr::status($msg)){
            $this->pagedata['status'] = $msg;
            $this->display('cachemgr/status.html');
        }else{
            echo '<p class="notice">'.(($msg) ? $msg : '无法查看状态').'</p>';
        }
    }//End Function

    public function optimize() 
    {
        $this->pagedata['optimize'] = 'current';
        $this->index();
        $this->begin();
        if(cachemgr::optimize($msg)){
            echo '<p class="notice">'.(($msg) ? $msg : '优化缓存成功').'</p>';
        }else{
            echo '<p class="notice">'.(($msg) ? $msg : '优化缓存失败').'</p>';
        }
    }//End Function
    
    public function clean() 
    {
        $this->pagedata['clean'] = 'current';
        $this->index();
        if(cachemgr::clean($msg)){
            echo '<p class="notice">'.(($msg) ? $msg : '清空缓存完成').'</p>';
        }else{
            echo '<p class="notice">'.(($msg) ? $msg : '清空缓存失败').'</p>';
        }
    }//End Function
}//End Class
