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
#include "php_datasafe.h"

/* PHP Includes */
#include "ext/standard/file.h"
#include "ext/standard/info.h"
#include "ext/standard/php_fopen_wrappers.h"
#include "ext/standard/md5.h"
#include "ext/standard/base64.h"

/* OpenSSL includes */
#include <openssl/evp.h>
#include <openssl/x509.h>
#include <openssl/x509v3.h>
#include <openssl/crypto.h>
#include <openssl/pem.h>
#include <openssl/err.h>
#include <openssl/conf.h>
#include <openssl/rand.h>
#include <openssl/ssl.h>
#include <openssl/pkcs12.h>

/* Common */
#include <time.h>
#ifdef NETWARE
#define timezone _timezone  /* timezone is called _timezone in LibC */
#endif

#define DEFAULT_KEY_LENGTH  512
#define MIN_KEY_LENGTH      384

#define OPENSSL_ALGO_SHA1   1
#define OPENSSL_ALGO_MD5    2
#define OPENSSL_ALGO_MD4    3
#ifdef HAVE_OPENSSL_MD2_H
#define OPENSSL_ALGO_MD2    4
#endif
#define OPENSSL_ALGO_DSS1   5

#define DEBUG_SMIME 0

/* FIXME: Use the openssl constants instead of
 * enum. It is now impossible to match real values
 * against php constants. Also sorry to break the
 * enum principles here, BC...
 */
enum php_openssl_key_type {
    OPENSSL_KEYTYPE_RSA,
    OPENSSL_KEYTYPE_DSA,
    OPENSSL_KEYTYPE_DH,
    OPENSSL_KEYTYPE_DEFAULT = OPENSSL_KEYTYPE_RSA,
#ifdef EVP_PKEY_EC
    OPENSSL_KEYTYPE_EC = OPENSSL_KEYTYPE_DH +1
#endif
};

enum php_openssl_cipher_type {
    PHP_OPENSSL_CIPHER_RC2_40,
    PHP_OPENSSL_CIPHER_RC2_128,
    PHP_OPENSSL_CIPHER_RC2_64,
    PHP_OPENSSL_CIPHER_DES,
    PHP_OPENSSL_CIPHER_3DES,

    PHP_OPENSSL_CIPHER_DEFAULT = PHP_OPENSSL_CIPHER_RC2_40
};

static EVP_PKEY * php_openssl_evp_from_zval(zval ** val, int public_key, char * passphrase, int makeresource, long * resourceval TSRMLS_DC);
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

