/*
* gcc -lcrypto phase.b.c -o phase.b
*/

#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <string.h>
#include <openssl/pem.h>
#include <openssl/err.h>
#include <openssl/rsa.h>
#define MAX 1024
#define uchar unsigned char
#define nn  "6D482F87C9151DA203A2785CE57DD4547AB5F5B79842519DB0A5A8A6C6A2E613734346713B08EC0297797466660859A2F6F92D96E5B1031EDF54FBD4A46606321EC15B9A90FC32E42970752041DAE30252916DDC21BB18B85B6CF5F8316207A0566F8262BABE02D301C8EE3707BCC22861B6947552A59FF139716DA8B9859571"
#define ee "10001"
#define dd "6C027AA7D440C0870EDC97E60914B2B5C48AEF8F4437D7FEE946F247D3EFD142CC1B641629E9098B0E3786AA66923E35E9B02235105441E75388E556281E8663DC7D52A0767DA462B0A7EE4AC8D7CB2DF74D007CE490D91418E37A0E31CD8136A1E091375DBFCD33F369C601897ECF01DA089D4235D8006878B06A5ED86304B9"
 
uchar rconv(uchar a) 
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

void chartohex(uchar *a,int len,uchar *b)   
{
 int i;
       
       
 for(i=0;i<len/2;i++) 
  b[i]=((rconv(a[2*i])<<4)|(rconv(a[2*i+1])));  
  if(len%2)
  b[i]=rconv(a[2*i])<<4;
}
void print(uchar *str,int len)
{
 int i;
 for(i=0;i<len;i++)
  printf("%02x ",str[i]);
 printf("\\n");
}
int main()
{
/*================== make a RSA structure （no p,q） ==================*/
     unsigned char *cleartext,*data,*ciphertext;
     unsigned char *e,*d,*n;
     int ret,flen;
     RSA *rsa;      
     BIGNUM *bnn, *bne, *bnd;
      rsa= RSA_new();
      bnn = BN_new();
      bne = BN_new();
      bnd = BN_new();
      BN_hex2bn(&bnn, nn);
      BN_hex2bn(&bne, ee);
      BN_hex2bn(&bnd, dd);
       rsa->n= bnn;
      rsa->e= bne;
      rsa->d= bnd;
 RSA_print_fp(stdout,rsa,11);
 if((e=malloc(MAX))==NULL)
   {
         printf("Not enough memory to allocate buffer3");
         exit(1);
   }
 if((n=malloc(MAX))==NULL)
   {
         printf("Not enough memory to allocate buffer4");
         exit(1);
   }
 if((d=malloc(MAX))==NULL)
   {
         printf("Not enough memory to allocate buffer5");
         exit(1);
   }
 

/*================== gen key （method 1） ==================*/
/*
     unsigned char *cleartext,*data,*ciphertext;
     int flen,ret;
     RSA *rsa;
 int bits=1024; 
 rsa=RSA_generate_key(bits,65537,NULL,NULL);
*/

/*================== gen key（method 2） ==================*/
/*
     unsigned char *cleartext,*data,*ciphertext;
     int flen,ret;
     RSA *rsa;
 int bits=1024; 
      BIGNUM *bne;
   bne=BN_new();
   ret=BN_set_word(bne,65537);
   rsa=RSA_new();
   ret=RSA_generate_key_ex(rsa,bits,bne,NULL);
   RSA_print_fp(stdout,rsa,11);
   if(ret!=1)
   {
         printf("RSA_generate_key_ex err!\\n");
         return -1;
   }
*/
 if((cleartext=malloc(MAX))==NULL)
   {
         printf("Not enough memory to allocate buffer1");
         exit(1);
   }
 if((ciphertext=malloc(MAX))==NULL)
   {
         printf("Not enough memory to allocate buffer2");
         exit(1);
   }
 if((data=malloc(MAX))==NULL)
   {
         printf("Not enough memory to allocate buffer6");
         exit(1);
   }
 /*==================== test data =====================*/
 chartohex("b4bce427be03e66a735a589ab04c52c4d6f1e115",40,data);

 /*==================== encrypt =====================*/ 
 printf("Begin encrypt...\\n");
 flen = RSA_size(rsa);
 ret = RSA_public_encrypt(flen, data, ciphertext, rsa, RSA_NO_PADDING);
 if (ret < 0)
     {
         printf("Encrypt failed!\\n");
         exit(1);
     }
        printf("Size:%d\\n", ret);
     printf("data:\\n");
 print(data,20);
 printf("\\n");
     printf("CipherText(Hex):\\n");
 print(ciphertext,ret);
        printf("\\n");

 /*==================== decrypt =====================*/
 printf("Begin decrypt...\\n");
      ret = RSA_private_decrypt(flen, ciphertext, cleartext, rsa, RSA_NO_PADDING);
     if (ret < 0)
     {
         printf("Decrypt failed!\\n");
         exit(1);
     }
         printf("Size:%d\\n", ret);
     printf("ClearText:\\n");
 print(cleartext,20);
 printf("\\n");
 /*==================== verify =====================*/
 if(memcmp(data,cleartext,20))
   {
         printf("err!\\n");
         return -1;
   }
   printf("test ok!\\n");
 return 0;
}
 