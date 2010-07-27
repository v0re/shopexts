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
	PHP_FE(shopex_data_encrypt,	NULL)		/* it will be use rsa . */
	PHP_FE(shopex_data_decrypt,	NULL)		/* it will be use rsa. */
	PHP_FE(shopex_data_encrypt_ex,	NULL)		/* it will be use rsa . */
	PHP_FE(shopex_data_decrypt_ex,	NULL)		/* it will be use rsa. */
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


PHP_FUNCTION(shopex_data_encrypt)
{
	char *config_filepath = NULL;
    int config_filepath_len;
    char *arg = NULL;
	int arg_len;

    char *output;
    int output_len;
    
    char * ret;    
    
	if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,"ss", &config_filepath,&config_filepath_len,&arg, &arg_len) == FAILURE){
		return;
	}
		
	shopex_data_rsa_encrypt(config_filepath,arg,arg_len,&output,&output_len);
	ret = estrndup(output,output_len);
	if(output){
	    free(output);
	    output = NULL;
    }
	RETURN_STRINGL(ret,output_len,0);
}

PHP_FUNCTION(shopex_data_decrypt)
{
	char *config_filepath = NULL;
    int config_filepath_len;
    char *arg = NULL;
	int arg_len;

    char *keyfile_path;
    char *output = NULL;
    int output_len;
    
    char * ret;
    char *privkeypos;
    
    zend_execute_data *zed;
    
    int allow_ret = 0;
    int new_len = 0;


	if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,"ss", &config_filepath,&config_filepath_len,&arg, &arg_len) == FAILURE){
		return;
	}
	
	
	//zed = EG(current_execute_data);
	//allow_ret = shopex_is_file_in_allowlist(config_filepath,zed->op_array->filename);
	//if(shopex_is_file_in_allowlist(config_filepath,zed->op_array->filename) == 0){
	new_len = 128 * ( arg_len / 117 + 1 );
    output = (char *)emalloc( new_len );
    memset(output,'\0',new_len);
    shopex_data_rsa_decrypt(config_filepath,arg,arg_len,&output,&output_len);
    
    RETURN_STRINGL(output,output_len,0);
	//}
	//RETURN_STRING(arg,arg_len);
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


static RSA* shopex_get_user_public_key(){
    FILE *fp;
    RSA *key=NULL;
    char *keyfile_path;
    
    keyfile_path = "/etc/shopex/skomart.com/pub.pem";
    if((fp = fopen(keyfile_path,"r")) == NULL) {
        php_error_docref(NULL TSRMLS_CC, E_WARNING, "public key file doesn't exists.");
    }
    if((key = PEM_read_RSAPublicKey(fp,NULL,NULL,NULL)) == NULL) {
        php_error_docref(NULL TSRMLS_CC, E_WARNING, "Error: problems while parse public key file");
    }
    fclose(fp);
    return key;
}

static RSA* shopex_get_user_private_key(){
    FILE *fp;
    RSA *key=NULL;
    char *keyfile_path;
    
    keyfile_path = "/etc/shopex/skomart.com/sec.pem.z";
    if((fp = fopen(keyfile_path,"r")) == NULL) {
        php_error_docref(NULL TSRMLS_CC, E_WARNING, "private key file doesn't exists.");
    }
    if((key = PEM_read_RSAPrivateKey(fp,NULL,NULL,NULL)) == NULL) {
        php_error_docref(NULL TSRMLS_CC, E_WARNING, "Error: problems while parse public key file");
    }
    fclose(fp);
    return key;
}

PHP_FUNCTION(shopex_data_encrypt_ex)
{
	zval *crypted;
	RSA *pkey;

	int successful = 0;

	char *data,*data_p;
	int data_len;
	
	int ks,chunk_len;
	int rsa_ret_buf_len;
    int ret_len;
    int ret_len_total;
	
	char *rsa_ret_buf_p,*rsa_ret_buf;
	char *plain_p,*plain;
	char *cipher_p,*cipher;
	
	char *config_filepath;
	int config_filepath_len;
	
	char *result;
	int result_len;

	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "ssz", &config_filepath,&config_filepath_len,&data, &data_len, &crypted) == FAILURE)
		return;

	RETVAL_FALSE;
	
	if (data_len == 0) {
		php_error_docref(NULL TSRMLS_CC, E_WARNING, "input data is empty");
		RETURN_FALSE;
	}
	
	pkey = shopex_get_user_public_key();
	if (pkey == NULL) {
		php_error_docref(NULL TSRMLS_CC, E_WARNING, "key parameter is not a valid public key");
		RETURN_FALSE;
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
	    result = php_base64_encode(rsa_ret_buf, ret_len_total, &result_len);
		zval_dtor(crypted);
		result[result_len] = '\0';
		ZVAL_STRINGL(crypted, result, result_len, 0);
		rsa_ret_buf = rsa_ret_buf_p = NULL;
		result = NULL;
		RETVAL_TRUE;
	}

	RSA_free(pkey);
	if (rsa_ret_buf_p) {
		efree(rsa_ret_buf_p);
	}	
	if (plain_p) {
		efree(plain_p);
	}	
	if (cipher_p) {
		efree(cipher_p);
	}	
}

static void shopex_get_config(char *filename){
    FILE *fp;

    fp = fopen(filename, "r");
    if (fp == NULL) {
        php_error_docref(NULL TSRMLS_CC, E_WARNING, "read shopex config file failure");
    }
    fseek(fp, 0L, SEEK_END);
    len = ftell(fp);
    fseek(fp, 0L, SEEK_SET);
    buffer = emalloc(len);
    fread( buffer, 1, len, fp );
    buffer[len] = '\0';
}

static void shopex_set_config(char *fielname){
    
}


PHP_FUNCTION(shopex_data_decrypt_ex)
{
	zval *result;
	RSA *pkey;

	int successful = 0;

	char *data,*data_p;
	int data_len;
	
	int ks,chunk_len;
	int rsa_ret_buf_len;
    int ret_len;
    int ret_len_total;
	
	char *rsa_ret_buf_p,*rsa_ret_buf;
	char *plain_p,*plain;
	char *cipher_p,*cipher;
	
	char *config_filepath;
	int config_filepath_len;
	
	char *de_buf,*de_buf_p;
	int de_len;

	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "ssz", &config_filepath,&config_filepath_len,&data, &data_len, &result) == FAILURE)
		return;

	RETVAL_FALSE;
	
	shopex_get_config(config_filepath);
	
	pkey = shopex_get_user_private_key();
	if (pkey == NULL) {
		php_error_docref(NULL TSRMLS_CC, E_WARNING, "key parameter is not a valid private key");
		RETURN_FALSE;
	}

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
        memcpy(rsa_ret_buf,plain,ret_len);
        ret_len_total += ret_len;
        rsa_ret_buf += ret_len;
        cipher = cipher_p;
        plain = plain_p;
        de_buf += ks;    
    }    
        
    rsa_ret_buf = rsa_ret_buf_p;
    ret_len_total = strlen(rsa_ret_buf);
	if ( successful == 0 ){
		rsa_ret_buf[ret_len_total] = '\0';
	    zval_dtor(result);
		ZVAL_STRINGL(result, rsa_ret_buf, ret_len_total, 0);
		rsa_ret_buf = rsa_ret_buf_p = NULL;
		de_buf = de_buf_p = NULL;
		RETVAL_TRUE;
	}

	RSA_free(pkey);
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
