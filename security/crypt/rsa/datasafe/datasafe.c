/*
  +----------------------------------------------------------------------+
  | PHP Version 5                                                        |
  +----------------------------------------------------------------------+
  | Copyright (c) 1997-2008 The PHP Group                                |
  +----------------------------------------------------------------------+
  | This source file is subject to version 3.01 of the PHP license,      |
  | that is bundled with this package in the file LICENSE, and is        |
  | available through the world-wide-web at the following url:           |
  | http://www.php.net/license/3_01.txt                                  |
  | If you did not receive a copy of the PHP license and are unable to   |
  | obtain it through the world-wide-web, please send a note to          |
  | license@php.net so we can mail you a copy immediately.               |
  +----------------------------------------------------------------------+
  | Author:                                                              |
  +----------------------------------------------------------------------+
*/

/* $Id: header 252479 2008-02-07 19:39:50Z iliaa $ */

#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include "php.h"
#include "php_ini.h"
#include "ext/standard/info.h"
#include "ext/standard/base64.h"
#include "ext/standard/md5.h"
#include "php_datasafe.h"
#include "protype/datasafe_api.h"
#include <openssl/rsa.h>
#include <openssl/pem.h>
#include <openssl/bio.h>
#include <openssl/buffer.h>
#include <openssl/evp.h>
#include <openssl/crypto.h>
#include <openssl/ssl.h>
#include <string.h>

/* If you declare any globals in php_datasafe.h uncomment this:
ZEND_DECLARE_MODULE_GLOBALS(datasafe)
*/

/* True global resources - no need for thread safety here */
static int le_datasafe;

/* {{{ datasafe_functions[]
 *
 * Every user visible function must have an entry in datasafe_functions[].
 */
const zend_function_entry datasafe_functions[] = {
	PHP_FE(confirm_datasafe_compiled,	NULL)		/* For testing, remove later. */
	PHP_FE(shopex_data_encrypt_ex,	NULL)		/* it will be use rsa . */
	PHP_FE(shopex_data_decrypt_ex,	NULL)		/* it will be use rsa. */
	PHP_FE(shopex_public_encrypt,NULL)
	PHP_FE(shopex_get_user_private_key,NULL)
	PHP_FE(shopex_gen_keypair,NULL)
	{NULL, NULL, NULL}	/* Must be the last line in datasafe_functions[] */
};
/* }}} */

/* {{{ datasafe_module_entry
 */
zend_module_entry datasafe_module_entry = {
#if ZEND_MODULE_API_NO >= 20010901
	STANDARD_MODULE_HEADER,
#endif
	"datasafe",
	datasafe_functions,
	PHP_MINIT(datasafe),
	PHP_MSHUTDOWN(datasafe),
	PHP_RINIT(datasafe),		/* Replace with NULL if there's nothing to do at request start */
	PHP_RSHUTDOWN(datasafe),	/* Replace with NULL if there's nothing to do at request end */
	PHP_MINFO(datasafe),
#if ZEND_MODULE_API_NO >= 20010901
	"0.1", /* Replace with version number for your extension */
#endif
	STANDARD_MODULE_PROPERTIES
};
/* }}} */

#ifdef COMPILE_DL_DATASAFE
ZEND_GET_MODULE(datasafe)
#endif

/* {{{ PHP_INI
 */
/* Remove comments and fill if you need to have entries in php.ini
PHP_INI_BEGIN()
    STD_PHP_INI_ENTRY("datasafe.global_value",      "42", PHP_INI_ALL, OnUpdateLong, global_value, zend_datasafe_globals, datasafe_globals)
    STD_PHP_INI_ENTRY("datasafe.global_string", "foobar", PHP_INI_ALL, OnUpdateString, global_string, zend_datasafe_globals, datasafe_globals)
PHP_INI_END()
*/
/* }}} */

/* {{{ php_datasafe_init_globals
 */
