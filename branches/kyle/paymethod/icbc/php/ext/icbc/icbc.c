/*
  +----------------------------------------------------------------------+
  | PHP Version 5                                                        |
  +----------------------------------------------------------------------+
  | Copyright (c) 1997-2007 The PHP Group                                |
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

/* $Id: header,v 1.16.2.1.2.1 2007/01/01 19:32:09 iliaa Exp $ */

#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include "php.h"
#include "php_ini.h"
#include "ext/standard/info.h"
#include "php_icbc.h"
#include "icbc.h"

/* If you declare any globals in php_icbc.h uncomment this:
ZEND_DECLARE_MODULE_GLOBALS(icbc)
*/

/* True global resources - no need for thread safety here */
static int le_icbc;

/* {{{ icbc_functions[]
 *
 * Every user visible function must have an entry in icbc_functions[].
 */
zend_function_entry icbc_functions[] = {
	PHP_FE(confirm_icbc_compiled,	NULL)		/* For testing, remove later. */
	PHP_FE(icbcSign,	NULL)
	PHP_FE(icbcVerifySign,	NULL)
	{NULL, NULL, NULL}	/* Must be the last line in icbc_functions[] */
};
/* }}} */

/* {{{ icbc_module_entry
 */
zend_module_entry icbc_module_entry = {
#if ZEND_MODULE_API_NO >= 20010901
	STANDARD_MODULE_HEADER,
#endif
	"icbc",
	icbc_functions,
	PHP_MINIT(icbc),
	PHP_MSHUTDOWN(icbc),
	PHP_RINIT(icbc),		/* Replace with NULL if there's nothing to do at request start */
	PHP_RSHUTDOWN(icbc),	/* Replace with NULL if there's nothing to do at request end */
	PHP_MINFO(icbc),
#if ZEND_MODULE_API_NO >= 20010901
	"0.1", /* Replace with version number for your extension */
#endif
	STANDARD_MODULE_PROPERTIES
};
/* }}} */

#ifdef COMPILE_DL_ICBC
ZEND_GET_MODULE(icbc)
#endif

/* {{{ PHP_INI
 */
/* Remove comments and fill if you need to have entries in php.ini
PHP_INI_BEGIN()
    STD_PHP_INI_ENTRY("icbc.global_value",      "42", PHP_INI_ALL, OnUpdateLong, global_value, zend_icbc_globals, icbc_globals)
    STD_PHP_INI_ENTRY("icbc.global_string", "foobar", PHP_INI_ALL, OnUpdateString, global_string, zend_icbc_globals, icbc_globals)
PHP_INI_END()
*/
/* }}} */

/* {{{ php_icbc_init_globals
 */
/* Uncomment this function if you have INI entries
static void php_icbc_init_globals(zend_icbc_globals *icbc_globals)
{
	icbc_globals->global_value = 0;
	icbc_globals->global_string = NULL;
}
*/
/* }}} */

/* {{{ PHP_MINIT_FUNCTION
 */
PHP_MINIT_FUNCTION(icbc)
{
	/* If you have INI entries, uncomment these lines 
	REGISTER_INI_ENTRIES();
	*/
	return SUCCESS;
}
/* }}} */

/* {{{ PHP_MSHUTDOWN_FUNCTION
 */
PHP_MSHUTDOWN_FUNCTION(icbc)
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
PHP_RINIT_FUNCTION(icbc)
{
	return SUCCESS;
}
/* }}} */

/* Remove if there's nothing to do at request end */
/* {{{ PHP_RSHUTDOWN_FUNCTION
 */
PHP_RSHUTDOWN_FUNCTION(icbc)
{
	return SUCCESS;
}
/* }}} */

/* {{{ PHP_MINFO_FUNCTION
 */
PHP_MINFO_FUNCTION(icbc)
{
	php_info_print_table_start();
	php_info_print_table_header(2, "icbc support", "enabled");
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
/* {{{ proto string confirm_icbc_compiled(string arg)
   Return a string to confirm that the module is compiled in */
PHP_FUNCTION(confirm_icbc_compiled)
{
	char *arg = NULL;
	int arg_len, len;
	char *strg;

	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s", &arg, &arg_len) == FAILURE) {
		return;
	}

	len = spprintf(&strg, 0, "Congratulations! You have successfully modified ext/%.78s/config.m4. Module %.78s is now compiled into PHP.", "icbc", arg);
	RETURN_STRINGL(strg, len, 0);
}
/* }}} */
/* The previous line is meant for vim and emacs, so it can correctly fold and 
   unfold functions in source code. See the corresponding marks just before 
   function definition, where the functions purpose is also documented. Please 
   follow this convention for the convenience of others editing your code.
*/

/* {{{ proto int icbcSign()
   unsigned char *src,unsigned char *privateKey,char *keyPass) */
PHP_FUNCTION(icbcSign)
{
	
	char	*src;
    int		srclen;

    char	*pkey;
    int		privateKeyLen;
    
	char	*keypass;
    int		keypasslen;
    
	char	*signedbuf;
    int		signedbuflen;
    
	FILE	*fp;
    char	key[2000];
    int		rcc,flen;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,"sss",&src,&srclen,&pkey,&privateKeyLen,&keypass,&keypasslen) == FAILURE){
        return;
    }

    fp = fopen(pkey,"rb");
    if(fp == NULL)
    {
        return;
    }
    fseek(fp,0,2);
	flen = ftell(fp);
	rewind(fp);
	fread(key,1,flen,fp);
	fclose(fp);
	privateKeyLen = ( key[0] << 8 ) | key[1];
	if( privateKeyLen < flen - 2 )
	{
		return;
	}

    if( rcc = sign(src,srclen,(key + 2),privateKeyLen,keypass,&signedbuf,&signedbuflen) == 0 ){
        base64enc(signedbuf,signedbuflen,&signedbuf,&signedbuflen);
        src = estrndup(signedbuf,signedbuflen);
        if(signedbuf != NULL) infosec_free(signedbuf);
        RETURN_STRING(src,1);
    }else{
        RETURN_LONG(rcc);
    }
}
/* }}} */

/* {{{ proto int icbcVerifySign()
   unsigned char *src,unsigned char *cert,unsigned char *signBuf) */
PHP_FUNCTION(icbcVerifySign)
{
	char	*src;
    int		srclen;

    char	*cert;
    int		certlen;
    
	char	*vsignedbuf;
    int		vsignedbuflen;
    
	FILE	*fp;
    char	vcert[2000];
    int		rcc,flen;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,"sss",&src,&srclen,&cert,&certlen,&vsignedbuf,&vsignedbuflen) == FAILURE){
        return;
    }

    fp = fopen(cert,"rb");
    if(fp == NULL)
    {
        return;
    }
	fseek(fp,0,2);
	flen = ftell(fp);
	rewind(fp);
	fread(vcert,1,flen,fp);
	fclose(fp);

    base64dec(vsignedbuf,vsignedbuflen,&vsignedbuf,&vsignedbuflen);

    if(	rcc = verifySign(src,srclen,vcert,flen,vsignedbuf,vsignedbuflen) == 0 ){
        if(vsignedbuf != NULL) infosec_free(vsignedbuf);
        RETURN_TRUE;
    }else{
        if(vsignedbuf != NULL) infosec_free(vsignedbuf);
        RETURN_LONG(rcc);
    }
}
/* }}} */


/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: noet sw=4 ts=4 fdm=marker
 * vim<600: noet sw=4 ts=4
 */
