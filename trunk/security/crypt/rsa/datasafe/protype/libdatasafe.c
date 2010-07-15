/*
gcc -lssl libdatasafe.c  -fPIC -shared -o libdatasafe.so &&
cp libdatasafe.so /usr/lib
Written by Ken Xu (Kyle<xuqinyong@gmail.com>)
Copyright (C) 2010, ShopEx. 
All rights reserved.
*/
#include <stdio.h>
#include <string.h>
#include <openssl/rsa.h>
#include <openssl/pem.h>
#include <openssl/sha.h>
#include <openssl/hmac.h>
#include <openssl/evp.h>
#include <openssl/bio.h>
#include <openssl/buffer.h>
#include <syslog.h>

#include "datasafe_api.h"

void base64_encode(unsigned char *input, int length,char *output, int *output_len){
    BIO *bmem, *b64;
    BUF_MEM *bptr;
    char *buff;    
    
    b64 = BIO_new(BIO_f_base64());
    BIO_set_flags(b64, BIO_FLAGS_BASE64_NO_NL);
    bmem = BIO_new(BIO_s_mem());
    b64 = BIO_push(b64, bmem);
    BIO_write(b64, input, length);
    BIO_flush(b64);
    BIO_get_mem_ptr(b64, &bptr);
    
    //buff = (char *)malloc(bptr->length);
    memcpy(output, bptr->data, bptr->length);
    output[bptr->length] = '\0';
    
    *output_len = bptr->length;
        
    BIO_free_all(b64);
}

void  base64_decode(unsigned char *input, int length,char *output, int *output_len){
    BIO *b64,*bmem;
    int max_len = (length * 6 + 7) / 8;
    int len;
    
    b64 = NULL;

    b64 = BIO_new(BIO_f_base64());
    BIO_set_flags(b64, BIO_FLAGS_BASE64_NO_NL);
    bmem = BIO_new_mem_buf(input, length);
    bmem = BIO_push(b64, bmem);
    
    len = BIO_read(bmem, output, max_len);
    output[len] = '\0';
    *output_len = len;
    
    BIO_free_all(b64);
    bmem = NULL;
    b64 = NULL;
}

void get_shopex_key(RSA **pubkey,RSA **privkey){
    unsigned char *pem_key_str = "MIGJAoGBAKz8scCXFg2O2r2sMsic40hSgHw1q52LUAvEHDH4S5pgflNjs8NfJKOjZmnkTpxI+eLmGKqPPWg7SF7YbUMmmTXvhuTWQF9OcXhIxzIUVFwQKZEWSgZyoaqwcy3XF6sIf7oFDRWfkIY5RCp03GdM0IjGK3lDIdfh0p6wSjTdfvvhAgMBAAEwggJcAgEAAoGBAKz8scCXFg2O2r2sMsic40hSgHw1q52LUAvEHDH4S5pgflNjs8NfJKOjZmnkTpxI+eLmGKqPPWg7SF7YbUMmmTXvhuTWQF9OcXhIxzIUVFwQKZEWSgZyoaqwcy3XF6sIf7oFDRWfkIY5RCp03GdM0IjGK3lDIdfh0p6wSjTdfvvhAgMBAAECgYBo1D1Xq3dWwgI2vPqNbd2h/zUTkGauczUP3EkF0yTlqaIEIMBYHfkTHTs74nns5aBg6vV5rpIU7w/9QgR8lBB1it3g6QU8RWdLG1cpckEL8LLPPWPIUOTSaId2BAeIU3Q0NOBc0sWO1pUTvYBGykQW9LYsP3254yIbc+5aQhwjAQJBANUh5TA45sMvpK+ZoRd3rWTQMU3Ted2/MCsGknPSPCk9ZxHTknU+q5O8L2kmWuc0b/IrVp4Zi9AUDx9AplRUvjECQQDPx7t6Iaim+jjO5y9FcKQPnFW4PRD2s2OffGisrIVAoLoQqNeHW5itltEs/CIT2AyTYRhg4uBIC37gt3kelDyxAkBhNv24Oiwf2apvok6VSrRfaIskqZJLr/pDldLVW46vbN+HhQ6nxfczAsJJXwJVtVheiKAQqyxXs96V7cIwcxrxAkEAihggRRK7yYaCXRkPtOIhV/K6kgGcFaqyapw/4Yuj4IkyQMJGxMKe3bhf+7rzVyb/bLBaiIIhOCDTybyHNkilcQJAHNSMtPgDVvYbzImMaNcpGHKJdkPoChO7W7EpRuCMlT7OMIc8cQIOiTBrHRDzF72NT0p+QfAXUAZxat7s1oqSDw==";
    
    unsigned char de_buf[2048],*p,*start;
    int de_len;


    p=de_buf;
    base64_decode(pem_key_str,strlen(pem_key_str),de_buf,&de_len);
    
    start = p = (unsigned char*)malloc(de_len);
    memcpy(p,de_buf,de_len);
    *pubkey=d2i_RSAPublicKey(NULL,(const unsigned char**)&p,(long)de_len);
    de_len-=(p-start);
    *privkey=d2i_RSAPrivateKey(NULL,(const unsigned char**)&p,(long)de_len);
    
}

