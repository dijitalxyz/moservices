#!/bin/sh
#
etc=/usr/local/etc
mos=$etc/mos/iconmenu
#
# =====================================
# $1 - what
#
hUmount()
{
	while cat /proc/mounts | grep -q $1 ; do
		umount $1
	done
}
# =====================================
# $1 - where
# $2 - what
#
hMount()
{
	if [ -f $1 ] ; then
		hUmount $1
		mount -o bind $2 $1
	fi
}
# =====================================
# main
#
case "$1" in
  start)
	hMount /usr/local/bin/home_menu/scripts/HomeMenu_noDB.rss   $mos/HomeMenu.rss
	hMount /usr/local/bin/guide_menu/scripts/GuideMenu_noDB.rss $mos/GuideMenu.rss
	hMount /usr/local/bin/home_menu/scripts/HomeMenu.rss   $mos/HomeMenu.rss
	hMount /usr/local/bin/guide_menu/scripts/GuideMenu.rss $mos/GuideMenu.rss

	cp -a $mos/getweather.cgi /tmp/www/cgi-bin/getweather.cgi
	;;

  stop)
	hUmount /usr/local/bin/home_menu/scripts/HomeMenu_noDB.rss
	hUmount /usr/local/bin/guide_menu/scripts/GuideMenu_noDB.rss
	hUmount /usr/local/bin/home_menu/scripts/HomeMenu.rss
	hUmount /usr/local/bin/guide_menu/scripts/GuideMenu.rss

	rm -f /tmp/www/cgi-bin/getweather.cgi
	;;

  status)
	if cat /proc/mounts | grep -Eq '/HomeMenu'
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
