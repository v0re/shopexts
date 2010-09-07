<?php
class svhost_finder_vhostlist{
    function __construct(&$app){
        $this->app=$app;
        $this->ui = new base_component_ui($this);
    }    
    var $detail_basic = ' 详细信息';
    function detail_basic($vhost_id){
        $vhostlist_model = $this->app->model('vhostlist');
        if($_POST){
            $_POST['vhost_id'] = $vhost_id;
            $vhostlist_model->save($_POST);
            $server = kernel::service('svhost_server', array('content_path'=>'svhost_server'));
            $sdf = $vhostlist_model->dump($vhost_id);
            $bash = $server->get_update_bash($sdf);
            $httpsqs = kernel::single('svhost_httpsqs'); 
            $queue_name = $sdf['ip'];
            $message = $bash;
            $result = $httpsqs->put("127.0.0.1", 1218, "utf-8", $queue_name, $message);
        }
        $this->app->render()->pagedata['form_start'] = $this->ui->form_start();
        $this->app->render()->pagedata['server'] =  $vhostlist_model->dump($vhost_id);
        $this->app->render()->pagedata['form_end'] = $this->ui->form_end();
        return $this->app->render()->fetch('admin/vhostlist/finder_detail.html');
    }    

}