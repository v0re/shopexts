#include <stdio.h>
#include <openssl/rsa.h>

char *shopex_base64_encode(char *input){
    BIO *bmem, *b64;
    BUF_MEM *bptr;

    b64 = BIO_new(BIO_f_base64());
    bmem = BIO_new(BIO_s_mem());
    b64 = BIO_push(b64, bmem);
    BIO_write(b64, input, strlen(input));
    BIO_flush(b64);
    BIO_get_mem_ptr(b64, &bptr);

    char *buff = (char *)malloc(bptr->length);
    memcpy(buff, bptr->data, bptr->length-1);
    buff[bptr->length-1] = 0;

    BIO_free_all(b64);

    return buff;
}

main(){
	
	RSA *rsa;
	int i,len;
	unsigned char buf[2048],*p;

	rsa=RSA_generate_key(1024,RSA_F4,NULL,NULL);

	p=buf;
	
	len=i2d_RSAPublicKey(rsa,&p);	
	len+=i2d_RSAPrivateKey(rsa,&p);

	RSA_free(rsa);
	
	p=buf;	
	printf("%s",shopex_base64_encode(p));
	printf("\n");
}