RSA *get_shopex_public_key(){
    RSA *pub_rsa,*priv_rsa;
    get_shopex_key(&pub_rsa,&priv_rsa);
    RSA_free(priv_rsa);
    return pub_rsa;
}

RSA *get_shopex_private_key(){
    RSA *pub_rsa,*priv_rsa;
    get_shopex_key(&pub_rsa,&priv_rsa);
    RSA_free(pub_rsa);
    return priv_rsa;
}

RSA *get_user_public_key(char *keyfile_path){
      FILE *fp;
      RSA *key=NULL;
    
    if((fp = fopen(keyfile_path,"r")) == NULL) {
      syslog(LOG_USER|LOG_INFO, "Error: Public Key file doesn't exists.\n");
      exit(EXIT_FAILURE);
    }
    if((key = PEM_read_RSAPublicKey(fp,NULL,NULL,NULL)) == NULL) {
      syslog(LOG_USER|LOG_INFO, "Error: problems while reading Public Key.\n");
      exit(EXIT_FAILURE);
    }
    fclose(fp);
    return key;
}

RSA *get_user_private_key(char *keyfile_path){
    FILE *fp;
    RSA *key=NULL;
    
    if((fp = fopen(keyfile_path,"r")) == NULL) {
      syslog(LOG_USER|LOG_INFO, "Error: Private Key file doesn't exists.\n");
      exit(EXIT_FAILURE);
    }
    if((key = PEM_read_RSAPrivateKey(fp,NULL,NULL,NULL)) == NULL) {
      syslog(LOG_USER|LOG_INFO, "Error: problmes while reading Private Key.\n");
      exit(EXIT_FAILURE);
    }
    fclose(fp);
    if(RSA_check_key(key) == -1) {
      syslog(LOG_USER|LOG_INFO, "Error: Problems while reading RSA Private Key in  file.\n");
      exit(EXIT_FAILURE);
    } else if(RSA_check_key(key) == 0) {
      syslog(LOG_USER|LOG_INFO, "Error: Bad RSA Private Key readed in  file.\n");
      exit(EXIT_FAILURE);
    }
    else
      return key;
}


void shopex_rsa_encrypt(RSA *pub_rsa,char *input,int input_len,char **output,int *output_len){    
    int ks,chunk_len,rsa_ret_buf_len,ret_len,ret_len_total,en_len;
    char *rsa_ret_buf_p,*rsa_ret_buf;
    char *plain_p,*plain;
    char *cipher_p,*cipher;
    char *b64_buf_p,*b64_buf;
    char *input_p;
    char *output_buf;

    ret_len = ret_len_total = 0;

    input_p = input;
    if((int)input_len < 0 ){
        input_len = strlen(input);
    }
    ks = RSA_size(pub_rsa);
    chunk_len = input_len > (ks - 11) ? ks - 11 : input_len;
    rsa_ret_buf_len = ( ( input_len / chunk_len + 1) * ks );
    rsa_ret_buf_p = rsa_ret_buf = (char *)malloc(rsa_ret_buf_len * sizeof(char));
    memset(rsa_ret_buf_p,'\0', rsa_ret_buf_len + 1);
    plain_p = plain = (char *)malloc(ks * sizeof(char));
    cipher_p = cipher = (char *)malloc(ks * sizeof(char));
    
    while(input - input_p < input_len) {
        memset(plain,'\0',ks + 1);
        memset(cipher, '\0', ks + 1);
        memcpy(plain, input, chunk_len);
        ret_len = RSA_public_encrypt(chunk_len, plain, cipher, pub_rsa, RSA_PKCS1_PADDING);
        memcpy(rsa_ret_buf,cipher,ret_len);
        plain = plain_p;
        cipher = cipher_p;
        ret_len_total += ret_len;
        input += chunk_len;
        rsa_ret_buf += ret_len;
    }
    
    b64_buf_p = b64_buf = (char *)malloc(ret_len_total * 1.5 );
    base64_encode(rsa_ret_buf_p,ret_len_total,b64_buf,&en_len);
    output_buf = (char *)malloc(en_len);
    memcpy(output_buf,b64_buf_p,en_len);
    output_buf[en_len] = '\0';
    
    *output = output_buf; 
    *output_len = en_len;
    
    free(b64_buf_p);
    free(cipher_p);
    free(plain_p);
    free(rsa_ret_buf_p);
    RSA_free(pub_rsa);
}

