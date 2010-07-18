#include <stdio.h>
#include <string.h>
#include <openssl/evp.h>
#include <openssl/md5.h>

static char *pt(unsigned char *md);

int main(int argc, char *argv[])
{
         unsigned char *str = "shopex";
         unsigned char md[MD5_DIGEST_LENGTH];
         char *p;

         //EVP_Digest(str, strlen(str), md, NULL, EVP_md5(), NULL);
         //p = pt(md);
		 p = MD5(str,strlen(str),md);
         printf("MD5(%s)= ", str);
         printf("%s\n",md);
		 p = pt(md);
		 printf("%s\n",p);
		 //printf("%s\n", p);
         return 0;
}

static char *pt(unsigned char *md)
{
         int i;
         static char buf[80];
         for (i=0; i<MD5_DIGEST_LENGTH; i++)
         {
                 sprintf(&(buf[i*2]),"%02x",md[i]);
         }
         return(buf);
}
