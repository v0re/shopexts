dnl $Id$
dnl config.m4 for extension cib

dnl Comments in this file start with the string 'dnl'.
dnl Remove where necessary. This file will not work
dnl without editing.

dnl If your extension references something external, use with:

    PHP_ARG_WITH(cib, for cib support,
dnl Make sure that the comment is aligned:
    [  --with-cib             Include cib support])

dnl Otherwise use enable:

dnl PHP_ARG_ENABLE(cib, whether to enable cib support,
dnl Make sure that the comment is aligned:
dnl [  --enable-cib           Enable cib support])

if test "$PHP_CIB" != "no"; then
  dnl Write more examples of tests here...

  dnl # --with-cib -> check with-path
  SEARCH_PATH="/usr/local /usr"
  SEARCH_FOR="/include/cib_api.h"
  if test -r $PHP_CIB/$SEARCH_FOR; then
    CIB_DIR=$PHP_CIB
  else
    AC_MSG_CHECKING([for cib files in default path])
    for i in $SEARCH_PATH ; do
        if test -r $i/$SEARCH_FOR; then
            CIB_DIR=$i
            AC_MSG_RESULT(found in $i)
        fi
    done
  fi
   
  if test -z "$CIB_DIR"; then
    AC_MSG_RESULT([not found])
    AC_MSG_ERROR([Make sure CIB header and lib files are correct! ])
  fi

  dnl # --with-cib -> add include path
  PHP_ADD_INCLUDE($CIB_DIR/include)

  dnl # --with-cib -> check for lib and symbol presence
  LIBNAME=cib 
  LIBSYMBOL=ANSIX99

  PHP_CHECK_LIBRARY($LIBNAME,$LIBSYMBOL,
  [
    PHP_ADD_LIBRARY_WITH_PATH($LIBNAME, $CIB_DIR/lib, CIB_SHARED_LIBADD)
    AC_DEFINE(HAVE_CIBLIB,1,[ ])
  ],[
    AC_MSG_ERROR([wrong cib lib version or lib not found])
  ],[
    -L$CIB_DIR/lib -lm -ldl
  ])
  dnl
  PHP_SUBST(CIB_SHARED_LIBADD)

  PHP_NEW_EXTENSION(cib, cib.c, $ext_shared)
fi
