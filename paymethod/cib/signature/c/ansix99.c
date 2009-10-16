#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include "cib_api.h"

void ANSIX99(mac_key, buf, len, mac)
char    *mac_key;
char    *buf;
int     len;
char    *mac;
{
int     i,j,k;
char    tmp[20];

    /* MAC/ECB */
    for (i=0; i<8; i++) mac[i]=0;
    for (i=0; i<len; i+=8) {
        /* right-justified with append 0x00 */
        if ((len-i) < 8) {
            memset(tmp, 0x00, 8);
            memcpy(tmp, buf+i, len-i);
            for (j=0; j<8; j++) mac[j]=mac[j]^tmp[j];
            DES(mac_key,mac, mac);
        } else {
            for (j=0; j<8; j++) mac[j]=mac[j]^buf[i+j];
            DES(mac_key,mac, mac);
        }
    }
}




/**

mac_key  
sawBuff 
tmpBuff 
*/
void desEncrypt(char *mac_key,char *sawBuff, char *dBuff ) {
    int i,j;
    char tmp[8];
    char tmpBuff[1024];
    int len = strlen(sawBuff);


    for (i=0; i<len; i+=8) {       
        if ((len-i) < 8) {
            memset(tmp, 0x30, 8);
            memcpy(tmp, sawBuff+i, len-i);              
            DES(mac_key,tmp, tmp);
        } else {
            memcpy(tmp,sawBuff+i,8);            
            DES(mac_key,tmp, tmp);      
        }
        memcpy(tmpBuff+i,tmp,8);
    }   
    bcd_to_asc( dBuff, tmpBuff , i*2 , 1 );
    memset(tmpBuff ,0x00, strlen(tmpBuff));

}

/**
mac_key 
encryptedBuf 
recoveredBuf 
*/
void desDeCrypt(char *mac_key,char *encryptedBuf, char *recoveredBuf ) {
    char tmpBuf[1024];
    char tmp[8];
    int len,i,j;
    char *ptr;
    char endFlag = '|'; 

    asc_to_bcd(tmpBuf, encryptedBuf,strlen(encryptedBuf),1);
        
    len = strlen(tmpBuf);

    for (i=0; i<len; i+=8) {        
        memcpy(tmp,tmpBuf+i,8);         
        _DES(mac_key,tmp, tmp);     
        memcpy(recoveredBuf+i,tmp,8);
    }
   
    ptr = strrchr(recoveredBuf, endFlag);    
    if (ptr){
        memcpy(tmpBuf,recoveredBuf,strlen(encryptedBuf) );
        memset(recoveredBuf,0x00,strlen(recoveredBuf) );
        memcpy(recoveredBuf,tmpBuf,ptr-recoveredBuf+1); 
    }
    memset(tmpBuf ,0x00, strlen(tmpBuf));

}


