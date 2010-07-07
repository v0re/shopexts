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
	
	len = BIO_read(bmem, output, max_len);
	output[len] = 0;
	*output_len = len;
	
	BIO_free_all(bmem);
}

main(){
	char *text = "ken";
	unsigned char buf[1024],*p,*start;
	char de_buf[1024],*de_p;
	int i,len,de_len,en_len;

	RSA *rsa,*pub_rsa,*priv_rsa;

	rsa = RSA_generate_key(1024,RSA_F4,NULL,NULL);
	
	p = buf;
	
	len = i2d_RSAPublicKey(rsa,&p);	
	len += i2d_RSAPrivateKey(rsa,&p);

	RSA_free(rsa);	

/*	
	p = (unsigned char*)malloc(len);
	memcpy(p,buf,len);
	start = p;	
	pub_rsa=d2i_RSAPublicKey(NULL,(const unsigned char**)&p,(long)len);
	len-=(p-start);
	priv_rsa=d2i_RSAPrivateKey(NULL,(const unsigned char**)&p,(long)len);

	if ((pub_rsa == NULL) || (priv_rsa == NULL))
		ERR_print_errors_fp(stderr);
	
	RSA_print_fp(stdout,pub_rsa,11);
	RSA_print_fp(stdout,priv_rsa,11);
	
	RSA_free(pub_rsa);
	RSA_free(priv_rsa);
*/
	text = (unsigned char*)malloc(len);
	memcpy(text,buf,len);	
	//text[len] = 0;

	for(i=0;i<len;i++){
		printf("%2x",text[i]);
	}
	printf("\n");
		
	p = buf;
	base64_encode(text,len,p,&en_len);
	printf("%d,%s\n",en_len,buf);
	p = buf;
	base64_decode(p,en_len,de_buf,&de_len);
	printf("%d,%s\n",de_len,de_buf);
	for(i=0;i<de_len;i++){
		printf("%2x",de_buf[i]);
	}
	printf("\n");
    
	p = (unsigned char*)malloc(de_len);
    memcpy(p,de_buf,de_len);
    start = p;  
    pub_rsa=d2i_RSAPublicKey(NULL,(const unsigned char**)&p,(long)len);
    len-=(p-start);
    priv_rsa=d2i_RSAPrivateKey(NULL,(const unsigned char**)&p,(long)len);

    if ((pub_rsa == NULL) || (priv_rsa == NULL))
        ERR_print_errors_fp(stderr);
    
    RSA_print_fp(stdout,pub_rsa,11);
    RSA_print_fp(stdout,priv_rsa,11);
    
    RSA_free(pub_rsa);
    RSA_free(priv_rsa);	
	
}
