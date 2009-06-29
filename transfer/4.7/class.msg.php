<?php

class msg
{
	
	function msg()
	{
		echo "<br>";
	}
	function scroll($str,$state)
	{
			if($state=="ok")
			{
				$str="<font color=\"green\">".$str."</font><br>";
			}
			if($state=="fail")
			{
				$str="<font color=\"red\" >".$str."</font><br>";
			}
			$str.=<<<MSG
			<script language="JavaScript">
				if(document.body.scrollHeight>document.body.clientHeight-30)
			 	{
					 scroll(0,document.body.scrollHeight-document.body.clientHeight+30);
				}
			</script>
MSG;
			echo $str;
			flush();
	}
	
	function rollback($msg)
	{
		echo "<script language=\"JavaScript\">alert(\"".$msg."\");history.go(-1);</script>";
	}
	
	function alert($msg)
	{
		echo "<script language=\"JavaScript\">alert(\"".$msg."\");</script>";
	}
	
	function err($imsg)
	{
		$msg="<span style='color:red;font-size:12px'>".$imsg."</span><br>";
		die($msg);
	}
	
	function confirm($msg)
	{
		
	}
	
}