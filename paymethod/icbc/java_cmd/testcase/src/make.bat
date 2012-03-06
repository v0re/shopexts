@echo off
set class_path=D:\code\googlecode\shopexts\branches\kyle\paymethod\icbc\java_cmd\testcase\lib\icbc.jar;D:\code\googlecode\shopexts\branches\kyle\paymethod\icbc\java_cmd\testcase\lib\InfosecCrypto_Java1_02_JDK14+.jar  

javac -classpath %class_path% icbc_sign.java
copy /Y icbc_sign.class ..\lib
del icbc_sign.class

javac -classpath %class_path% icbc_verify.java
copy /Y  icbc_verify.class  ..\lib
del icbc_verify.class

echo all done