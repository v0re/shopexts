<?php

define(INT,1);
define(CHAR,2);
define(TEXT,3);
define(INDEX,8);
define(PRIMARY,9);

class sestore {

	var $orgin;
	var $db;
	var $fp;
	var $version;
	
	function sestore(){
		$this->_file = dirname(__FILE__)."/data/log/sestore.data.php";
		$this->version = '0.0.1';
		#���������
		$this->fhome = 64;
        #��ṹ�洢�����,Ϊ�������ȼ���+��
		$this->shome = 16<<10 + $this->fhome;
		#�������
		$this->ihome = 128<<10 + $this->shome;	
        #�������
        $this->dhome = 1<<20 + $this->ihome;
        #������
        $this->fat = array();
	}

    function workat($file=''){
        $this->_file = $file ? $file : $this->_file;
        if(!file_exists($this->_file)){
            $this->create();
        }else{
            $this->_rs = fopen($this->_file,'rb+') or $this->trigger_error('Can\'t open the cachefile: '.realpath($this->_file),E_USER_ERROR);
           
        }
        
        $this->_getwp();
    }

     /**
     * lock 
     * ���flock�����ã���̳б��࣬�����ش˷���
     * 
     * @param mixed $is_block �Ƿ�����
     * @access public
     * @return void
     */
    function lock($is_block,$whatever=false){
        return flock($this->_rs, $is_block?LOCK_EX:LOCK_EX+LOCK_NB);
    }

    /**
     * unlock 
     * ���flock�����ã���̳б��࣬�����ش˷���
     * 
     * @access public
     * @return void
     */   
    function unlock(){
        return flock($this->_rs, LOCK_UN);
    }

	function create(){ 
        $this->_rs = fopen($this->_file,'wb+') or $this->trigger_error('Can\'t open the cachefile: '.realpath($this->_file),E_USER_ERROR);
		$stopcode = "<?php exit(); ?>";
		$header = $stopcode.$this->version;
        $this->_puts(0,$header);
        #��¼������ͷ������λ��
        $this->_puts($this->fhome,pack('VV',$this->ihome,$this->dhome)); 
        return true;
	}

	function hastable($tblname){
		$tblnames = array_keys($this->meta['table']);
		return in_array($tblname,$tblnames);
	}

	function newtable($table){
		#��ȡ�����ͱ�ṹ
		list($name,$desc) = each($table);
		if($this->hastable($name)) return false;
		$this->meta['table'][$name]['desc'] = $desc;		
		$this->setmeta();
	}

   	function store($tblname,$value){	
		$key = $this->getkey($tblname,$value);
        $value = serialize($value);
        $size = strlen($value);
        $indexdesc = pack('VVV',$this->_dwp,$size,$key);
        $indexdesc .= "\n";
        if($this->lock(true)){
            #д������
            $this->_puts($this->_dwp,$value);
            #д������
            $this->_puts($this->_iwp,$indexdesc);

            #��������д��ָ��
            $this->_dwp += $size;
            $this->_iwp += 12;
    
            $this->_setwp();
            
        }else{
            $this->trigger_error("Couldn't lock the file !",E_USER_WARNING);
            return false;
        }
    }

	function getkey($tblname,$value){
        return $value['id'];
		$desc = $this->meta['table'][$tblname]['desc'];
		foreach($desc as $st){
			$name = $st[0];
			$ext = $st[2];
			#Ŀǰֻ�������������������ֶ���������Ҫ��B+Tree�����ݽṹ
			if($ext == PRIMARY){
				return $value[$name];
			}
		}
	}

	function fetch($key){
        $offset = $this->_getindexentry();
        $index = $this->_getindex($offset);

		
		return false;
	}

    function _getindexentry(){
        if($fatsize = count($this->fat)){
            return unpack('V',$this->_gets($this->fhome + $fatsize * 8,4));            
        }else{
            return $this->ihome;
        }
    }

    function _getindex($offset){
        $this->_seek($offset);
        $indexblock = fgets($this->_rs);
        $indexblock = substr($indexblock,0,-1);
        $indexs = str_split($indexblock,12);
        print_r($indexs);
        foreach($indexs as $indexdesc){
            $index = unpack('V1pos/V1size/H*key',$indexdesc);            
        }      
    }



    function _getwp(){
         $this->_seek($this->fhome);
         $fatdesc = fgets($this->_rs);
         $this->fat = unpack('V*',$fatdesc);
         #����д��ָ��
         $this->_dwp = array_pop($this->fat);
         #����д��ָ�� 
         $this->_iwp = array_pop($this->fat);
    }

    function _setwp(){      
        $fatsize = count($this->fat);
        $wpdesc = pack('VV',$this->_iwp,$this->_dwp);       
        $wpdesc .= "\n";
        $this->_puts($this->fhome + $fatsize * 8,$wpdesc);       
    }

    function _puts($offset,$data){
        $this->_seek($offset);
        fputs($this->_rs,$data);
    }

    function _gets($offset,$size){
        $this->_seek($offset);
        return fread($this->_rs,$size);
    }

    function _seek($offset){
        return fseek($this->_rs,$offset);
    }

    function trigger_error($errstr,$errno){
        error_log($errstr,3,__FILE__.".fatal.log");
		exit();
    }

}