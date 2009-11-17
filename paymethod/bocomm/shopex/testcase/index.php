<?php

$ret = bocomm_sign('bocomm');

var_export($ret);

function bocomm_sign($message){
    if (strtoupper(substr(PHP_OS,0,3))=="WIN")
		{
			$bb = new COM("B2CClientCOMCtrl.B2CClientCOM");
			$configFile = dirname(__FILE__)."/ini/B2CMerchant.xml";
			$rc=$bb->Initialize($configFile);

			if ($rc)
			{
				$err = $bb->GetLastErr();
				echo $err;
				exit;
		  }
		  //ил╪рг╘цШ
      $merSignMsg = $bb->Sign_detachsign($message);
	  }
		else
		{
				//cmd = "java bocomm_sign \"ini/B2CMerchant.xml\" \"{$message}\"";
				$cmd = "java bocomm_sign ini/B2CMerchant.xml bocomm";
				$handle = popen($cmd, 'r');
				$merSignMsg = fread($handle, 2048);
				pclose($handle);
		}

		return $merSignMsg;
}

?>