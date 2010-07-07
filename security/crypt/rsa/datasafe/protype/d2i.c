#include <stdio.h>
#include <openssl/rsa.h>
#include <openssl/sha.h>
#include <openssl/hmac.h>
#include <openssl/evp.h>
#include <openssl/bio.h>
#include <openssl/buffer.h>


void  base64_decode(unsigned char *input, int length,char **output, int *output_len)
{
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
	
	unsigned char pem_key_str[] = "MIGJAoGBALXlmXKlo4sdoz8qeBPmLou247lwEHgdmNkPSNN78phIy8Ke4jo5+UIYkE0/9epKwqG3wEYKyVywmA3VqWDmXJYW6BOhEtCFFz0QmAnfw99+xXIcKxOUcsAahJFCRI6/eT1J3Z/66QmXm4XAYloLKNXC9Tmyn+chqi1uNtfANroRAgMBAAEwggJcAgEAAoGBALXlmXKlo4sdoz8qeBPmLou247lwEHgdmNkPSNN78phIy8Ke4jo5+UIYkE0/9epKwqG3wEYKyVywmA3VqWDmXJYW6BOhEtCFFz0QmAnfw99+xXIcKxOUcsAahJFCRI6/eT1J3Z/66QmXm4XAYloLKNXC9Tmyn+chqi1uNtfANroRAgMBAAECgYEAl+K0kysEuPFykxgfVF5sl3WMChgtaF8udnFw2kcxdz+yBT0uonguTqa8OAUkjxMGGouZHeN76M386fBzktpIjBMJKKa1GnAC3Zqph1DJVymUVIC2zKPqbGHceUgIkZ7fz66x+J8KHTOwvKo5HpljCv/pPq+UziFLwfWqLjp8cAECQQDZRZd4QgXACgq8MMUm+dXCKan6NGZxWdLnhpcXsIqqACYfkstZnBUDmiOHRNkCD3ooFxjFhoaRMiu5buviKNtRAkEA1lHMyllKljWIRGPQ8HkqJrCPp5vJD8Dig2TXby/OFVqiS+kBy9uYsYFanLYY5B3FprYXYQ9u3obZTNWs3FbCwQJAMBMS6dwJ860FJRDRfsdHAfhAEQmpJSmP3gTMx8QbWnQ/+zp63jAIAk0H0XVtYuRTzi0WIRacDeKBBD3D2b3akQJAfP5sH79/3qcN+ET2wKkJylLDFY+n7cYi1VrkwnXxDUc0zGzynUBPh4bXn/ob/j7W3WnprLPhh2rCJSuhi0gWgQJANTKCrgVdm87QaQlkeunRoJR/1eDpyVU4b/+ODgTNCpTVWBmwEM7F4Q44zJ5xZkJozSTX1oC4/meL/hm1b98rNQ=";
	
	RSA *pub_rsa,*priv_rsa;
	unsigned char buf[2048],*p;
	int len = 0;
	
	p=buf;
	base64_decode(pem_key_str,strlen(pem_key_str),&p,&len);
	
	//printf("%d",len);
	/*
	p=buf;
	pub_rsa=d2i_RSAPublicKey(NULL,&p,(long)len);
	len-=(p-buf);
	priv_rsa=d2i_RSAPrivateKey(NULL,&p,(long)len);

	if ((pub_rsa == NULL) || (priv_rsa == NULL))
		ERR_print_errors_fp(stderr);

	RSA_free(pub_rsa);
	RSA_free(priv_rsa);
	*/

}

