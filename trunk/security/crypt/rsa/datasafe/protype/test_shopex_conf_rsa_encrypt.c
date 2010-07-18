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
	
	char *filename = "/etc/shopex/skomart.com/setting.conf";
	char *config_content = NULL;
	int config_content_len = 0;
	char *en_content;
	int en_content_len;
	
	shopex_read_conf_file(filename,config_content,config_content_len);
	shopex_conf_rsa_encrypt(config_content,config_content_len,&en_content,&en_content_len);
	
	printf("%s\n",en_content);
}