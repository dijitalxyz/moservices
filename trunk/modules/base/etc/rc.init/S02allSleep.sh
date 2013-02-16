#!/bin/sh
#
# description: patch of umount loop devices and unplug USB devices
#
case "$1" in
  start)
	;;
  stop)
	sync
	echo 3 >/proc/sys/vm/drop_caches

	echo "$0 : unloop"
	l=$( losetup -f )
	for i in /dev/loop/* ; do
		[ -e $i ] || continue
		[ "$i" == "$l" ] && break
		losetup $i | grep -qe '/tmp/usbmounts/' -e '/tmp/hdd/' || continue

		d=$( cat /proc/mounts | grep -E '^'$i | cut -d ' ' -f 2 )
		if [ -n "$d" ] ; then
			mount -o remount,ro $d
			umount $d
			losetup | grep -q $i && losetup -d $i
		fi
	done;

	echo "$0 : mount as read only and umount"
	for i in $( cat /proc/mounts | sed -n '1!G;h;$p' | grep -E -e '^/dev/scsi/host' -e '^/dev/ide/host' -e '^/dev/sd' | cut -d" " -f 2) ; do
		mount -o remount,ro $i
		umount $i
	done;

	echo "$0 : unplug USB"

	for i in $( lsmod | cut -d ' ' -f 1 ) ; do
		cat /lib/modules/$( uname -r )/modules.usbmap | grep -qE '^'$i' ' && rmmod $i
	done

	echo "$0 : unload modules"
	for i in $( lsmod | cut -d ' ' -f 1 ) ; do
		rmmod $i
	done
	;;
  *)
	echo "Usage: $0 {start|stop}"
esac

exit $?
