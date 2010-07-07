#include <stdio.h>
#include <openssl/rsa.h>

/* This is a simple program to generate an RSA private key.  It then
 * saves both the public and private key into a char array, then
 * re-reads them.  It saves them as DER encoded binary data.
 */

void callback(stage,count,arg)
int stage,count;
char *arg;
	{
	FILE *out;

	out=(FILE *)arg;
	fprintf(out,"%d",stage);
	if (stage == 3)
		fprintf(out,"\n");
	fflush(out);
	}

unsigned char rconv(unsigned char a) 
{
 switch(a)
 {
  case '0':return 0;
  case '1':return 1;
  case '2':return 2;
  case '3':return 3;
  case '4':return 4;
  case '5':return 5;
  case '6':return 6;
  case '7':return 7;
  case '8':return 8;
  case '9':return 9;  
  case 'a':return 10;
  case 'b':return 11;
  case 'c':return 12;
  case 'd':return 13;
  case 'e':return 14;
  case 'f':return 15;
  default:return ' ';
 }
}

void chartohex(unsigned char *a,int len,unsigned char *b)   
{
 int i;
       
       
 for(i=0;i<len/2;i++) 
  b[i]=((rconv(a[2*i])<<4)|(rconv(a[2*i+1])));  
  if(len%2)
  b[i]=rconv(a[2*i])<<4;
}

void print(unsigned char *str,int len)
{
 int i;
 for(i=0;i<len;i++)
  printf("%02x",str[i]);
printf("\n");
}

main()
	{
	RSA *rsa,*pub_rsa,*priv_rsa;
	int len;
	unsigned char buf[1024],*p;
	unsigned char *pubkey = "000000000000bc0f8db7e059aebf02000000a859aebf658f8cb70800000014000000bc0f8db708000000460d8cb7ccb258b7c0b258b708000000bc0f8db7010000001c158db700000000";
	unsigned char pubkey_bin_buf[1024],*pubkey_bin;
	rsa=RSA_generate_key(512,RSA_F4,NULL,NULL);

	p=buf;

	/* Save the public key into buffer, we know it will be big enough
	 * but we should really check how much space we need by calling the
	 * i2d functions with a NULL second parameter */
	
	len=i2d_RSAPublicKey(rsa,&p);
	
	print(p,len);		
	//len = strlen(pubkey);	
	pubkey_bin = pubkey_bin_buf;
	chartohex(pubkey,len,pubkey_bin);
	
	pub_rsa = d2i_RSAPublicKey(NULL,&pubkey_bin,(long)len);	
 	RSA_print_fp(stdout,pub_rsa,11);	
/*
	len+=i2d_RSAPrivateKey(rsa,&p);

	printf("The public and private key are now both in a char array\n");
	printf("and are taking up %d bytes\n",len);
	RSA_free(rsa);

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

