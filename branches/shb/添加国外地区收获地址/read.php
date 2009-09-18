<?php
/*  该程序是根据http://code.google.com/intl/zh-CN/apis/adwords/docs/developer/adwords_api_cities.html上各个国家城市信息代码
    归纳出各省或州所包含的城市，并以 "省/州:城市" 形式输出。
 *Author: shuhanbing
 *email :sanow@126.com
 *
 *
*/
$file="/DE.txt";                          //存放国家城市信息文件
 if($handle = fopen(dirname(__FILE__).$file,"r")){               

while ($data = fgets($handle, 500)){
                $data = trim($data);      // 获取每行数据
				$aTmp=explode(',',$data); 
				$a[$aTmp[1]][]=$aTmp[0];  //每个省份或州所具有的城市
				$c[$aTmp[1]]=0;          //所有省份或州


}

}
fclose($handle);

$cKey=array_keys ($c);//返回数组C的所有键，即所有省或州存入数组cKey

foreach($cKey as $d){
	echo $d.":";
	$count=count($a[$d]);
	$i=0;
	foreach($a[$d] as $b){
		if($i==$count-1){   //判断该城市是否是该省或州的最后一个城市
			$content="";
		}
		else{
			$content=",";
		}
echo $b.$content;
		$i++;
}
echo "<br>";
}

?>