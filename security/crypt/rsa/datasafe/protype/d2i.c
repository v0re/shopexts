#include <stdio.h>
#include <openssl/rsa.h>


void print(unsigned char *str,int len)
{
 int i;
 for(i=0;i<len;i++)
  printf("%02x",str[i]);
printf("\n");
}

main(){
	
	RSA *rsa,*pub_rsa,*priv_rsa;
	int len;
	unsigned char buf[2048],*p;

	rsa=RSA_generate_key(1024,RSA_F4,NULL,NULL);

	p=buf;
	
	len=i2d_RSAPublicKey(rsa,&p);	
	len+=i2d_RSAPrivateKey(rsa,&p);

	RSA_free(rsa);
	print()
	p=buf;
	pub_rsa=d2i_RSAPublicKey(NULL,&p,(long)len);
	len-=(p-buf);
	priv_rsa=d2i_RSAPrivateKey(NULL,&p,(long)len);

	if ((pub_rsa == NULL) || (priv_rsa == NULL))
		ERR_print_errors_fp(stderr);

	RSA_free(pub_rsa);
	RSA_free(priv_rsa);

	}