/* Uncomment this function if you have INI entries
static void php_datasafe_init_globals(zend_datasafe_globals *datasafe_globals)
{
	datasafe_globals->global_value = 0;
	datasafe_globals->global_string = NULL;
}
*/
/* }}} */


static RSA * shopex_get_shopex_public_key();
static RSA* shopex_get_shopex_private_key();
static RSA* shopex_get_user_public_key();
static RSA* shopex_get_user_private_key(char *keyfile_path);
static RSA* shopex_get_user_private_key_en(char *filename);
static void shopex_rsa_encrypt(RSA *pkey,char *data,int data_len,char **output,int *output_len);
static void shopex_rsa_decrypt(RSA *pkey,char *data,int data_len,char **output,int *output_len);
static void shopex_get_config(char *filename,char **output,int *output_len);


/* {{{ PHP_MINIT_FUNCTION
 */
PHP_MINIT_FUNCTION(datasafe)
{
	/* If you have INI entries, uncomment these lines 
	REGISTER_INI_ENTRIES();
	*/
	return SUCCESS;
}
/* }}} */

/* {{{ PHP_MSHUTDOWN_FUNCTION
 */
PHP_MSHUTDOWN_FUNCTION(datasafe)
{
	/* uncomment this line if you have INI entries
	UNREGISTER_INI_ENTRIES();
	*/
	return SUCCESS;
}
/* }}} */

/* Remove if there's nothing to do at request start */
/* {{{ PHP_RINIT_FUNCTION
 */
PHP_RINIT_FUNCTION(datasafe)
{
	return SUCCESS;
}
/* }}} */

/* Remove if there's nothing to do at request end */
/* {{{ PHP_RSHUTDOWN_FUNCTION
 */
PHP_RSHUTDOWN_FUNCTION(datasafe)
{
	return SUCCESS;
}
/* }}} */

/* {{{ PHP_MINFO_FUNCTION
 */
PHP_MINFO_FUNCTION(datasafe)
{
	php_info_print_table_start();
	php_info_print_table_header(2, "datasafe support", "enabled");
	php_info_print_table_end();

	/* Remove comments if you have entries in php.ini
	DISPLAY_INI_ENTRIES();
	*/
}
/* }}} */


/* Remove the following function when you have succesfully modified config.m4
   so that your module can be compiled into PHP, it exists only for testing
   purposes. */

/* Every user-visible function in PHP should document itself in the source */
/* {{{ proto string confirm_datasafe_compiled(string arg)
   Return a string to confirm that the module is compiled in */
PHP_FUNCTION(confirm_datasafe_compiled)
{
	char *arg = NULL;
	int arg_len, len;
	char *strg;

	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s", &arg, &arg_len) == FAILURE) {
		return;
	}

	len = spprintf(&strg, 0, "Congratulations! You have successfully modified ext/%.78s/config.m4. Module %.78s is now compiled into PHP.", "datasafe", arg);
	RETURN_STRINGL(strg, len, 0);
}

static RSA* shopex_get_shopex_public_key(){
    unsigned char *pem_key_str = "MIGHAoGBAJOgBnKvVN5PtNDXBO0TNRZeILWmo0rpPLAU6s1IYHfhxKBGm44qDH8ONjJFk8NT70zGtOiwoqKv6UjQvHwNCjyLalIUN2mgV7AvqC0Tj8Gw6P9LaYgMY8V/vRSqhGGgRDRVxXS1KipPrueDMQSjBO/N3WSN6ac+N+JEcTtpopUjAgED";
    unsigned char *result;
    int ret_length = 0;
    RSA *pubkey;
    
    result = php_base64_decode(pem_key_str, strlen(pem_key_str), &ret_length);
    
    pubkey = d2i_RSAPublicKey(NULL,(const unsigned char**)&result,(long)ret_length);

    return pubkey;
}

