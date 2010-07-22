/*
gcc -lssl libdatasafe.c  -fPIC -shared -o libdatasafe.so &&
cp libdatasafe.so /usr/lib
Written by Ken Xu (Kyle<xuqinyong@gmail.com>)
Copyright (C) 2010, ShopEx. 
All rights reserved.
*/
#include <stdio.h>
#include <string.h>
#include <openssl/rsa.h>
#include <openssl/pem.h>
#include <openssl/sha.h>
#include <openssl/hmac.h>
#include <openssl/evp.h>
#include <openssl/bio.h>
#include <openssl/buffer.h>
#include <openssl/md5.h>
#include <syslog.h>
#include <assert.h>

#include "datasafe_api.h"

#define MAX_FILENAME_LEN 256

static RSA *shopex_pubkey,*shopex_privkey,*user_pubkey,*user_priv_key;
*user_priv_key = NULL;

int is_encrypted(char *filename){
    int len = 0;
    char ext[3]  = {'\0'};
    
    len = strlen(filename);
    memcpy(ext,filename + (len -2),2);

    if( strcmp(ext,"en") == 0 ){
        return 0;
    }else{
        return -1;
    }
}

void shopex_read_line(char *filename,int line_no,char **output,size_t *output_len){
    FILE * fp;
    char * line = NULL;
    size_t len = 0;
    ssize_t read;
    int i = 1;
    
    fp = fopen(filename, "r");
    if (fp == NULL)
        exit(EXIT_FAILURE);
    while ((read = getline(&line, &len, fp)) != -1) {
        if(i == line_no){
            line[read] = '\0';
            *output = line;
            *output_len = read;
            break;
        }
        i++;
    }
}



void base64_encode(unsigned char *input, int length,char *output, int *output_len){
    BIO *bmem, *b64;
    BUF_MEM *bptr;
    char *buff;    
    
    b64 = BIO_new(BIO_f_base64());
    BIO_set_flags(b64, BIO_FLAGS_BASE64_NO_NL);
    bmem = BIO_new(BIO_s_mem());
    b64 = BIO_push(b64, bmem);
    BIO_write(b64, input, length);
    BIO_flush(b64);
    BIO_get_mem_ptr(b64, &bptr);
    
    //buff = (char *)malloc(bptr->length);
    memcpy(output, bptr->data, bptr->length);
    output[bptr->length] = '\0';
    
    *output_len = bptr->length;
        
    BIO_free_all(b64);
}

void  base64_decode(unsigned char *input, int length,char *output, int *output_len){
    BIO *b64, *bmem;
    char *buffer;
    int max_len = (length * 6 + 7) / 8;
    int len;
        
    b64 = BIO_new(BIO_f_base64());
    BIO_set_flags(b64, BIO_FLAGS_BASE64_NO_NL);
    bmem = BIO_new_mem_buf(input, length);
    bmem = BIO_push(b64, bmem);
    
    len = BIO_read(bmem, output, max_len);
    output[len] = 0;
    *output_len = len;
    
    BIO_free_all(bmem);
}

