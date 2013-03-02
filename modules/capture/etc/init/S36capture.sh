#!/bin/sh
#
# description: Enable and disable Capture
#
etc=/usr/local/etc
mos=$etc/mos
rc=$etc/rcS
daemon=$mos/bin/captured

case "$1" in
  start)
	mkdir -p /tmp/nfs
	mkfifo /tmp/ir
	;;

stop)
	rm -f /tmp/ir
	;;

status)
	if [ -e /tmp/ir ]
	then echo "capture running"
	else echo "capture stopped"
	fi
	;;
  enable)
	cat $etc/rcS | grep -q $daemon || sed -ri '
s!^([ 	]*)(.*RootApp DvdPlayer.*)$!\1'$daemon' | \2!
s!^([ 	]*)(.*\./DvdPlayer.*)$!\1'$daemon' | \2!
' $etc/rcS

	echo "Please, reboot device!"
	;;

  disable)
	cat $etc/rcS | grep -q $daemon && sed -ri 's!^([ 	]*)'$daemon' \| (.*)$!\1\2!' $etc/rcS

	echo "Please, reboot device!"
	;;

  *)
	echo "Usage: $0 {start|stop|status|enable|disable}"
	;;
esac

exit $?
