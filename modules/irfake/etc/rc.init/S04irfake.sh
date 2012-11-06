#!/bin/sh

case "$1" in
  start)
	#
	/sbin/insmod /usr/local/etc/irfake/venus_ir_new.ko
	/usr/local/etc/irfake/irfake4 -R -f /usr/local/etc/irfake/irfake.conf&
	;;
  stop)
	killall irfake4
	rmmod venus_ir_new.ko
	;;
  *)
	echo $"Usage: $0 {start|stop}"
	;;
esac

exit $?
