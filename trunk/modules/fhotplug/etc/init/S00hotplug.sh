#!/bin/sh
#
# fork hotplug starting script
#
bin=/usr/local/etc/mos/bin
orig=/tmp/hotplug.orig

case "$1" in
  start)
	fork=$bin/fork_hotplug
	if [ -f $fork ] ; then
		[ ! -f $orig ] && cat /proc/sys/kernel/hotplug > $orig
		echo $fork >/proc/sys/kernel/hotplug
	fi
	;;

  stop)
	if [ -f $orig ] ; then
		cat $orig >/proc/sys/kernel/hotplug
		rm -f $orig
	else
		echo '/sbin/hotplug' >/proc/sys/kernel/hotplug
	fi
	;;

  status)
	if [ -f $orig ] ; then
		echo 'fork hotplug running'
	else
		echo 'fork hotplug stopped'
	fi
	;;

  enable|disable)
	;;
  *)
	echo $"Usage: $0 {start|stop|status|enable|disable}"
	;;
esac

exit $?
