#include <stdio.h>
#include <string.h>
#include <openssl/rsa.h>
#include <openssl/sha.h>
#include <openssl/hmac.h>
#include <openssl/evp.h>
#include <openssl/bio.h>
#include <openssl/buffer.h>

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

main(){
	
	RSA *rsa;
	int i,len,en_len;
	unsigned char buf[2048],*p,*key_p;

	rsa=RSA_generate_key(1024,RSA_F4,NULL,NULL);

	p=buf;
	
	len=i2d_RSAPublicKey(rsa,&p);	
	len+=i2d_RSAPrivateKey(rsa,&p);

	RSA_free(rsa);
	
	key_p = (unsigned char*)malloc(len);
	memcpy(key_p,buf,len);			
	
	p = buf;
	base64_encode(key_p,len,p,&en_len);
	printf("%s\n",buf);	
	
	free(key_p);
}


