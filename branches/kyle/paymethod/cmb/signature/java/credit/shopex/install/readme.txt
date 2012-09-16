相关文件下载：

svn co  https://shopexts.googlecode.com/svn/branches/kyle/paymethod/cmb/signature/java/


具体操作步骤如下


1. 编译生成可执行文件

javac Check.java

如果一切顺利会生成一个Check.class，我们下面要将这个class打到一个jar包里去，相关的包也一起打进去，方便部署。

2. 建一个规格文件Check.mf,内容如下

Create-By: kyle@shopcare.net
Main-Class: Check

这个文件有两个需要注意的地方

   1. ":"后面要留空格
   2. 最后一行要回车，就是说这个文件的最后一行应该是一个空行


3. 解开招行的包

unzip cmbjava.zip

4. 让用下面的命令打包

jar cfmv Check.jar Check.mf Check.class cmb com javax

mf文件一定要class文件前面。


5. 验证

php test_check.php


正确的结果应该是true


注意事项：


prima的java

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


不是sun的java







 