void get_shopex_key(RSA **pubkey,RSA **privkey){
    unsigned char *pem_key_str = "MIGJAoGBAKz8scCXFg2O2r2sMsic40hSgHw1q52LUAvEHDH4S5pgflNjs8NfJKOjZmnkTpxI+eLmGKqPPWg7SF7YbUMmmTXvhuTWQF9OcXhIxzIUVFwQKZEWSgZyoaqwcy3XF6sIf7oFDRWfkIY5RCp03GdM0IjGK3lDIdfh0p6wSjTdfvvhAgMBAAEwggJcAgEAAoGBAKz8scCXFg2O2r2sMsic40hSgHw1q52LUAvEHDH4S5pgflNjs8NfJKOjZmnkTpxI+eLmGKqPPWg7SF7YbUMmmTXvhuTWQF9OcXhIxzIUVFwQKZEWSgZyoaqwcy3XF6sIf7oFDRWfkIY5RCp03GdM0IjGK3lDIdfh0p6wSjTdfvvhAgMBAAECgYBo1D1Xq3dWwgI2vPqNbd2h/zUTkGauczUP3EkF0yTlqaIEIMBYHfkTHTs74nns5aBg6vV5rpIU7w/9QgR8lBB1it3g6QU8RWdLG1cpckEL8LLPPWPIUOTSaId2BAeIU3Q0NOBc0sWO1pUTvYBGykQW9LYsP3254yIbc+5aQhwjAQJBANUh5TA45sMvpK+ZoRd3rWTQMU3Ted2/MCsGknPSPCk9ZxHTknU+q5O8L2kmWuc0b/IrVp4Zi9AUDx9AplRUvjECQQDPx7t6Iaim+jjO5y9FcKQPnFW4PRD2s2OffGisrIVAoLoQqNeHW5itltEs/CIT2AyTYRhg4uBIC37gt3kelDyxAkBhNv24Oiwf2apvok6VSrRfaIskqZJLr/pDldLVW46vbN+HhQ6nxfczAsJJXwJVtVheiKAQqyxXs96V7cIwcxrxAkEAihggRRK7yYaCXRkPtOIhV/K6kgGcFaqyapw/4Yuj4IkyQMJGxMKe3bhf+7rzVyb/bLBaiIIhOCDTybyHNkilcQJAHNSMtPgDVvYbzImMaNcpGHKJdkPoChO7W7EpRuCMlT7OMIc8cQIOiTBrHRDzF72NT0p+QfAXUAZxat7s1oqSDw==";
    
    unsigned char de_buf[2048],*p,*start;
    int de_len;


    p=de_buf;
    base64_decode(pem_key_str,strlen(pem_key_str),de_buf,&de_len);
    
    start = p = (unsigned char*)malloc(de_len);
    memcpy(p,de_buf,de_len);
    *pubkey=d2i_RSAPublicKey(NULL,(const unsigned char**)&p,(long)de_len);
    de_len-=(p-start);
    *privkey=d2i_RSAPrivateKey(NULL,(const unsigned char**)&p,(long)de_len);
    
}

RSA *get_shopex_public_key(){
    RSA *pub_rsa,*priv_rsa;
    get_shopex_key(&pub_rsa,&priv_rsa);
    RSA_free(priv_rsa);
    return pub_rsa;
}

RSA *get_shopex_private_key(){
    RSA *pub_rsa,*priv_rsa;
    get_shopex_key(&pub_rsa,&priv_rsa);
    RSA_free(pub_rsa);
    return priv_rsa;
}

RSA *get_user_public_key(char *keyfile_path){
      FILE *fp;
      RSA *key=NULL;
    
    if((fp = fopen(keyfile_path,"r")) == NULL) {
      syslog(LOG_USER|LOG_INFO, "Error: Public Key file doesn't exists.\n");
      exit(EXIT_FAILURE);
    }
    if((key = PEM_read_RSAPublicKey(fp,NULL,NULL,NULL)) == NULL) {
      syslog(LOG_USER|LOG_INFO, "Error: problems while reading Public Key.\n");
      exit(EXIT_FAILURE);
    }
    fclose(fp);
    return key;
}

RSA *get_user_private_key(char *keyfile_path){
    FILE *fp;
    RSA *key=NULL;
    
    if(is_encrypted(keyfile_path) == 0){
        return get_user_private_key_en(keyfile_path);
    }
    
    if((fp = fopen(keyfile_path,"r")) == NULL) {
      syslog(LOG_USER|LOG_INFO, "Error: Private Key file doesn't exists.\n");
      exit(EXIT_FAILURE);
    }
    if((key = PEM_read_RSAPrivateKey(fp,NULL,NULL,NULL)) == NULL) {
      syslog(LOG_USER|LOG_INFO, "Error: problmes while reading Private Key.\n");
      exit(EXIT_FAILURE);
    }
    fclose(fp);
    if(RSA_check_key(key) == -1) {
      syslog(LOG_USER|LOG_INFO, "Error: Problems while reading RSA Private Key in  file.\n");
      exit(EXIT_FAILURE);
    } else if(RSA_check_key(key) == 0) {
      syslog(LOG_USER|LOG_INFO, "Error: Bad RSA Private Key readed in  file.\n");
      exit(EXIT_FAILURE);
    }
    else
      return key;
}

