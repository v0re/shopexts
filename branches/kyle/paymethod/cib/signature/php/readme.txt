一、文件目录结构

│  config.m4			
│  cib.c			主程序
│  php_cib.h			头文件
│  cib.php			编译结果测试脚本,php -f cib.php
│  test_cib.php		函数测试程序，从浏览器浏览
│
├─include
│      cib_api.h		CIB函数导出表
│
└─lib
        libcib.a		CIB函数库


二、编译步骤

1. 创建php扩展工作环境

cd到php源码目录ext目录下

cp -v ~/cib/php/ext/cib.def .
./ext_skel --extname=cib --proto=cib.def

2.拷贝做好的文件

cp -v ~/cib/php/ext/cib/* cib/

3. 生成扩展的configure脚本

cd cib
phpize

4. 编译安装 

./configure --with-cib=$PWD
make
make install

5. 配置php.ini

打开php.ini在文件最末尾添加一句

extension=cib.so

重启web服务器后就可以使用工行的签名和签名验证函数了
                

三、封装函数用法

string cibSign(unsigned char *key,unsigned char *message)

key		--	密钥
message		--  	需要加密的明文

返回符合CIB标准的8位ASCII码





