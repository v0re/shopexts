#include <stdio.h>
#include <openssl/rsa.h>
#include <openssl/sha.h>
#include <openssl/hmac.h>
#include <openssl/evp.h>
#include <openssl/bio.h>
#include <openssl/buffer.h>

void base64_encode(const unsigned char *input, int length,char *output, int *output_len){
	BIO *bmem, *b64;
	BUF_MEM *bptr;
	
	b64 = BIO_new(BIO_f_base64());
	BIO_set_flags(b64, BIO_FLAGS_BASE64_NO_NL);
	bmem = BIO_new(BIO_s_mem());
	b64 = BIO_push(b64, bmem);
	BIO_write(b64, input, length);
	BIO_flush(b64);
	BIO_get_mem_ptr(b64, &bptr);
	
	//char *buff = (char *)malloc(bptr->length);
	memcpy(output, bptr->data, bptr->length-1);
	output[bptr->length-1] = 0;
	
	*output_len = bptr->length - 1;
		
	BIO_free_all(b64);
}

void  base64_decode(unsigned char *input, int length,char **output, int *output_len){
	BIO *b64, *bmem;
	
	char *buffer;
	int max_len = (length * 6 + 7) / 8;
	int len;
	
	buffer = (char *)malloc(max_len);
	memset(buffer, 0, max_len);
	
	b64 = BIO_new(BIO_f_base64());
	BIO_set_flags(b64, BIO_FLAGS_BASE64_NO_NL);
	bmem = BIO_new_mem_buf(input, length);
	bmem = BIO_push(b64, bmem);
	len = BIO_read(bmem, buffer, max_len);
	printf("%d",len);
	memcpy(output, buffer, len);
	output_len = len;
	
	BIO_free_all(bmem);
	
}

main(){
	char *text = 'ken';
	char buf[1024],*p;
	int len;
	
	p = buf;
	base64_encode(text,strlen(text),p,&len);
}