RSA *get_user_private_key_en(char *source_filename){
    char *file_content = NULL;
    int file_content_len = 0;
    FILE *fp;
    char *output;
    int output_len;
    int de_len;
    int i = 0;
    RSA *priv_rsa;
    char *b64_decode;
    int b64_decode_len = 0;
    char *input = NULL;
    
    if((fp=fopen(source_filename,"rb"))==NULL)
    {
        printf("cant open the file");
        exit(0);
    }
    
    fseek(fp, 0L, SEEK_END);
    file_content_len = ftell(fp);
    fseek(fp, 0L, SEEK_SET);
    file_content = (char *)malloc(file_content_len);
    fread(file_content, 1, file_content_len, fp );
    file_content[file_content_len] = '\0';
    //fclose(fp);
    
    shopex_conf_rsa_decrypt(file_content,file_content_len,&output,&output_len);
    output_len = output_len > strlen(output) ?  strlen(output) :  output_len;
    b64_decode = (char *)malloc(output_len);
    input = (char *)malloc(output_len);
    memcpy(input,output,output_len);
    base64_decode(input,output_len,b64_decode,&b64_decode_len);

    priv_rsa=d2i_RSAPrivateKey(NULL,(const unsigned char**)&b64_decode,(long)b64_decode_len);
    if(RSA_check_key(priv_rsa) == -1) {
      syslog(LOG_USER|LOG_INFO, "Error: Problems while reading RSA Private Key in  file.\n");
      exit(EXIT_FAILURE);
    } else if(RSA_check_key(priv_rsa) == 0) {
      syslog(LOG_USER|LOG_INFO, "Error: Bad RSA Private Key readed in  file.\n");
      exit(EXIT_FAILURE);
    }
    else
      return priv_rsa;

}


void shopex_read_conf_file(char *filename,char **output,int *output_len){
     FILE * fp;
     int len;
     char * buffer = NULL;
     char *de_buffer = NULL;
     int de_len = 0;
     
     fp = fopen(filename, "r");
     if (fp == NULL) {
        syslog(LOG_USER|LOG_INFO, "read shopex config file failure");
        exit(EXIT_FAILURE);
     }
     fseek(fp, 0L, SEEK_END);
     len = ftell(fp);
     fseek(fp, 0L, SEEK_SET);
     buffer = (char *)malloc(len);
     fread( buffer, 1, len, fp );
     buffer[len] = '\0';
     
     if( is_encrypted(filename) == 0 ){
        shopex_conf_rsa_decrypt(buffer,len,&de_buffer,&de_len);     
        *output = de_buffer;
        *output_len = de_len;
     }else{
        *output = buffer;
        *output_len = len;
    }
          
     //fclose(fp);
}


