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
#include <openssl/rsa.h>
#include <openssl/pem.h>
#include "datasafe_api.h"

main(){
    RSA *pub_rsa,*priv_rsa;
    
    get_shopex_key(&pub_rsa,&priv_rsa);
    
    if ((pub_rsa == NULL) || (priv_rsa == NULL))
        ERR_print_errors_fp(stderr);

    RSA_print_fp(stdout,pub_rsa,11);
    RSA_print_fp(stdout,priv_rsa,11);
    
    RSA_free(pub_rsa);
    RSA_free(priv_rsa);
}