static RSA* shopex_get_shopex_private_key(){
    unsigned char *pem_key_str = "MIICXAIBAAKBgQCToAZyr1TeT7TQ1wTtEzUWXiC1pqNK6TywFOrNSGB34cSgRpuOKgx/DjYyRZPDU+9MxrTosKKir+lI0Lx8DQo8i2pSFDdpoFewL6gtE4/BsOj/S2mIDGPFf70UqoRhoEQ0VcV0tSoqT67ngzEEowTvzd1kjemnPjfiRHE7aaKVIwIBAwKBgGJqrvcfjemKeIs6A0i3eLmUFc5vF4dGKHVjRzOFlaVBLcAvEl7Gsv9ezswuYoI39N3ZzfB1wcHKm4XgfagIsXyvDs90xgStABo3lML4zBJklKGad8/3L5a2fsjGUMU160S02t3DTUHO9OBiEa3ts1Fz/FB/7DTI/M8T2IFUmV7rAkEAxJ+1Ow78xMIZzQol47vUAjDHQEzLPjbjhgSRSprSYpeu+pmmp0pCVf5Y988BsSsOJTHIjpZHDlW+k5L/GD3FEwJBAMA0Zan/ZdgVbw8+4rqh0hfZRaNpBNtlf+f6VjZwZ2zLnkvjgWjsBUNBPfhHfg1M53qxIz9xEQJm7RMZelJ+wbECQQCDFSN8tKiDLBEzXBlCfTgBddoq3dzUJJeurbYxvIxBunSnERnE3Cw5VDtP31Z2HLQYy9sJuYS0OSm3t1S609i3AkEAgCLucVTukA5KCinsfGvhZTuDwkYDPO5VRVGOzvWaSIe+3UJWRfKuLNYpUC+pXjNE/HYXf6C2AZnzYhD8Nv8rywJBALBfzIMk/JuJcWLHYnGTNAYpZaAEFv6UVSx1bLFEKfMmIIO+KuOyVMGMqwTKjazfiMVVrnXzoWC7MM9WsBlZi+M=";
    unsigned char *result;
    int ret_length = 0;
    RSA *privkey;
    
    result = php_base64_decode(pem_key_str, strlen(pem_key_str), &ret_length);

    privkey=d2i_RSAPrivateKey(NULL,(const unsigned char**)&result,(long)ret_length);    
    
    return privkey;
}


static RSA* shopex_get_user_public_key(char *keyfile_path){
    FILE *fp;
    RSA *key=NULL;
    //char *keyfile_path;
    
    //keyfile_path = "/etc/shopex/skomart.com/pub.pem";
    if((fp = fopen(keyfile_path,"r")) == NULL) {
        php_error_docref(NULL TSRMLS_CC, E_ERROR, "public key file doesn't exists.");
    }
    if((key = PEM_read_RSAPublicKey(fp,NULL,NULL,NULL)) == NULL) {
        php_error_docref(NULL TSRMLS_CC, E_ERROR, "problems while parse public key file");
    }
    fclose(fp);
    return key;
}

static RSA* shopex_get_user_private_key(char *keyfile_path){
    FILE *fp;
    RSA *key=NULL;
    //char *keyfile_path;    
    //keyfile_path = "/etc/shopex/skomart.com/sec.pem.z";
    if((fp = fopen(keyfile_path,"r")) == NULL) {
        php_error_docref(NULL TSRMLS_CC, E_ERROR, "private key file doesn't exists.");
    }
    if((key = PEM_read_RSAPrivateKey(fp,NULL,NULL,NULL)) == NULL) {
        php_error_docref(NULL TSRMLS_CC, E_ERROR, "problems while parse private key file");
    }
    fclose(fp);
    return key;
}

