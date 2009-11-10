<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title> PHPZip 压缩工具 power by Shopex TST </title>
</head>
<body style="font-size:12px;">
<script language="javascript">
<!--
function s()
{
	if(document.body.scrollheight>document.body.clientheight-30)
	{
		scroll(0,document.body.scrollheight-document.body.clientheight+30);
	}
}
//-->
</script>


<?php

define("LENGTH",150);
define("TARGET","../syssite/home/shop/1/pictures/productsimg/big");


function printForm($str){
	echo "<b><font color=red>$str</font></b>";
	exit;
}

function rptout($str){
	echo $str."<br><script>s();</script>";
	flush();
}

function jump($url){
	$url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].$url;
	echo "<script> window.location=\"".$url."\" </script>";
}

function GetFileList($dir)
{
	if (file_exists($dir))
	{
		$args = func_get_args();
		$pref = $args[1];

		$dh = opendir($dir);
		while($files = readdir($dh))
		{
			if (($files!=".")&&($files!="..")) 
			{
				if (is_dir($dir.$files)) 
				{
					$curdir = getcwd();
					chdir($dir.$files);
					$file = array_merge($file,GetFileList("$pref$files/"));
					chdir($curdir);
				}
				else 
					$file[]=$pref.$files;
			}
		}
		closedir($dh);
	}
	return $file;
}


if($_REQUEST['vol'] != '' && $_REQUEST['act'] == 'do'){
	$vol = $_REQUEST['vol'];
	$filelist = file("tmp/filelist.log");
	$offset = $vol * LENGTH;
	//偏移大于数组长度，则结束
	if($offset < count($filelist)){
		$files = array_slice($filelist,$offset,LENGTH);
		unset($filelist);
		$z = new PHPZip();
		$z->Zip($files,TARGET,"pictures/big/big.".$vol.".zip");
		unset($z);
		rptout("volume \t\t".$vol." (".$_REQUEST['total'].") finished!");
		//下一卷，控制从0开始，
		$vol++;
		//停3秒，不然服务器会挂掉
		sleep(3);
		jump("?act=do&vol=".$vol."&total=".$_REQUEST['total']);
	}else{
		printForm("<hr><b>Total ".$_REQUEST['total']." volumes all done!</b>");
	}
}

if($_REQUEST['submit'] != '' && $_REQUEST['act'] == ''){
	if(!file_exists(TARGET)){
		printForm('Folder not exists!');
	}
	# Create File List
	$filelist = array();
	$filelist = GetFileList(TARGET);
	# Put all files from file list in ZIP archive
	foreach($filelist as $val){
		$content .= $val."\r\n";
	}
	$fp = fopen("tmp/filelist.log","w+");
	fwrite($fp,$content,strlen($content));
	fclose($fp);
	$totalvol = ceil(count($filelist) / LENGTH);
	rptout("应该有 ".$totalvol." 个备份，现在开始备份...<hr>");
	//备份从这里起跳
	jump('?act=do&vol=0&total='.$totalvol);	
}else{
?>
<form><input type=submit name=submit value='开始'></form>

<?php
}
?>
</body>
</html>


<?php
# 
# PHPZip v1.2 by Sext (sext@neud.net) 2002-11-18
# 	(Changed: 2003-03-01)
# 
# Makes zip archive
#
# Based on "Zip file creation class", uses zLib
#
# Examples in sample1.php, sample2.php and sample3.php
#

class PHPZip
{
	function Zip($filelist,$dir,$zipfilename)
	{
    	$curdir = getcwd();
		chdir($dir);
		if (@function_exists('gzcompress')){
			if (count($filelist) > 0){
				foreach($filelist as $filename){
					$filename = trim($filename);
					if (is_file($filename))
					{
						$fd = fopen ($filename, "r");
						$content = fread ($fd, filesize ($filename));
						fclose ($fd);
						if (is_array($dir)) $filename = basename($filename);
						$this -> addFile($content, $filename);
					}
				}
				$out = $this -> file();
				chdir($curdir);
				$fp = fopen($zipfilename, "w");
				fwrite($fp, $out, strlen($out));
				fclose($fp);
			}
			return 1;
		} 
		else 
			return 0;
	}

    var $datasec      = array();
    var $ctrl_dir     = array();
    var $eof_ctrl_dir = "\x50\x4b\x05\x06\x00\x00\x00\x00";
    var $old_offset   = 0;

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
    function file()
    {
        $data    = implode('', $this -> datasec);
        $ctrldir = implode('', $this -> ctrl_dir);

        return
            $data .
            $ctrldir .
            $this -> eof_ctrl_dir .
            pack('v', sizeof($this -> ctrl_dir)) .  // total # of entries "on this disk"
            pack('v', sizeof($this -> ctrl_dir)) .  // total # of entries overall
            pack('V', strlen($ctrldir)) .           // size of central dir
            pack('V', strlen($data)) .              // offset to start of central dir
            "\x00\x00";                             // .zip file comment length
    } // end of the 'file()' method


} // end of the 'PHPZip' class
?>