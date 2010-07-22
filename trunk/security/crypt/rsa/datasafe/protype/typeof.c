/*
Simple RSA Key generator. Saves keys in PEM format.
Coded by Paolo Ardoino - <paolo.ardoino@gmail.com>
Compile: gcc -lssl genkey.c -o genkey
Usage: ./keygen <numbits>
Ex. ./genkey 1024
*/

#include <stdio.h>
#include <openssl/err.h>
#include <openssl/rsa.h>
#include <openssl/pem.h>
#define SECFILE "sec.pem"
#define PUBFILE "pub.pem"
int main(int argc, char *argv[])
{
	 RSA *key;
 	FILE *fp;
 	int keylen=0;
 	if(argc!=2)
 	{
  		fprintf(stderr,"Error: too many/few arguments.\n "
  		"Usage: %s <numbits>\n",argv[0]);
  	exit(0);
 	}
	keylen = atoi(argv[1]);
 	if((key = RSA_generate_key(keylen,3,NULL,NULL)) == NULL)
 	{
  		fprintf(stderr,"%s\n",ERR_error_string(ERR_get_error(),NULL));
  		exit(-1);
 	}
 	if(key->n != NULL){
 		printf("n is ok");
 	}
 	free(key);
 	key = NULL;
 	 if((RSA *)key->n != NULL){
 		printf("n is ok");
 	}else{
 		printf("not ok");
 	}
 	/*
 	if(RSA_check_key(key) < 1)
 	{
 		fprintf(stderr,"Error: Problems while generating RSA Key.\nRetry.\n");
 		exit(-1);
 	}
 	fp=fopen(SECFILE,"w");
 	if(PEM_write_RSAPrivateKey(fp,key,NULL,NULL,0,0,NULL) == 0)
 	{
  		fprintf(stderr,"Error: problems while writing RSA Private Key.\n");
  		exit(-1);
 	}
 	fclose(fp);
 	fp=fopen(PUBFILE,"w");
 	if(PEM_write_RSAPublicKey(fp,key) == 0)
 	{
  		fprintf(stderr,"Error: problems while writing RSA Public Key.\n");
  		exit(-1);
 	}
 	fclose(fp);
 	RSA_free(key);
 	printf("RSA key generated.\nLenght = %d bits.\n",keylen);
 	*/
 	return 0;
}
