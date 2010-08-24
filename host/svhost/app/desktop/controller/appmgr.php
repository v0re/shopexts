<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class desktop_ctl_appmgr extends desktop_controller{

    var $workground = 'desktop_ctl_dashboard';
    var $require_super_op = true;

    public function index(){
        $this->finder('base_mdl_apps',array(
            'base_filter'=>array('installed'=>true),
            'title'=>'应用程序','actions'=>array(
                array('label'=>'添加应用程序','icon'=>'add.gif','href'=>'index.php?ctl=appmgr&act=browser'),
                array('label'=>'检查更新','icon'=>'afresh.gif','href'=>"index.php?ctl=appmgr&act=fetchindex",'target'=>'command::'),
                array('label'=>'维护','icon'=>'edit01.gif','href'=>"index.php?ctl=appmgr&act=maintenance",'target'=>'command::'),
            ),'use_buildin_recycle'=>false));
    }
    
    public function browser(){
        $this->finder('base_mdl_apps',array(
            'base_filter'=>array('installed'=>false),
            'title'=>'应用程序','actions'=>array(
                //array('label'=>'安装选中的应用','icon'=>'add.gif','submit'=>'index.php?ctl=appmgr&act=install_app','target'=>'command::'),
                array('label'=>'已安装的应用程序','href'=>'index.php?ctl=appmgr&act=index'),
                array('label'=>'检查更新','icon'=>'afresh.gif','href'=>'index.php?ctl=appmgr&act=fetchindex','target'=>'command::'),
            ),'use_buildin_recycle'=>false));
    }
    
    function prepare(){
        if(method_exists($this,'prepare_'.$_POST['action'])){
            $prepare_result = $this->{'prepare_'.$_POST['action']}($_POST['app_id']);
            foreach($prepare_result['queue'] as $k=>$queue){
                $prepare_result['queue'][$k]['data'] = serialize($queue['data']);
            }
            echo json_encode($prepare_result);
        }
    }
    
    public function command(){
        if(method_exists($this,'command_'.$_GET['command_id'])){
            $this->{'command_'.$_GET['command_id']}(unserialize($_GET['data']));
            echo "\nok.";
        }
    }
    
    public function maintenance(){
        kernel::single('base_shell_webproxy')->exec_command('update');
    }
    
    public function fetchindex(){
        kernel::single('base_shell_webproxy')->exec_command('update --sync-only');
    }

    private function prepare_install($app_id){
        $depends_install = "以下应用将被安装, 是否继续?\n";
        $install_queue = kernel::single('base_application_manage')->install_queue(array($app_id));
        $queue = array();
        $download_queue = array();
        foreach($install_queue as $queue_app_id=>$appinfo){
            $depends_install .= "\t".($queue_app_id==$app_id?$appinfo['name']:str_pad($appinfo['name'],20)."\t(被依赖)")."\n";
            if(!file_exists(APP_DIR.'/'.$queue_app_id.'/app.xml')){
                $download_queue[] = array('type'=>'command','command_id'=>'download','data'=>$queue_app_id);                
            }
            $queue[] = array('type'=>'command','command_id'=>'install','data'=>$queue_app_id);
        }
        
        if($queue){
            array_unshift($queue,array('type'=>'dialog','action'=>'install_options','data'=>array_keys($install_queue)));
        }
        
        if($download_queue){
            $queue = array_merge($download_queue,$queue);
        }
        
        $return = array(
                'status' => 'confirm',
                'message' => $depends_install,
                'queue' => $queue
            );    
        return $return;
    }
    
    public function install_options(){
        $apps = unserialize($_GET['data']);
        if(!$apps){
            return;
        }
        $rows = app::get('base')->model('apps')->getList('app_id,app_name',array('app_id'=>$apps));
        foreach($rows as $r){
            $apps_name[$r['app_id']] = $r['app_name'];
        }
        foreach($apps as $app_id){
            $option = app::get($app_id)->runtask('install_options');
            if(is_array($option) && count($option)>0){
                $install_options[$app_id] = $option;
            }
        }
        $this->pagedata['install_options'] = &$install_options;
        $this->pagedata['apps_name'] = &$apps_name;
        $this->display('appmgr/install.html');
    }
    
    private function command_install($app_id){
        $shell = kernel::single('base_shell_webproxy');
        $shell->input = $_POST['options'];
        $shell->exec_command('install '.$app_id);
    }
    
    private function prepare_uninstall($app_id){
        $depends_uninstall = "以下应用将被删除, 是否继续?\n";
        $uninstall_queue = kernel::single('base_application_manage')->uninstall_queue(array($app_id));
        $queue = array();
        foreach($uninstall_queue as $queue_app_id=>$appinfo){
            $depends_uninstall .= "\t".$appinfo[0].' '.($appinfo[1]?"\t(依赖)":'')."\n";
            $queue[] = array('type'=>'command','command_id'=>'uninstall','data'=>$queue_app_id);
        }
        
        $return = array(
                'status' => 'confirm',
                'message' => $depends_uninstall,
                'queue' => $queue
            );    
        return $return;
    }
    
    private function command_uninstall($app_id){
        kernel::single('base_shell_webproxy')->exec_command('uninstall '.$app_id);
    }
    
    private function command_download($app_id){
        kernel::single('base_shell_webproxy')->exec_command('update --force-download --download-only '.$app_id);
    }
    
    private function command_update($app_id){
        kernel::single('base_shell_webproxy')->exec_command('update '.$app_id);
    }
    
    /* start/stop
    private function prepare_start($app_id){
        $return = array(
                'queue' => array(
                    array('type'=>'command','command_id'=>'start'),
                    )
            );
        return $return;
    }
    
    private function prepare_stop($app_id){
        $return = array(
                'queue' => array(
                    array('type'=>'command','command_id'=>'stop'),
                    )
            );
        return $return;
    }
    */
    
    private function prepare_download($app_id){
        $return = array(
                'queue' => array(
                    array('type'=>'command','command_id'=>'download','data'=>$app_id),
                    )
            );
        return $return;
    }
    
    private function prepare_update($app_id){
        $return = array(
                'queue' => array(
                    array('type'=>'command','command_id'=>'update','data'=>$app_id),
                    )
            );
        return $return;
    }


}
