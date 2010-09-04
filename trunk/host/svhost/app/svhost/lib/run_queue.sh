#!/bin/sh
HTTPSQS_HOST=192.168.0.2:1218
QUEUE_NAME=192.168.0.2

while true
do
	curl "http://"$HTTPSQS_HOST"/?charset=utf-8&name="$QUEUE_NAME"&opt=get&pos=1" > tmp.sh 2>/dev/null
	if [ "$(cat tmp.sh)" != "HTTPSQS_GET_END" ] ; then
		sh tmp.sh
		rm -f tmp.sh
		echo "ok"
	fi
	sleep 1
done
