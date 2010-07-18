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
    char *str = "hello world";
    int len;
    char *en_buf;
    char *de_buf;
    
    en_buf = (char *)malloc(strlen(str) * 1.5);
    base64_encode(str,strlen(str),en_buf,&len);
    printf("%s\n",en_buf);
    de_buf = (char *)malloc(len);
    base64_decode(en_buf,len,de_buf,&len);
    printf("%s\n",de_buf);
    
    free(en_buf);
    free(de_buf);
    
    en_buf = (char *)malloc(strlen(str) * 1.5);
    base64_encode(str,strlen(str),en_buf,&len);
    printf("%s\n",en_buf);
    de_buf = (char *)malloc(len);
    base64_decode(en_buf,len,de_buf,&len);
    printf("%s\n",de_buf);
    
    free(en_buf);
    free(de_buf);
}
