#!/bin/sh

case "$1" in
  start)
	#
	# moServices goes to root
	mount -o remount,rw /
	mount /usr/share/mos /usr/local/etc/mos
	;;
  stop)
	;;
  *)
	echo $"Usage: $0 {start|stop}"
	;;
esac

exit $?
