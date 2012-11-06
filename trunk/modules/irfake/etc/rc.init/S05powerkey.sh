#!/bin/sh

case "$1" in
  start)
	# fork for r6+
	echo "J_MODE|1" > /sys/devices/platform/VenusIR/powerkey_irrp_new
	;;
  stop)
	;;
  *)
	echo $"Usage: $0 {start|stop}"
	;;
esac

exit $?
