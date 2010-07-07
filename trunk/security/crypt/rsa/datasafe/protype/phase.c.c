/*
* gcc -lcrypto phase.c.c -o phase.c
*/

#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <string.h>
#include <openssl/bio.h>

#define SECFILE "sec.pem"
#define PUBFILE "pub.pem"
#define READPUB 0
#define READSEC 1

RSA* readpemkeys(int type)
{
  FILE *fp;
  RSA *key=NULL;

  if(type == READPUB) {
    if((fp = fopen(PUBFILE,"r")) == NULL) {
      fprintf(stderr,"Error: Public Key file doesn't exists.\n");
      exit(EXIT_FAILURE);
    }
    if((key = PEM_read_RSAPublicKey(fp,NULL,NULL,NULL)) == NULL) {
      fprintf(stderr,"Error: problems while reading Public Key.\n");
      exit(EXIT_FAILURE);
    }
    fclose(fp);
    return key;
  }
  if(type == READSEC) {
    if((fp = fopen(SECFILE,"r")) == NULL) {
      fprintf(stderr,"Error: Private Key file doesn't exists.\n");
      exit(EXIT_FAILURE);
    }
    if((key = PEM_read_RSAPrivateKey(fp,NULL,NULL,NULL)) == NULL) {
      fprintf(stderr,"Error: problmes while reading Private Key.\n");
      exit(EXIT_FAILURE);
    }
    fclose(fp);
    if(RSA_check_key(key) == -1) {
      fprintf(stderr,"Error: Problems while reading RSA Private Key in '%s' file.\n",SECFILE);
      exit(EXIT_FAILURE);
    } else if(RSA_check_key(key) == 0) {
      fprintf(stderr,"Error: Bad RSA Private Key readed in '%s' file.\n",SECFILE);
      exit(EXIT_FAILURE);
    }
    else
      return key;
  }
  return key;
}

RSA *gen_public_key(){
	char *pubkey = "-----BEGIN RSA PUBLIC KEY-----\nMIGHAoGBALagXIxAJkQ7XDnBsWlIXVc8/mrKYN87D2yOdZq9j7B8b1IZEXnobrn9\nnR9NdxSmEfQkYXG3TaTjD5k2BErEOicY7TvoXk3ReQmYv7Milz8mz/f+/eqQq/gK\nKi6VY17lyyF4ZAPcAusdcXYPRWoUerC6KiC33r+9W90eCX0HVrDHAgED\n-----END RSA PUBLIC KEY-----";
	
	RSA *pubkey_rsa;
	BIO  *in;
	
	in = BIO_new_mem_buf(pubkey, strlen(pubkey));	
	pubkey_rsa = PEM_read_bio_PUBKEY(in, NULL,NULL, NULL);
	
	return pubkey_rsa;		
}

main(){
	
	char *message = "hi ken";
	
	char *pubkey = "-----BEGIN RSA PUBLIC KEY-----MIGHAoGBALagXIxAJkQ7XDnBsWlIXVc8/mrKYN87D2yOdZq9j7B8b1IZEXnobrn9nR9NdxSmEfQkYXG3TaTjD5k2BErEOicY7TvoXk3ReQmYv7Milz8mz/f+/eqQq/gKKi6VY17lyyF4ZAPcAusdcXYPRWoUerC6KiC33r+9W90eCX0HVrDHAgED-----END RSA PUBLIC KEY-----" ;

	char *prikey  = "-----BEGIN RSA PRIVATE KEY-----MIICWwIBAAKBgQC2oFyMQCZEO1w5wbFpSF1XPP5qymDfOw9sjnWavY+wfG9SGRF56G65/Z0fTXcUphH0JGFxt02k4w+ZNgRKxDonGO076F5N0XkJmL+zIpc/Js/3/v3qkKv4CioulWNe5csheGQD3ALrHXF2D0VqFHqwuiogt96/vVvdHgl9B1awxwIBAwKBgHnAPbLVbtgnktEry5uFk499/vHcQJTSCkhe+RHTtSBS9OFmC6aa9Hv+aL+I+g3EC/gYQPZ6M8NCCmYkAtyC0W5FIjA+KB2NCEOgOw5BeiLMsqGOUHxi1HaJ2NvhSX4h8y/xARPLiVqfrhDSOgxVfFQ4Y6WEu796/hwpqJcbbitLAkEA5ZwZORyM+1h+EBmzkFpHcVaqu7Z1DpzC11xQ362UUsrPxcsH0w1+7lGbqkYU/RiE+g4nz8gVpSCmPfnnWsS6DwJBAMud2lIFlIKUJhhNajAFw4JtWu3Ouu3Qg2QIk+PHjWATickXXlenZD+awSmoQpcnrWuGgKD9Ct++DKGSswNstckCQQCZErt7aF385algESJgPC+g5HHSeaNfEyyPkuCVHmLh3IqD3K/iCP9Ji70cLriouwNRXsU1MA5uFcQpUUTnLdFfAkEAh76RjAO4Vw1uut5Gyq6CVvOR898nSTWs7VsNQoUI6rexMLo+5RpC1RHWG8WBuhpznQRVwKix6n6zFmHMrPMj2wJAKxAfmh6q64AaTJeeACAo1+gj0kiQlj4Jq2mMKZbBuw0mpH59lifH3Q9br9NlWt39pEA+JpSQIYxNlGXD8Glzdw==-----END RSA PRIVATE KEY-----";
	
	BIO  *in;
	RSA  *pubkey_rsa,*prikey_rsa;
	char  *passphrase = "";
	int ks=0;
	
	pubkey_rsa = readpemkeys(READPUB);
	ks = RSA_size(pubkey_rsa);
	printf("%d",ks);
	
	/*
	pubkey_rsa = readpemkeys(READPUB);
	ks = RSA_size(pubkey_rsa);
	printf("%d",ks);
	RSA_print_fp(stdout,pubkey_rsa,11);
	
	//printf("%s",pubkey);
/*
	in = BIO_new_mem_buf(pubkey, strlen(pubkey));	
	pubkey_rsa = PEM_read_bio_PUBKEY(in, NULL,NULL, NULL);
	//BIO_free(in);
	
	/*
	ks = RSA_size(pubkey_rsa);
	printf("%d",ks);
	/*
    plain = (unsigned char *)malloc(ks * sizeof(unsigned char));
    cipher = (unsigned char*)malloc(ks * sizeof(unsigned char));
    */
	/*
	in = BIO_new_mem_buf(prikey, strlen(prikey));	
	prikey_rsa = PEM_read_bio_PrivateKey(in, NULL,NULL, passphrase);
	BIO_free(in);
	*/
	
	

}