static RSA* shopex_get_user_private_key_en(char *filename){
    RSA *pkey = NULL;
    RSA *key = NULL;
    
    FILE *fp;
    int len;
    char *buffer;
    char *data;
    int data_len;
    char *b64_decode;
    int de_len;
    
    fp = fopen(filename, "r");
    if (fp == NULL) {
        php_error_docref(NULL TSRMLS_CC, E_ERROR, "read shopex config file failure");
    }
    fseek(fp, 0L, SEEK_END);
    len = ftell(fp);
    fseek(fp, 0L, SEEK_SET);
    buffer = emalloc(len + 1);
    fread( buffer, 1, len, fp );
    buffer[len] = '\0';
    fclose(fp);   
     
    pkey = shopex_get_shopex_private_key();
    shopex_rsa_decrypt(pkey,buffer,len,&data,&data_len);
    RSA_free(pkey);  
        
    b64_decode = php_base64_decode(data,data_len,&de_len);    
    
    key=d2i_RSAPrivateKey(NULL,(const unsigned char**)&b64_decode,(long)de_len);
    if(RSA_check_key(key) == -1) {
      php_error_docref(NULL TSRMLS_CC, E_ERROR, "Problems while reading RSA Private Key in  file.");
    } else if(RSA_check_key(key) == 0) {
      php_error_docref(NULL TSRMLS_CC, E_ERROR, "Bad RSA Private Key readed in  file.");
    }
    else
      return key;
}

static void shopex_rsa_encrypt(RSA *pkey,char *data,int data_len,char **output,int *output_len){
	int successful = 0;

	char *data_p;

	int ks,chunk_len;
	int rsa_ret_buf_len;
    int ret_len;
    int ret_len_total;
	
	char *rsa_ret_buf_p,*rsa_ret_buf;
	char *plain_p,*plain;
	char *cipher_p,*cipher;
	
	char *result;
	int result_len;
	
	if (data_len == 0) {
		php_error_docref(NULL TSRMLS_CC, E_ERROR, "input data is empty");
	}
	
	if (pkey == NULL) {
		php_error_docref(NULL TSRMLS_CC, E_ERROR, "key parameter is not a valid public key");
	}

	data_p = data;
	ret_len = ret_len_total = 0;
		    
    ks = RSA_size(pkey);
    chunk_len = data_len > (ks - 11) ? ks - 11 : data_len;
    rsa_ret_buf_len = ( ( data_len / chunk_len + 1) * ks );
    rsa_ret_buf_p = rsa_ret_buf = emalloc(rsa_ret_buf_len + 1);
    memset(rsa_ret_buf_p,'\0', rsa_ret_buf_len + 1);
    plain_p = plain = (char *)emalloc(ks + 1);
    cipher_p = cipher = (char *)emalloc(ks + 1);
    
    while(data - data_p < data_len) {
        memset(plain,'\0',ks + 1);
        memset(cipher, '\0', ks + 1);
        memcpy(plain, data, chunk_len);
        ret_len = RSA_public_encrypt(chunk_len, plain, cipher, pkey, RSA_PKCS1_PADDING);
        if(ret_len != ks){
            successful = -1;
            break;
        }
        memcpy(rsa_ret_buf,cipher,ret_len);
        rsa_ret_buf += ret_len;
        plain = plain_p;
        cipher = cipher_p;
        ret_len_total += ret_len;
        data += chunk_len;
    }
    
    rsa_ret_buf = rsa_ret_buf_p;
	if ( successful == 0 ){
	    *output = php_base64_encode(rsa_ret_buf, ret_len_total, &result_len);
		*output_len = result_len;
	}
	rsa_ret_buf = rsa_ret_buf_p = NULL;
	result = NULL;
	if (rsa_ret_buf_p) {
		efree(rsa_ret_buf_p);
		rsa_ret_buf_p = NULL;
	}	
	if (plain_p) {
		efree(plain_p);
		plain_p = NULL;
	}	
	if (cipher_p) {
		efree(cipher_p);
		cipher_p = NULL;
	}	
}

