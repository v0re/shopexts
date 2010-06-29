<?php
class APP_ID_ctl_DEFAULT_CTL extends desktop_controller{
    function index(){
        $this->pagedata['my_location'] = realpath(dirname(__FILE__)."/../view/default.html");
        $this->display('default.html');
    }
}