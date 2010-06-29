<?php
class dev_ctl_shell extends base_controller{
    
    function index(){
        $this->display('shell.html');
    }

    function exec(){
        $shell = new base_command_webproxy;
        $shell->exec_command($_GET['cmd']);
    }

}