/* {{{ php_openssl_evp_from_zval
   Given a zval, coerce it into a EVP_PKEY object.
	It can be:
		1. private key resource from openssl_get_privatekey()
		2. X509 resource -> public key will be extracted from it
		3. if it starts with file:// interpreted as path to key file
		4. interpreted as the data from the cert/key file and interpreted in same way as openssl_get_privatekey()
		5. an array(0 => [items 2..4], 1 => passphrase)
		6. if val is a string (possibly starting with file:///) and it is not an X509 certificate, then interpret as public key
	NOTE: If you are requesting a private key but have not specified a passphrase, you should use an
	empty string rather than NULL for the passphrase - NULL causes a passphrase prompt to be emitted in
	the Apache error log!
*/
static EVP_PKEY * php_openssl_evp_from_zval(zval ** val, int public_key, char * passphrase, int makeresource, long * resourceval TSRMLS_DC)
{
	EVP_PKEY * key = NULL;
	X509 * cert = NULL;
	int free_cert = 0;
	long cert_res = -1;
	char * filename = NULL;
	zval tmp;

	Z_TYPE(tmp) = IS_NULL;

#define TMP_CLEAN \
	if (Z_TYPE(tmp) == IS_STRING) {\
		zval_dtor(&tmp); \
	} \
	return NULL;

	if (resourceval) {
		*resourceval = -1;
	}
	if (Z_TYPE_PP(val) == IS_ARRAY) {
		zval ** zphrase;
		
		/* get passphrase */

		if (zend_hash_index_find(HASH_OF(*val), 1, (void **)&zphrase) == FAILURE) {
			php_error_docref(NULL TSRMLS_CC, E_WARNING, "key array must be of the form array(0 => key, 1 => phrase)");
			return NULL;
		}
		
		if (Z_TYPE_PP(zphrase) == IS_STRING) {
			passphrase = Z_STRVAL_PP(zphrase);
		} else {
			tmp = **zphrase;
			zval_copy_ctor(&tmp);
			convert_to_string(&tmp);
			passphrase = Z_STRVAL(tmp);
		}

		/* now set val to be the key param and continue */
		if (zend_hash_index_find(HASH_OF(*val), 0, (void **)&val) == FAILURE) {
			php_error_docref(NULL TSRMLS_CC, E_WARNING, "key array must be of the form array(0 => key, 1 => phrase)");
			TMP_CLEAN;
		}
	}

	if (Z_TYPE_PP(val) == IS_RESOURCE) {
		void * what;
		int type;

		what = zend_fetch_resource(val TSRMLS_CC, -1, "OpenSSL X.509/key", &type, 2, le_x509, le_key);
		if (!what) {
			TMP_CLEAN;
		}
		if (resourceval) { 
			*resourceval = Z_LVAL_PP(val);
		}
		if (type == le_x509) {
			/* extract key from cert, depending on public_key param */
			cert = (X509*)what;
			free_cert = 0;
		} else if (type == le_key) {
			int is_priv;

			is_priv = php_openssl_is_private_key((EVP_PKEY*)what TSRMLS_CC);

			/* check whether it is actually a private key if requested */
			if (!public_key && !is_priv) {
				php_error_docref(NULL TSRMLS_CC, E_WARNING, "supplied key param is a public key");
				TMP_CLEAN;
			}

			if (public_key && is_priv) {
				php_error_docref(NULL TSRMLS_CC, E_WARNING, "Don't know how to get public key from this private key");
				TMP_CLEAN;
			} else {
				if (Z_TYPE(tmp) == IS_STRING) {
					zval_dtor(&tmp);
				}
				/* got the key - return it */
				return (EVP_PKEY*)what;
			}
		} else {
			/* other types could be used here - eg: file pointers and read in the data from them */
			TMP_CLEAN;
		}
	} else {
		/* force it to be a string and check if it refers to a file */
		/* passing non string values leaks, object uses toString, it returns NULL 
		 * See bug38255.phpt 
		 */
		if (!(Z_TYPE_PP(val) == IS_STRING || Z_TYPE_PP(val) == IS_OBJECT)) {
			TMP_CLEAN;
		}
		convert_to_string_ex(val);

		if (Z_STRLEN_PP(val) > 7 && memcmp(Z_STRVAL_PP(val), "file://", sizeof("file://") - 1) == 0) {
			filename = Z_STRVAL_PP(val) + (sizeof("file://") - 1);
		}
		/* it's an X509 file/cert of some kind, and we need to extract the data from that */
		if (public_key) {
			cert = php_openssl_x509_from_zval(val, 0, &cert_res TSRMLS_CC);
			free_cert = (cert_res == -1);
			/* actual extraction done later */
			if (!cert) {
				/* not a X509 certificate, try to retrieve public key */
				BIO* in;
				if (filename) {
					in = BIO_new_file(filename, "r");
				} else {
					in = BIO_new_mem_buf(Z_STRVAL_PP(val), Z_STRLEN_PP(val));
				}
				if (in == NULL) {
					TMP_CLEAN;
				}
				key = PEM_read_bio_PUBKEY(in, NULL,NULL, NULL);
				BIO_free(in);
			}
		} else {
			/* we want the private key */
			BIO *in;

			if (filename) {
				if (php_openssl_safe_mode_chk(filename TSRMLS_CC)) {
					TMP_CLEAN;
				}
				in = BIO_new_file(filename, "r");
			} else {
				in = BIO_new_mem_buf(Z_STRVAL_PP(val), Z_STRLEN_PP(val));
			}

			if (in == NULL) {
				TMP_CLEAN;
			}
			key = PEM_read_bio_PrivateKey(in, NULL,NULL, passphrase);
			BIO_free(in);
		}
	}

	if (public_key && cert && key == NULL) {
		/* extract public key from X509 cert */
		key = (EVP_PKEY *) X509_get_pubkey(cert);
	}

	if (free_cert && cert) {
		X509_free(cert);
	}
	if (key && makeresource && resourceval) {
		*resourceval = ZEND_REGISTER_RESOURCE(NULL, key, le_key);
	}
	if (Z_TYPE(tmp) == IS_STRING) {
		zval_dtor(&tmp);
	}
	return key;
}
/* }}} */



PHP_FUNCTION(shopex_data_encrypt)
{
	zval **key, *crypted;
    EVP_PKEY *pkey;
    int cryptedlen;
    unsigned char *cryptedbuf;
    int successful = 0;
    long keyresource = -1;
    long padding = RSA_PKCS1_PADDING;
    char * data;
    int data_len;

    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "szZ", &data, &data_len, &crypted, &key) == FAILURE)
        return;

    pkey = php_openssl_evp_from_zval(key, 1, NULL, 0, &keyresource TSRMLS_CC);
    if (pkey == NULL) {
        php_error_docref(NULL TSRMLS_CC, E_WARNING, "key parameter is not a valid public key");
        RETURN_FALSE;
    }    

    cryptedlen = EVP_PKEY_size(pkey);
    cryptedbuf = emalloc(cryptedlen + 1);

    switch (pkey->type) {
        case EVP_PKEY_RSA:
        case EVP_PKEY_RSA2:
            successful = (RSA_public_encrypt(data_len, 
                        (unsigned char *)data, 
                        cryptedbuf, 
                        pkey->pkey.rsa, 
                        padding) == cryptedlen);
            break;
        default:
            php_error_docref(NULL TSRMLS_CC, E_WARNING, "key type not supported in this PHP build!");

    }    

    if (successful) {
        zval_dtor(crypted);
        cryptedbuf[cryptedlen] = '\0';
        ZVAL_STRINGL(crypted, (char *)cryptedbuf, cryptedlen, 0);
        cryptedbuf = NULL;
        RETVAL_TRUE;
    }    
    if (keyresource == -1) {
        EVP_PKEY_free(pkey);
    }    
    if (cryptedbuf) {
        efree(cryptedbuf);
    }   

	/*RETURN_STRING(strg, len);*/
}

PHP_FUNCTION(shopex_data_decrypt)
{
	char *arg = NULL;
	int arg_len, len;
	char *strg;

	if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,"s", &arg, &arg_len) == FAILURE){
		return;
	}
	
	strg = php_base64_decode(arg,arg_len,&len);

	RETURN_STRING(strg, len);
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
