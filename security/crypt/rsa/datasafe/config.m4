dnl $Id$
dnl config.m4 for extension datasafe

dnl Comments in this file start with the string 'dnl'.
dnl Remove where necessary. This file will not work
dnl without editing.

dnl If your extension references something external, use with:

dnl PHP_ARG_WITH(datasafe, for datasafe support,
dnl Make sure that the comment is aligned:
dnl [  --with-datasafe             Include datasafe support])

dnl Otherwise use enable:

PHP_ARG_ENABLE(datasafe, whether to enable datasafe support,
dnl Make sure that the comment is aligned:
[  --enable-datasafe           Enable datasafe support])

if test "$PHP_DATASAFE" != "no"; then
  dnl Write more examples of tests here...

  dnl # --with-datasafe -> check with-path
  dnl SEARCH_PATH="/usr/local /usr"     # you might want to change this
  dnl SEARCH_FOR="/include/datasafe.h"  # you most likely want to change this
  dnl if test -r $PHP_DATASAFE/$SEARCH_FOR; then # path given as parameter
  dnl   DATASAFE_DIR=$PHP_DATASAFE
  dnl else # search default path list
  dnl   AC_MSG_CHECKING([for datasafe files in default path])
  dnl   for i in $SEARCH_PATH ; do
  dnl     if test -r $i/$SEARCH_FOR; then
  dnl       DATASAFE_DIR=$i
  dnl       AC_MSG_RESULT(found in $i)
  dnl     fi
  dnl   done
  dnl fi
  dnl
  dnl if test -z "$DATASAFE_DIR"; then
  dnl   AC_MSG_RESULT([not found])
  dnl   AC_MSG_ERROR([Please reinstall the datasafe distribution])
  dnl fi

  dnl # --with-datasafe -> add include path
  dnl PHP_ADD_INCLUDE($DATASAFE_DIR/include)

  dnl # --with-datasafe -> check for lib and symbol presence
  dnl LIBNAME=datasafe # you may want to change this
  dnl LIBSYMBOL=datasafe # you most likely want to change this 

  dnl PHP_CHECK_LIBRARY($LIBNAME,$LIBSYMBOL,
  dnl [
  dnl   PHP_ADD_LIBRARY_WITH_PATH($LIBNAME, $DATASAFE_DIR/lib, DATASAFE_SHARED_LIBADD)
  dnl   AC_DEFINE(HAVE_DATASAFELIB,1,[ ])
  dnl ],[
  dnl   AC_MSG_ERROR([wrong datasafe lib version or lib not found])
  dnl ],[
  dnl   -L$DATASAFE_DIR/lib -lm
  dnl ])
  dnl
  dnl PHP_SUBST(DATASAFE_SHARED_LIBADD)

  PHP_NEW_EXTENSION(datasafe, datasafe.c, $ext_shared)
fi
