#!/bin/sh
#
# description: Enable and disable Capture
#
etc=/usr/local/etc
mos=$etc/mos
rc=$etc/rcS

case "$1" in
  start)
	mkdir -p /tmp/nfs
	touch /tmp/ir
	;;

stop)
	rm -f /tmp/ir
	;;

status)
	if [ -f /tmp/ir ]
	then echo "capture running"
	else echo "capture stopped"
	fi
	;;
  enable)
	echo "Enabling Capture..."

	if ! cat $etc/rcS | grep -q 'tail -f /tmp/ir' ; then
		sed -i '
s/^[ 	]*\(.*RootApp DvdPlayer\&.*\)$/		tail -f \/tmp\/ir | \1/
s/^[ 	]*\(.*\.\/DvdPlayer\&.*\)$/		tail -f \/tmp\/ir | \1/
' $etc/rcS
	fi
	echo "Please, reboot device!"
	;;

  disable)
	echo "Disabling capture..."
	#check for already disabled

	if cat $etc/rcS | grep -q 'tail -f /tmp/ir' ; then
		sed -i '
s/^\([ 	]*\)tail -f \/tmp\/ir | \(.*\)$/\1\2/
' $etc/rcS
	fi
	echo "Please, reboot device!"
	;;

  *)
	echo "Usage: $0 {start|stop|status|enable|disable}"
	;;
esac

exit $?
