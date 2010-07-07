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

EVP_PKEY  *shopex_gen_public_key(){
	char *pubkey = "-----BEGIN RSA PUBLIC KEY-----\nMIGHAoGBALagXIxAJkQ7XDnBsWlIXVc8/mrKYN87D2yOdZq9j7B8b1IZEXnobrn9\nnR9NdxSmEfQkYXG3TaTjD5k2BErEOicY7TvoXk3ReQmYv7Milz8mz/f+/eqQq/gK\nKi6VY17lyyF4ZAPcAusdcXYPRWoUerC6KiC33r+9W90eCX0HVrDHAgED\n-----END RSA PUBLIC KEY-----";
	
	EVP_PKEY *pubkey_rsa;
	BIO  *in;
	
	in = BIO_new_mem_buf(pubkey, strlen(pubkey));	
	pubkey_rsa = PEM_read_bio_PUBKEY(in, NULL,NULL, NULL);
	//BIO_free(in);
	return pubkey_rsa;		
}

void test_shopex_gen_public_key(){
	EVP_PKEY *pubkey_rsa;
	int ks;
	RSA *rsa;
	
	pubkey_rsa = shopex_gen_public_key();
	
	rsa = pubkey_rsa->pkey.rsa;
	ks = RSA_size(rsa);
	printf("%d",ks);
	RSA_print_fp(stdout,rsa,11);
}

RSA *shopex_gen_private_key(){
	
	char *prikey = "-----BEGIN RSA PUBLIC KEY-----\nMIGHAoGBALagXIxAJkQ7XDnBsWlIXVc8/mrKYN87D2yOdZq9j7B8b1IZEXnobrn9\nnR9NdxSmEfQkYXG3TaTjD5k2BErEOicY7TvoXk3ReQmYv7Milz8mz/f+/eqQq/gK\nKi6VY17lyyF4ZAPcAusdcXYPRWoUerC6KiC33r+9W90eCX0HVrDHAgED\n-----END RSA PUBLIC KEY-----";
	
	RSA *prikey_rsa;
	BIO  *in;
	char  *passphrase = "";
	
	
	in = BIO_new_mem_buf(prikey, strlen(prikey));	
	prikey_rsa = PEM_read_bio_PrivateKey(in, NULL,NULL, passphrase);
	//BIO_free(in);
	return prikey_rsa;
}

main(){
	
	char *message = "hi ken";
	
	test_shopex_gen_public_key();
	
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
