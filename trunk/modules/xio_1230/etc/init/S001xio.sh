#!/bin/sh
#
# manage x_io kernel module
#
mos=/usr/local/etc/mos

case "$1" in
    start)
	insmod $mos/etc/x_io.ko
	;;
    stop)
	rmmod x_io
	;;
    status)
	if  lsmod | grep -q 'x_io' ; then
		echo "xio running"
	else
		echo "xio stopped"
	fi
	;;
  enable | disable)
	;;
  *)
	echo "Usage: $0 {start|stop|enable|disable|status}"
	;;
esac

exit $?
