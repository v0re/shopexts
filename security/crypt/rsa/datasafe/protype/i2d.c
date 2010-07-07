#include <stdio.h>
#include <openssl/rsa.h>

main(){
	
	RSA *rsa;
	int i,len;
	unsigned char buf[2048],*p;

	rsa=RSA_generate_key(1024,RSA_F4,NULL,NULL);

	p=buf;
	
	len=i2d_RSAPublicKey(rsa,&p);	
	len+=i2d_RSAPrivateKey(rsa,&p);

	RSA_free(rsa);
		
	for(i=0;i<len;i++)   printf("%02x",buf[i]);
	printf("\n");
}


