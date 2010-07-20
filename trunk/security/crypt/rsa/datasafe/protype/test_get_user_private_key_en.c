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

main(int argc,char *argv[]){	
	
    char *filename;
    RSA *key;
    
    if(!argv[1]){
        filename = "/etc/shopex/skomart.com/sec.pem.en";
    }else{
        filename = argv[1];
    }
    key = get_user_private_key_en(filename);    
    RSA_print_fp(stdout,key,11);	
	RSA_free(key);
}