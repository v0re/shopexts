/*
*gcc openssl_rsa.c -o openssl_rsa -lcrypto
*/
#include <openssl/rsa.h>
#include <openssl/pem.h>
#include<openssl/evp.h>
#include <string.h>
EVP_PKEY* open();
EVP_PKEY* Popen();
void print(const char *promptStr,unsigned char * data,int len);
void generate_key();
void  main(){ 
	EVP_PKEY* pkey=NULL,*pkey1=NULL;
	generate_key();
	pkey1=Popen();
	if(pkey1==NULL)	{  
		printf("encrypt by public key failed\n");
		exit(-1);
	}
	pkey=open();
	if(pkey==NULL){  
		exit(-1);
	}
	int len=-1;
	char plainText[]="[For test to public/private key encryption/decryption]";
	char encData[512];
	char decData[512];
	OpenSSL_add_all_ciphers();
	len=EVP_PKEY_encrypt((unsigned char *)encData,(const unsigned char *)plainText,sizeof(plainText),	pkey1);
	if(len==-1){
		printf("EVP_PKEY_encrypt failed\n");
		exit(-1);
	}
	print("encrypted data : ",(unsigned char *)encData,len);
	len=EVP_PKEY_decrypt(
		(unsigned char *)decData,
		(const unsigned char *)encData,
		len,
		pkey
	);
	if(len==-1){
		printf("EVP_PKEY_decrypt failed\n");
		exit(-1);
	}
	print("decrypted data : ",(unsigned char *)decData,len);
	printf("\nmessage:[length=%dbit]:%s\n",len,decData);
	EVP_PKEY_free(pkey);
}

void generate_key(){ 
	RSA *rsa=NULL;
	printf("generate key pair...\n");
	rsa = RSA_generate_key(1024, RSA_F4, NULL, NULL);
	if(rsa==NULL)	{
		printf("gen rsa error\n");
		exit(-1);
	}
	// public key
	BIO *bp = BIO_new(BIO_s_file());
	if(BIO_write_filename(bp, "openssl_rsa.pub.key")<=0){
		printf("write error\n");
		exit(-1);
	}
	if(PEM_write_bio_RSAPublicKey(bp, rsa)!=1)	{
		printf("write public key error\n");
		exit(-1);
	}  
	printf("save public key ok\n");
	BIO_free_all(bp);
	
	char passwd[]="1234";
	// private key
	bp = BIO_new_file("openssl_rsa.pri.key", "w+");
	if(PEM_write_bio_RSAPrivateKey(bp, rsa, EVP_des_ede3(), (unsigned char*)passwd, 4, NULL, NULL)!=1){
		printf("write private key error\n");
		exit(-1);
	}
	printf("save private key ok\n");
	BIO_free_all(bp); 
}

EVP_PKEY* open(){
	EVP_PKEY* pkey=NULL;
	RSA *rsaK;
	OpenSSL_add_all_algorithms();
	BIO *BP=BIO_new(BIO_s_file());;
	char FileName[]="openssl_rsa.pri.key";
	BIO_read_filename(BP,FileName); 
	char PSW[]="1234";
	rsaK=PEM_read_bio_RSAPrivateKey(BP,NULL,NULL,PSW);
	if(rsaK==NULL){
		printf("read private key failed!\n");
		BIO_free(BP);
		RSA_free(rsaK);
		exit(-1);
	}
	printf("read private key ok\n");
	pkey=EVP_PKEY_new();
	if(NULL==pkey)	{
		printf("EVP_PKEY_new failed\n");
		RSA_free(rsaK);
		return NULL;
	}
	EVP_PKEY_assign_RSA(pkey,rsaK);
	return pkey;
}

EVP_PKEY* Popen(){
	EVP_PKEY* pkey=NULL;
	RSA *rsaK;
	OpenSSL_add_all_algorithms();
	BIO *BP=BIO_new(BIO_s_file());;
	char FileName[]="openssl_rsa.pub.key";
	BIO_read_filename(BP,FileName); 
	rsaK=PEM_read_bio_RSAPublicKey(BP,NULL,NULL,NULL);
	if(rsaK==NULL)	{
		printf("read public key failed\n");
		BIO_free(BP);
		RSA_free(rsaK);
		exit(-1);
	}
	printf("read public key ok!\n");
	pkey=EVP_PKEY_new();
	if(NULL==pkey)	{
		printf("EVP_PKEY_new failed\n");
		RSA_free(rsaK);
		return NULL;
	}
	EVP_PKEY_assign_RSA(pkey,rsaK);
	return pkey;
}

void print(const char *promptStr,unsigned char * data,int len){
	int i;
	printf("\n===%s[length=%d bit]======\n",promptStr,len);
	for(i=0;i<len;i++)printf("%02x",data[i]);
	printf("\n======================================\n");
}
