/*
gcc -lssl libdatasafe.c  -fPIC -shared -o libdatasafe.so

Written by Ken Xu (Kyle<xuqinyong@gmail.com>)
Copyright (C) 2010, ShopEx. 
All rights reserved.
*/
#include <stdio.h>
#include <string.h>
#include <openssl/rsa.h>
#include <openssl/sha.h>
#include <openssl/hmac.h>
#include <openssl/evp.h>
#include <openssl/bio.h>
#include <openssl/buffer.h>

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
	output[bptr->length] = 0;
	
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
	output[len] = 0;
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


void shopex_rsa_encrypt(RSA *pub_rsa,char *input,char **output){	
	int input_len,rsa_len,buf_num,chunk_len,ret_len,ret_len_total,en_len;
	char *rsa_ret_buf_p,*rsa_ret_buf;
	char *ciphertext_p,*ciphertext;
	char *rsa_input,*rsa_input_p;
	char *b64_buf_p,*b64_buf;
	char *input_p;
	char *output_buf;

	ret_len = ret_len_total = 0;

	input_len = strlen(input);
	rsa_len = RSA_size(pub_rsa);
	buf_num = input_len / rsa_len + 1;
	input_p = input;
	rsa_ret_buf_p = rsa_ret_buf = (char *)malloc(rsa_len * buf_num);
	ciphertext_p = ciphertext = (char * )malloc(RSA_size(pub_rsa));
	rsa_input_p = rsa_input = (char *)malloc(110);
	do{			
			chunk_len = input_len > 110 ? 110 : input_len;
			memcpy(rsa_input,input,chunk_len);
			//input_len must lower then RSA_size(pub_rsa) - 11			
			ret_len = RSA_public_encrypt(chunk_len, rsa_input, ciphertext, pub_rsa, RSA_PKCS1_PADDING);
			memcpy(rsa_ret_buf,ciphertext_p,ret_len);
			ret_len_total += ret_len;
			rsa_ret_buf += ret_len;
			ciphertext = ciphertext_p;
			rsa_input = rsa_input_p;
			input = input + 110;
	}while(input - input_p < input_len );
	b64_buf_p = b64_buf = (char *)malloc( ret_len_total * 1.5 );
	base64_encode(rsa_ret_buf_p,ret_len_total,b64_buf,&en_len);
	output_buf = (char *)malloc(en_len);
	memcpy(output_buf,b64_buf_p,en_len);
	output_buf[en_len] = 0;
	
	*output = output_buf; 

	free(b64_buf_p);
	free(ciphertext_p);
	free(rsa_ret_buf_p);
	RSA_free(pub_rsa);
}

void shopex_rsa_decrypt(RSA *priv_rsa,char *input,char **output){
	int input_len,de_len,chunk_len,ret_len,ret_len_total;
	char *rsa_ret_buf_p,*rsa_ret_buf;
	char *cleartext_p,*cleartext;
	char *de_buf_p,*de_buf;
	char *rsa_input,*rsa_input_p;
	char *output_buf;
	
	ret_len = ret_len_total = 0;
	
	input_len = strlen(input);
	de_buf_p = de_buf = (char *)malloc(input_len);
	base64_decode(input,input_len,de_buf,&de_len);	
	rsa_ret_buf_p = rsa_ret_buf = (char *)malloc(input_len);
	cleartext_p = cleartext = (char *)malloc(RSA_size(priv_rsa));
	rsa_input_p = rsa_input = (char *)malloc(110);
	do{
		chunk_len = de_len > 110 ? 110 : de_len;
		memcpy(rsa_input,de_buf,chunk_len);
		ret_len = RSA_private_decrypt(chunk_len, rsa_input, cleartext, priv_rsa, RSA_PKCS1_PADDING);
		memcpy(rsa_ret_buf,cleartext_p,ret_len);
		ret_len_total += ret_len;
		rsa_ret_buf += ret_len;
		cleartext = cleartext_p;
		rsa_input = rsa_input_p;
		de_buf = de_buf + 110;		
	}while(de_buf - de_buf_p < de_len);	
	output_buf = (char *)malloc(ret_len_total);	
	memcpy(output_buf,rsa_ret_buf_p,ret_len_total);
	output_buf[ret_len_total] = 0;
	
	*output = output_buf;
	
	free(de_buf_p);
	free(rsa_ret_buf_p);
	free(cleartext_p);
	RSA_free(priv_rsa);
}

void shopex_conf_rsa_encrypt(char *input,char **output ){
	RSA *pub_rsa;
	pub_rsa = get_shopex_public_key();
	shopex_rsa_encrypt(pub_rsa,input,output);	
}

void shopex_conf_rsa_decrypt(char *input,char **output){
	RSA *priv_rsa;
	priv_rsa = get_shopex_private_key();
	//shopex_rsa_decrypt(priv_rsa,input,output);	
}

void shopex_data_rsa_encrypt(char *input,char *output){
	
}

void shopex_data_rsa_decrypt(char *input,char *output){
	
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



