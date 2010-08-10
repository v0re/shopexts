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
        $this->display('default.html');
    }
    
    function view($t2tfile){
    	$t2tfile = realpath("$this->t2t_dir/$t2tfile");
    	if(!file_exists($t2tfile)){
    		die("file {$t2tfile} not found");
    	}
    	$python = "D:\python26\python.exe";
    	$txt2tags =realpath( APP_DIR."/ttnote/lib/txt2tags");
    	$tmp_out = realpath("$this->t2t_dir/tmp");
    	$t2t_cmd = "$python $txt2tags --target=html --infile=$t2tfile --outfile=$tmp_out";
    	//$t2t_cmd = "$python $txt2tags --help";
    	$r = system($t2t_cmd,$ret);
    	var_dump($r,$t2t_cmd);
    }
}