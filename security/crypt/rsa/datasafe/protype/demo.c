/*

	Simple RSA encrypting/decrypting tool.
	Coded by binduck - <binduck@coder.hu>
	Compile: gcc -lssl demo.c -o demo
	Usage: ./demo -h    for help

*/

#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <string.h>
#include <openssl/pem.h>
#include <openssl/err.h>
#include <openssl/rsa.h>

#define SECFILE "sec.pem"
#define PUBFILE "pub.pem"

#define READPUB 0
#define READSEC 1

#define HELP 'h'
#define GENKEY 'g'
#define CIPHER 'c'
#define DECIPHER 'd'

RSA* readpemkeys(int type);
void genkey(int size);
int rsa_encrypt(RSA *key, unsigned char *plain, int len, unsigned char **cipher);
int rsa_decrypt(RSA *key, unsigned char *cipher, int len, unsigned char **plain);

int main(int argc,char *argv[])
{
  int ch,size=0,len=0,ks=0;
  RSA *key=NULL;
  FILE *fpin=NULL, *fpout=NULL;
  char char_opt[] = {HELP,GENKEY,CIPHER,DECIPHER};
  unsigned char *cipher=NULL,*plain=NULL;

  printf("RSA Cipher/Decipher\n");
  printf("Coded by Paolo Ardoino - <ardoino.gnu@disi.unige.it>\n\n");
  if (argc == 1)
    printf("'%s -h' for help.\n", *argv);
  while((ch=getopt(argc,argv,char_opt)) != -1) {
   switch(ch) {
     case HELP:
       printf("%s -g <num_bits>\n", *argv);
       printf("\tgenerates RSA keys and save them in PEM format.(1024 or 2048 are strongly suggested).\n");
       printf("%s -c <file_in> <file_out>\n", *argv);
       printf("\tcrypts datas in 'file_in' and stores output in 'file_out'.\n");
       printf("%s -d <file_in> <file_out>\n", *argv);
       printf("\tdecript datas in 'file_in' and stores output in 'file_out'.\n");
     break;

     case GENKEY:
       if(argc != 3) {
         fprintf(stderr,"Error: check arguments.\n'%s -h' for help.\n",*argv);
         exit(EXIT_FAILURE);
       }
       ks = atoi(*(argv + 2));
       printf("Generating RSA keys [size = %d bits]\n", ks);
       genkey(ks);
       printf("Private Key saved in %s file.\n", SECFILE);
       printf("Public Key saved in %s file.\n", PUBFILE);
       printf("Done.\n");
     break;

     case CIPHER:
       if(argc != 4) {
         fprintf(stderr,"Error: check arguments.\n'%s -h' for help.\n", *argv);
         exit(EXIT_FAILURE);
       }
       key = readpemkeys(READPUB);
       if(!(fpin = fopen(*(argv + 2), "r"))) {
         fprintf(stderr, "Error: Cannot locate input file.\n");
         exit(EXIT_FAILURE);
       }
       fpout = fopen(*(argv + 3), "w");
       ks = RSA_size(key);
       plain = (unsigned char *)malloc(ks * sizeof(unsigned char));
       cipher = (unsigned char*)malloc(ks * sizeof(unsigned char));
       printf("Encrypting '%s' file.\n",*(argv + 2));
       while(!feof(fpin)) {
         memset(plain,'\0',ks + 1);
         memset(cipher, '\0', ks + 1);
         len = fread(plain, 1, ks - 11, fpin);
         size = rsa_encrypt(key, plain, len, &cipher);
         fwrite(cipher, 1, size, fpout);
       }
       fclose(fpout);
       fclose(fpin);
       free(cipher);
       free(plain);
       RSA_free(key);
       printf("Done.\n");
     break;

     case DECIPHER:
       if(argc != 4) {
         fprintf(stderr,"Error: check arguments.\n'%s -h' for help.\n", *argv);
         exit(EXIT_FAILURE);
       }
       key = readpemkeys(READSEC);
       if(!(fpin = fopen(*(argv + 2), "r"))) {
         fprintf(stderr, "Error: Cannot locate input file.\n");
         exit(EXIT_FAILURE);
       }
       fpout = fopen(*(argv + 3), "w");
       ks = RSA_size(key);
       cipher = (unsigned char*)malloc(ks * sizeof(unsigned char));
       plain = (unsigned char*)malloc(ks * sizeof(unsigned char));
       printf("Decrypting '%s' file.\n", *(argv + 2));
       while(!feof(fpin)) {
         memset(cipher, '\0', ks);
         memset(plain, '\0', ks);
         if ((len = fread(cipher, 1, ks, fpin)) == 0)
 	  break;
         size = rsa_decrypt(key, cipher, len, &plain);
         fwrite(plain, 1, size, fpout);
       }
       fclose(fpout);
       fclose(fpin);
       free(plain);
       free(cipher);
       RSA_free(key);
       printf("Done.\n");
      break;
    }
  }
  return 0;
}

void genkey(int size)
{
  RSA *key=NULL;
  FILE *fp;

  if((key = RSA_generate_key(size,3,NULL,NULL)) == NULL) {
    fprintf(stderr,"%s\n",ERR_error_string(ERR_get_error(),NULL));
    exit(EXIT_FAILURE);
  }
  if(RSA_check_key(key) < 1) {
    fprintf(stderr,"Error: Problems while generating RSA Key.\nRetry.\n");
    exit(EXIT_FAILURE);
  }
  fp=fopen(SECFILE,"w");
  if(PEM_write_RSAPrivateKey(fp,key,NULL,NULL,0,0,NULL) == 0) {
    fprintf(stderr,"Error: problems while writing RSA Private Key.\n");
    exit(EXIT_FAILURE);
  }
  fclose(fp);
  fp=fopen(PUBFILE,"w");
  if(PEM_write_RSAPublicKey(fp,key) == 0) {
    fprintf(stderr,"Error: problems while writing RSA Public Key.\n");
    exit(EXIT_FAILURE);
  }
  fclose(fp);
  RSA_free(key);
  return;
}

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

int rsa_encrypt(RSA *key, unsigned char *plain, int len, unsigned char **cipher)
{
  int clen=0;

  srand(time(NULL));
  if((clen = RSA_public_encrypt(len, plain, *cipher, key, RSA_PKCS1_PADDING)) == -1) {
    fprintf(stderr, "%s\n", ERR_error_string(ERR_get_error(), NULL));
    exit(EXIT_FAILURE);
  } else
    return clen;
}

int rsa_decrypt(RSA *key, unsigned char *cipher, int len, unsigned char **plain)
{
  int plen=0;

  if((plen = RSA_private_decrypt(len, cipher, *plain, key, RSA_PKCS1_PADDING)) == -1) {
    fprintf(stderr, "%s\n", ERR_error_string(ERR_get_error(), NULL));
    exit(EXIT_FAILURE);
  } else
    return plen;
}