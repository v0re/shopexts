һ���ļ�Ŀ¼�ṹ

����include
��      icbc.h				������ /usr/local/include
��      
����lib
��      libicbc.so			������/usr/local/lib
��      
����php
    ����ext
        ��  icbc.def			������phpԴ�����extĿ¼
        ��  
        ����icbc
                config.m4		���ǵ����ɵ�icbcĿ¼
                icbc.c			���ǵ����ɵ�icbcĿ¼
                php_icbc.h		���ǵ����ɵ�icbcĿ¼



�������벽��

1. ��װ��icbc�Ŀ��ͷ�ļ�

cp -v include/icbc.h /usr/local/include/
cp -v lib/libicbc.so /usr/local/lib
chmod +x /usr/local/lib/libicbc.so


2. ����php��չ��������

cd��phpԴ��Ŀ¼extĿ¼��

cp -v ~/icbc/php/ext/icbc.def

./ext_skel --extname=icbc --proto=icbc.def

3.�������õ��ļ�

cp -v ~/icbc/php/ext/icbc/* icbc/

4. ������չ��configure�ű�
cd icbc
phpize

5. ���밲װ 

./configure --with-icbc
make
make install

6. ����php.ini

��php.ini���ļ���ĩβ���һ��

extension=icbc.so

����web��������Ϳ���ʹ�ù��е�ǩ����ǩ����֤������
                

������װ�����÷�

string icbcSign(string src, string privateKey, string keyPass)

src 		--	��Ҫ���ܵ��ַ���
privateKey	--  	�̻�˽Կ�ļ��ľ��Ե�ַ
keyPass		-- 	�̻�˽Կ�ļ��ı�������


bool icbcVerifySign(string src, string cert, string singned)

src		-- 	��Ҫ��֤�ַ�������
cert		-- 	���й�Կ�ļ��ľ��Ե�ַ
signed		--	��Ҫ��֤�ַ�������	