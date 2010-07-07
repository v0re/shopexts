#include <stdio.h>
#include <openssl/rsa.h>

unsigned char  rconv(unsigned char a) {
	switch(a){
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

void stringtohex(unsigned char *a,int len,unsigned char *b) {
 int i;       
 for(i=0;i<len/2;i++) 
  b[i]=((rconv(a[2*i])<<4)|(rconv(a[2*i+1])));  
  if(len%2)
  b[i]=rconv(a[2*i])<<4;
}

main(){
	char *pem_key_str = "30818902818100a895c67f8d93f29b6cc895ef0e784d47c7f10c728b206e67cfc6af8a5cf6106037d75298c3973a9c3a97194a6920a881d07ce35a79070433ae021456be7174a575051f865bc42c8f87530c962ce4108db56df08a1dab73c19f660ce692886e3b6f192d137acdd6293318495dee33b13f2052c2bc33fc6b880453be639a04e55302030100013082025b02010002818100a895c67f8d93f29b6cc895ef0e784d47c7f10c728b206e67cfc6af8a5cf6106037d75298c3973a9c3a97194a6920a881d07ce35a79070433ae021456be7174a575051f865bc42c8f87530c962ce4108db56df08a1dab73c19f660ce692886e3b6f192d137acdd6293318495dee33b13f2052c2bc33fc6b880453be639a04e553020301000102818009ad5f519f1ce90646fe54acfe55a58a034e30faba45c850a93aac84f2c5253780bd197eaf6b94efb6e5498c5df083fbbfae0b96fbef8d2c3246e847fa72a4d16ac40fe32278798cc3a0b0fdcb994e5f3b73c9a9b65c116a51a4509bb714fb2d7f115393202df61257931aa3d503a842e4278cbc5e3da728902e23a14ebd16c1024100d5af110db63e645b1aecd11b8923f6c362ebdd66f7fa2f3b782c276905271e86ae0ab3d5a949a7d1d5900cd4a55babf1658979a364e1a7c64027ad9bd6f4d331024100c9f86074f2949d8597fcd9ba4b1365040a2bb67d1b3a555fb3a2cf939f06843513e2558d96a5ea8b3e219aa6fa36cb49e3fe706f4cfe5c71a651a4363f90b7c302401f6b1232d5ecee8c86b4f339fd3c107841ff341a83b38166591104ea681b8c5d791191c7849093e0426a5fb894679a2c70257a810fc02e0ef437e14adc9c3ac102402ae204c0effca411887ad453dcbaea78d81bfdec444ab773d17561338bd15b62d5acf9d34d483f2b7f6ead2284fffb5bddffd92998a46d466834ef73e13d3df7024050c23c1b737f9454b1f112a6fdc9b9273086bec18933c76859bc400b2b8a42a906870e987af943936d1b03644e26530bc6de650b4719f03ba04568baea7fd0e7";
	
	RSA *pub_rsa,*priv_rsa;
	
	unsigned char buf[2048],*p;
	
	
	
	p=buf;
	pub_rsa=d2i_RSAPublicKey(NULL,&p,(long)len);
	len-=(p-buf);
	priv_rsa=d2i_RSAPrivateKey(NULL,&p,(long)len);

	if ((pub_rsa == NULL) || (priv_rsa == NULL))
		ERR_print_errors_fp(stderr);

	RSA_free(pub_rsa);
	RSA_free(priv_rsa);

	}