static void shopex_rsa_decrypt(RSA *pkey,char *data,int data_len,char **output,int *output_len){
	
	char *data_p;
	int ret_len,ret_len_total;
	
	char *de_buf_p,*de_buf;
	int de_len;
	
	int ks;
	
	char *cipher_p,*cipher;
	char *plain_p,*plain;
	char *rsa_ret_buf_p,*rsa_ret_buf;
	
	int successful = 0;
	
	
	data_p = data;
	ret_len = ret_len_total = 0;
	de_buf_p = de_buf = php_base64_decode(data,data_len,&de_len);    
	data = data_p;
	
    ks = RSA_size(pkey);
    cipher_p = cipher = emalloc( ks + 1);
    plain_p = plain = emalloc( ks + 1);
    rsa_ret_buf_p = rsa_ret_buf = emalloc(de_len);
    memset(rsa_ret_buf,'\0',de_len);
    while( de_buf - de_buf_p < de_len ) {
        memset(cipher, '\0', ks + 1);
        memset(plain, '\0', ks + 1);
        memcpy(cipher,de_buf,ks);
        ret_len = RSA_private_decrypt(ks, cipher, plain, pkey, RSA_PKCS1_PADDING);
        if(ret_len == -1){
        	successful = -1;
        	break;
        }
        memcpy(rsa_ret_buf,plain,ret_len);
        ret_len_total += ret_len;
        rsa_ret_buf += ret_len;
        cipher = cipher_p;
        plain = plain_p;
        de_buf += ks;    
    }    
        
    rsa_ret_buf = rsa_ret_buf_p;
    ret_len_total = strlen(rsa_ret_buf);
	if(successful == 0){
		rsa_ret_buf[ret_len_total] = '\0';
        *output = rsa_ret_buf;
		*output_len = ret_len_total;
	}
	rsa_ret_buf = rsa_ret_buf_p = NULL;
	de_buf = de_buf_p = NULL;
	if (rsa_ret_buf_p) {
		efree(rsa_ret_buf_p);
		rsa_ret_buf_p = NULL;
	}	
	if (plain_p) {
		efree(plain_p);
		plain_p = NULL;
	}	
	if (cipher_p) {
		efree(cipher_p);
		cipher_p = NULL;
	}	
}


static void shopex_get_config(char *filename,char **output,int *output_len){
    FILE *fp;
    int len;
    char *buffer;
    RSA *pkey;
    
    char *config;
    int config_len = 0;

    fp = fopen(filename, "r");
    if (fp == NULL) {
        php_error_docref(NULL TSRMLS_CC, E_ERROR, "read shopex config file failure");
    }
    fseek(fp, 0L, SEEK_END);
    len = ftell(fp);
    fseek(fp, 0L, SEEK_SET);
    buffer = emalloc(len);
    fread( buffer, 1, len, fp );
    buffer[len] = '\0';
    
    pkey = shopex_get_shopex_private_key();
    shopex_rsa_decrypt(pkey,buffer,len,&config,&config_len);
    if(config_len != 0){
       *output =  config;
       *output_len = config_len;
    }
    RSA_free(pkey);
    fclose(fp);        
}


void shopex_md5_file(char *filename,char **output){
    char          md5str[33];
    unsigned char buf[1024];
    unsigned char digest[16];
    PHP_MD5_CTX   context;
    int           n;  
    php_stream    *stream;
        
    stream = php_stream_open_wrapper(filename, "rb", REPORT_ERRORS | ENFORCE_SAFE_MODE, NULL);  
    PHP_MD5Init(&context);    
    while ((n = php_stream_read(stream, buf, sizeof(buf))) > 0) {
        PHP_MD5Update(&context, buf, n); 
    }       
    PHP_MD5Final(digest, &context);    
    php_stream_close(stream);
    make_digest_ex(md5str, digest, 16);
    *output = estrndup(md5str,33);
}


