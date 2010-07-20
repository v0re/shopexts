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
	
    char *priv_keyfile_path;
    char *input = NULL;
    char *output = NULL;
    int output_len;
    
    if(!argv[1]){
        priv_keyfile_path  = "/etc/shopex/skomart.com/sec.pem";
    }else{
        priv_keyfile_path = argv[1];
    }
    
    input = "KTmCZFBep8qJnIZeo0hSq1Owc/QRWu66EZXb+gPj5fCrh1Vgpj1u+nWJb8aQpy4EOxNu1r7kuibF3OIekLjvrspnd1kD3mMUYuoDOXbp5rIv+EtDieRKbJqeDmfD8GrEGHwrHlec/gnLqhyN1cWXFDD1x7xSULMPLmTzbnbTWk4=";
    
    shopex_data_rsa_decrypt(priv_keyfile_path,input,strlen(input),&output,&output_len);
    output[output_len] = '\0';
    printf("%s\n",output);    
}