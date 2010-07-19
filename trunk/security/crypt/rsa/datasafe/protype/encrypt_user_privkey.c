/*
cp libdatasafe.so /usr/lib
gcc -ldatasafe -L/usr/lib test_shopex_data_encrypt.c -o test_shopex_data_encrypt 

Written by Ken Xu (Kyle<xuqinyong@gmail.com>)
Copyright (C) 2010, ShopEx. 
All rights reserved.
*/
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <openssl/rsa.h>
#include <openssl/pem.h>
#include "datasafe_api.h"

main(){	
	
	char *source_filename = "/etc/shopex/skomart.com/sec.pem";
	char *dest_filename = "/etc/shopex/skomart.com/sec.pem.en";
	
	char *file_content = NULL;
	int file_content_len = 0;
	
	FILE *fp;
	int en_content_len;
	char *en_content;
	
	unsigned char *p,buf[2048]={'\0'};
	char *key_buf_p,*key_buf;
	int len = 0;
	int en_len = 0;
	
	RSA *priv_key;	
		
	priv_key = get_user_private_key(source_filename);
	RSA_print_fp(stdout,priv_key,11);
		
	p=buf;	
	len =i2d_RSAPrivateKey(priv_key,&p);
	RSA_free(priv_key);
		
	key_buf_p = key_buf = (unsigned char*)malloc(len);
	memcpy(key_buf,buf,len);			
	
	shopex_conf_rsa_encrypt(key_buf,len,&en_content,&en_content_len);	

	if((fp=fopen(dest_filename,"wb+"))==NULL)
	{
		printf("cant open the file");
		exit(0);
	}
	fwrite(en_content,en_content_len,1,fp);
	fclose(fp);	
}