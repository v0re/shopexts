#!/bin/sh
HTTPSQS_HOST=192.168.0.2:1218
QUEUE_NAME=192.168.0.2

curl "http://"$HTTPSQS_HOST"/?charset=utf-8&name="$QUEUE_NAME"&opt=view&pos=1" > tmp.sh
if [ "$(cat tmp.sh)" != "HTTPSQS_GET_END" ] ; then
	sh tmp.sh
	rm -f tmp.sh
	echo "ok"
fi
