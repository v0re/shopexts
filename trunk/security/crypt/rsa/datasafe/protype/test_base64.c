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
    unsigned char  *key_buf;
    int i=0;
    
    en_buf = (char *)malloc(strlen(str) * 1.5);
    base64_encode(str,strlen(str),en_buf,&len);
    printf("%s\n",en_buf);
    de_buf = (char *)malloc(len);
    base64_decode(en_buf,len,de_buf,&len);
    printf("%s\n",de_buf);
    
    free(en_buf);
    free(de_buf);
    
    key_buf = " MIGJAoGBAL2R4cFnsIubFWTzmhASuDMkRityA5r9chN5KKtuy6SvR8G8JYdK+1y6MqxwssuG/65//fV8c+jQ5dTP8Pf92mqkEpgQK8afLcTCUAyV6kwr0n9B0FJT7uaGIRfmkW2GeFdsgyGi2UQos/6fLpLmkf7UGsiKe0FZqluSYTIXB1AnAgMBAAEwggJdAgEAAoGBAL2R4cFnsIubFWTzmhASuDMkRityA5r9chN5KKtuy6SvR8G8JYdK+1y6MqxwssuG/65//fV8c+jQ5dTP8Pf92mqkEpgQK8afLcTCUAyV6kwr0n9B0FJT7uaGIRfmkW2GeFdsgyGi2UQos/6fLpLmkf7UGsiKe0FZqluSYTIXB1AnAgMBAAECgYAfDfbdqA7Tz+QBVaa3W1mxhw+3rkXI7hvx9Lck3lGd+NjYc+nx7+admiMS/KNNlB/uhikkPe7/BLmh0y62cW+GIIt0c2T+PW758r271Rzl6zBpziVoWKqvFvggEihhbmtQwbdfJpTE5++flq1bbHD37CNbXKF1A53BGsZuInhDgQJBAN+JeI6WYBHwjNVJdSNO327g+6VZnE/I4Te4hpffoDdBElRdzlkMKb2C8+r838H5gtdUOYQxZrFyZC3Qwdj7hMsCQQDZGZmevEbT4wDyLxEVsIbev2p3F/BvSI3Z328bXouGnSfJvYf8pAGyzzF3/J5Oph1wfSDD+tcaOiSXIHDLDlKVAkEAmPoJSPtcD6rhlm1I/rrRaZ4KWSQ0Nt2wU2OyqGjfyjB8DMSNnJ8YSZs+tggMUUEh6562JeNw9erk5/2/S3EFdQJBAK/ZCJGGt2gRS0bf0NrcraHnMfldLqc1AZEkZf6pSiKzUPMbzfZZcfa/1LX3rNLPwrDG6a9Ukr7vsYun/zS6WTECQHG0oyaJRl13OENvj2ZoB4Qvp4jzy5jFj7wfOJSMGBtGuZ9BMrqWzdoPaaRw9v43+Bq2sGJkPs29goK2Jdcj/IE=";
    
    de_buf = (char *)malloc(strlen(key_buf));
    base64_decode(key_buf,strlen(key_buf),de_buf,&len);
    for(i=0;i<len;i++){
		printf("%2x",de_buf[i]);
	}
	printf("\n");
    
    free(de_buf);
}
