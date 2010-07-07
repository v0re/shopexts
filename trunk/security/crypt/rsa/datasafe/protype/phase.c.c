/*
* gcc -lcrypto phase.c.c -o phase.c
*/

#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <string.h>
#include <openssl/bio.h>


main(){
	char *pubkey = "-----BEGIN RSA PUBLIC KEY-----MIGHAoGBALagXIxAJkQ7XDnBsWlIXVc8/mrKYN87D2yOdZq9j7B8b1IZEXnobrn9nR9NdxSmEfQkYXG3TaTjD5k2BErEOicY7TvoXk3ReQmYv7Milz8mz/f+/eqQq/gKKi6VY17lyyF4ZAPcAusdcXYPRWoUerC6KiC33r+9W90eCX0HVrDHAgED-----END RSA PUBLIC KEY-----" ;

	char *prikey  = "-----BEGIN RSA PRIVATE KEY-----MIICWwIBAAKBgQC2oFyMQCZEO1w5wbFpSF1XPP5qymDfOw9sjnWavY+wfG9SGRF56G65/Z0fTXcUphH0JGFxt02k4w+ZNgRKxDonGO076F5N0XkJmL+zIpc/Js/3/v3qkKv4CioulWNe5csheGQD3ALrHXF2D0VqFHqwuiogt96/vVvdHgl9B1awxwIBAwKBgHnAPbLVbtgnktEry5uFk499/vHcQJTSCkhe+RHTtSBS9OFmC6aa9Hv+aL+I+g3EC/gYQPZ6M8NCCmYkAtyC0W5FIjA+KB2NCEOgOw5BeiLMsqGOUHxi1HaJ2NvhSX4h8y/xARPLiVqfrhDSOgxVfFQ4Y6WEu796/hwpqJcbbitLAkEA5ZwZORyM+1h+EBmzkFpHcVaqu7Z1DpzC11xQ362UUsrPxcsH0w1+7lGbqkYU/RiE+g4nz8gVpSCmPfnnWsS6DwJBAMud2lIFlIKUJhhNajAFw4JtWu3Ouu3Qg2QIk+PHjWATickXXlenZD+awSmoQpcnrWuGgKD9Ct++DKGSswNstckCQQCZErt7aF385algESJgPC+g5HHSeaNfEyyPkuCVHmLh3IqD3K/iCP9Ji70cLriouwNRXsU1MA5uFcQpUUTnLdFfAkEAh76RjAO4Vw1uut5Gyq6CVvOR898nSTWs7VsNQoUI6rexMLo+5RpC1RHWG8WBuhpznQRVwKix6n6zFmHMrPMj2wJAKxAfmh6q64AaTJeeACAo1+gj0kiQlj4Jq2mMKZbBuw0mpH59lifH3Q9br9NlWt39pEA+JpSQIYxNlGXD8Glzdw==-----END RSA PRIVATE KEY-----";
	
	BIO  *in;
	EVP_PKEY *pubkey_pem,*prikey_pem;
	char  *passphrase = "";
	
	//printf("%s",pubkey);

	in = BIO_new_mem_buf(pubkey, strlen(pubkey));	
	pubkey_pem = PEM_read_bio_PUBKEY(in, NULL,NULL, NULL);
	//BIO_free(in);
	
	 RSA_print_fp(stdout,pubkey_pem,11);
	/*
	in = BIO_new_mem_buf(prikey, strlen(prikey));	
	prikey_pem = PEM_read_bio_PrivateKey(in, NULL,NULL, passphrase);
	BIO_free(in);
	*/
	
	

}
