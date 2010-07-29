<?php
class %*APP_NAME*%_ctl_default extends base_controller{
    
    function index(){
        $this->pagedata['project_name'] = '%*APP_NAME*%';
        $this->display('default.html');
    }
    
}