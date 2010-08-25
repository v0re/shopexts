<?php
class svhost_finder_serverlist{
    function __construct(&$app){
        $this->app=$app;
        $this->ui = new base_component_ui($this);
    }    
    var $detail_basic = '服务器信息';
    function detail_basic($server_id){
        $serverlist_model = $this->app->model('serverlist');
        if($_POST){
            $_POST['server_id'] = $server_id;
            $serverlist_model->save($_POST);
        }
        $data = $serverlist_model->dump($server_id); 
        $html .= $this->ui->form_start();
        foreach($data as $k=>$val){
            $col = $serverlist_model->schema['columns'][$k];
            if($col['extra'] == 'auto_increment'){
                continue;
            }
            if($col['type'] == 'bool'){
                $input['type'] = 'bool';
            }
            $input['value'] = $val;
            $input['name'] = $k;
            $input['title'] =$col ['label'];
            $html .= $this->ui->form_input($input);
            unset($input);
        }
        $html .= $this->ui->form_end();
        return $html;
    }
}