PHP_FUNCTION(shopex_data_encrypt_ex)
{
	zval *crypted;
	RSA *pkey;

	char *data,*data_p;
	int data_len;
		
	char *config_filepath;
	int config_filepath_len;
	
	char *output;
	int output_len;
	
	char *config_content;
	int config_content_len;
	
	char *pos,*public_file_pos;
	
	char filename_buf[255];
	
	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "ssz", &config_filepath,&config_filepath_len,&data, &data_len, &crypted) == FAILURE)
		return;

	RETVAL_FALSE;
	

	
	if (data_len == 0) {
		php_error_docref(NULL TSRMLS_CC, E_ERROR, "input data is empty");
		RETURN_FALSE;
	}
	sprintf(filename_buf,"/etc/shopex/%s",config_filepath);
	shopex_get_config(filename_buf,&config_content,&config_content_len);
	if (config_content_len < 1) {
		php_error_docref(NULL TSRMLS_CC, E_ERROR, "decode setting file fail");
		RETURN_FALSE;
	}

    pos = strstr(config_content,"\n");
    *pos = '\0';
	public_file_pos = config_content;
    
	pkey = shopex_get_user_public_key(public_file_pos);
	if(config_content){
		efree(config_content);
	}
	if (pkey == NULL) {
		php_error_docref(NULL TSRMLS_CC, E_ERROR, "key parameter is not a valid public key");
		RETURN_FALSE;
	}
	
	shopex_rsa_encrypt(pkey,data,data_len,&output,&output_len);

	if ( output_len > 0 ){
		zval_dtor(crypted);
		ZVAL_STRINGL(crypted, output, output_len, 0);
		RETVAL_TRUE;
	}

	RSA_free(pkey);

}

PHP_FUNCTION(shopex_get_user_private_key){
    
    RSA *pkey;
    unsigned char *p,buf[2048]={'\0'};
    char *filepath;
    int filepath_len;
    int len;
    
    char *result;
    int result_len = 0;
    
    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s", &filepath,&filepath_len) == FAILURE)
    return;
    
    RETVAL_FALSE;
    
    pkey = shopex_get_user_private_key(filepath);
    
    p = buf;
    len =i2d_RSAPrivateKey(pkey,&p);
    p = buf;
    result = php_base64_encode(p, len, &result_len);
    if(result_len > 0){
        RETURN_STRINGL(result,result_len,0);
    }
}

PHP_FUNCTION(shopex_public_encrypt){    
    char *data;
    int data_len;
    
    char *crypted;
    int crypted_len;
    
    RSA *pkey;
    
    zval *result = NULL;
    
    
    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "sz", &data, &data_len,&result) == FAILURE)
        return;
    
    RETVAL_FALSE;
    
    pkey = shopex_get_shopex_public_key();
    shopex_rsa_encrypt(pkey,data,data_len,&crypted,&crypted_len);
    
    if ( crypted_len > 0 ){
	    zval_dtor(result);
		ZVAL_STRINGL(result, crypted, crypted_len, 1);
		crypted =  NULL;
		RETVAL_TRUE;
	}
    
    RSA_free(pkey);
}

