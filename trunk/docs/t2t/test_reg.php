<?php

//$str = '
//fdsfsdfjsdlfsjdlsdjf;sldsd
//<A NAME="toc4"></A>
//<H3>2.1.1. MySQL</H3>
//<P>
//��װ����
//</P>
//<H4>2.1.1.1. ����MySQL</H4>
//<P>
//�뵽<A HREF="http://dev.mysql.com/downloads/">http://dev.mysql.com/downloads/</A>����MySQL��Դ�����
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