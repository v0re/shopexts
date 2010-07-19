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
	
	char *p,buf[2048]={'\0'};
	char *key_buf_p,*key_buf;
	int len = 0;
	int en_len = 0;
	
	RSA *priv_key;	
		
	priv_key = get_user_private_key(source_filename);
	
	p=buf;	
	len =i2d_RSAPrivateKey(priv_key,&p);
	
	RSA_print_fp(stdout,priv_key,11);
	
	RSA_free(priv_key);
		
	key_buf_p = key_buf = (unsigned char*)malloc(len);
	memcpy(key_buf,buf,len);			
	
	p = buf;
	base64_encode(key_buf,len,p,&en_len);
	printf("%s\n",buf);	
	
	free(key_buf_p);
	
	/*
	
	
	shopex_conf_rsa_encrypt(config_content,config_content_len,&en_content,&en_content_len);
	

	if((fp=fopen(dest_filename,"wb+"))==NULL)
	{
		printf("cant open the file");
		exit(0);
	}

	fwrite(en_content,en_content_len,1,fp);

	fclose(fp);
	*/

}