void shopex_rsa_encrypt(RSA *pub_rsa,char *input,int input_len,char **output,int *output_len){    
    int ks,chunk_len,rsa_ret_buf_len,ret_len,ret_len_total,en_len;
    char *rsa_ret_buf_p,*rsa_ret_buf;
    char *plain_p,*plain;
    char *cipher_p,*cipher;
    char *b64_buf_p,*b64_buf;
    char *input_p;
    char *output_buf;

    ret_len = ret_len_total = 0;

    input_p = input;
    if((int)input_len < 0 ){
        input_len = strlen(input);
    }
    ks = RSA_size(pub_rsa);
    chunk_len = input_len > (ks - 11) ? ks - 11 : input_len;
    rsa_ret_buf_len = ( ( input_len / chunk_len + 1) * ks );
    rsa_ret_buf_p = rsa_ret_buf = (char *)malloc(rsa_ret_buf_len * sizeof(char));
    memset(rsa_ret_buf_p,'\0', rsa_ret_buf_len + 1);
    plain_p = plain = (char *)malloc(ks * sizeof(char));
    cipher_p = cipher = (char *)malloc(ks * sizeof(char));
    
    while(input - input_p < input_len) {
        memset(plain,'\0',ks + 1);
        memset(cipher, '\0', ks + 1);
        memcpy(plain, input, chunk_len);
        ret_len = RSA_public_encrypt(chunk_len, plain, cipher, pub_rsa, RSA_PKCS1_PADDING);
        memcpy(rsa_ret_buf,cipher,ret_len);
        plain = plain_p;
        cipher = cipher_p;
        ret_len_total += ret_len;
        input += chunk_len;
        rsa_ret_buf += ret_len;
    }
    
    b64_buf_p = b64_buf = (char *)malloc(ret_len_total * 1.5 );
    base64_encode(rsa_ret_buf_p,ret_len_total,b64_buf,&en_len);
    output_buf = (char *)malloc(en_len);
    memcpy(output_buf,b64_buf_p,en_len);
    output_buf[en_len] = '\0';
    
    *output = output_buf; 
    *output_len = en_len;
    
   /*
     free(b64_buf_p);
    b64_buf_p =NULL;
    
    free(cipher_p);
    cipher_p = NULL;
    free(plain_p);
    plain_p = NULL;
    free(rsa_ret_buf_p);
    rsa_ret_buf_p = NULL;
    */
    RSA_free(pub_rsa);
}

void shopex_rsa_decrypt(RSA *priv_rsa,char *input,int input_len,char **output,int *output_len){
    int de_len,ks,ret_len,ret_len_total,remaining;
    char *rsa_ret_buf_p,*rsa_ret_buf;
    char *cipher_p,*cipher;
    char *de_buf_p,*de_buf;
    char *plain_p,*plain;
    char *output_buf;
    
    ret_len = ret_len_total = remaining = 0;
    
    if((int)input_len < 0 ){
        input_len = strlen(input);
    }    
    de_buf_p = de_buf = (char *)malloc(input_len * sizeof(char));
    base64_decode(input,input_len,de_buf,&de_len);    
    de_buf = de_buf_p;
    
    rsa_ret_buf_p = rsa_ret_buf = (char *)malloc(input_len * sizeof(char));
    
    ks = RSA_size(priv_rsa);
    cipher_p = cipher = (char*)malloc(ks * sizeof(char));
    plain_p = plain = (char*)malloc(ks * sizeof(char));
    while(de_buf - de_buf_p < de_len) {
        memset(cipher, '\0', ks);
        memset(plain, '\0', ks);
        memcpy(cipher,de_buf,ks);
        ret_len = RSA_private_decrypt(ks, cipher, plain, priv_rsa, RSA_PKCS1_PADDING);
        memcpy(rsa_ret_buf,plain,ret_len);
        ret_len_total += ret_len;
        rsa_ret_buf += ret_len;
        cipher = cipher_p;
        plain = plain_p;
        de_buf += ks;    
    }    
    
    output_buf = (char *)malloc(ret_len_total * sizeof(char));    
    memcpy(output_buf,rsa_ret_buf_p,ret_len_total);
    output_buf[ret_len_total] = '\0';
    
    *output = output_buf;
    //attention due to the segment algorithm the output_len may not be correct
    *output_len = ret_len_total;
    /*
    free(de_buf_p);
    free(rsa_ret_buf_p);
    free(cipher_p);
    free(plain_p);
    */
    RSA_free(priv_rsa);
}

void shopex_read_pubkeypos_in_file(char *config_filename,char **file_pos){
    char *output,*output_p;
    int len = 0;
    
    shopex_read_conf_file(config_filename,&output,&len);
    
    output_p = output;
    while(*output != '\n' && len < MAX_FILENAME_LEN){
        output++;
        len++;        
    }
    
    *output = '\0';   
    strcpy(*file_pos,output_p);
    
    if(output){
        free(output_p);
        output_p = output = NULL;
    }    
}

