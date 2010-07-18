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
#include <openssl/rsa.h>
#include <openssl/pem.h>

main(){
    char *str = "hello world";
    int len;
    char *en_buf;
    char *de_buf;
    unsigned char  *key_buf;
    int i=0;
    unsigned char *start,*p;
    RSA *pub_rsa,*priv_rsa;
    int de_len;
    
    
    en_buf = (char *)malloc(strlen(str) * 1.5);
    base64_encode(str,strlen(str),en_buf,&len);
    printf("%s\n",en_buf);
    de_buf = (char *)malloc(len);
    base64_decode(en_buf,len,de_buf,&len);
    printf("%s\n",de_buf);
    
    free(en_buf);
    free(de_buf);
    
    key_buf = "MIGJAoGBAKharbgn9dHnXvzM1w9UVZqkIsNjghuk0JwFhs5Z9C007csKPTzm+PZfsAAeJpVr6a9beKd93xwJgQcI+RZERNh0rjINQqkmyqLB5fDBP15RWk8kiWWcZxs5m6P1w3ld7V7/iO8MC3xV3IsuktN7dpQekIqknaLQ5ym6uX5KkyhnAgMBAAEwggJdAgEAAoGBAKharbgn9dHnXvzM1w9UVZqkIsNjghuk0JwFhs5Z9C007csKPTzm+PZfsAAeJpVr6a9beKd93xwJgQcI+RZERNh0rjINQqkmyqLB5fDBP15RWk8kiWWcZxs5m6P1w3ld7V7/iO8MC3xV3IsuktN7dpQekIqknaLQ5ym6uX5KkyhnAgMBAAECgYEAjcGkyw6itvbaDYUxhM/fMNIKD3mnYT17BhTFONWXe8U4gSGDBNf7RAC4QrEgjeqKn0QnKOkcxw/dnB3bFvuu9zIU2T0dRCXm8d86MYihIluSzWm/bhV17o56haJssil+G2DEi6OvSxAAmmrazPuDZZYezAEqfrA+/WqiMk6850ECQQDXgpIJlDG1awgwfXrkof11vJaLmO5DgT6QQuXQYLYXZ5j4uRvjiLOkmpqpYnHkFAnkQhDtTh3nGPNN+IaAfhWHAkEAx/wLe/K2F+AUkQb/mjd6+Qgt779AK5Gva0QGItBu6sJ8dc65NexAKqtVYbVDvxpsrcpvwF0lGlwkoYVT6vMOIQJAKut6IiY9cCAM1XtoCjiovdX9NXgTm6YVy61HD6TzHt4m/QIAp+QFwvZ3btyISiAjiC4QKPKxyeCSNCkWwGhb6QJBALeH7z4sS8LzicxjINZ2DixXvlilKw4RIG0Pu5Xdtb1LCY/QTRdYnrbKEIygHvFWHeEMxKxEj1V2tMNrKc2YjCECQEYUOOVFMj1hml0RxO+Sdf+tBzzj0IPj9aewFyAgSDPMObXo7Nn4OC4bYd72Se2VABRLhem8vVOqrn+oHEkSZ28=";
    
    de_buf = (char *)malloc(strlen(key_buf));
    base64_decode(key_buf,strlen(key_buf),de_buf,&de_len);
    for(i=0;i<len;i++){
		printf("%2x",de_buf[i]);
	}
	printf("\n");
    
	start = p = (unsigned char*)malloc(de_len);
    memcpy(p,de_buf,de_len);
    pub_rsa=d2i_RSAPublicKey(NULL,(const unsigned char**)&p,(long)de_len);
    de_len-=(p-start);
    priv_rsa=d2i_RSAPrivateKey(NULL,(const unsigned char**)&p,(long)de_len);
	if ((pub_rsa == NULL) || (priv_rsa == NULL))
        ERR_print_errors_fp(stderr);

    RSA_print_fp(stdout,pub_rsa,11);
    RSA_print_fp(stdout,priv_rsa,11);
    
    RSA_free(pub_rsa);
    RSA_free(priv_rsa);
    
    free(de_buf);
}
