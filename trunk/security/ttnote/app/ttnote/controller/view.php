<?php
class ttnote_ctl_view extends base_controller{

	function __construct(&$app){
		parent::__construct(&$app);
		$this->t2t_dir = DATA_DIR."/ttnote";
	}
    
    function index(){    		
    	$python = "D:\python26\python.exe";
    	$txt2tags = APP_DIR."/ttnote/lib/txt2tags";
    	$t2t_cmd = "$pythone $txt2tags -i $item";
        $this->display('detail.html');
    }
    
}