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
        }
        $this->app->render()->pagedata['form_start'] = $this->ui->form_start();
        $this->app->render()->pagedata['server'] =  $vhostlist_model->dump($vhost_id);
        $this->app->render()->pagedata['form_end'] = $this->ui->form_end();
        return $this->app->render()->fetch('admin/vhostlist/finder_detail.html');
    }    

}