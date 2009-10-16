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
#include "php_chinapay.h"
#include "chinapay.h"

/* If you declare any globals in php_chinapay.h uncomment this:
ZEND_DECLARE_MODULE_GLOBALS(chinapay)
*/

/* True global resources - no need for thread safety here */
static int le_chinapay;

/* {{{ chinapay_functions[]
 *
 * Every user visible function must have an entry in chinapay_functions[].
 */
zend_function_entry chinapay_functions[] = {
	PHP_FE(confirm_chinapay_compiled,	NULL)		/* For testing, remove later. */
	PHP_FE(setMerKeyFile,	NULL)
	PHP_FE(unsetMerKeyFile,	NULL)
	PHP_FE(setPubKeyFile,	NULL)
	PHP_FE(setPubKeyFile2,	NULL)
	PHP_FE(unsetPubKeyFile,	NULL)
	PHP_FE(signOrder,	NULL)
	PHP_FE(verifyTransResponse,	NULL)
	PHP_FE(signData,	NULL)
	PHP_FE(verifySignData,	NULL)
	PHP_FE(,	NULL)
	{NULL, NULL, NULL}	/* Must be the last line in chinapay_functions[] */
};
/* }}} */

/* {{{ chinapay_module_entry
 */
zend_module_entry chinapay_module_entry = {
#if ZEND_MODULE_API_NO >= 20010901
	STANDARD_MODULE_HEADER,
#endif
	"chinapay",
	chinapay_functions,
	PHP_MINIT(chinapay),
	PHP_MSHUTDOWN(chinapay),
	PHP_RINIT(chinapay),		/* Replace with NULL if there's nothing to do at request start */
	PHP_RSHUTDOWN(chinapay),	/* Replace with NULL if there's nothing to do at request end */
	PHP_MINFO(chinapay),
#if ZEND_MODULE_API_NO >= 20010901
	"0.1", /* Replace with version number for your extension */
#endif
	STANDARD_MODULE_PROPERTIES
};
/* }}} */

#ifdef COMPILE_DL_CHINAPAY
ZEND_GET_MODULE(chinapay)
#endif

/* {{{ PHP_INI
 */
/* Remove comments and fill if you need to have entries in php.ini
PHP_INI_BEGIN()
    STD_PHP_INI_ENTRY("chinapay.global_value",      "42", PHP_INI_ALL, OnUpdateLong, global_value, zend_chinapay_globals, chinapay_globals)
    STD_PHP_INI_ENTRY("chinapay.global_string", "foobar", PHP_INI_ALL, OnUpdateString, global_string, zend_chinapay_globals, chinapay_globals)
PHP_INI_END()
*/
/* }}} */

/* {{{ php_chinapay_init_globals
 */
/* Uncomment this function if you have INI entries
static void php_chinapay_init_globals(zend_chinapay_globals *chinapay_globals)
{
	chinapay_globals->global_value = 0;
	chinapay_globals->global_string = NULL;
}
*/
/* }}} */

/* {{{ PHP_MINIT_FUNCTION
 */
PHP_MINIT_FUNCTION(chinapay)
{
	/* If you have INI entries, uncomment these lines 
	REGISTER_INI_ENTRIES();
	*/
	return SUCCESS;
}
/* }}} */

/* {{{ PHP_MSHUTDOWN_FUNCTION
 */
PHP_MSHUTDOWN_FUNCTION(chinapay)
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
PHP_RINIT_FUNCTION(chinapay)
{
	return SUCCESS;
}
/* }}} */

/* Remove if there's nothing to do at request end */
/* {{{ PHP_RSHUTDOWN_FUNCTION
 */
PHP_RSHUTDOWN_FUNCTION(chinapay)
{
	return SUCCESS;
}
/* }}} */

/* {{{ PHP_MINFO_FUNCTION
 */
PHP_MINFO_FUNCTION(chinapay)
{
	php_info_print_table_start();
	php_info_print_table_header(2, "chinapay support", "enabled");
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
/* {{{ proto string confirm_chinapay_compiled(string arg)
   Return a string to confirm that the module is compiled in */
PHP_FUNCTION(confirm_chinapay_compiled)
{
	char *arg = NULL;
	int arg_len, len;
	char string[256];

	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s", &arg, &arg_len) == FAILURE) {
		return;
	}

	len = sprintf(string, "Congratulations! You have successfully modified ext/%.78s/config.m4. Module %.78s is now compiled into PHP.", "chinapay", arg);
	RETURN_STRINGL(string, len, 1);
}
/* }}} */
/* The previous line is meant for vim and emacs, so it can correctly fold and 
   unfold functions in source code. See the corresponding marks just before 
   function definition, where the functions purpose is also documented. Please 
   follow this convention for the convenience of others editing your code.
*/

/* {{{ proto void setMerKeyFile()
   char keyFile[256]) */
PHP_FUNCTION(setMerKeyFile)
{
	char *filePath = NULL;
	int argc = ZEND_NUM_ARGS();
    int filePathLen;

	if (ZEND_NUM_ARGS() == 0) {
		WRONG_PARAM_COUNT;
	}
	
	 if (zend_parse_parameters(argc TSRMLS_CC, "s", &filePath, &filePathLen) == FAILURE) 
        return;
	setMerKeyFile(filePath);	
	
	
}
/* }}} */

/* {{{ proto void unsetMerKeyFile()
    */
PHP_FUNCTION(unsetMerKeyFile)
{
	unsetMerKeyFile();

}
/* }}} */

/* {{{ proto void setPubKeyFile()
   char keyFile[256]) */
