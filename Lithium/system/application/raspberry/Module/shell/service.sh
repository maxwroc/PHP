#!/bin/sh

if [ "$1" != "deluge-daemon" -a "$1" != "minidlna" -a "$1" != "ddclient" -a "$1" != "samba" ] 
then
	echo "Error: Invalid service name!"
	exit
fi

if [ "$2" != "start" -a "$2" != "stop" -a "$2" != "restart" ] 
then
	echo "Error: Invalid action!"
	exit
fi

/usr/sbin/service $1 $2

echo "OK"
