dnl $Id$
dnl config.m4 for extension datasafe

PHP_ARG_ENABLE(datasafe, whether to enable datasafe support,
[  --enable-datasafe           Enable datasafe support])

if test "$PHP_DATASAFE" != "no"; then
    $INCLUDE_DIR=/usr/include
    $LIB_DIR=/usr/lib
    
    PHP_ADD_INCLUDE($INCLUDE_DIR)
    
    LIBNAME=datasafe 
    LIBSYMBOL=shopex_rsa_encrypt
    
    PHP_CHECK_LIBRARY($LIBNAME,$LIBSYMBOL,
    [
        PHP_ADD_LIBRARY_WITH_PATH($LIBNAME, $LIB_DIR, DATASAFE_SHARED_LIBADD)
        AC_DEFINE(HAVE_DATASAFELIB,1,[ ])
    ],[
        AC_MSG_ERROR([libdatasafe.so not found])
    ],[
        -L$LIB_DIR -lm -ldl
    ])

    PHP_SUBST(DATASAFE_SHARED_LIBADD)
    PHP_NEW_EXTENSION(datasafe, datasafe.c, $ext_shared)
fi
