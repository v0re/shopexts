#!/bin/sh
svn update
cd protype
sh build
cd ..
phpize
./configure --enable-datasafe
make 