PHP_FUNCTION(setPubKeyFile)
{
	char *filePath = NULL;
	int argc = ZEND_NUM_ARGS();
    int filePathLen;

	if (ZEND_NUM_ARGS() == 0) {
		WRONG_PARAM_COUNT;
	}
	
	 if (zend_parse_parameters(argc TSRMLS_CC, "s", &filePath, &filePathLen) == FAILURE) 
        return;

	setPubKeyFile(filePath);	
	setPubKeyFile2(filePath, "555555");	
}

/* }}} */

/* {{{ proto void setPubKeyFile2()
   char keyFile[256]) */
PHP_FUNCTION(setPubKeyFile2)
{
	char *filePath = NULL;
	char *file2 = NULL;
	int argc = ZEND_NUM_ARGS();
    int filePathLen,file2Len;

	if (ZEND_NUM_ARGS() == 0) {
		WRONG_PARAM_COUNT;
	}
	
	 if (zend_parse_parameters(argc TSRMLS_CC, "ss", &filePath, &filePathLen,&file2, &file2Len) == FAILURE) 
        return;

	setPubKeyFile2(filePath, file2);	
}

/* }}} */


/* {{{ proto void unsetPubKeyFile()
    */
PHP_FUNCTION(unsetPubKeyFile)
{
	unsetPubKeyFile();

}
/* }}} */

/* {{{ proto int signOrder()
   char *MerId,OrderNo,char *TransAmt,char *CurrencyCode,char *TransDate,char *TransType,char *CheckValue) */
PHP_FUNCTION(signOrder)
{
	char *MerID = NULL,*OrderNO = NULL,*TransAmt = NULL,*CurrencyCode=NULL,*TransDate=NULL,*TransType=NULL;
	char CheckValue[257]; 
	int MerIDLen,OrderNOLen,TransAmountLen,CurrencyCodeLen,TransDateLen,TransTypeLen;
	
	int err;

	if (ZEND_NUM_ARGS() != 6) {
		WRONG_PARAM_COUNT;
	}
	
	//获得参数
	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "ssssss", &MerID, &MerIDLen, &OrderNO, &OrderNOLen, &TransAmt, &TransAmountLen, &CurrencyCode, &CurrencyCodeLen, &TransDate, &TransDateLen, &TransType, &TransTypeLen) == FAILURE)
		return;
	
	memset(CheckValue,0,sizeof(CheckValue));
	
	memset(CheckValue, 0, sizeof(CheckValue));
	err = signOrder(MerID,OrderNO,TransAmt,CurrencyCode,TransDate,TransType,CheckValue); 
	if(err < 0) {
		RETURN_LONG(err);
	}
    RETURN_STRING(CheckValue,1);

}
/* }}} */

/* {{{ proto int verifyTransResponse()
   char *MerId,char *OrdId,char *TransAmt,char *CuryId,char *TransDate,char *TransType,char *OrdStat,char *ChkValue) */
PHP_FUNCTION(verifyTransResponse)
{
	char *MerID = NULL,*OrderNO = NULL,*TransAmt = NULL;
	char *CurrencyCode=NULL,*TransDate=NULL,*TransType=NULL;
	char *CheckValue=NULL,*TransStat=NULL; 

	int MerIDLen,OrderNOLen,TransAmountLen;
	int CurrencyCodeLen,TransDateLen,TransTypeLen;
	int CheckValueLen,TransStatLen;
	int err;
	
	if (ZEND_NUM_ARGS() != 8) {
		WRONG_PARAM_COUNT;
	}

	//获得参数
	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "ssssssss", &MerID, &MerIDLen, &OrderNO, &OrderNOLen, &TransAmt, &TransAmountLen, &CurrencyCode, &CurrencyCodeLen, &TransDate, &TransDateLen, &TransType, &TransTypeLen, &TransStat,&TransStatLen, &CheckValue, &CheckValueLen) == FAILURE)
		return;

	err = verifyTransResponse(MerID,OrderNO,TransAmt,CurrencyCode,TransDate,TransType,TransStat,CheckValue);
	
	RETURN_LONG(err);		
	
}
/* }}} */

/* {{{ proto int signData()
   char MerId[15], char SignData[2048], char ChkValue[256]) */
PHP_FUNCTION(signData)
{
	char *MerID = NULL,*SignData = NULL;
	char CheckValue[257]; 
	int MerIDLen,SignDataLen;
	int err;
	
	if (ZEND_NUM_ARGS() != 2) {
		WRONG_PARAM_COUNT;
	}

	//获得参数
	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "ss", &MerID, &MerIDLen, &SignData, &SignDataLen) == FAILURE)
		return;
	
	memset(CheckValue,0,sizeof(CheckValue));
	
	memset(CheckValue, 0, sizeof(CheckValue));
	err = signData(MerID,SignData,CheckValue); 
	if(err < 0) {
		RETURN_LONG(err);
	}
    RETURN_STRING(CheckValue,1);
	
}
/* }}} */

/* {{{ proto int verifySignData()
   char SignData[2049], char ChkValue[256]) */
PHP_FUNCTION(verifySignData)
{
	char *SignData = NULL,*CheckValue = NULL;
	int SignDataLen,CheckValueLen;
	int err;

	if (ZEND_NUM_ARGS() != 2) {
		WRONG_PARAM_COUNT;
	}


	//获得参数
	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "ss", &SignData, &SignDataLen, &CheckValue, &CheckValueLen) == FAILURE)
		return;

	err = verifySignData(SignData,CheckValue);
	
	RETURN_LONG(err);		


}
/* }}} */


/* {{{ proto  ()
    */
PHP_FUNCTION()
{
	if (ZEND_NUM_ARGS() != 0) {
		WRONG_PARAM_COUNT;
	}
	php_error(E_WARNING, ": not yet implemented");
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
