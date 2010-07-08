#include <stdio.h>
#include <string.h>
#include <openssl/rsa.h>
#include <openssl/sha.h>
#include <openssl/hmac.h>
#include <openssl/evp.h>
#include <openssl/bio.h>
#include <openssl/buffer.h>

void  base64_decode(unsigned char *input, int length,char *output, int *output_len){
	BIO *b64, *bmem;
	char *buffer;
	int max_len = (length * 6 + 7) / 8;
	int len;
		
	b64 = BIO_new(BIO_f_base64());
	BIO_set_flags(b64, BIO_FLAGS_BASE64_NO_NL);
	bmem = BIO_new_mem_buf(input, length);
	bmem = BIO_push(b64, bmem);
	
	len = BIO_read(bmem, output, max_len);
	output[len] = 0;
	*output_len = len;
	
	BIO_free_all(bmem);
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



main(){
	
	char *message = "hi ken";
	RSA *pub_rsa,*priv_rsa;
	char buf[2048],*ciphertext,*cleartext;

	int ret;
	//test_get_shopex_key();
	//strlen(message) must lower then RSA_size(pub_rsa) - 11
	get_shopex_key(&pub_rsa,&priv_rsa);
	ciphertext = (char *)malloc(RSA_size(pub_rsa));
	
	printf("encrypt data is: %s\n",message);

	ret = RSA_public_encrypt(strlen(message), message, ciphertext, pub_rsa, RSA_PKCS1_PADDING);
	
	printf("encrypted data size %d\n",ret);
	
	cleartext = buf;
	ret = RSA_private_decrypt(ret, ciphertext, cleartext, priv_rsa, RSA_PKCS1_PADDING);
	buf[ret] = 0;
	printf("decrypted data is %s\n",buf);
	
}
