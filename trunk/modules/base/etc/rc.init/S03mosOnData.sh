#!/bin/sh

case "$1" in
  start)
	#
	# moServices goes to data
	mount /data/mos /usr/local/etc/mos
	;;
  stop)
	umount /usr/local/etc/mos
	;;
  *)
	echo $"Usage: $0 {start|stop}"
	;;
esac

exit $?
