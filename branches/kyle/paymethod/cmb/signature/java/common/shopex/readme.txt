 ����JAVA��������˵��

�������°汾�뿴��http://docs.google.com/View?id=dcv935t2_178g4m6mbc5


����ļ����أ�

svn co  https://shopexts.googlecode.com/svn/branches/kyle/paymethod/cmb/signature/java/


���������������


1. �������ɿ�ִ���ļ�

javac Check.java

���һ��˳��������һ��Check.class����������Ҫ�����class��һ��jar����ȥ����صİ�Ҳһ����ȥ�����㲿��

2. ��һ������ļ�Check.mf,��������

Create-By: kyle@shopcare.net
Main-Class: Check

����ļ���������Ҫע��ĵط�

   1. ":"����Ҫ���ո�
   2. ���һ��Ҫ�س�������˵����ļ������һ��Ӧ����һ������


3. �⿪���еİ�

unzip cmbjava.zip

4. ���������������

jar cfmv Check.jar Check.mf Check.class cmb

mf�ļ�һ��Ҫclass�ļ�ǰ�档

5. �����ɵ�jar��������/binĿ¼��

cp -v Check.jar /bin

6. ��֤

[root@ts cmb]# java -jar /bin/Check.jar  "Succeed=Y&BillNo=9102357423&Amount=5.10&Date=20091023&Msg=00100063142009102309302359700000002340&Signature=126|3|78|18|144|142|249|99|26|243|124|38|111|24|6|63|94|242|98|209|71|228|138|80|80|17|157|145|77|70|182|103|33|8|82|129|100|213|250|237|212|148|197|228|73|196|82|188|224|123|14|14|194|105|3|76|185|37|54|86|155|152|19|117|"
true
[root@ts cmb]#


��ȷ�Ľ��Ӧ����true


ע�����


prima��java

[root@prima1 java]# java
Usage: gij [OPTION] ... CLASS [ARGS] ...
          to invoke CLASS.main, or
       gij -jar [OPTION] ... JARFILE [ARGS] ...
          to execute a jar file
Try `gij --help' for more information.
[root@prima1 java]#

[root@prima1 java]# gij --version
java version "1.4.2"
gij (GNU libgcj) version 4.1.2 20080704 (Red Hat 4.1.2-44)

Copyright (C) 2006 Free Software Foundation, Inc.
This is free software; see the source for copying conditions.  There is NO
warranty; not even for MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
[root@prima1 java]#


����sun��java







 
