#!/bin/sh

curl "http://192.168.0.2:1218/?charset=utf-8&name=192.168.0.2&opt=view&pos=1" > tmp.sh
if [ "$(cat tmp.sh)" != "HTTPSQS_GET_END" ] ; then
	#sh tmp.sh
	#rm -f tmp.sh
	echo "ok"
fi
