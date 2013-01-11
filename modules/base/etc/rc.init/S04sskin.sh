#!/bin/sh

case "$1" in
  start)
	#
	# bin.squash hack
	[ -f /usr/share/bin/boot_fw ] && /usr/share/bin/boot_fw
	;;
  *)
	echo $"Usage: $0 {start|stop}"
	;;
esac

exit $?
