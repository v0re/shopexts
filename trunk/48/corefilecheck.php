<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<?php

$data = Array(
				'bG9naW4uaHRtbA=='=>'f27607ccc788bb73c56ea0903ffa77b5',
        'ZGFzaGJvYXJkLmh0bWw='=>'8c3db2dc094bf5f292143a625b16b3aa',
        'aW5kZXguaHRtbA=='=>'d550d1c4578d5ad1dead7aaac0300594',
        'c3lzdGVtL3Rvb2xzL2Fib3V0Lmh0bWw='=>'0ff7ff4fd1d69b39c9581c74d15ac9a3',
       );

foreach($data as $k=>$v){
	$filename = 'core/admin/view/'.base64_decode($k);
	if( $v != md5(file_get_contents($filename))){
			echo "<font color=red>".$filename."校验失败，请重新上传该文件！</font><br/>";
	}
}

echo "<hr><b>全部检测完毕</b>";


?>
</body>
</html>