 招行JAVA环境配置说明

在线最新版本请看：http://docs.google.com/View?id=dcv935t2_178g4m6mbc5


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

jar cfmv Check.jar Check.mf Check.class cmb

mf文件一定要class文件前面。

5. 将生成的jar包拷贝到/bin目录下

cp -v Check.jar /bin

6. 验证

[root@ts cmb]# java -jar /bin/Check.jar  "Succeed=Y&BillNo=9102357423&Amount=5.10&Date=20091023&Msg=00100063142009102309302359700000002340&Signature=126|3|78|18|144|142|249|99|26|243|124|38|111|24|6|63|94|242|98|209|71|228|138|80|80|17|157|145|77|70|182|103|33|8|82|129|100|213|250|237|212|148|197|228|73|196|82|188|224|123|14|14|194|105|3|76|185|37|54|86|155|152|19|117|"
true
[root@ts cmb]#


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







 