PHP_FUNCTION(shopex_data_decrypt_ex)
{
	zval *result = NULL;
	RSA *pkey;
	zend_execute_data *zed;

	char *data,*data_p;
	int data_len;
	
	int rsa_ret_buf_len;
    int ret_len_total;
	
	char *rsa_ret_buf_p,*rsa_ret_buf;
	
	char *config_filepath;
	int config_filepath_len;
	
	char *de_buf,*de_buf_p;
	int de_len;
	
	char *config_content,*config_content_p;
	int config_content_len = 0;
	int found = 0;
	
    char *start,*end;
    int len = 0;
    char *file_pos;
    char *line,*line_p;
    char *cln_pos;
    char *filename;
    int filename_len = 0;
    char *md5_string;
    int md5_string_len = 0;
    char *allowfile;
    char *md5_return;
    
    char *filename_buf[255];

	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "ssz", &config_filepath,&config_filepath_len,&data, &data_len, &result) == FAILURE)
		return;

	RETVAL_FALSE;
	sprintf(filename_buf,"/etc/shopex/%s",config_filepath);
	shopex_get_config(filename_buf,&config_content,&config_content_len);
	if (config_content_len < 1) {
		php_error_docref(NULL TSRMLS_CC, E_ERROR, "key parameter is not a valid private key");
		RETURN_FALSE;
	}
	config_content_p = config_content;
	
	zed = EG(current_execute_data);
	filename = zed->op_array->filename;
	line_p = line = emalloc(512);
	md5_return = emalloc(33);
	while(*config_content != '\0' && (config_content - config_content_p) < config_content_len - 1 ){
	   	start = strstr(config_content,"\n");
	   	end = strstr(++start,"\n");
	    len = end - start;	    
    	line = estrndup(start,len);
    	if(cln_pos = strstr(line,":")){
			*cln_pos = '\0';
			allowfile = line;
			md5_string = ++cln_pos;
			shopex_md5_file(allowfile,&md5_return);
			if ( strcmp(allowfile,filename) != 0 || strcmp(md5_string,md5_return) != 0 ){
				php_error_docref(NULL TSRMLS_CC, E_ERROR, "this php file is not allow to run decrypt function");
				RETURN_FALSE;
            }
    	}    	
    	line = line_p;
    	config_content = end;
	}
	efree(line_p);
	
	config_content = config_content_p;
    start = strstr(config_content,"\n");
    end = strstr(++start,"\n");
    len = end - start;
    file_pos = emalloc(len + 1);
    file_pos = estrndup(start,len);
	
	pkey = shopex_get_user_private_key_en(file_pos);
	if(config_content_p){
		efree(config_content_p);
	}
	if (pkey == NULL) {
		php_error_docref(NULL TSRMLS_CC, E_ERROR, "key parameter is not a valid private key");
		RETURN_FALSE;
	}
    rsa_ret_buf_len = 0;
    rsa_ret_buf = NULL;
    shopex_rsa_decrypt(pkey,data,data_len,&rsa_ret_buf,&rsa_ret_buf_len);

	if ( rsa_ret_buf_len > 0 ){
	    zval_dtor(result);
		ZVAL_STRINGL(result, rsa_ret_buf, rsa_ret_buf_len, 1);
		rsa_ret_buf = rsa_ret_buf_p = NULL;
		de_buf = de_buf_p = NULL;
		RETVAL_TRUE;
	}

	RSA_free(pkey);

}

PHP_FUNCTION(shopex_gen_keypair)
{
	RSA *key;
 	FILE *fp;
 	
 	char *pub,*priv;
 	int pub_len,priv_len;

	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "ss", &pub,&pub_len,&priv, &priv_len) == FAILURE)
		return;

	RETVAL_FALSE;

 	if((key = RSA_generate_key(1024,3,NULL,NULL)) == NULL)	{
		php_error_docref(NULL TSRMLS_CC, E_ERROR, "generate key fail");
		RETURN_FALSE;
 	}
 	if(RSA_check_key(key) < 1) 	{
		php_error_docref(NULL TSRMLS_CC, E_ERROR, "key is not good");
		RETURN_FALSE;
 	}
 	fp=fopen(priv,"w");
 	if(PEM_write_RSAPrivateKey(fp,key,NULL,NULL,0,0,NULL) == 0) 	{
		php_error_docref(NULL TSRMLS_CC, E_ERROR, "write private key fail");
		RETURN_FALSE;
 	}
 	fclose(fp);
 	fp=fopen(pub,"w");
 	if(PEM_write_RSAPublicKey(fp,key) == 0) 	{
		php_error_docref(NULL TSRMLS_CC, E_ERROR, "write public key fail");
		RETURN_FALSE;
 	}
 	fclose(fp);
 	RSA_free(key);
}

/* }}} */
/* The previous line is meant for vim and emacs, so it can correctly fold and 
   unfold functions in source code. See the corresponding marks just before 
   function definition, where the functions purpose is also documented. Please 
   follow this convention for the convenience of others editing your code.
*/


/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: noet sw=4 ts=4 fdm=marker
 * vim<600: noet sw=4 ts=4
 */
