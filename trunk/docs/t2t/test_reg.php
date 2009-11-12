<?php

//$str = '
//fdsfsdfjsdlfsjdlsdjf;sldsd
//<A NAME="toc4"></A>
//<H3>2.1.1. MySQL</H3>
//<P>
//安装流程
//</P>
//<H4>2.1.1.1. 编译MySQL</H4>
//<P>
//请到<A HREF="http://dev.mysql.com/downloads/">http://dev.mysql.com/downloads/</A>下载MySQL的源码包。
//</P>
//<A NAME="toc5"></A>
//fsfsdfsdfsdfsf
//fsdjflsdjfsdlfsd
//';
//
//

$str = file_get_contents('index.html');

preg_match('/[\s\S]+<BODY>/',$str,$match);
//
var_export($match);

		

?>