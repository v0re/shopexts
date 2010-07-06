dnl $Id$
dnl config.m4 for extension datasafe

PHP_ARG_ENABLE(datasafe, whether to enable datasafe support,
[  --enable-datasafe           Enable datasafe support])

if test "$PHP_DATASAFE" != "no"; then
  	AC_CHECK_LIB(ssl, DSA_get_default_method, AC_DEFINE(HAVE_DSA_DEFAULT_METHOD, 1, [OpenSSL 0.9.7 or later]))
	PHP_NEW_EXTENSION(datasafe, datasafe.c, $ext_shared)
	dnl PHP_SUBST(DATASAFE_SHARED_LIBADD)
	
	dnl AC_CHECK_LIB(ssl, DSA_get_default_method, AC_DEFINE(HAVE_DSA_DEFAULT_METHOD, 1, [OpenSSL 0.9.7 or later]))
	dnl PHP_SETUP_OPENSSL(DATASAFE_SHARED_LIBADD, 
  	dnl [
    dnl 	AC_DEFINE(HAVE_OPENSSL_EXT,1,[ ])
  	dnl ], [
    dnl 	AC_MSG_ERROR([OpenSSL check failed. Please check config.log for more information.])
  	dnl ])
fi
