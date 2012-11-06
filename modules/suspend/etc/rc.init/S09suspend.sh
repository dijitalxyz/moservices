#!/bin/sh

if [ -z "$1" ] ; then
	/usr/local/etc/rc.stop
	sync

	echo mem > /sys/power/state

	reboot -f
fi

case "$1" in
  start)
	#
	# mount suspend
	mount -o bind /usr/local/etc/rc.init/S09suspend.sh  /sbin/suspend
	;;
  stop)
	;;
  *)
	echo $"Usage: $0 {start|stop}"
	;;
esac

exit $?