void shopex_rsa_decrypt(RSA *priv_rsa,char *input,int input_len,char **output,int *output_len){
    int de_len,ks,ret_len,ret_len_total;
    char *rsa_ret_buf_p,*rsa_ret_buf;
    char *cipher_p,*cipher;
    char *de_buf_p,*de_buf;
    char *plain_p,*plain;
    char *output_buf;
    
    ret_len = ret_len_total = 0;
    
    if((int)input_len < 0 ){
        input_len = strlen(input);
    }    
    de_buf_p = de_buf = (char *)malloc(input_len * sizeof(char));
    base64_decode(input,input_len,de_buf,&de_len);    
    de_buf = de_buf_p;
    
    rsa_ret_buf_p = rsa_ret_buf = (char *)malloc(input_len * sizeof(char));
    
    ks = RSA_size(priv_rsa);
    cipher_p = cipher = (char*)malloc(ks * sizeof(char));
    plain_p = plain = (char*)malloc(ks * sizeof(char));
    while(de_buf - de_buf_p < de_len) {
        memset(cipher, '\0', ks);
        memset(plain, '\0', ks);
        memcpy(cipher,de_buf,ks);
        ret_len = RSA_private_decrypt(ks, cipher, plain, priv_rsa, RSA_PKCS1_PADDING);
        memcpy(rsa_ret_buf,plain,ret_len);
        ret_len_total += ret_len;
        rsa_ret_buf += ret_len;
        cipher = cipher_p;
        plain = plain_p;
        de_buf += ks;    
    }    
    
    output_buf = (char *)malloc(ret_len_total * sizeof(char));    
    memcpy(output_buf,rsa_ret_buf_p,ret_len_total);
    output_buf[ret_len_total] = '\0';
    
    *output = output_buf;
    *output_len = ret_len_total;
    
    free(de_buf_p);
    free(rsa_ret_buf_p);
    free(cipher_p);
    free(plain_p);
    RSA_free(priv_rsa);
}

void shopex_conf_rsa_encrypt(char *input,int input_len,char **output,int *output_len){
    RSA *pub_rsa;
    pub_rsa = get_shopex_public_key();
    shopex_rsa_encrypt(pub_rsa,input,input_len,output,output_len);    
}

void shopex_conf_rsa_decrypt(char *input,int input_len,char **output,int *output_len){
    RSA *priv_rsa;
    priv_rsa = get_shopex_private_key();
    shopex_rsa_decrypt(priv_rsa,input,input_len,output,output_len);    
}

void shopex_data_rsa_encrypt(char *keyfile_path,char *input,int input_len,char * *output,int *output_len){
    RSA *pub_rsa;
    pub_rsa = get_user_public_key(keyfile_path);
    shopex_rsa_encrypt(pub_rsa,input,input_len,output,output_len);    
}

void shopex_data_rsa_decrypt(char *keyfile_path,char *input,int input_len,char **output,int *output_len){
    RSA *priv_rsa;
    priv_rsa = get_user_private_key(keyfile_path);
    shopex_rsa_decrypt(priv_rsa,input,input_len,output,output_len);    
}