void shopex_read_privkeypos_in_file(char *config_filename,char **file_pos){
    char *output,*start,*end;
    int len = 0;
    
    shopex_read_conf_file(config_filename,&output,&len);
        
    start = strstr(output,"\n");
    end = strstr(++start,"\n");
    *end = '\0';
    strcpy(*file_pos,start);
    
    if(output){
        free(output);
        output = start = end = NULL;
    }    
}


void shopex_conf_rsa_encrypt(char *input,int input_len,char **output,int *output_len){
    RSA *pub_rsa;
    pub_rsa = get_shopex_public_key();
    shopex_rsa_encrypt(pub_rsa,input,input_len,output,output_len);    
}

void shopex_conf_rsa_decrypt(char *input,int input_len,char **output,int *output_len){
    RSA *priv_rsa;
    priv_rsa = get_shopex_private_key();
    shopex_rsa_decrypt(priv_rsa,input,input_len,output,output_len);    
}

void shopex_data_rsa_encrypt(char *config_file,char *input,int input_len,char * *output,int *output_len){
    RSA *pub_rsa;
    char *keyfile_path = NULL;
    
    keyfile_path = (char *)malloc(MAX_FILENAME_LEN);
    assert( keyfile_path != NULL );
    memset(keyfile_path,'\0',MAX_FILENAME_LEN);
    shopex_read_pubkeypos_in_file(config_file,&keyfile_path);
    pub_rsa = get_user_public_key(keyfile_path);
    shopex_rsa_encrypt(pub_rsa,input,input_len,output,output_len);    
    
    if(keyfile_path){
        free(keyfile_path);
        keyfile_path = NULL;
    }
}

void shopex_data_rsa_decrypt(char *config_file,char *input,int input_len,char **output,int *output_len){
    char *keyfile_path = NULL;
    char *de_buf = NULL;
    int de_buf_len = 0;
    
    if( *user_priv_key  == NULL){
	    keyfile_path = (char *)malloc(MAX_FILENAME_LEN);
	    assert( keyfile_path != NULL );
	    memset(keyfile_path,'\0',MAX_FILENAME_LEN);
	    shopex_read_privkeypos_in_file(config_file,&keyfile_path);
	    user_priv_key = get_user_private_key(keyfile_path);
	    if(keyfile_path != NULL){
        	free(keyfile_path);
        	keyfile_path = NULL;
    	}
    }
    //shopex_rsa_decrypt(priv_rsa,input,input_len,&de_buf,&de_buf_len);    
    de_buf = "ken";
    de_buf_len = 3;
    if(de_buf_len){
       memcpy( *output,de_buf,de_buf_len);
       *output_len = de_buf_len;
    }
    

    /*
    if(de_buf != NULL){
        free(de_buf);
        de_buf = NULL;
    }
    */
}





int shopex_checkfile_md5(char *allowfile,char *allowfile_md5){
    FILE * fp;
    int len,i;
    unsigned char * buffer = NULL;
    unsigned char md[MD5_DIGEST_LENGTH] = {'\0'};
    unsigned char buf[MD5_DIGEST_LENGTH * 2] = {'\0'};
    
    fp = fopen(allowfile, "r");
    if (fp == NULL) {
        syslog(LOG_USER|LOG_INFO, "read php file  failure");
        exit(EXIT_FAILURE);
    }
    fseek(fp, 0L, SEEK_END);
    len = ftell(fp);
    fseek(fp, 0L, SEEK_SET);
    buffer = (char *)malloc(len);
    fread( buffer, 1, len, fp );
    buffer[len] = '\0';
    //fclose(fp);
    
    MD5(buffer,len,md);

    for (i=0; i<MD5_DIGEST_LENGTH; i++) {
        sprintf(&(buf[i*2]),"%02x",md[i]);
    }
    allowfile_md5 = (unsigned char *)allowfile_md5;
    if(strcmp(allowfile_md5,buf) == 0 ){
        return 0;
    }
    return -1;
}

