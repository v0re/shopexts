<?php
class ttnote_ctl_default extends base_controller{
    
    function index(){
        $this->pagedata['project_name'] = 'ttnote';
        $this->display('default.html');
    }
    
}