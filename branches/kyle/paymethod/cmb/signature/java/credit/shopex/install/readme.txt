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

jar cfmv Check.jar Check.mf Check.class cmb com javax

mf�ļ�һ��Ҫclass�ļ�ǰ�档


5. ��֤

php test_check.php


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







 
