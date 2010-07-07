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

void  base64_decode(unsigned char *input, int length,char *output, int *output_len){
	BIO *b64, *bmem;
	char *buffer;
	int max_len = (length * 6 + 7) / 8;
	int len;
		
	b64 = BIO_new(BIO_f_base64());
	BIO_set_flags(b64, BIO_FLAGS_BASE64_NO_NL);
	bmem = BIO_new_mem_buf(input, length);
	bmem = BIO_push(b64, bmem);
	
	buffer = (char *)malloc(max_len);
	len = BIO_read(bmem, buffer, max_len);
	memcpy(output,buffer,len);
	*output_len = len;
	
	BIO_free_all(bmem);
}

main(){
	char *text = "ken";
	char buf[1024],*p;
	char de_buf[1024],*de_p;
	int len,de_len;
	
	p = buf;
	base64_encode(text,strlen(text),p,&len);
	printf("%d,%s\n",len,buf);
	p = buf;
	base64_decode(p,strlen(p),de_buf,&de_len);
	printf("%d,%s\n",de_len,de_buf);
}
