dnl $Id$
dnl config.m4 for extension chinapay

dnl Comments in this file start with the string 'dnl'.
dnl Remove where necessary. This file will not work
dnl without editing.

dnl If your extension references something external, use with:

PHP_ARG_WITH(chinapay, for chinapay support,
[  --with-chinapay             Include chinapay support])

dnl Otherwise use enable:

dnl PHP_ARG_ENABLE(chinapay, whether to enable chinapay support,
dnl Make sure that the comment is aligned:
dnl [  --enable-chinapay           Enable chinapay support])

if test "$PHP_CHINAPAY" != "no"; then
  dnl Write more examples of tests here...

  dnl # --with-chinapay -> check with-path
  SEARCH_PATH="/usr/local /usr"    
  SEARCH_FOR="/include/chinapay.h"  
  if test -r $PHP_CHINAPAY/$SEARCH_FOR; then 
     CHINAPAY_DIR=$PHP_CHINAPAY
  else 
     AC_MSG_CHECKING([for chinapay files in default path])
     for i in $SEARCH_PATH ; do
       if test -r $i/$SEARCH_FOR; then
         CHINAPAY_DIR=$i
         AC_MSG_RESULT(found in $i)
       fi
     done
   fi
  
  if test -z "$CHINAPAY_DIR"; then
     AC_MSG_RESULT([not found])
     AC_MSG_ERROR([Please reinstall the chinapay distribution])
  fi

  dnl  # --with-chinapay -> add include path
  PHP_ADD_INCLUDE($CHINAPAY_DIR/include)

  dnl # --with-chinapay -> check for lib and symbol presence
  LIBNAME=chinapay 
  LIBSYMBOL=signOrder

  PHP_CHECK_LIBRARY($LIBNAME,$LIBSYMBOL,
  [
     PHP_ADD_LIBRARY_WITH_PATH($LIBNAME, $CHINAPAY_DIR/lib, CHINAPAY_SHARED_LIBADD)
     AC_DEFINE(HAVE_CHINAPAYLIB,1,[ ])
  ],[
     AC_MSG_ERROR([wrong chinapay lib version or lib not found])
  ],[
     -L$CHINAPAY_DIR/lib -lm -ldl
   ])
  
  PHP_SUBST(CHINAPAY_SHARED_LIBADD)

  PHP_NEW_EXTENSION(chinapay, chinapay.c, $ext_shared)
fi
