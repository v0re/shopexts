һ���ļ�Ŀ¼�ṹ

��  config.m4			
��  cib.c			������
��  php_cib.h			ͷ�ļ�
��  cib.php			���������Խű�,php -f cib.php
��  test_cib.php		�������Գ��򣬴���������
��
����include
��      cib_api.h		CIB����������
��
����lib
        libcib.a		CIB������


�������벽��

1. ����php��չ��������

cd��phpԴ��Ŀ¼extĿ¼��

cp -v ~/cib/php/ext/cib.def .
./ext_skel --extname=cib --proto=cib.def

2.�������õ��ļ�

cp -v ~/cib/php/ext/cib/* cib/

3. ������չ��configure�ű�

cd cib
phpize

4. ���밲װ 

./configure --with-cib=$PWD
make
make install

5. ����php.ini

��php.ini���ļ���ĩβ���һ��

extension=cib.so

����web��������Ϳ���ʹ�ù��е�ǩ����ǩ����֤������
                

������װ�����÷�

string cibSign(unsigned char *key,unsigned char *message)

key		--	��Կ
message		--  	��Ҫ���ܵ�����

���ط���CIB��׼��8λASCII��





