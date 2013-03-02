#!/bin/sh
#
etc=/usr/local/etc
mos=$etc/mos/iconmenu

guide=/usr/local/bin/guide_menu/scripts/GuideMenu.rss
home=/usr/local/bin/home_menu/scripts/HomeMenu.rss

case "$1" in
  start)
	if [ -f /usr/share/bin/boot_fw.conf ] ; then
		. /usr/share/bin/boot_fw.conf

		if ! [ "$fw" == "hds42l" ] ; then
			echo 'iconmenu: Supports iconbit gui only'
			exit 0
		fi
	else
		if ! [ -f /usr/local/bin/scripts/iconbit-keyboard.rss ] ; then
			echo 'iconmenu: Supports iconbit fw only'
			exit 0
		fi
	fi

	if ! cat /proc/mounts | grep -q '/GuideMenu\.rss' ; then
		[ -f $guide ] && mount -o bind $mos/GuideMenu.rss $guide
	fi

	if ! cat /proc/mounts | grep -q '/HomeMenu\.rss' ; then
		[ -f $home ] && mount -o bind $mos/HomeMenu.rss $home
	fi

	cp -a $mos/getweather.cgi /tmp/www/cgi-bin/getweather.cgi
	;;

  stop)
	cat /proc/mounts | grep -q '/GuideMenu\.rss'&& umount $guide
	cat /proc/mounts | grep -q '/HomeMenu\.rss' && umount $home
	rm -f /tmp/www/cgi-bin/getweather.cgi
	;;

  status)
	if cat /proc/mounts | grep -q '/HomeMenu\.rss'
	then echo "iconmenu running"
	else echo "iconmenu stopped"
	fi
	;;

  enable|disable)
	;;
  *)
	echo "Usage: $0 {start|stop|status|enable|disable}"
	;;
esac

exit $?
