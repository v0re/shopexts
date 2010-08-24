<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class desktop_ctl_backup extends desktop_controller{

    function index(){
        $this->path[] = array('text'=>__('数据备份'));
        if($time = app::get('shopex')->getConf("system.last_backup")){
            $this->pagedata['time'] = date('Y-m-d H:i:s',$time);
        }
        $this->pagedata['backup'] = 'current';
        kernel::single("desktop_ctl_data")->index();
        $this->page('system/backup/backup.html');
    }



    function backup_sdf(){
        ini_set("max_exection_time", 0);
        header("Content-type:text/html;charset=utf-8");

        $params['dirname'] = ($_GET["dirname"]=="") ? date("YmdHis", time()) : $_GET["dirname"];
        $params['appname'] = $_GET['appname'];
        #$params['cols'] = $_GET['cols'] ? $_GET['cols'] : 0;
        #$params['model'] = $_GET['model'] ? $_GET['model'] : '';
        #$params['startid'] = $_GET['startid'] ? $_GET['startid'] : 0;
        
        $oBackup = kernel::single('desktop_system_backup');
        
        if(!$oBackup->start_backup_sdf($params,$nexturl)){
            echo __('正在备份app：').($params['appname']).__('请勿进行其他页面操作。').'<script>runbackup("'.$nexturl.'")</script>';
        }
        else{
            app::get('shopex')->setConf("system.last_backup", time(), true);
            echo "<a href='index.php?app=desktop&ctl=backup&act=getFile&file=multibak_{$params['dirname']}". $oBackup->get('tar_ext') ."' target='_blank'>备份完毕，请点击本处下载</a>";
        }

    }
    
    
      
    function backup(){
        ini_set("max_exection_time", 0);
        header("Content-type:text/html;charset=utf-8");
        $params['sizelimit'] = 1024;
        $params['filename'] = ($_GET["filename"]=="") ? date("YmdHis", time()) : $_GET["filename"];
        $params['fileid'] = ($_GET["fileid"]=="")   ? "0"  : intval($_GET["fileid"]);
        $params['tableid'] = ($_GET["tableid"]=="") ? "0"  : intval($_GET["tableid"]);
        $params['startid'] = ($_GET["startid"]=="") ? "-1" : intval($_GET["startid"]);
        if ($params['sizelimit']!="")
        {
            $oBackup = kernel::single('desktop_system_backup');
            if(!$oBackup->startBackup($params,$nexturl)){
                echo __('正在备份第').($params['fileid']+2).__('卷，请勿进行其他页面操作。').'<script>runbackup("'.$nexturl.'")</script>';
            }
            else{
                app::get('shopex')->setConf("system.last_backup", time(), true);
                echo "<a href='index.php?app=desktop&ctl=backup&act=getFile&file=multibak_{$params['filename']}.php' target='_blank'>备份完毕，请点击本处下载</a>";
            }
        }
    }
    
    
    public function getFile() {
        $file = $_GET['file'];
        kernel::single('desktop_system_backup')->download($file);
    }
    

}
