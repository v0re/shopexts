dnl $Id$
dnl config.m4 for extension icbc

dnl Comments in this file start with the string 'dnl'.
dnl Remove where necessary. This file will not work
dnl without editing.

dnl If your extension references something external, use with:

PHP_ARG_WITH(icbc, for icbc support,
dnl Make sure that the comment is aligned:
[  --with-icbc             Include icbc support])

dnl Otherwise use enable:

dnl PHP_ARG_ENABLE(icbc, whether to enable icbc support,
dnl Make sure that the comment is aligned:
dnl [  --enable-icbc           Enable icbc support])

if test "$PHP_ICBC" != "no"; then
  dnl Write more examples of tests here...

  dnl # --with-icbc -> check with-path
  SEARCH_PATH="/usr/local /usr"   
  SEARCH_FOR="/include/icbc.h"  
  if test -r $PHP_ICBC/$SEARCH_FOR; then 
  	ICBC_DIR=$PHP_ICBC
  else
     	AC_MSG_CHECKING([for icbc files in default path])
     	for i in $SEARCH_PATH ; do
       		if test -r $i/$SEARCH_FOR; then
         		ICBC_DIR=$i
         		AC_MSG_RESULT(found in $i)
       		fi
     	done
  fi
  dnl
  if test -z "$ICBC_DIR"; then
     	AC_MSG_RESULT([not found])
     	AC_MSG_ERROR([Please reinstall the icbc distribution])
  fi

  dnl # --with-icbc -> add include path
  PHP_ADD_INCLUDE($ICBC_DIR/include)

  dnl # --with-icbc -> check for lib and symbol presence
  LIBNAME=icbc 
  LIBSYMBOL=sign

  PHP_CHECK_LIBRARY($LIBNAME,$LIBSYMBOL,
  [
     	PHP_ADD_LIBRARY_WITH_PATH($LIBNAME, $ICBC_DIR/lib, ICBC_SHARED_LIBADD)
     	AC_DEFINE(HAVE_ICBCLIB,1,[ ])
  ],[
     	AC_MSG_ERROR([wrong icbc lib version or lib not found])
  ],[
     	-L$ICBC_DIR/lib -lm -ldl
  ])
  dnl
  PHP_SUBST(ICBC_SHARED_LIBADD)

  PHP_NEW_EXTENSION(icbc, icbc.c, $ext_shared)
fi
