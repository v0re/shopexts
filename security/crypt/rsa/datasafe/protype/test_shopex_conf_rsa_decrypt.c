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
	
	char *input = "bo7Oj0zcx9qk3LJMdyUqH71FMqGt7JLlTjQLpDhOr2MDrYf6JTvjP1AehKBVZaqyxmsP3U8CTCp2tpBbOKDSlS2gNq1gPx3YQmYvxhEP/qJlJhDMwSsFdbwLH99RvLyFXATbfJUbMdkv8TkITp5z1H9qVfwiP0u2nLJKVYKPyPRaXIT4h5+rSdQWQGDeIu7ghmm97MyvRr9jF73gZZqFiW8Z19fI7422m//DRRu3NGtuz703EgiMMb67p5XqF4zQLQkhjFcntG1gMAPAAnV0BT2O7tJtEKd3O2iy+TuXxt+w/tl3hrMQ5KJJx5uCqAG1d5xS43gXNGtGQO/FYpqVug==";
	int input_len = 0;
	
	char *output = NULL;
	int output_len = 0;
	
	input_len = strlen(input);
	
	shopex_conf_rsa_decrypt(input,input_len,&output,&output_len);
	
	printf("%s\n",output);
}