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
#include "datasafe_api.h"

main(){	
	
	char *source_filename = "/etc/shopex/skomart.com/setting.conf.ed";
	
	char *config_content = NULL;
	int config_content_len = 0;
	
	FILE *fp;

	char *output;
	int output_len;
	
	if((fp=fopen(source_filename,"rb"))==NULL)
	{
		printf("cant open the file");
		exit(0);
	}
	
	fseek(fp, 0L, SEEK_END);
	config_content_len = ftell(fp);
	fseek(fp, 0L, SEEK_SET);
	config_content = (char *)malloc(config_content_len);
	fread(config_content, 1, config_content_len, fp );
	config_content[config_content_len] = '\0';
	fclose(fp);
	
	shopex_conf_rsa_decrypt(config_content,config_content_len,&output,&output_len);
	
	printf("%s\n",output);


}