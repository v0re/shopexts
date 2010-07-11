#!/bin/sh
ls | grep  -v "config.m4\|datasafe.c\|php_datasafe.h\|clean.sh\|build.sh\|test.php\|protype\|datasafe_api.h\|libdatasafe.c\|test_datasafe.c" | xargs -i rm -rf {}
