<?php
# 
# PHPZip v2.0 by alacner (alacner@gmail.com) 2005-12-31
# QQ:964142  MSN:alacn@msn.com
# Makes zip archive
#
# Based on "Zip file creation class", uses zLib
#
# More Info "readme.txt"
#

class PHPZip
{

	var $rootDir = '';

    //数据数组
	var $datasec      = array();
    //目录信息
	var $ctrl_dir     = array();
	//数据长度标识
	var $ctrl_length  = 0;
    var $eof_ctrl_dir = "\x50\x4b\x05\x06\x00\x00\x00\x00";
    //每次压缩后长度偏移
	var $old_offset   = 0;
	//是否要分卷标识
	var $ismulti	  = false;
	//卷标，0起跳
	var $vol		  = 0;
	//要保存的zip文件名
	var $zipfilename  = 'data';

	function Zip($dir, $zipfilename)
	{
    	$this->zipfilename = $zipfilename;
		if (@function_exists('gzcompress'))
		{	
			@set_time_limit("0");
			$this->dirTree($dir,$dir);	
			//如果不用分卷的话，直接用$zipfilename做文件名保存
			if(!$this->ismulti){
				$this -> filezip($this->zipfilename.".zip");
			}
				
		}
	}//end func.
	
	
	//recursion get dir tree..
	function dirTree($directory,$rootDir)
	{
		global $_SERVER,$dirInfo,$rootDir;
		echo "<ul>\n";
		$fileDir = $rootDir;
		$myDir = dir($directory);
		while($file = $myDir->read()){
			if($file == "." || $file == "..") continue;
			if(is_dir("$directory/$file")){
				$rootDir .= "$file/";
				$this->addFile('', "$rootDir");
				echo "<li><font color=red><b>$file</b></font></li>\n";
				$this->dirTree("$directory/$file",$rootDir);
			}else{
				$fd = fopen ("$directory/$file", "r");
				$fileValue = fread ($fd, filesize ("$directory/$file"));
				fclose ($fd);
				$this->addFile($fileValue, "$fileDir$file");
				if($this->ctrl_length > 1000000){
					$this->ismulti = true;
					$out = $this->filezip($this->zipfilename.".".$this->vol++.".zip");
				}
				echo "<li>$file</li>\n";
			}
		}
		echo "</ul>\n";
		$myDir->close();
	}


    /**
     * Converts an Unix timestamp to a four byte DOS date and time format (date
     * in high two bytes, time in low two bytes allowing magnitude comparison).
     *
     * @param  integer  the current Unix timestamp
     *
     * @return integer  the current date in a four byte DOS format
     *
     * @access private
     */
    function unix2DosTime($unixtime = 0) {
        $timearray = ($unixtime == 0) ? getdate() : getdate($unixtime);

        if ($timearray['year'] < 1980) {
        	$timearray['year']    = 1980;
        	$timearray['mon']     = 1;
        	$timearray['mday']    = 1;
        	$timearray['hours']   = 0;
        	$timearray['minutes'] = 0;
        	$timearray['seconds'] = 0;
        } // end if

        return (($timearray['year'] - 1980) << 25) | ($timearray['mon'] << 21) | ($timearray['mday'] << 16) |
                ($timearray['hours'] << 11) | ($timearray['minutes'] << 5) | ($timearray['seconds'] >> 1);
    } // end of the 'unix2DosTime()' method


