<?php
/*  �ó����Ǹ���http://code.google.com/intl/zh-CN/apis/adwords/docs/developer/adwords_api_cities.html�ϸ������ҳ�����Ϣ����
    ���ɳ���ʡ�����������ĳ��У����� "ʡ/��:����" ��ʽ�����
 *Author: shuhanbing
 *email :sanow@126.com
 *
 *
*/
$file="/DE.txt";                          //��Ź��ҳ�����Ϣ�ļ�
 if($handle = fopen(dirname(__FILE__).$file,"r")){               

while ($data = fgets($handle, 500)){
                $data = trim($data);      // ��ȡÿ������
				$aTmp=explode(',',$data); 
				$a[$aTmp[1]][]=$aTmp[0];  //ÿ��ʡ�ݻ��������еĳ���
				$c[$aTmp[1]]=0;          //����ʡ�ݻ���


}

}
fclose($handle);

$cKey=array_keys ($c);//��������C�����м���������ʡ���ݴ�������cKey

foreach($cKey as $d){
	echo $d.":";
	$count=count($a[$d]);
	$i=0;
	foreach($a[$d] as $b){
		if($i==$count-1){   //�жϸó����Ƿ��Ǹ�ʡ���ݵ����һ������
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