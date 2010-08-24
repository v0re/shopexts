<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class desktop_ctl_comeback extends desktop_controller{


    function index(){
        $this->path[] = array('text'=>__('数据恢复'));
        $oDSB = kernel::single("desktop_system_backup");
        $this->pagedata['archive']=$oDSB->getList();
        
        $this->pagedata['comeback'] = 'current';
        kernel::single("desktop_ctl_data")->index();
        
        $this->page('system/comeback/tgzFileList.html');
    }
    
    function comeback(){
        $filename = $_GET['file'];
        $mtime = $_GET['time'];
        $this->pagedata['filename']=$filename;
        $this->pagedata['mtime'] = $mtime;
        $this->display('system/comeback/comeback.html');
    }
    
    
    
    function recover(){
        $filename = $_GET['file'];
        $fileid = intval($_GET['fileid']) ? intval($_GET['fileid']) : 1;
        $vols = $_GET['vols'] ? $_GET['vols'] : 1;
        $oDSB = kernel::single("desktop_system_backup");
        
        $oDSB->recover($filename, $vols, $fileid);//exit;
        if($vols==$fileid){
            echo __('数据库已恢复完毕 <script>$("btnarea").innerHTML = \'<button class="btn"  onclick="sqlcomeback.close()" type="button"><span><span>确定</span></span></button>\';</script>');
        }
        else{
            echo __('正在恢复第').($fileid+1).__('卷 共').$vols.__('卷<script>dorecover("index.php?app=desktop&ctl=comeback&act=recover&file=').$filename.'&vols='.$vols.'&fileid='.($fileid+1).'");</script>';
        }
        if(is_file(MEDIA_DIR.'/goods_cat.data'))
            @unlink(MEDIA_DIR.'/goods_cat.data');
        if(is_file(MEDIA_DIR.'/goods_virtual_cat.data'))
            @unlink(MEDIA_DIR.'/goods_virtual_cat.data');
    }
    
    function removeTgz() {
        $this->begin('index.php?app=desktop&ctl=comeback&act=index');
        $arr_tgz = $_POST['tgz'];

        if(empty($arr_tgz)) $this->end(false, __('数据错误！'));
        kernel::single("desktop_system_backup")->removeTgz($arr_tgz);
        $this->end(true, '移除成功！');
    }
    
    
}
