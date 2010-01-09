<?php

define(INT,1);
define(CHAR,2);
define(TEXT,3);
define(AUTO_INCREMENT,9);


class sestore {
	
	function sestore(){
		$this->db = dirname(__FILE__)."/data/log";
		
		$this->sed = fopen("$this->db/log.sed",'ab+');
		$this->frm = fopen("$this->db/log.frm",'ab+');
		$this->sei = fopen("$this->db/log.sei",'ab+');
	
	
	}

	function hastable($table){
		$frm = "$this->db/{$table}.frm";
		return file_exists($frm) && filesize($frm);
	}


	function newtable($table){
		list($name,$fields) = each($table);
		$structure = $name."::";
		foreach($fields as $field){
			$structure .= $this->addfield($field);
		}
		
		$this->put($this->frm,$structure);
	}

	function addfield($field){
		$ret = '';
		switch($field[1]){
			case INT:
				$ret = "$field[0]:$field[1]";
			case CHAR:
				$ret = "$field[0]:$field[1]";
			case TEXT:
				$ret = "$field[0]:$field[1]";
		}

		return $ret."|";
	}

    
	function store($value){
		$key = $value['id'];
		$index = $this->wdata(serialize($value));
		list($offset,$size) = $index;
		$this->windx($key,$offset,$size);
    }

	function wdata($content){
		fseek($this->sed,0,SEEK_END);
		$offset = ftell($this->sed);
		$size = $this->put($this->sed,$content);
		return array($offset,$size);
	}

	function windx($key,$offset,$size){
		$content = "$key:$offset:$size\n";
		$this->put($this->sei,$content);
	}

	function put($fp,$content){
		flock($fp,LOCK_EX);
		$size = fwrite($fp,$content);
		flock($fp,LOCK_UN);
		rewind($fp);
		return $size;
	}

	function fetch($key){
		if($index = $this->rindx($key)){
			list($offset,$size) = $index;
			$content = $this->rdata($offset,$size);

			return unserialize($content);
		}

		return false;
	}

	function rindx($key){
		
		while(!feof($this->sei)){
			flock($this->sei,LOCK_SH);
			$line = fgets($this->sei,1024);
			flock($this->sei,LOCK_UN);
			$index = explode(":",$line);
			
			if($key == array_shift($index)){
				return $index;
			}
		}
		

		return false;
		
	}

	function rdata($offset,$size){
		fseek($this->sed,$offset);
		$content = $this->get($this->sed,$size);
		return $content;
	}

	function get($fp,$size){
		flock($fp,LOCK_SH);
		$ret = fread($fp,$size);
		flock($fp,LOCK_UN);
		rewind($fp);
		return $ret;
	}

}