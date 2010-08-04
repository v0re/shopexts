<?php
class ttnote_ctl_default extends base_controller{

	function __construct(&$app){
		parent::__construct(&$app);
		$this->t2t_dir = DATA_DIR."/ttnote";
	}
    
    function index(){    		
    	$obj_dir = dir($this->t2t_dir);
    	$i = 0;
    	while( ($file = $obj_dir->read()) !== false){
    		$file_path = realpath($this->t2t_dir."/".$file);
    		if ( substr($file,-3) != "t2t" ) continue;
    		$items[$i]['filename'] = $file;
    		$fp = fopen($file_path,'rb');
    		$items[$i]['title']= fgets($fp);
    		fclose($fp);
    		$i++;    		
    	}
        $this->pagedata['project_name'] = 'ShopEx Security Vulnerability Bulletin Board';
        $this->pagedata['items'] = $items;
        var_dump($items);
        $this->display('default.html');
    }
    
    function show($t2tfile){
    	$t2tfile = realpath("$this->t2t_dir/$t2tfile");
    	$python = "D:\python26\python.exe";
    	$txt2tags =realpath( APP_DIR."/ttnote/lib/txt2tags");
    	$t2t_cmd = "$python $txt2tags  $t2tfile -o -";
    	//exec($t2t_cmd,$ret);
    	var_dump($t2t_cmd);
    }
}