一、文件目录结构

├─include
│      icbc.h				拷贝到 /usr/local/include
│      
├─lib
│      libicbc.so			拷贝到/usr/local/lib
│      
└─php
    └─ext
        │  icbc.def			拷贝到php源码包的ext目录
        │  
        └─icbc
                config.m4		覆盖到生成的icbc目录
                icbc.c			覆盖到生成的icbc目录
                php_icbc.h		覆盖到生成的icbc目录



二、编译步骤

1. 安装好icbc的库和头文件

cp -v include/icbc.h /usr/local/include/
cp -v lib/libicbc.so /usr/local/lib
chmod +x /usr/local/lib/libicbc.so


2. 创建php扩展工作环境

cd到php源码目录ext目录下

cp -v ~/icbc/php/ext/icbc.def

./ext_skel --extname=icbc --proto=icbc.def

3.拷贝做好的文件

cp -v ~/icbc/php/ext/icbc/* icbc/

4. 生成扩展的configure脚本
cd icbc
phpize

5. 编译安装 

./configure --with-icbc
make
make install

6. 配置php.ini

打开php.ini在文件最末尾添加一句

extension=icbc.so

重启web服务器后就可以使用工行的签名和签名验证函数了
                

三、封装函数用法

string icbcSign(string src, string privateKey, string keyPass)

src 		--	需要加密的字符串
privateKey	--  	商户私钥文件的绝对地址
keyPass		-- 	商户私钥文件的保护密码


bool icbcVerifySign(string src, string cert, string singned)

src		-- 	需要验证字符串明码
cert		-- 	工行公钥文件的绝对地址
signed		--	需要验证字符串密码	