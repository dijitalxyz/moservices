#!/bin/sh

case "$1" in
  start)
	#
	lsmod | grep -q venus_ir_new || insmod /usr/local/etc/irfake/venus_ir_new.ko
	ps | grep -q [iI]rfake4 || /usr/local/etc/irfake/irfake4 -R -f /usr/local/etc/irfake/irfake.conf &
	;;
  stop)
	;;
  *)
	echo $"Usage: $0 {start|stop}"
	;;
esac

exit $?