void shopex_read_conf_file(char *filename,char **output,int *output_len){
	 FILE * fp;
	 int len;
	 char * buffer = NULL;
	 fp = fopen(filename, "r");
	 if (fp == NULL) {
		syslog(LOG_USER|LOG_INFO, "read shopex config file failure");
		exit(EXIT_FAILURE);
	 }
	 fseek(fp, 0L, SEEK_END);
	 len = ftell(fp);
	 fseek(fp, 0L, SEEK_SET);
	 buffer = (char *)malloc(len);
	 fread( buffer, 1, len, fp );
	 *output = buffer;
	 *output_len = len;
	 
	 fclose(fp);
}




void test_get_shopex_key(){
    RSA *pub_rsa,*priv_rsa;
    
    get_shopex_key(&pub_rsa,&priv_rsa);
    
    if ((pub_rsa == NULL) || (priv_rsa == NULL))
        ERR_print_errors_fp(stderr);

    RSA_print_fp(stdout,pub_rsa,11);
    RSA_print_fp(stdout,priv_rsa,11);
    
    RSA_free(pub_rsa);
    RSA_free(priv_rsa);
}

void test_get_user_key(){
	 RSA *pub_rsa,*priv_rsa;
	 
	 char *pub_keyfile_path  = "/etc/shopex/skomart.com/pub.pem";
	 char *priv_keyfile_path = "/etc/shopex/skomart.com/sec.pem";
	 pub_rsa = get_user_public_key(pub_keyfile_path);
	 priv_rsa = get_user_private_key(priv_keyfile_path);
	 
    if ((pub_rsa == NULL) || (priv_rsa == NULL))
        ERR_print_errors_fp(stderr);
    
    RSA_print_fp(stdout,pub_rsa,11);
    RSA_print_fp(stdout,priv_rsa,11);
    
    RSA_free(pub_rsa);
    RSA_free(priv_rsa);
}

void test_get_user_public_key(){
	 RSA *pub_rsa;
	 
	 char *pub_keyfile_path  = "/etc/shopex/skomart.com/pub.pem";
	 pub_rsa = get_user_public_key(pub_keyfile_path);
	 
	RSA_print_fp(stdout,pub_rsa,11);
	
	RSA_free(pub_rsa);
}

void test_get_user_private_key(){
	RSA *priv_rsa;
	 

	 char *priv_keyfile_path = "/etc/shopex/skomart.com/sec.pem";

	 priv_rsa = get_user_private_key(priv_keyfile_path);
	 
    RSA_print_fp(stdout,priv_rsa,11);
    
    RSA_free(priv_rsa);
}

void test_shopex_data_rsa_encrypt(){
    char *pub_keyfile_path  = "/etc/shopex/skomart.com/pub.pem";
    char *input = NULL;
    char *output = NULL;
    int output_len;    
    
    input = "hello world!";
    
    shopex_data_rsa_encrypt(pub_keyfile_path,input,strlen(input),&output,&output_len);
    output[output_len] = '\0';
    printf("%s\n",output);

}
	 
void test_shopex_data_rsa_decrypt(){
    char *priv_keyfile_path  = "/etc/shopex/skomart.com/sec.pem";
    char *input = NULL;
    char *output = NULL;
    int output_len;
    
    input = "KTmCZFBep8qJnIZeo0hSq1Owc/QRWu66EZXb+gPj5fCrh1Vgpj1u+nWJb8aQpy4EOxNu1r7kuibF3OIekLjvrspnd1kD3mMUYuoDOXbp5rIv+EtDieRKbJqeDmfD8GrEGHwrHlec/gnLqhyN1cWXFDD1x7xSULMPLmTzbnbTWk4=";
    
    shopex_data_rsa_decrypt(priv_keyfile_path,input,strlen(input),&output,&output_len);
    output[output_len] = '\0';
    printf("%s\n",output);

}

void test_shopex_read_conf_file(){
	char *filename;
	char *output;
	int len;
	
	filename = "/etc/shopex/skomart.com/setting.conf";
	shopex_read_conf_file(filename,&output,&len);
	printf("%s",output);
	
	free(output);
}

void test_shopex_read_pubkey_pos_file(){
	char *filename;
	char *output;
	int len;
	char *pos_start,*pos_end,pub_buf;
	
	filename = "/etc/shopex/skomart.com/setting.conf";
	shopex_read_conf_file(filename,&output,&len);
	
	pos_start = output;
	pos_end = strstr(output,"\n");
	len = pos_end - pos_start;
	pub_buf = (char *)malloc(len);
	memcpy(pub_buf,pos_start,len);
	printf("%s",pub_buf);	
	free(output);
}



