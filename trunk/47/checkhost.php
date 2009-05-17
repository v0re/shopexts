<?php
error_reporting(0);
ob_start();
phpinfo(1);
$content = ob_get_contents();
ob_end_clean();
preg_match("/Optimizer&nbsp;v([^,]*)/", $content, $regs);
$OptimizerVersion_show   =  $regs[0]; 
$OptimizerVersion   =  $regs[1]; 

function checkDNS(){
	$ip = @gethostbyname('www.example.com');
	if(strstr($ip,'.')) return true;
	else return false;
}

function getMysqlVersion()
{
    if($_SERVER['REQUEST_METHOD']=='GET'){
		$ip = '127.0.0.1';
		$port = '3306';
	}else{
		$ip = $_POST['ip'];
		$port = intval($_POST['port']);	
	}
	$fp = @fsockopen($ip, $port, &$errno, &$errstr, 2);
	if(!$fp) {
			return false;
	} else {
		$ret = '';
		fgets($fp,1024);
		for($i=1;$i<30;$i++)
		$ret .=fgetc($fp);
		flush();
		fclose($fp);
	}
	preg_match("/([\d+\.]+\d+)/", $ret, $regs);
	return $regs[1];
}
$mysqlVersion = getMysqlVersion();
$pass='<font color=green>通过</font>';
$fail='<font color=red>不通过</font>';
?>
<style type="text/css">
<!--
.STYLE2 {color: #000000}
.STYLE3{color: #000000;font-size:12px}
-->
</style>
<p>ShopEx合作伙伴主机检测程序</p>
<table width="100%" border="0" cellpadding="3" bgcolor="#FF9900">
  <tr>
    <td height="20" colspan="2" bgcolor="#CCCCCC">主机基本信息</td>
  </tr>
  <tr>
    <td width="33%" height="20" align="left" bgcolor="#FFFFFF">服务器操作系统</td>
    <td width="67%" height="20" align="left" bgcolor="#FFFFFF"><?php echo PHP_OS;?></td>
  </tr>
  <tr>
    <td height="20" bgcolor="#FFFFFF">Web服务器(要求Apache或IIS)</td>
    <td height="20" bgcolor="#FFFFFF"><?php echo $_SERVER["SERVER_SOFTWARE"];?></td>
  </tr>
</table>
<table width="100%" border="0" cellpadding="3" bgcolor="#FF9900">
  <tr>
    <td height="20" colspan="3" bgcolor="#CCCCCC">软件环境检测</td>
  </tr>
  <tr>
    <td width="70%" height="20" align="center" bgcolor="#FFFFFF">要求</td>
    <td width="17%" height="20" align="center" bgcolor="#FFFFFF">实测</td>
    <td width="13%" align="center" bgcolor="#FFFFFF">结果</td>
  </tr>
  
  <form method="POST">
  <tr>
    <td height="20" bgcolor="#FFFFFF">数据库Mysql 4.1.2以上<span class="STYLE3"> (如数据库非位于本机,请录入主机IP:
        <label>
        <input name="ip" type="text" id="ip" value="<?php echo ($_SERVER['REQUEST_METHOD']=='POST')?$_POST[ip]:"127.0.0.1";?>" size="15" maxlength="30" />
        </label>
      端口:
      
      <input name="port" type="text" id="port" value="<?php echo ($_SERVER['REQUEST_METHOD']=='POST')?intval($_POST[port]):"3306";?>" size="6" maxlength="6" />
      <label>
      <input type="submit" name="Submit" value="提交" />
      </label>
    )</span> </td>
    <td height="20" bgcolor="#FFFFFF"><?php echo $mysqlVersion;?>&nbsp;</td>
    <td height="20" bgcolor="#FFFFFF"><?php echo $mysqlVersion?(version_compare($mysqlVersion,'4.1.2')>=0?$pass:$fail):"<font color=red>连接失败</font>";?></td>
  </tr>
  </form>
  <tr>
    <td height="20" bgcolor="#FFFFFF">PHP4.4以上</td>
    <td height="20" bgcolor="#FFFFFF"><?php echo PHP_VERSION;?></td>
    <td height="20" bgcolor="#FFFFFF"><?php echo version_compare(PHP_VERSION,'4.4')>=0?$pass:$fail;?></td>
  </tr>
  <tr>
    <td height="20" bgcolor="#FFFFFF">ZEND Optimizer2.5.7以上 </td>
    <td height="20" bgcolor="#FFFFFF"><?php echo $OptimizerVersion_show;?></td>
    <td height="20" bgcolor="#FFFFFF"><?php echo version_compare($OptimizerVersion,'2.5.7')>=0?$pass:$fail;?></td>
  </tr>
  
  <tr>
    <td height="20" bgcolor="#FFFFFF">DNS配置完成,本机上能通过域名访问网络</td>
    <td height="20" bgcolor="#FFFFFF">&nbsp;</td>
    <td height="20" bgcolor="#FFFFFF"><?php echo checkDNS()?$pass:$fail;?></td>
  </tr>
</table>
<table width="100%" border="0" cellpadding="3" bgcolor="#FF9900">
  <tr>
    <td height="20" colspan="3" bgcolor="#CCCCCC">PHP配置检测</td>
  </tr>
  <tr>
    <td width="70%" height="20" align="center" bgcolor="#FFFFFF">要求</td>
    <td width="17%" height="20" align="center" bgcolor="#FFFFFF">实测</td>
    <td width="13%" align="center" bgcolor="#FFFFFF">结果</td>
  </tr>
  <tr>
    <td height="20" bgcolor="#FFFFFF">Mysql支持</td>
    <td height="20" bgcolor="#FFFFFF">&nbsp;</td>
    <td height="20" bgcolor="#FFFFFF"><?php echo function_exists('mysql_close')?$pass:$fail;?></td>
  </tr>
  <tr>
    <td height="20" bgcolor="#FFFFFF">GD2支持</td>
    <td height="20" bgcolor="#FFFFFF">&nbsp;</td>
    <td height="20" bgcolor="#FFFFFF"><?php echo function_exists('imagecreatetruecolor')?$pass:$fail;?></td>
  </tr>
  <tr>
    <td height="20" bgcolor="#FFFFFF">ZLIB支持</td>
    <td height="20" bgcolor="#FFFFFF">&nbsp;</td>
    <td height="20" bgcolor="#FFFFFF"><?php echo function_exists('gzclose')?$pass:$fail;?></td>
  </tr>
  
  <tr>
    <td height="20" bgcolor="#FFFFFF">MBString支持</td>
    <td height="20" bgcolor="#FFFFFF">&nbsp;</td>
    <td height="20" bgcolor="#FFFFFF"><?php echo function_exists('mb_convert_encoding')?$pass:$fail;?></td>
  </tr>
  <tr>
    <td height="20" bgcolor="#FFFFFF">Iconv支持</td>
    <td height="20" bgcolor="#FFFFFF">&nbsp;</td>
    <td height="20" bgcolor="#FFFFFF"><?php echo function_exists('iconv')?$pass:$fail;?></td>
  </tr>
  <tr>
    <td height="20" bgcolor="#FFFFFF">fsockopen支持</td>
    <td height="20" bgcolor="#FFFFFF">&nbsp;</td>
    <td height="20" bgcolor="#FFFFFF"><?php echo function_exists('fsockopen')?$pass:$fail;?></td>
  </tr>
  <tr>
    <td height="20" bgcolor="#FFFFFF">Register Globals关闭</td>
    <td height="20" bgcolor="#FFFFFF"><?php echo get_cfg_var("register_globals") ? 'ON' : 'OFF';?></td>
    <td height="20" bgcolor="#FFFFFF"><?php echo get_cfg_var("register_globals") ? $fail : $pass;?></td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF">安全模式(当本项开启时,FTP支持必须开启)</td>
    <td height="20" bgcolor="#FFFFFF"><?php echo get_cfg_var("safe_mode") ? "ON" : "OFF";?></td>
    <td height="20" bgcolor="#FFFFFF">---&nbsp;</td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF">FTP支持</td>
    <td height="20" bgcolor="#FFFFFF"><?php echo get_cfg_var("ftp_login") ? "ON" : "OFF";?></td>
    <td height="20" bgcolor="#FFFFFF"><?php echo !get_cfg_var("ftp_login")&&get_cfg_var("safe_mode") ? $fail : $pass;?></td>
  </tr>
  <tr>
    <td height="20" bgcolor="#FFFFFF">被禁用函数(本项为附加信息,不列入评估)</td>
    <td height="20" bgcolor="#FFFFFF">&nbsp;</td>
    <td height="20" bgcolor="#FFFFFF"><?php echo get_cfg_var("disable_functions") ? get_cfg_var("disable_functions") : "无";?>&nbsp;</td>
  </tr>
</table>
<p>&nbsp;</p>
