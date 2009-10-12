部署说明

一、程序包清单: 

加密包   crypt.jar 
加密命令  cibsign.class(含源程序) 产生mac测试
验证命令  cibverify.class(含源程序)  校验mac测试
sun的jce1.2.2包

二、使用说明:

将jce/lib目录下的四个.jar文件拷贝到$java_home/jre/lib/ext目录
在CLASSPATH中包含crypt.jar

命令行使用说明

加密

java cibsign kyle 12345678

其中

kyle是要加密的消息
12345678 是密钥

验证

java cibverify 12345678 kyle A8EAB40E

其中

12345678 是密钥
kyle 是明文消息
A8EAB40E 是明文加密后的密文
