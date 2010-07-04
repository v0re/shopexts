#!/bin/sh
ls | grep  -v "config.m4\|datasafe.c\|php_datasafe.h\|clean.sh\|build.sh\|test.php" | xargs -i rm -rf {}
