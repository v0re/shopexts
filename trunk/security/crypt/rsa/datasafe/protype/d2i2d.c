/*
Simple RSA Key generator. Saves keys in PEM format.
Coded by Paolo Ardoino - <paolo.ardoino@gmail.com>
Compile: gcc -lssl d2i2d.c -o d2i2d
Usage: ./keygen <numbits>
Ex. ./genkey 1024
*/

#include <stdio.h>
#include <openssl/err.h>
#include <openssl/rsa.h>
#include <openssl/pem.h>

int main(int argc, char *argv[])
{
	RSA *key;
 	int keylen=1024;
 	int ret;
 	unsigned char *buf;

 	if((key = RSA_generate_key(keylen,3,NULL,NULL)) == NULL)
 	{
  		fprintf(stderr,"%s\n",ERR_error_string(ERR_get_error(),NULL));
  		exit(-1);
 	}
 	if(RSA_check_key(key) < 1)
 	{
 		fprintf(stderr,"Error: Problems while generating RSA Key.\nRetry.\n");
 		exit(-1);
 	}
 	
 	ret = i2d_RSAPublicKey(key,&buf);

	printf("%d",ret);
 	return 0;
}
