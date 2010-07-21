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
	
    char *filename;
    char *output;
    int len;
    
    if(!argv[1]){
        filename = "/etc/shopex/skomart.com/setting.conf";
    }else{
        filename = argv[1];
    }
    output = (char *)malloc(256);
    shopex_read_privkeypos_in_file(filename,&output);
    printf("%s",output);
    
    free(output);
}