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

main(int argc,char *argv[]){	
	
    char *pub_keyfile_path;
    char *input = NULL;
    char *output = NULL;
    int output_len;    
    
    if(!argv[1]){
        pub_keyfile_path  =  "/etc/shopex/skomart.com/pub.pem";
    }else{
        pub_keyfile_path = argv[1];
    }

    
    input = "hello world!";
    
    shopex_data_rsa_encrypt(pub_keyfile_path,input,strlen(input),&output,&output_len);
    output[output_len] = '\0';
    printf("%s\n",output);
}