<?php
/*
添加国外收获地址操作方法
1，拷贝worldwide文件夹到网店目录\plugins\location下。
2生成某个国家城市和地区信息：在
http://code.google.com/intl/zh-CN/apis/adwords/docs/developer/adwords_api_cities.html 

上把你要生成的国家信息复制到TXT文件里面（注意：复制表格里面的内容，中文不复制），并且把文件名取为该国家的国家代码，放在和read.php文件同目录下，并修改read.php文件里的$file内容为你刚命名的文件名。执行read.php文件。在浏览器上输出了该国家各个省或州对应的城市信息。去掉最后一行的:,把浏览器内容复制到worldwide下的area.txt文件里。并在最后一行加上以下引号里面的内容，以便管理
“其他::
其他
“
然后在后台商店配置->地区设置->国外地区->加载使用
.这样你需要的国家的城市或地区的信息就添加到了收获地址中去了
*/
?>