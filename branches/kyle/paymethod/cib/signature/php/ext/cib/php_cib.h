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

#ifndef PHP_CIB_H
#define PHP_CIB_H

extern zend_module_entry cib_module_entry;
#define phpext_cib_ptr &cib_module_entry

#ifdef PHP_WIN32
#define PHP_CIB_API __declspec(dllexport)
#else
#define PHP_CIB_API
#endif

#ifdef ZTS
#include "TSRM.h"
#endif

PHP_MINIT_FUNCTION(cib);
PHP_MSHUTDOWN_FUNCTION(cib);
PHP_RINIT_FUNCTION(cib);
PHP_RSHUTDOWN_FUNCTION(cib);
PHP_MINFO_FUNCTION(cib);

PHP_FUNCTION(confirm_cib_compiled);	/* For testing, remove later. */
PHP_FUNCTION(cibSign);
PHP_FUNCTION();

/* 
  	Declare any global variables you may need between the BEGIN
	and END macros here:     

ZEND_BEGIN_MODULE_GLOBALS(cib)
	long  global_value;
	char *global_string;
ZEND_END_MODULE_GLOBALS(cib)
*/

/* In every utility function you add that needs to use variables 
   in php_cib_globals, call TSRMLS_FETCH(); after declaring other 
   variables used by that function, or better yet, pass in TSRMLS_CC
   after the last function argument and declare your utility function
   with TSRMLS_DC after the last declared argument.  Always refer to
   the globals in your function as CIB_G(variable).  You are 
   encouraged to rename these macros something shorter, see
   examples in any other php module directory.
*/

#ifdef ZTS
#define CIB_G(v) TSRMG(cib_globals_id, zend_cib_globals *, v)
#else
#define CIB_G(v) (cib_globals.v)
#endif

#endif	/* PHP_CIB_H */


/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: noet sw=4 ts=4 fdm=marker
 * vim<600: noet sw=4 ts=4
 */
