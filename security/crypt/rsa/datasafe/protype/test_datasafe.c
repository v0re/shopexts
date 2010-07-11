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
	char buf[2048],de_buf[2048];
	
	printf("encrypt data  : %s\n",message);
	shopex_rsa_encrypt(message,buf);
	printf("encrypted data :  %s\n",buf);
	
	shopex_rsa_decrypt(buf,de_buf);
	printf("decrypted data is : %s\n",de_buf);
	
}