int shopex_is_file_in_allowlist(char *config_filename,char *filename){
    char *output,*output_p;
    int len,buf_len;
    char *pos_start,*pos_end;
    char *cln_pos;
    int i = 0;
    char *buf;
    char *allowfile;
    char *allowfile_md5;
    
    len = buf_len = 0;
    
    shopex_read_conf_file(config_filename,&output,&len);
    output_p = output;
    pos_start = pos_end = output;
    len = strlen(output);
    while((pos_end - output_p) < len && strlen(output) > 0){
        pos_end = strstr(output,"\n");
        if(i > 1){
            buf_len = pos_end - output;
            buf = (char *)malloc(buf_len);
            memset(buf,'\0',buf_len + 1);
            memcpy(buf,output,buf_len);
            buf[buf_len] = '\0';
            cln_pos = strstr(buf,":");
            len = cln_pos - buf;
            allowfile = (char *)malloc(len);
            memcpy(allowfile,buf,len);
            len = buf_len - strlen(cln_pos+1);
            allowfile_md5 = (char *)malloc(len);
            memcpy(allowfile_md5,cln_pos+1,len);
            if ( strcmp(allowfile,filename) != 0  || shopex_checkfile_md5(allowfile,allowfile_md5) != 0 ){
                return -1;
            }
            /*
            free(buf);
            buf = NULL;
            free(allowfile);
            allowfile = NULL;
            free(allowfile_md5);
            allowfile_md5 = NULL;
            */
        }
        output = pos_end + 1;
        i++;
    }

    //free(output_p);
    
    return 0;
}


void test_get_user_key(){
     RSA *pub_rsa,*priv_rsa;
     
     char *pub_keyfile_path  = "/etc/shopex/skomart.com/pub.pem";
     char *priv_keyfile_path = "/etc/shopex/skomart.com/sec.pem";
     pub_rsa = get_user_public_key(pub_keyfile_path);
     priv_rsa = get_user_private_key(priv_keyfile_path);
     
    if ((pub_rsa == NULL) || (priv_rsa == NULL))
        ERR_print_errors_fp(stderr);
    
    RSA_print_fp(stdout,pub_rsa,11);
    RSA_print_fp(stdout,priv_rsa,11);
    
    RSA_free(pub_rsa);
    RSA_free(priv_rsa);
}

void test_get_user_public_key(){
     RSA *pub_rsa;
     
     char *pub_keyfile_path  = "/etc/shopex/skomart.com/pub.pem";
     pub_rsa = get_user_public_key(pub_keyfile_path);
     
    RSA_print_fp(stdout,pub_rsa,11);
    
    RSA_free(pub_rsa);
}

void test_get_user_private_key(){
    RSA *priv_rsa;
     

     char *priv_keyfile_path = "/etc/shopex/skomart.com/sec.pem";

     priv_rsa = get_user_private_key(priv_keyfile_path);
     
    RSA_print_fp(stdout,priv_rsa,11);
    
    RSA_free(priv_rsa);
}



void test_shopex_read_privkeypos_in_file(){
    char *filename;
    char *output;
    
    filename = "/etc/shopex/skomart.com/setting.conf";
    shopex_read_privkeypos_in_file(filename,&output);
    printf("%s\n",output);
    
}

void test_shopex_is_file_in_allowlist(){
    char *filename;
    char *config_filename;
    int ret;
    
    config_filename = "/etc/shopex/skomart.com/setting.conf";
    filename = "/srv/http/security/crypt/rsa/datasafe/test.php";
    
    ret = shopex_is_file_in_allowlist(config_filename,filename);    
    printf("%d\n",ret);
    
    filename = "/srv/http/security/crypt/rsa/datasafe/heihei.php";
    ret = shopex_is_file_in_allowlist(config_filename,filename);    
    printf("%d\n",ret);
}
