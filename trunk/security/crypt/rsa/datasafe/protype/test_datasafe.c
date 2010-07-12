/*
cp libdatasafe.so /usr/lib
gcc -ldatasafe -L/usr/lib test_datasafe.c -o test_datasafe 

Written by Ken Xu (Kyle<xuqinyong@gmail.com>)
Copyright (C) 2010, ShopEx. 
All rights reserved.
*/
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include "datasafe_api.h"

main(){
	char *message = "hi ken";
	char *en_buf,*de_buf;
	
	printf("encrypt data  : %s\n",message);
	shopex_conf_rsa_encrypt(message,en_buf);
	printf("encrypted data :  %s\n",en_buf);
	
	shopex_conf_rsa_decrypt(en_buf,de_buf);
	printf("decrypted data is : %s\n",de_buf);
	
}f