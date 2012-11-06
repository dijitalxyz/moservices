#!/bin/sh

case "$1" in
  start)
	#
	# moServices goes to root
	mount -o remount,rw /
	mount /usr/share/mos /usr/local/etc/mos
	;;
  stop)
	umount /usr/local/etc/mos
	mount -o remount,ro /
	;;
  *)
	echo $"Usage: $0 {start|stop}"
	;;
esac

exit $?