    /**
     * Adds "file" to archive
     *
     * @param  string   file contents
     * @param  string   name of the file in the archive (may contains the path)
     * @param  integer  the current timestamp
     *
     * @access public
     */
    function addFile($data, $name, $time = 0)
    {
        $name     = str_replace('\\', '/', $name);

        $dtime    = dechex($this->unix2DosTime($time));
        $hexdtime = '\x' . $dtime[6] . $dtime[7]
                  . '\x' . $dtime[4] . $dtime[5]
                  . '\x' . $dtime[2] . $dtime[3]
                  . '\x' . $dtime[0] . $dtime[1];
        eval('$hexdtime = "' . $hexdtime . '";');

        $fr   = "\x50\x4b\x03\x04";
        $fr   .= "\x14\x00";            // ver needed to extract
        $fr   .= "\x00\x00";            // gen purpose bit flag
        $fr   .= "\x08\x00";            // compression method
        $fr   .= $hexdtime;             // last mod time and date

        // "local file header" segment
        $unc_len = strlen($data);
        $crc     = crc32($data);
        $zdata   = gzcompress($data);
        $c_len   = strlen($zdata);
        $zdata   = substr(substr($zdata, 0, strlen($zdata) - 4), 2); // fix crc bug
        $fr      .= pack('V', $crc);             // crc32
        $fr      .= pack('V', $c_len);           // compressed filesize
        $fr      .= pack('V', $unc_len);         // uncompressed filesize
        $fr      .= pack('v', strlen($name));    // length of filename
        $fr      .= pack('v', 0);                // extra field length
        $fr      .= $name;

        // "file data" segment
        $fr .= $zdata;

        // "data descriptor" segment (optional but necessary if archive is not
        // served as file)
        $fr .= pack('V', $crc);                 // crc32
        $fr .= pack('V', $c_len);               // compressed filesize
        $fr .= pack('V', $unc_len);             // uncompressed filesize

        // add this entry to array
        $this -> datasec[] = $fr;
        $new_offset        = strlen(implode('', $this->datasec));
		$this -> ctrl_length += $new_offset;
        // now add to central directory record
        $cdrec = "\x50\x4b\x01\x02";
        $cdrec .= "\x00\x00";                // version made by
        $cdrec .= "\x14\x00";                // version needed to extract
        $cdrec .= "\x00\x00";                // gen purpose bit flag
        $cdrec .= "\x08\x00";                // compression method
        $cdrec .= $hexdtime;                 // last mod time & date
        $cdrec .= pack('V', $crc);           // crc32
        $cdrec .= pack('V', $c_len);         // compressed filesize
        $cdrec .= pack('V', $unc_len);       // uncompressed filesize
        $cdrec .= pack('v', strlen($name) ); // length of filename
        $cdrec .= pack('v', 0 );             // extra field length
        $cdrec .= pack('v', 0 );             // file comment length
        $cdrec .= pack('v', 0 );             // disk number start
        $cdrec .= pack('v', 0 );             // internal file attributes
        $cdrec .= pack('V', 32 );            // external file attributes - 'archive' bit set

        $cdrec .= pack('V', $this -> old_offset ); // relative offset of local header
        $this -> old_offset = $new_offset;

        $cdrec .= $name;

        // optional extra field, file comment goes here
        // save to central directory
        $this -> ctrl_dir[] = $cdrec;
    } // end of the 'addFile()' method


    /**
     * Dumps out file
     *
     * @return  string  the zipped file
     *
     * @access public
     */
    function filezip($zipfilename)
    {
        $data    = implode('', $this -> datasec);
        $ctrldir = implode('', $this -> ctrl_dir);

		$out =  $data .
				$ctrldir .
				$this -> eof_ctrl_dir .
				pack('v', sizeof($this -> ctrl_dir)) .  // total # of entries "on this disk"
				pack('v', sizeof($this -> ctrl_dir)) .  // total # of entries overall
				pack('V', strlen($ctrldir)) .           // size of central dir
				pack('V', strlen($data)) .              // offset to start of central dir
				"\x00\x00";                             // .zip file comment length
		//write ziped data to file
		$fp = fopen($zipfilename, "w");
		fwrite($fp, $out, strlen($out));
		fclose($fp);
		//Reset global variable for multiply zip
		$this->datasec		= array();
		$this->ctrl_dir		= array();
		$this->ctrl_length	= 0;
		$this->old_offset   = 0;
        
		return true;
            
    } // end of the 'filezip()' method
} // end of the 'PHPZip' class

if($_REQUEST['submit'] && file_exists($_REQUEST['dirname'])){
	$z = new PHPZip();
	$z->Zip($_REQUEST['dirname'],$_REQUEST['dirname']);
	echo "<br><b>Finished!!</b>";
}else{
?>

<form><input type=text name=dirname ><input type=submit name=submit value=submit></form>

<?php
}
?>