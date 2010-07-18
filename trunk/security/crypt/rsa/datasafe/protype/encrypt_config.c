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
	
	char *source_filename = "/etc/shopex/skomart.com/setting.conf";
	char *dest_filename = "/etc/shopex/skomart.com/setting.conf.ed";
	
	char *config_content = NULL;
	int config_content_len = 0;
	
	FILE *fp;
	int i;
	
	shopex_read_conf_file(source_filename,&config_content,&config_content_len);
	shopex_conf_rsa_encrypt(config_content,config_content_len,&en_content,&en_content_len);
	

	if((fp=fopen(dest_filename,"wb+"))==NULL)
	{
		printf("cant open the file");
		exit(0);
	}

	fwrite(en_content,en_content_len,1,fp);

	fclose(fp);

}