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
    RSA *pubkey,*privkey;
    int de_len;
    
    
    en_buf = (char *)malloc(strlen(str) * 1.5);
    base64_encode(str,strlen(str),en_buf,&len);
    printf("%s\n",en_buf);
    de_buf = (char *)malloc(len);
    base64_decode(en_buf,len,de_buf,&len);
    printf("%s\n",de_buf);
    
    free(en_buf);
    free(de_buf);
    
    key_buf = "MIGJAoGBAKz8scCXFg2O2r2sMsic40hSgHw1q52LUAvEHDH4S5pgflNjs8NfJKOjZmnkTpxI+eLmGKqPPWg7SF7YbUMmmTXvhuTWQF9OcXhIxzIUVFwQKZEWSgZyoaqwcy3XF6sIf7oFDRWfkIY5RCp03GdM0IjGK3lDIdfh0p6wSjTdfvvhAgMBAAEwggJcAgEAAoGBAKz8scCXFg2O2r2sMsic40hSgHw1q52LUAvEHDH4S5pgflNjs8NfJKOjZmnkTpxI+eLmGKqPPWg7SF7YbUMmmTXvhuTWQF9OcXhIxzIUVFwQKZEWSgZyoaqwcy3XF6sIf7oFDRWfkIY5RCp03GdM0IjGK3lDIdfh0p6wSjTdfvvhAgMBAAECgYBo1D1Xq3dWwgI2vPqNbd2h/zUTkGauczUP3EkF0yTlqaIEIMBYHfkTHTs74nns5aBg6vV5rpIU7w/9QgR8lBB1it3g6QU8RWdLG1cpckEL8LLPPWPIUOTSaId2BAeIU3Q0NOBc0sWO1pUTvYBGykQW9LYsP3254yIbc+5aQhwjAQJBANUh5TA45sMvpK+ZoRd3rWTQMU3Ted2/MCsGknPSPCk9ZxHTknU+q5O8L2kmWuc0b/IrVp4Zi9AUDx9AplRUvjECQQDPx7t6Iaim+jjO5y9FcKQPnFW4PRD2s2OffGisrIVAoLoQqNeHW5itltEs/CIT2AyTYRhg4uBIC37gt3kelDyxAkBhNv24Oiwf2apvok6VSrRfaIskqZJLr/pDldLVW46vbN+HhQ6nxfczAsJJXwJVtVheiKAQqyxXs96V7cIwcxrxAkEAihggRRK7yYaCXRkPtOIhV/K6kgGcFaqyapw/4Yuj4IkyQMJGxMKe3bhf+7rzVyb/bLBaiIIhOCDTybyHNkilcQJAHNSMtPgDVvYbzImMaNcpGHKJdkPoChO7W7EpRuCMlT7OMIc8cQIOiTBrHRDzF72NT0p+QfAXUAZxat7s1oqSDw==";
    
    de_buf = (char *)malloc(strlen(key_buf));
    base64_decode(key_buf,strlen(key_buf),de_buf,&len);
    for(i=0;i<len;i++){
		printf("%2x",de_buf[i]);
	}
	printf("\n");
    
	start = p = (unsigned char*)malloc(de_len);
    memcpy(p,de_buf,de_len);
    pubkey=d2i_RSAPublicKey(NULL,(const unsigned char**)&p,(long)de_len);
    de_len-=(p-start);
    privkey=d2i_RSAPrivateKey(NULL,(const unsigned char**)&p,(long)de_len);
	if ((pub_rsa == NULL) || (priv_rsa == NULL))
        ERR_print_errors_fp(stderr);

    RSA_print_fp(stdout,pub_rsa,11);
    RSA_print_fp(stdout,priv_rsa,11);
    
    RSA_free(pub_rsa);
    RSA_free(priv_rsa);
    
    free(de_buf);
}
