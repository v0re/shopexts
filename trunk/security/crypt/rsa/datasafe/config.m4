dnl $Id$
dnl config.m4 for extension datasafe

PHP_ARG_ENABLE(datasafe, whether to enable datasafe support,
[  --enable-datasafe           Enable datasafe support])

if test "$PHP_DATASAFE" != "no"; then
  	PHP_NEW_EXTENSION(datasafe, datasafe.c, $ext_shared)
	PHP_SUBST(OPENSSL_SHARED_LIBADD)
	
	AC_CHECK_LIB(ssl, DSA_get_default_method, AC_DEFINE(HAVE_DSA_DEFAULT_METHOD, 1, [OpenSSL 0.9.7 or later]))
	PHP_SETUP_OPENSSL(OPENSSL_SHARED_LIBADD, 
  	[
    	AC_DEFINE(HAVE_OPENSSL_EXT,1,[ ])
  	], [
    	AC_MSG_ERROR([OpenSSL check failed. Please check config.log for more information.])
  	])
fi
