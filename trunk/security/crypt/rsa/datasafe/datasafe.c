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

    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "szZ|l", &data, &data_len, &crypted, &key, &padding) == FAILURE)
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

	RETURN_STRING(strg, len);
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
